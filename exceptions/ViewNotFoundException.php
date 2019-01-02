<?php

namespace Exceptions;

use Throwable;

/**
 * Class ViewNotFoundException
 * Thrown when a view wasn't found
 */
class ViewNotFoundException extends \Exception
{
	/**
	 * ViewNotFoundException constructor.
	 * @param string $view The name of the view not found
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $view = "", int $code = 0, Throwable $previous = null)
	{
		$message = "View {$view} not found";
		parent::__construct($message, $code, $previous);
	}
}