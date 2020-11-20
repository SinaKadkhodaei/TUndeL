<?php

namespace __Bootstrap;

class __Configs
{
	/**
	 * @var object $configs
	 * @var __Configs $instance
	 */
	private static
		$configs = null,
		$instance = null;

	function __construct()
	{
		$tmp = file_get_contents(PrjDir . '/.configs.json');
		self::$configs =  json_decode($tmp, false);
	}

	/**
	 * @return object
	 */
	public static function __callStatic($_name, $_args)
	{
		if (self::$instance === null)
			self::$instance = new self();

		return self::$configs->{$_name};
	}

	public static function change($_index, $_value)
	{
		$ptr = &self::$configs;
		foreach (explode('.', $_index) as $index)
			$ptr = &$ptr->{$index};

		$ptr = $_value;
	}
}

// like get database config as bottom :
// __Configs::database()->username
