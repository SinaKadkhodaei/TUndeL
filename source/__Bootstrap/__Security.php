<?php

namespace __Bootstrap;

use __Bootstrap\__Keywords as __K;
use Session;

class __Security
{
	private static
		$csrfForAll = false;

	/**
	 * @return string csrf code
	 */
	public static function makeCsrf()
	{
		$csrf = Session::__();
		if (empty($csrf[__K::security()->csrf])) {
			$csrf = bin2hex(random_bytes(32));
			Session::__([__K::security()->csrf => $csrf]);
		} else
			$csrf = $csrf[__K::security()->csrf];

		return $csrf;
	}

	/**
	 * @return bool trust?
	 */
	public static function checkCsrf()
	{
		$csrf = null;
		$tokenFieldName = '__token';

		if (isset(getallheaders()[__K::security()->csrf]))
			$csrf = getallheaders()[__K::security()->csrf];
		elseif (isset($_GET[$tokenFieldName]))
			$csrf = $_GET[$tokenFieldName];
		elseif (isset($_POST[$tokenFieldName]))
			$csrf = $_POST[$tokenFieldName];

		if (hash_equals($csrf, Session::__()[__K::security()->csrf] ?? null))
			return true;

		return false;
	}

	/**
	 * @param mixed 
	 * @return mixed
	 */
	public static function allRoutesShouldUseCsrf($_justReturnValue = false)
	{
		if ($_justReturnValue === true)
			return self::$csrfForAll;

		return self::$csrfForAll = $_justReturnValue;
	}
}
