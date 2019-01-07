<?php
namespace Exceptions;

use Throwable;

/**
 * Class RouteNotFoundException
 * Thrown if a controller doesn't implement a given request type
 */
class RouteNotFoundException extends \Exception
{
	/**
	 * RouteNotFoundException constructor.
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(int $code = 0, Throwable $previous = null)
	{
		$message = 'The controller couldn\'t handle your request';
		parent::__construct($message, $code, $previous);
	}
}