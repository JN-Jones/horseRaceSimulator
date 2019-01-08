<?php
namespace Controllers;

use Collections\HorseCollection;
use Collections\RaceCollection;
use Exceptions\QueryException;
use Exceptions\ViewNotFoundException;
use Models\Race;
use Models\Horse;
use Managers\ViewManager;
use Managers\ConfigManager;

/**
 * Class IndexController
 *
 * Controller which handles all the index and listing stuff
 */
class IndexController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handleGetRequest()
	{
		try
		{
			// First get everything we need: The active races, the last races and our fastest horse
			$activeRaces = RaceCollection::getRunningRaces();
			$lastRaces = RaceCollection::getLastResults(ConfigManager::getInstance()->getLastRacesDisplayed());
			$fastestHorse = HorseCollection::getFastest();

			// The rest is done by the ViewManager. To do so we pass all variables we created befores
			ViewManager::getView('index', [
				'activeRaces' => $activeRaces,
				'lastRaces' => $lastRaces,
				'fastestHorse' => $fastestHorse
			]);
		}
		catch (QueryException | ViewNotFoundException $exception)
		{
			ViewManager::error('System error');
		}
	}
}