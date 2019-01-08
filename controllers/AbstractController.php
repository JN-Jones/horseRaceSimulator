<?php
namespace Controllers;

use Exceptions\RouteNotFoundException;

/**
 * Abstract class ControllerInterface
 *
 * Basic interface for Controllers used to make sure they contain the same functions
 */
abstract class AbstractController
{
	/**
	 * Function called on a Controller if it should handle a get request
	 * Should either create a view or a redirect
	 * @throws RouteNotFoundException In case the controller doesn't implement this method
	 */
	public function handleGetRequest()
	{
		throw new RouteNotFoundException();
	}

	/**
	 * Function called on a Controller if it should handle a post request
	 * Should either create a view or a redirect
	 * @throws RouteNotFoundException In case the controller doesn't implement this method
	 */
	public function handlePostRequest()
	{
		throw new RouteNotFoundException();
	}
}