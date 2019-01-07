<?php
// Entry point for user requests

// First include the autoloader which is the only initialization needed here
require_once "./misc/autoloader.php";

// Afterwards let the ControllerManager decide how to handle the request. Controllers then output one of the available views
// If the controller couldn't handle the request we simply show the index page (could also throw a 404 or something else)
try
{
	Managers\ControllerManager::handleRequest();
}
catch (\Exceptions\RouteNotFoundException $exception)
{
	// This will automatically fall back to index now
	unset($_GET['module']);
	$_SERVER['REQUEST_METHOD'] = 'GET';
	Managers\ControllerManager::handleRequest();
}
