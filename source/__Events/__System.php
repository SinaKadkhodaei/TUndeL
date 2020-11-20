<?php

namespace __Events;

class __System
{
	/**
	 * when script start
	 */
	public static function boot()
	{
		date_default_timezone_set("Asia/Tehran");
 	}

	/**
	 * when script start
	 */
	public static function shutdown()
	{
	}

	/**
	 * @return array|object
	 * when script show json response
	 */
	public static function mergeResponse()
	{
	}
}
