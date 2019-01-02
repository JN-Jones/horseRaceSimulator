<?php
namespace Managers;

/**
 * Class ViewManager
 *
 * Basic class to manage views, load and display them
 */
class ViewManager
{
	/**
	 * Includes a given view
	 *
	 * @param string $view The name of the view to load
	 * @param array $params An array of parameters to make available for the view. Should be in format "name => value"
	 * @throws \Exception In case no view with the given name was found
	 */
	public static function getView($view, $params=[])
	{
		// Test whether a view with the name exists
		if(!file_exists("./views/{$view}.php"))
		{
			throw new \Exception("View {$view} not found");
		}

		// Some PHP magic to make all parameters easily accessible from the view
		foreach ($params as $name => $value)
		{
			// Don't overwrite the view variable!
			if($name == 'view')
			{
				continue;
			}
			// Assign value to the variable with the given name
			$$name = $value;
		}

		// Include the actual view. PHP is used as an extension as views may contain PHP code
		require "./views/{$view}.php";
	}

	/**
	 * Redirect to a given module while showing a short redirection message
	 *
	 * @param string $module The module to redirect to
	 * @throws \Exception In case the "redirect" view is not available
	 */
	public static function redirectTo($module)
	{
		// The actual redirect is handled in a view so load that
		self::getView('redirect', ['module' => $module]);
	}

	/**
	 * Show an error message with a simple link back to the index
	 *
	 * @param string $message The error message to display
	 * @throws \Exception In case the "error" view is not available
	 */
	public static function error($message)
	{
		// Simply display the error view with the given message
		self::getView('error', ['msg' => $message]);
	}
}