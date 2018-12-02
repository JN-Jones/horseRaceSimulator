<?php

/**
 * Class IndexController
 *
 * Controller which handles all the index and listing stuff
 */
class IndexController implements ControllerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function handleRequest()
	{
		try
		{
			// First get everything we need: The active races, the last races and our fastest horse
			$activeRaces = Race::getRunningRaces();
			$lastRaces = Race::getLastResults(ConfigManager::getInstance()->getLastRacesDisplayed());
			$fastestHorse = Horse::getFastest();

			// The rest is done by the ViewManager. To do so we pass all variables we created befores
			ViewManager::getView('index', [
				'activeRaces' => $activeRaces,
				'lastRaces' => $lastRaces,
				'fastestHorse' => $fastestHorse
			]);
		}
		catch (Exception $exception)
		{
			ViewManager::error($exception->getMessage());
		}
	}
}