<?php

/*
 * Register an autoloader to avoid blocks of "require" statements everywhere
 * In case of this project classes with the same ending are grouped in the same directory (Controller/Interface/Manager)
 * Simply check for the ending of the class name, check whether the file exists and if so load it
 * Special cases are Models and the Random class which don't have a proper ending. However models are preferred over misc classes
 */
spl_autoload_register(function ($class) {
	if(substr($class, -10) == "Controller" && file_exists("./controllers/{$class}.php"))
	{
		require_once "./controllers/{$class}.php";
	}
	elseif (substr($class, -9) == "Interface" && file_exists("./interfaces/{$class}.php"))
	{
		require_once "./interfaces/{$class}.php";
	}
	elseif (substr($class, -7) == "Manager" && file_exists("./managers/{$class}.php"))
	{
		require_once "./managers/{$class}.php";
	}
	elseif (file_exists("./models/{$class}.php"))
	{
		require_once "./models/{$class}.php";
	}
	elseif (file_exists("./misc/{$class}.php"))
	{
		require_once "./misc/{$class}.php";
	}
});
