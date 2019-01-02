<?php

namespace Exceptions;

use Throwable;

/**
 * Class TooManyRacesException
 * Used when an attempt is made to create another race even if there are already too many
 */
class TooManyRacesException extends \Exception
{
	/**
	 * TooManyRacesException constructor.
	 * @param string $num The max number of races that are allowed at the same time
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $num = "", int $code = 0, Throwable $previous = null)
	{
		$message = "There are {$num} races active, please finish one first";
		parent::__construct($message, $code, $previous);
	}
}