<?php
namespace Collections;

use Exceptions\QueryException;
use Managers\DatabaseManager;
use Models\Race;

/**
 * Class RaceCollection
 * A collection of multiple races
 */
class RaceCollection extends AbstractCollection
{
	/** @var string $supportedClass The class this collection contains */
	protected static $supportedClass = 'Models\Race';

	/**
	 * Get all currently running races
	 *
	 * @return RaceCollection All races currently running
	 * @throws QueryException|\Exception In case the request couldn't be handled
	 */
	public static function getRunningRaces()
	{
		$db = DatabaseManager::getInstance();

		$rawRaces = $db->selectMulti("SELECT * FROM races WHERE timeFinished IS NULL ORDER BY timeStarted ASC");

		$races = new RaceCollection();
		foreach($rawRaces as $rawRace)
		{
			$races->add(Race::find($rawRace['id']));
		}
		return $races;
	}

	/**
	 * Get a certain amount of recently finished races
	 *
	 * @param int $num The number of races to return
	 * @return RaceCollection
	 * @throws QueryException|\Exception In case the request couldn't be handled
	 */
	public static function getLastResults($num=5)
	{
		$db = DatabaseManager::getInstance();

		$rawRaces = $db->selectMulti("SELECT id FROM races WHERE timeFinished IS NOT NULL ORDER BY timeFinished DESC LIMIT :num", ["num" => $num]);

		$races = new RaceCollection();
		foreach($rawRaces as $rawRace)
		{
			$races->add(Race::find($rawRace['id']));
		}
		return $races;
	}
}