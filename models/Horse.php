<?php

/**
 * Class Horse
 *
 * Model for a single horse
 */
class Horse
{
	/** @var int $baseSpeed The base speed of all horses */
	private static $baseSpeed = 5;

	/** @var int $id Internal ID of this horse */
	private $id = -1;
	/** @var int $raceId The internal ID of the @Race this horse participates in */
	private $raceId = -1;

	/** @var float $speed The individual speed stat for this horse */
	private $speed = 0.;
	/** @var float $strength The individual strength stat for this horse */
	private $strength = 0.;
	/** @var float $endurance The individual endurance stat for this horse */
	private $endurance = 0.;

	/** @var int $timeNeeded As soon as this horse has finished it race the total time needed will be saved here (in seconds) */
	private $timeNeeded = -1;

	/**
	 * Creates a single horse for a given race
	 *
	 * @param Race $race The instance of the race this horse will participate in
	 * @return Horse The generated horse
	 * @throws Exception In case the horse couldn't be generated
	 */
	public static function create(Race $race)
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

		return $horse;
	}

	/**
	 * Find all horses for a given race
	 *
	 * @param Race $race The race to find horses for
	 * @return Horse[] An array containing all horses for the given race
	 * @throws Exception In case the requests couldn't be handled properly
	 */
	public static function findByRace($race)
	{
		$db = DatabaseManager::getInstance();

		$rawHorses = $db->selectMulti("SELECT * FROM horses WHERE raceId=:raceId", ['raceId' => $race->getId()]);

		$horses = [];
		foreach($rawHorses as $rawHorse)
		{
			$horse = new static();
			$horse->id = $rawHorse['id'];
			$horse->raceId = $rawHorse['raceId'];
			$horse->speed = $rawHorse['speed'];
			$horse->strength = $rawHorse['strength'];
			$horse->endurance = $rawHorse['endurance'];
			$horse->timeNeeded = $rawHorse['timeNeeded'];
			$horses[] = $horse;
		}
		return $horses;
	}

	/**
	 * Select the fastest horse
	 * @return Horse|null Returns either the horse instance or null in case no fastest horse exists
	 * @throws Exception In case the fastest horse couldn't be retrieved
	 */
	public static function getFastest()
	{
		$db = DatabaseManager::getInstance();

		// First query would select the fastest horse in all races, finished or not
		// The second query only selects the fastest horse from all finished races and excludes horses from races which are still running
		//$rawData = $db->select("SELECT * FROM horses WHERE timeNeeded>-1 ORDER BY timeNeeded ASC LIMIT 1");
		$rawData = $db->select("SELECT h.* FROM horses h LEFT JOIN races r ON(h.raceId=r.id) WHERE h.timeNeeded>-1 AND r.timeFinished IS NOT NULL ORDER BY timeNeeded ASC LIMIT 1");

		// No horse was found, return null
		if(empty($rawData))
		{
			return null;
		}
		
		$horse = new static();
		$horse->id = $rawData['id'];
		$horse->raceId = $rawData['raceId'];
		$horse->speed = $rawData['speed'];
		$horse->strength = $rawData['strength'];
		$horse->endurance = $rawData['endurance'];
		$horse->timeNeeded = $rawData['timeNeeded'];
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
			catch (Exception $e)
			{
				// Do nothing. If the query didn't work the next call to this function will properly recalculate the time needed and retry the update
			}

			// The distance ran is obviously the race distance
			$totalDistance = ConfigManager::getInstance()->getRaceDistance();
		}
		return round($totalDistance,2);
	}
}