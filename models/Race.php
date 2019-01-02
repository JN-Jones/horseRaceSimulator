<?php
namespace Models;

use DateTime;
use Managers\DatabaseManager;
use Managers\ConfigManager;

/**
 * Class Race
 *
 * Model for a single race
 */
class Race
{
	/** @var int $id The internal ID of this race */
	private $id = -1;

	/** @var int $timeRun The total time this race ran so far (in seconds) */
	private $timeRun = 0;
	/** @var Horse[] An array of horses participating in this race */
	private $horses = [];

	/** @var DateTime $timeStarted The time this race was created/started. Used for sorting */
	private $timeStarted;
	/** @var DateTime|null $timeFinished The time this race was finished or null. Used for sorting */
	private $timeFinished = null;

	/**
	 * Function to create a new race including its horses
	 *
	 * @return Race The created race
	 * @throws \Exception In case the new race couldn't be created
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
			throw new \Exception('There are ' . ConfigManager::getInstance()->getNumRaces() . ' races active, please finish one first');
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
			for ($i = 0; $i < ConfigManager::getInstance()->getNumHorses(); $i++)
			{
				$race->horses[] = Horse::create($race);
			}

			// And commit the whole transaction
			$db->commit();
		}
		catch (\Exception $exception)
		{
			// If we had some sort of error try to roll back the changes made but rethrow the exception so the controller can properly handle it
			$db->rollBack();
			throw $exception;
		}

		return $race;
	}

	/**
	 * Get all currently running races
	 *
	 * @return Race[] All races currently running
	 * @throws \Exception In case the request couldn't be handled
	 */
	public static function getRunningRaces()
	{
		$db = DatabaseManager::getInstance();

		$rawRaces = $db->selectMulti("SELECT * FROM races WHERE timeFinished IS NULL ORDER BY timeStarted ASC");

		$races = [];
		foreach($rawRaces as $rawRace)
		{
			$race = new static();
			$race->id = $rawRace['id'];
			$race->timeRun = $rawRace['timeRun'];
			$race->timeStarted = new DateTime($rawRace['timeStarted']);
			$race->timeFinished = $rawRace['timeFinished'] == null ? null : new DateTime($rawRace['timeFinished']);
			$race->horses = Horse::findByRace($race);
			$races[] = $race;
		}
		return $races;
	}

	/**
	 * Get a certain amount of recently finished races
	 *
	 * @param int $num The number of races to return
	 * @return Race[]
	 * @throws \Exception In case the request couldn't be handled
	 */
	public static function getLastResults($num=5)
	{
		$db = DatabaseManager::getInstance();

		$rawRaces = $db->selectMulti("SELECT * FROM races WHERE timeFinished IS NOT NULL ORDER BY timeFinished DESC LIMIT :num", ["num" => $num]);

		$races = [];
		foreach($rawRaces as $rawRace)
		{
			$race = new static();
			$race->id = $rawRace['id'];
			$race->timeRun = $rawRace['timeRun'];
			$race->timeStarted = new DateTime($rawRace['timeStarted']);
			$race->timeFinished = $rawRace['timeFinished'] == null ? null : new DateTime($rawRace['timeFinished']);
			$race->horses = Horse::findByRace($race);
			$races[] = $race;
		}
		return $races;
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
	 * @throws \Exception In case the request couldn't be handled properly
	 */
	public function advanceTenSeconds()
	{
		if($this->timeFinished != null)
		{
			throw new \Exception('This race has already finished');
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
		catch (\Exception $exception)
		{
			// If something went wrong roll everything back and rethrow the exception for the controller
			$db->rollBack();
			throw $exception;
		}
	}

	/**
	 * Returns an array of all horses, sorted by their position in the race
	 * @return Horse[]
	 */
	public function getSortedHorses()
	{
		usort($this->horses, function (Horse $a, Horse $b) {
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