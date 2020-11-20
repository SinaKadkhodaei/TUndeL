<?php

use __DataLayer\__Driver\__DB;

class DB
{
	public static function closeDatabaseConnection()
	{
		__DB::end();
	}

	public static function doRaw($_do = true)
	{
		__DB::$doRaw = $_do;
	}

	public static function startTransaction()
	{
		return __DB::instance()->transaction();
	}

	public static function commitTransaction()
	{
		return __DB::instance()->commit();
	}

	public static function rollBack()
	{
		return __DB::instance()->rollback();
	}

	public static function raw(string $_sql, array $_params = [], bool $_detail = false)
	{
		return __DB::instance()->raw($_sql, $_params, $_detail);
	}

	public static function changeDB(string $_dbName)
	{
		return __DB::changeDB($_dbName);
	}
}
