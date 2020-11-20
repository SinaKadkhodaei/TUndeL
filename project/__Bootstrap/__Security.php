<?php

namespace __Bootstrap;

use __Bootstrap\__Keywords as __K;
use Session;

class __Security
{
	private static
		$csrfForAll = false,
		$csrfCreationFlag = false;

	/**
	 * @return string csrf code
	 */
	public static function makeCsrf()
	{
		if (self::$csrfCreationFlag === false) {
			$csrf = md5(date(__Configs::app()->privateKey . 'dMY' . ((string) rand(0, 5000)) . 'His'));
			$csrfS = Session::__();
			if (isset($csrfS['csrfPointer']))
				$csrfS['csrfPointer'] = ($csrfS['csrfPointer'] + 1) % __Configs::app()->csrfCountAtTime;
			else
				$csrfS['csrfPointer'] = 0;

			$csrfS[__K::security()->csrf][$csrfS['csrfPointer']] = $csrf;
			Session::__(
				[
					__K::security()->csrf => $csrfS[__K::security()->csrf],
					'csrfPointer' => $csrfS['csrfPointer']
				]
			);

			self::$csrfCreationFlag = true;
		} else {
			$csrf = Session::__();
			$csrf = $csrf[__K::security()->csrf][$csrf['csrfPointer']];
		}

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

		if (in_array($csrf, Session::__()[__K::security()->csrf] ?? []))
			return true;

		return false;
	}

	public static function csrfUnlock()
	{
		self::$csrfCreationFlag = false;
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
