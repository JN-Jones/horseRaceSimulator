<?php
namespace Models;

use Managers\DatabaseManager;

/**
 * Class AbstractModel
 * Parent class for all models
 */
abstract class AbstractModel
{
	/** @var array $models Contains references to all instances of models already loaded. Structure: [classname][id] => instance */
	protected static $models = [];

	/** @var string $key The table key which is used to identify a single model. Subclasses should have a field named like this */
	protected static $key = 'id';
	/** @var string $table The table that stores these models. Used to query when searching for one */
	protected static $table = '';

	/**
	 * AbstractModel constructor. Used internally to either create a new empty instance or an instance from the database
	 * @param array $rawData The raw data as saved in the database
	 */
	protected function __construct($rawData=[])
	{
	}

	/**
	 * Finds a model by it's ID/Key
	 * @param mixed $id The key by which the model should be searched
	 * @return AbstractModel The model loaded either via internal cache or from the database
	 * @throws \Exceptions\QueryException
	 */
	public static function find($id)
	{
		if(!isset(static::$models[static::class][$id]))
		{
			$db = DatabaseManager::getInstance();
			$rawData = $db->select("SELECT * FROM ".static::$table." WHERE ".static::$key."=:id", ['id' => $id]);
			$instance = new static($rawData);
			static::$models[static::class][$id] = $instance;
		}
		return static::$models[static::class][$id];
	}

	/**
	 * External function used to create a new instance of this model
	 */
	public static function create()
	{
	}

	/**
	 * Returns the actual classname (subclass/model)
	 * @return string
	 */
	public function getClassname()
	{
		return static::class;
	}

	/**
	 * Adds an instance to the internal cache, should be called after creating a new instance
	 * @param AbstractModel $model
	 */
	protected static function addInstance(AbstractModel $model)
	{
		static::$models[$model->getClassname()][$model->{static::$key}] = $model;
	}
}