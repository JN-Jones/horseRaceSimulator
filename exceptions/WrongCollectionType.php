<?php

namespace Exceptions;

use Throwable;

/**
 * Class WrongCollectionType
 * Thrown when a model of wrong type is added to a collection
 */
class WrongCollectionType extends \Exception
{
	/**
	 * WrongCollectionType constructor.
	 * @param string $collectionType The type the collection expects
	 * @param string $modelType The actual type of the model tried to add
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $collectionType, string $modelType, int $code = 0, Throwable $previous = null)
	{
		$message = "This collection expects models of the type {$collectionType}, tried adding one of type {$modelType}";
		parent::__construct($message, $code, $previous);
	}
}