<?php

/*
 * Register an autoloader to avoid blocks of "require" statements everywhere
 * In case of this project classes with the same ending are grouped in the same directory (Controller/Interface/Manager)
 * Simply check for the ending of the class name, check whether the file exists and if so load it
 * Special cases are Models and the Random class which don't have a proper ending. However models are preferred over misc classes
 */
spl_autoload_register(function ($class) {
	$filename = '.' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	if(file_exists($filename))
	{
		require_once $filename;
	}
});
