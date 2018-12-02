<?php

/**
 * Class AdvanceRacesController
 *
 * Controller which advances all active races by 10 seconds
 */
class AdvanceRacesController implements ControllerInterface
{
	/**
	 *  {@inheritdoc}
	 */
	public function handleRequest()
	{
		try
		{
			// Get all active races
			$activeRaces = Race::getRunningRaces();

			// Loop through them and advance them
			/** @var Race $activeRace */
			foreach ($activeRaces as $activeRace)
			{
				$activeRace->advanceTenSeconds();
			}

			// Redirect back to index to show actualized race list
			ViewManager::redirectTo('index');
		}
		catch (Exception $exception)
		{
			ViewManager::error('There was some sort of error with your request: ' . $exception->getMessage());
		}
	}
}