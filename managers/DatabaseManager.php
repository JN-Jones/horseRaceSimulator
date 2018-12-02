<?php

/**
 * Class DatabaseManager
 *
 * Basic class to handle a database connection
 * Could be expanded to use a proper query builder instead of writing the queries manually
 * Note: MySQL has been used to develop and test this class, different databases may require some changes
 */
class DatabaseManager
{
	//Singleton
	/** @var null|DatabaseManager $instance The instance if one has been created*/
	private static $instance = null;

	/**
	 * Returns the instance of the DatabaseManager
	 * @return DatabaseManager
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

	/** @var null|PDO $connection Our active database connection */
	private $connection = null;

	/**
	 * Private DatabaseManager constructor
	 * Opens the database connection and saves it
	 * @throws PDOException In case no connection can be established
	 */
	private function __construct()
	{
		// Get our config
		$config = ConfigManager::getInstance();
		// Try to open a connection to the database provided
		$this->connection = new PDO('mysql:host=' . $config->getDbHost() . ';dbname=' . $config->getDbName(), $config->getDbUser(), $config->getDbPass());
		// Disable emulating of queries. Needed to allow prepared statements in LIMIT clauses
		// Another workaround would be to force limits to int but that would require a proper query builder
		$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}

	/**
	 * Execute a query on the given connection
	 *
	 * @param string $query The query to execute. Can include named placeholders for variables
	 * @param array $params Values used for named placeholders. Should always be of form "name => value"
	 * @param bool $multi Whether or not multiple rows will be fetched
	 * @param bool $fetch Whether or not something will be fetched (should be true for SELECT and false for UPDATE/INSERT)
	 * @return mixed Returns the result of the query
	 * @throws Exception In case there was something wrong with the given query an exception with more infos is thrown
	 */
	public function query($query, $params = [], $multi = false, $fetch = true)
	{
		$data = null;

		// We're doing prepared statements so prepare the query for that
		$query = $this->connection->prepare($query);

		// Bind all parameters. Types are automatically determined
		foreach ($params as $name => $value)
		{
			$query->bindValue(":{$name}", $value);
		}

		// Execute the query. If there were no problems and we want to fetch data do so now
		if ($query->execute() && $fetch)
		{
			// Either fetch only one row
			if (!$multi)
			{
				$data = $query->fetch(PDO::FETCH_ASSOC);
			}
			// Or multiple rows
			else
			{
				$data = $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}

		// If the error code does indicate an issue with the query retrieve that data
		if ($query->errorCode() != "00000")
		{
			$info = $query->errorInfo();

			// Make sure the query cursor is closed
			$query->closeCursor();
			// And throw an exception with the error data
			throw new Exception("Error with Query ('{$query->queryString}') ({$info[1]}): {$info[2]}");
		}
		$query->closeCursor();

		return $data;
	}

	/**
	 * Easy access function for a single select
	 *
	 * @param string $query The query to execute. Can include named placeholders for variables
	 * @param array $params Values used for named placeholders. Should always be of form "name => value"
	 * @return mixed Returns the result of the query
	 * @throws Exception In case there was something wrong with the given query an exception with more infos is thrown
	 */
	public function select($query, $params = [])
	{
		return $this->query($query, $params, false, true);
	}

	/**
	 * Easy access function for selecting multiple rows
	 *
	 * @param string $query The query to execute. Can include named placeholders for variables
	 * @param array $params Values used for named placeholders. Should always be of form "name => value"
	 * @return mixed Returns the result of the query
	 * @throws Exception In case there was something wrong with the given query an exception with more infos is thrown
	 */
	public function selectMulti($query, $params = [])
	{
		return $this->query($query, $params, true, true);
	}


	/**
	 * Easy access function for an update
	 *
	 * @param string $query The query to execute. Can include named placeholders for variables
	 * @param array $params Values used for named placeholders. Should always be of form "name => value"
	 * @throws Exception In case there was something wrong with the given query an exception with more infos is thrown
	 */
	public function update($query, $params = [])
	{
		$this->query($query, $params, false, false);
	}

	/**
	 * Easy access function for an insert
	 *
	 * @param string $query The query to execute. Can include named placeholders for variables
	 * @param array $params Values used for named placeholders. Should always be of form "name => value"
	 * @throws Exception In case there was something wrong with the given query an exception with more infos is thrown
	 */
	public function insert($query, $params = [])
	{
		$this->query($query, $params, false, false);
	}

	/**
	 * Begins a transaction
	 */
	public function beginTransaction()
	{
		$this->connection->beginTransaction();
	}

	/**
	 * Commits all changes made during a transaction
	 */
	public function commit()
	{
		$this->connection->commit();
	}

	/**
	 * Rolls back all changes made during a transaction
	 */
	public function rollBack()
	{
		$this->connection->rollBack();
	}

	/**
	 * Returns the ID of the last inserted row. In case of MySQL this is an integer
	 *
	 * @return int
	 */
	public function getInsertedId()
	{
		return (int)$this->connection->lastInsertId();
	}

	/**
	 * Destructor used to explicitly set the connection to null and close it with this
	 */
	public function __destruct()
	{
		$this->connection = null;
	}
}