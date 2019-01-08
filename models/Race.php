<?php
namespace Models;

use Collections\HorseCollection;
use DateTime;
use Exceptions\QueryException;
use Exceptions\RaceFinishedException;
use Exceptions\TooManyRacesException;
use Managers\DatabaseManager;
use Managers\ConfigManager;

/**
 * Class Race
 *
 * Model for a single race
 */
class Race extends AbstractModel
{
	/** @var string $table The table that stores these models. Used to query when searching for one */
	protected static $table = 'races';

	/** @var int $id The internal ID of this race */
	protected $id = -1;

	/** @var int $timeRun The total time this race ran so far (in seconds) */
	protected $timeRun = 0;
	/** @var HorseCollection An array of horses participating in this race */
	protected $horses = null;

	/** @var DateTime $timeStarted The time this race was created/started. Used for sorting */
	protected $timeStarted;
	/** @var DateTime|null $timeFinished The time this race was finished or null. Used for sorting */
	protected $timeFinished = null;

	/**
	 * {@inheritdoc}
	 */
	protected function __construct($rawData=[])
	{
		parent::__construct($rawData);
		if(!empty($rawData))
		{
			$this->id = $rawData['id'];
			$this->timeRun = $rawData['timeRun'];
			$this->timeStarted = new DateTime($rawData['timeStarted']);
			$this->timeFinished = $rawData['timeFinished'] == null ? null : new DateTime($rawData['timeFinished']);
			$this->horses = HorseCollection::findByRace($this);
		}
	}

	/**
	 * Function to create a new race including its horses
	 *
	 * @return Race The created race
	 * @throws QueryException|TooManyRacesException|\Exception In case the new race couldn't be created
	 */
	public static function create()
	{
		$db = DatabaseManager::getInstance();

		// Check how many races are currently running
		$numRunning = $db->select("SELECT COUNT(id) AS num FROM races WHERE timeFinished IS NULL");
		$numRunning = $numRunning['num'];

		// If there are already enough races throw a simple exception
		if($numRunning >= ConfigManager::getInstance()->getNumRaces())
		{
			throw new TooManyRacesException(ConfigManager::getInstance()->getNumRaces());
		}

		// Otherwise create the race, save the current time
		$race = new static();
		$race->timeStarted = new DateTime();

		// As we're doing multiple queries do them in a transaction
		$db->beginTransaction();
		try
		{
			// Insert the race into the database and save the ID of the race
			$db->insert("INSERT INTO races (timeRun, timeStarted, timeFinished) VALUES (0, :timeStarted, NULL)", [
				"timeStarted" => $race->timeStarted->format("Y-m-d H:i:s")
			]);
			$race->id = $db->getInsertedId();

			// Afterwards create all horses for this race
			$race->horses = new HorseCollection();
			for ($i = 0; $i < ConfigManager::getInstance()->getNumHorses(); $i++)
			{
				$race->horses->add(Horse::createForRace($race));
			}

			// And commit the whole transaction
			$db->commit();
		}
		catch (QueryException $exception)
		{
			// If we had some sort of error try to roll back the changes made but rethrow the exception so the controller can properly handle it
			$db->rollBack();
			throw $exception;
		}

		parent::addInstance($race);
		return $race;
	}

	/**
	 * Returns the internal ID of this race
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the time in seconds this race ran
	 * @return int
	 */
	public function getTimeRun()
	{
		return $this->timeRun;
	}

	/**
	 * Get the time this race finished
	 * @return DateTime|null
	 */
	public function getTimeFinished()
	{
		return $this->timeFinished;
	}

	/**
	 * Advance the race by ten seconds
	 * @throws RaceFinishedException|QueryException In case the request couldn't be handled properly
	 */
	public function advanceTenSeconds()
	{
		if($this->timeFinished != null)
		{
			throw new RaceFinishedException();
		}

		$db = DatabaseManager::getInstance();
		// As we're potential doing two queries we're going to do that in a transaction
		$db->beginTransaction();
		try
		{
			// First increase the counter and save it in our database
			$this->timeRun += 10;
			$db->update("UPDATE races SET timeRun=:timeRun WHERE id=:id", ["timeRun" => $this->timeRun, "id" => $this->id]);

			// Afterwards we need to check whether the race has finished. To do so we grab the sorted horse list and check the last one
			$sortedHorses = $this->getSortedHorses();
			$numHorses = ConfigManager::getInstance()->getNumHorses();
			$lastHorse = $sortedHorses[$numHorses - 1];

			// If the last horse has completed the race we can update the time finished and mark this race as completed
			if ($lastHorse->getCoveredDistance($this->timeRun) >= ConfigManager::getInstance()->getRaceDistance())
			{
				DatabaseManager::getInstance()->update("UPDATE races SET timeFinished=NOW() WHERE id=:id", ["id" => $this->id]);
			}

			// Now try to commit everything
			$db->commit();
		}
		catch (QueryException $exception)
		{
			// If something went wrong roll everything back and rethrow the exception for the controller
			$db->rollBack();
			throw $exception;
		}
	}

	/**
	 * Returns an array of all horses, sorted by their position in the race
	 * @return HorseCollection
	 */
	public function getSortedHorses()
	{
		$this->horses->sort(function (Horse $a, Horse $b) {
			// If the covered distance is the same (eg both horses finished the race already) we compare the time they needed
			if ($a->getCoveredDistance($this->timeRun) == $b->getCoveredDistance($this->timeRun))
			{
				return ($a->getTimeNeeded() < $b->getTimeNeeded()) ? -1 : 1;
			}
			// Otherwise we simply compare the distance covered
			return ($a->getCoveredDistance($this->timeRun) > $b->getCoveredDistance($this->timeRun)) ? -1 : 1;
		});

		return $this->horses;
	}
}