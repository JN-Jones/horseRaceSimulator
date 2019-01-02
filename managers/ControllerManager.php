<?php
namespace Managers;

/**
 * Class ControllerManager
 *
 * First class to handle a request. Checks which controller should actually handle it and calls that controller
 */
class ControllerManager
{
	/**
	 * Handles the actual request
	 */
	public static function handleRequest()
	{
		// Base module is index
		$module = 'index';

		// If a module is set and doesn't contain a "." or "/" (potential path to do odd stuff) we try to load that module
		if(!empty($_GET['module']) && strpos($_GET['module'], '.') === false && strpos($_GET['module'], '/') === false)
		{
			$module = $_GET['module'];
		}

		// Generate the proper controller name for this module
		$controllerName = "Controllers\\" . ucfirst($module) . "Controller";

		// Make sure we have a class with that name and it implements our ControllerInterface
		if(!class_exists($controllerName) || !is_subclass_of($controllerName, 'Controllers\ControllerInterface'))
		{
			// Simply fall back to the index if we don't have a proper module
			// Could also throw a 404 or any other error message
			$controllerName = 'Controllers\IndexController';
		}

		// Create the controller and let that controller handle this request
		/** @var \Controllers\ControllerInterface $controllerInstance */
		$controllerInstance = new $controllerName();
		$controllerInstance->handleRequest();
	}
}