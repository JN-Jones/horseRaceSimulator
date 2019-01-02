<?php
// Entry point for user requests

// First include the autoloader which is the only initialization needed here
require_once "./misc/autoloader.php";

// Afterwards let the ControllerManager decide how to handle the request. Controllers then output one of the available views
Managers\ControllerManager::handleRequest();