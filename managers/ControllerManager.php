<?php
namespace Managers;

use Exceptions\RouteNotFoundException;

/**
 * Class ControllerManager
 *
 * First class to handle a request. Checks which controller should actually handle it and calls that controller
 */
class ControllerManager
{
	/**
	 * Handles the actual request
	 * @throws RouteNotFoundException In case no controller could handle the given input
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

		// Make sure we have a class with that name and it extends our AbstractController
		if(!class_exists($controllerName) || !is_subclass_of($controllerName, 'Controllers\AbstractController'))
		{
			// Throw an exception so the application can decide what to do
			throw new RouteNotFoundException();
		}

		// Create the controller and let that controller handle this request
		/** @var \Controllers\AbstractController $controllerInstance */
		$controllerInstance = new $controllerName();
		if($_SERVER['REQUEST_METHOD'] == 'GET')
		{
			$controllerInstance->handleGetRequest();
		}
		elseif($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$controllerInstance->handlePostRequest();
		}
		else
		{
			throw new RouteNotFoundException();
		}
	}
}