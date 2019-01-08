<?php
namespace Controllers;

use Collections\RaceCollection;
use Exceptions\QueryException;
use Exceptions\RaceFinishedException;
use Exceptions\ViewNotFoundException;
use Models\Race;
use Managers\ViewManager;

/**
 * Class AdvanceRacesController
 *
 * Controller which advances all active races by 10 seconds
 */
class AdvanceRacesController extends AbstractController
{
	/**
	 *  {@inheritdoc}
	 */
	public function handleGetRequest()
	{
		try
		{
			// Get all active races
			$activeRaces = RaceCollection::getRunningRaces();

			// Loop through them and advance them
			/** @var Race $activeRace */
			foreach ($activeRaces as $activeRace)
			{
				$activeRace->advanceTenSeconds();
			}

			// Redirect back to index to show actualized race list
			ViewManager::redirectTo('index');
		}
		catch (RaceFinishedException $exception)
		{
			ViewManager::error($exception->getMessage());
		}
		catch (QueryException | ViewNotFoundException $exception)
		{
			ViewManager::error('System error');
		}
	}
}