<?php
namespace Exceptions;

use Throwable;

/**
 * Class RaceFinishedException
 * Thrown if a race is tried to advance after it finished
 */
class RaceFinishedException extends \Exception
{
	/**
	 * RaceFinishedException constructor.
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(int $code = 0, Throwable $previous = null)
	{
		$message = 'This race has already finished';
		parent::__construct($message, $code, $previous);
	}
}