<?php
namespace Models;

use Exceptions\QueryException;
use Misc\Random;
use Managers\DatabaseManager;
use Managers\ConfigManager;

/**
 * Class Horse
 *
 * Model for a single horse
 */
class Horse extends AbstractModel
{
	/** @var string $table The table that stores these models. Used to query when searching for one */
	protected static $table = 'horses';

	/** @var int $baseSpeed The base speed of all horses */
	protected static $baseSpeed = 5;

	/** @var int $id Internal ID of this horse */
	protected $id = -1;
	/** @var int $raceId The internal ID of the @Race this horse participates in */
	protected $raceId = -1;

	/** @var float $speed The individual speed stat for this horse */
	protected $speed = 0.;
	/** @var float $strength The individual strength stat for this horse */
	protected $strength = 0.;
	/** @var float $endurance The individual endurance stat for this horse */
	protected $endurance = 0.;

	/** @var int $timeNeeded As soon as this horse has finished it race the total time needed will be saved here (in seconds) */
	protected $timeNeeded = -1;

	/**
	 * {@inheritdoc}
	 */
	protected function __construct($rawData=[])
	{
		parent::__construct($rawData);
		if(!empty($rawData))
		{
			$this->id = $rawData['id'];
			$this->raceId = $rawData['raceId'];
			$this->speed = $rawData['speed'];
			$this->strength = $rawData['strength'];
			$this->endurance = $rawData['endurance'];
			$this->timeNeeded = $rawData['timeNeeded'];
		}
	}

	/**
	 * Not implemented, see @createForRace
	 * @throws \Exception
	 */
	public static function create()
	{
		throw new \Exception("Horse doesn't implement this method, please use `createForRace`");
	}

	/**
	 * Creates a single horse for a given race
	 *
	 * @param Race $race The instance of the race this horse will participate in
	 * @return Horse The generated horse
	 * @throws QueryException In case the insert query couldn't be handled
	 */
	public static function createForRace(Race $race)
	{
		// Create a horse object and assign it to the race
		$horse = new static();
		$horse->raceId = $race->getId();
		// Generate the random stats. getFloat returns a number between 0.0 and 1.0 so multiply by 10
		$horse->speed = Random::getFloat()*10;
		$horse->strength = Random::getFloat()*10;
		$horse->endurance = Random::getFloat()*10;

		// Insert the horse into the database with the generated stats
		$db = DatabaseManager::getInstance();
		$db->insert("INSERT INTO horses (raceId, speed, strength, endurance, timeNeeded)
			VALUES (:raceId, :speed, :strength, :endurance, -1)", [
				"raceId" => $horse->raceId,
				"speed" => $horse->speed,
				"strength" => $horse->strength,
				"endurance" => $horse->endurance
		]);

		// Save the id
		$horse->id = $db->getInsertedId();

		parent::addInstance($horse);
		return $horse;
	}

	/**
	 * Returns the internal ID of the horse
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns the internal ID of the race the horse participates in
	 * @return int
	 */
	public function getRaceId()
	{
		return $this->raceId;
	}

	/**
	 * Returns the speed stat of this horse
	 * @return float
	 */
	public function getSpeed()
	{
		return $this->speed;
	}

	/**
	 * Returns the strength stat of this horse
	 * @return float
	 */
	public function getStrength()
	{
		return $this->strength;
	}

	/**
	 * Returns the endurance stat of this horse
	 * @return float
	 */
	public function getEndurance()
	{
		return $this->endurance;
	}

	/**
	 * Returns the time needed to complete the race (in seconds, -1 if the horse is not finished yet)
	 * @return int
	 */
	public function getTimeNeeded()
	{
		return $this->timeNeeded;
	}

	/**
	 * Calculates the distance covered by this horse after the given amount of seconds
	 * @param int $seconds The amount of seconds to calculate the distance for
	 * @return float The distance covered in meters
	 */
	public function getCoveredDistance($seconds)
	{
		// If we have a timeNeeded it means the horse has finished the race so simply return the race distance
		if($this->timeNeeded > 0)
		{
			return ConfigManager::getInstance()->getRaceDistance();
		}

		// First calculate the actual speed of the horse and check how far it would've run if it ran the whole time at that speed
		$fullSpeed = self::$baseSpeed + $this->speed;
		$distanceAtFullSpeed = $fullSpeed * $seconds;

		// If it's less than it's endurance allows it we're finished and can simply return the distance at full speed
		if($distanceAtFullSpeed < $this->endurance*100)
		{
			return round($distanceAtFullSpeed,2);
		}

		// Otherwise we need to calculate the time it was able to run at full speed
		$distanceAtFullSpeed = $this->endurance*100;
		$timeAtFullSpeed = $distanceAtFullSpeed / $fullSpeed;

		// So we can calculate the time it ran at the slower speed, calculate that speed and the distance that covered
		$slowedTime = $seconds - $timeAtFullSpeed;
		$slowedSpeed = $fullSpeed - (5 * (1- $this->strength*0.08));
		$slowedDistance = $slowedTime * $slowedSpeed;

		// The total distance is the sum of the distances at full and slow speed
		$totalDistance = $distanceAtFullSpeed + $slowedDistance;

		// If the total distance is more than the actual race distance it means the horse has finished the race
		// Due to the time steps we need to calculate the exact time needed
		if($totalDistance >= ConfigManager::getInstance()->getRaceDistance())
		{
			// Calculate the actual time ran at slow speed
			$actualSlowedDistance = ConfigManager::getInstance()->getRaceDistance() - $distanceAtFullSpeed;
			$slowedTime = $actualSlowedDistance / $slowedSpeed;
			// Calculate the total time, save it in @timeNeeded and update our database with the value
			$totalTime = $timeAtFullSpeed + $slowedTime;
			$this->timeNeeded = $totalTime;

			try
			{
				DatabaseManager::getInstance()->update("UPDATE horses SET timeNeeded=:timeNeeded WHERE id=:id", [
					"timeNeeded" => $this->timeNeeded,
					"id" => $this->id
				]);
			}
			catch (QueryException $e)
			{
				// Do nothing. If the query didn't work the next call to this function will properly recalculate the time needed and retry the update
			}

			// The distance ran is obviously the race distance
			$totalDistance = ConfigManager::getInstance()->getRaceDistance();
		}
		return round($totalDistance,2);
	}
}