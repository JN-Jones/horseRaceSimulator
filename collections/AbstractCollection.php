<?php
namespace Collections;

use Exceptions\WrongCollectionType;
use Models\AbstractModel;

/**
 * Class AbstractCollection
 * Parent class for all collection, adds some general functions to handle collections
 */
abstract class AbstractCollection implements \Iterator, \ArrayAccess
{
	/** @var AbstractModel[] $collection The actual collection as array */
	protected $collection = [];

	/** @var string $supportedClass The class this collection contains */
	protected static $supportedClass = 'Models\AbstractModel';

	/**
	 * Adds a single model to the collection
	 * @param AbstractModel $model Should be of $supportedClass
	 * @throws WrongCollectionType If a model of the wrong type is tried to add
	 */
	public function add(AbstractModel $model)
	{
		if($model->getClassname() != static::$supportedClass)
		{
			throw new WrongCollectionType(static::$supportedClass, $model->getClassname());
		}
		$this->collection[] = $model;
	}

	/**
	 * Allows sorting the collection by a user defined function
	 * @param callable $sortFunction The sortfuntion to use
	 */
	public function sort(callable $sortFunction)
	{
		usort($this->collection, $sortFunction);
	}

	/**
	 * @return mixed
	 */
	public function rewind()
	{
		return reset($this->collection);
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		return current($this->collection);
	}

	/**
	 * @return mixed
	 */
	public function key()
	{
		return key($this->collection);
	}

	/**
	 * @return mixed
	 */
	public function next()
	{
		return next($this->collection);
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return key($this->collection) !== null;
	}

	/**
	 * @param mixed $offset
	 * @param AbstractModel $value
	 * @throws WrongCollectionType
	 */
	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
		{
			$this->add($value);
		}
		else
		{
			$this->collection[$offset] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->collection[$offset]);
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->collection[$offset]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return AbstractModel
	 */
	public function offsetGet($offset)
	{
		return $this->collection[$offset];
	}
}