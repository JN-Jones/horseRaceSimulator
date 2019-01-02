<?php

namespace Exceptions;

use Throwable;

/**
 * Class QueryException
 * Used when a query execution fails for whatever reason
 */
class QueryException extends \Exception
{
	/**
	 * QueryException constructor.
	 * @param string $query The query that caused the exception
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $query = "", int $code = 0, Throwable $previous = null)
	{
		$message = 'Error with Query: ' . $query;
		parent::__construct($message, $code, $previous);
	}
}