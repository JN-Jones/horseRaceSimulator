<?php

/**
 * Class ConfigManager
 *
 * Handles configuration
 * In case of this example the configuration is saved in private variables
 * but it's also possible to load the configuration from an external file, a database or similar locations
 */
class ConfigManager
{
	// Database configuration
	/** @var string $db_host Hostname of the Server to connect to */
	private $db_host = 'localhost';
	/** @var string $db_user Username to use for authorization */
	private $db_user = '';
	/** @var string $db_pass Password to use for authorization */
	private $db_pass = '';
	/** @var string $db_name The database name to connect to */
	private $db_name = 'horserace';

	// General configuration
	/** @var int $numRaces The maximum number of races allowed to be active at the same time */
	private $numRaces = 3;
	/** @var int $numHorses The number of horses in each race */
	private $numHorses = 8;
	/** @var int $raceDistance The race distance in meters */
	private $raceDistance = 1500;
	/** @var int $lastRacesDisplayed The number of finished races to display */
	private $lastRacesDisplayed = 5;

	// Singleton
	/** @var null|ConfigManager $instance The instance if one has been created */
	private static $instance = null;

	/**
	 * Make the constructor private to avoid direct usage
	 */
	private function __construct() {}

	/**
	 * Returns the instance of the ConfigManager
	 * @return ConfigManager
	 */
	public static function getInstance()
	{
		// If no instance has been created so far do so
		if (self::$instance == null)
		{
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Returns the database host
	 * @return string
	 */
	public function getDbHost()
	{
		return $this->db_host;
	}

	/**
	 * Returns the database user
	 * @return string
	 */
	public function getDbUser()
	{
		return $this->db_user;
	}

	/**
	 * Returns the database password
	 * @return string
	 */
	public function getDbPass()
	{
		return $this->db_pass;
	}

	/**
	 * Returns the database name
	 * @return string
	 */
	public function getDbName()
	{
		return $this->db_name;
	}

	/**
	 * Returns the number of races allowed to be active at the same time
	 * @return int
	 */
	public function getNumRaces()
	{
		return $this->numRaces;
	}

	/**
	 * Returns the number of horses in each race
	 * @return int
	 */
	public function getNumHorses()
	{
		return $this->numHorses;
	}

	/**
	 * Returns the race distance
	 * @return int
	 */
	public function getRaceDistance()
	{
		return $this->raceDistance;
	}

	/**
	 * Returns the number of finished races that should be displayed
	 * @return int
	 */
	public function getLastRacesDisplayed()
	{
		return $this->lastRacesDisplayed;
	}
}