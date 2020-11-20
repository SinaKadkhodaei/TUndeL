<?php

use __Bootstrap\__Configs;

class Session
{

	/**
	 * @return object
	 */
	public static function __callStatic($_name, $_args)
	{
		self::decrypt();

		if (count($_args) > 0) {
			if (is_array($_args[0])) {
				if (isset($_SESSION[$_name]) === false)
					$_SESSION[$_name] = [];
				$_SESSION[$_name] = array_merge($_SESSION[$_name], $_args[0]);
			} else
				$_SESSION[$_name] = $_args[0];


			self::encrypt();
			return $_args[0];
		}

		if ($_name === '__ALL')
			$res = $_SESSION;
		else {
			if (isset($_SESSION[$_name]))
				$res = $_SESSION[$_name];
			else
				$res = null;
		}

		self::encrypt();
		return $res;
	}

	/**
	 * @return object
	 */
	public static function put($_data)
	{
		self::decrypt();

		$_SESSION = array_merge($_SESSION, $_data);

		self::encrypt();

		return $_data;
	}

	/**
	 * @return object
	 */
	public static function delete($_key)
	{
		self::decrypt();

		$res = $_SESSION[$_key];
		unset($_SESSION[$_key]);

		self::encrypt();

		return $res;
	}

	public static function flush()
	{
		$_SESSION = [];
	}

	private static function encrypt()
	{
		if (__Configs::app()->sessionEncrypt !== true)
			return false;

		$tmp = base64_encode(json_encode($_SESSION));
		$tHash = __Configs::app()->privateKey . md5(date('dMYHis'));
		$len = strlen($tHash);
		$rnd = rand(1, $len);
		$tHash = substr($tHash, 0, floor($len / $rnd)) . '/' . md5($len) . '==' . substr($tHash, floor($len / $rnd), $len - floor($len / 2));
		$len = strlen($tmp);
		$rnd = rand(1, $len);
		$tmp = substr($tmp, 0, floor($len / $rnd)) . __Configs::app()->privateKey . $tHash . 'iqu5p=jnR' . md5(rand(0, 10)) . '/' . substr($tmp, floor($len / $rnd), $len - floor($len / $rnd));
		$tmp = base64_encode($tmp);

		$_SESSION = [];
		$_SESSION['__data'] = $tmp;
	}

	private static function decrypt()
	{
		if (__Configs::app()->sessionEncrypt !== true)
			return false;

		if (isset($_SESSION['__data'])) {
			$tmp = base64_decode($_SESSION['__data']);
			$tmp = preg_replace('/((' . preg_quote(__Configs::app()->privateKey) . ').+(iqu5p=jnR.+\/))/', '', $tmp);
			$tmp = json_decode(base64_decode($tmp), true);

			$_SESSION = $tmp;
		}
	}
}
