<?php
namespace Collections;

use Exceptions\QueryException;
use Exceptions\WrongCollectionType;
use Managers\DatabaseManager;
use Models\Horse;
use Models\Race;

/**
 * Class HorseCollection
 * A collection of multiple horses
 */
class HorseCollection extends AbstractCollection
{
	/** @var string $supportedClass The class this collection contains */
	protected static $supportedClass = 'Models\Horse';

	/**
	 * Find all horses for a given race
	 *
	 * @param Race $race The race to find horses for
	 * @return HorseCollection A collection containing all horses for the given race
	 * @throws QueryException|WrongCollectionType In case the requests couldn't be handled properly
	 */
	public static function findByRace(Race $race)
	{
		$db = DatabaseManager::getInstance();

		$rawHorses = $db->selectMulti("SELECT id FROM horses WHERE raceId=:raceId", ['raceId' => $race->getId()]);

		$horses = new HorseCollection();
		foreach($rawHorses as $rawHorse)
		{
			$horses->add(Horse::find($rawHorse['id']));
		}
		return $horses;
	}

	/**
	 * Select the fastest horse
	 * @return Horse|null Returns either the horse instance or null in case no fastest horse exists
	 * @throws QueryException In case the fastest horse couldn't be retrieved
	 */
	public static function getFastest()
	{
		$db = DatabaseManager::getInstance();

		// First query would select the fastest horse in all races, finished or not
		// The second query only selects the fastest horse from all finished races and excludes horses from races which are still running
		//$rawData = $db->select("SELECT id FROM horses WHERE timeNeeded>-1 ORDER BY timeNeeded ASC LIMIT 1");
		$rawData = $db->select("SELECT h.id FROM horses h LEFT JOIN races r ON(h.raceId=r.id) WHERE h.timeNeeded>-1 AND r.timeFinished IS NOT NULL ORDER BY timeNeeded ASC LIMIT 1");

		// No horse was found, return null
		if(empty($rawData))
		{
			return null;
		}

		$horse = Horse::find($rawData['id']);
		return $horse;
	}

}