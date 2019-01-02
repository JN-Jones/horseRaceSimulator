<?php
namespace Controllers;

use Models\Race;
use Managers\ViewManager;

/**
 * Class CreateRaceController
 *
 * Controller to create a new race
 */
class CreateRaceController implements ControllerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function handleRequest()
	{
		// Try to create a new race and catch the exception if we have too many races already
		try
		{
			Race::create();

			// In case of success we redirect back to index where the new race will be displayed
			ViewManager::redirectTo('index');
		}
		catch (\Exception $exception)
		{
			// Otherwise we show the error message to the user
			ViewManager::error($exception->getMessage());
		}
	}
}