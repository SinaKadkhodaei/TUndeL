<?php

namespace __DataLayer\__Driver;

use __Bootstrap\__Configs;
use \PDO;

class __DB
{
	/**
	 * @var \PDO $connection
	 * @var __DB $instance
	 */
	private static
		$connection = null,
		$instance = null;

	public static
		$doRaw = false;

	/**
	 * @return __DB
	 */
	public static function instance()
	{
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	public static function changeDB(string $_dbName)
	{
		self::$instance = new self($_dbName);
	}

	function __construct($_dbName = null)
	{
		$dbConfig = __Configs::database();
		$dsn = 'mysql:host=' . $dbConfig->host . ';port=' . $dbConfig->port . ';dbname=' . ($_dbName ?? $dbConfig->name) . ';';

		$options = [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', //;set global sql_mode="NO_BACKSLASH_ESCAPES";',
			//\PDO::ATTR_PERSISTENT => true, // persistent connect and should after uses call $this->
		];
		try {
			self::$connection = new PDO($dsn, $dbConfig->username, $dbConfig->password, $options);
			self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	public function escapeInjection(&$_var)
	{
		$chars = ["\\", "select", "from", "union", "concat", "like", "or", "and", ":=", "=", "<>", "--"];
		$_var = str_ireplace($chars, '', $_var);
		return $_var;
	}

	public function pdoType(&$_var)
	{
		$res = -1;
		switch (strtolower(gettype($_var))) {
			case 'boolean':
				// $res = PDO::PARAM_BOOL;
				// break;
			case 'integer':
				$res = PDO::PARAM_INT;
				$_var = strval((int) $_var);
				break;
			case 'string':
				$res = PDO::PARAM_STR;
				// if (self::$doRaw === false)
				// 	$this->escapeInjection($_var);
				break;
			case 'null':
				$res = PDO::PARAM_NULL;
				break;
		}

		// $_var = self::$connection->quote($_var, $res);
		return $res;
	}

	/**
	 * @param string $_query sql query for Create,Update,Delete operation
	 * @param array $_parameters parameters with format (?) or (:paramName)
	 * @return array
	 */
	public function raw(string $_sql, array $_parameters = [], bool $_detail = false)
	{
		try {
			$db = self::$connection->prepare($_sql);
			foreach ($_parameters as $key => $value) {
				$valType = $this->pdoType($value);
				$db->bindValue($key + 1, $value, $valType);
			}

			$res = $db->execute();
			if ($_detail === true)
				return [
					'status' => $res,
					'output' => $db->fetchAll(PDO::FETCH_ASSOC),
					'lastId' => self::$connection->lastInsertId(),
					'rowCount' => $db->rowCount(),
				];

			return $res;
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	/**
	 * @param string $_query sql query for Create,Update,Delete operation
	 * @param array $_parameters parameters with format (?) or (:paramName)
	 * @return mixed stdClass or array
	 */
	public function read(string $_class, string $_query, array $_parameters = [])
	{
		try {
			$db = self::$connection->prepare($_query);
			foreach ($_parameters as $key => &$value) {
				$valType = $this->pdoType($value);
				$db->bindValue($key + 1, $value, $valType);
			}

			$db->execute();
			$res = $db->fetchAll(PDO::FETCH_CLASS, $_class); //'\__DataLayer\Users'

			return $res;
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	/**
	 * @param string $_query sql query for Create,Update,Delete operation
	 * @param array $_parameters parameters with format (?) or (:paramName)
	 * @return mixed bool or array
	 */
	public function write(string $_query, array $_parameters = [], $_detail = false)
	{
		try {
			$db = self::$connection->prepare($_query);
			foreach ($_parameters as $key => $value) {
				$valType = $this->pdoType($value);
				$db->bindValue($key + 1, $value, $valType);
			}

			$res = $db->execute();

			if ($_detail === true)
				return [
					'status' => ($db->rowCount() > 0),
					'lastId' => self::$connection->lastInsertId(),
					'rowCount' => $db->rowCount(),
					'error' => $db->errorInfo()
				];

			return ($db->rowCount() > 0);
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	/**
	 * start a transaction
	 * @return bool
	 */
	public function transaction()
	{
		try {
			return self::$connection->beginTransaction();
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	/**
	 * commit a transaction
	 * @return bool
	 */
	public function commit()
	{
		try {
			if (self::$connection->inTransaction())
				return self::$connection->commit();
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	/**
	 * rollback a transaction
	 * @return bool
	 */
	public function rollback()
	{
		try {
			if (self::$connection->inTransaction())
				return self::$connection->rollBack();
		} catch (\PDOException $e) {
			fire($e->getMessage());
		}
	}

	public static function end()
	{
		self::$connection = null;
	}
}
