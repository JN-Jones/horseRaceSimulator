<?php
namespace Controllers;

/**
 * Interface ControllerInterface
 *
 * Basic interface for Controllers used to make sure they contain the same function
 */
interface ControllerInterface
{
	/**
	 * Function called on a Controller if it should handle the request
	 * Should either create a view or a redirect
	 */
	public function handleRequest();
}