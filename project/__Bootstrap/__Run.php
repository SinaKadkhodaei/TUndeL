<?php

namespace __Bootstrap;

use __Bootstrap\__Keywords as __K;
use Session;
use Tools\Response;

class __Run
{
	const
		MWNS = '\\__App\\__Middlewares\\', // middleware namespace
		CNS = '\\__App\\__Controllers\\'; // controllers namespace
	/**
	 * fail with exit ,
	 * failBack with exit ,
	 * run with return ,
	 * next with return ,
	 */

	/**
	 * @param string $_name middleware name
	 * @param bool $_isFinal level of current route
	 */
	public static function middleware(string $_name, bool $_isFinal)
	{
		$_name = str_replace('.', '\\', $_name);
		$_name = self::MWNS . $_name;
		$middleware = new $_name();
		return $middleware->handle(new __DoAction($_isFinal));
	}

	/**
	 * @param string|Closure $_target target name or function
	 */
	public static function target($_target, $_args = [])
	{
		// if (isset(Session::__()['data'])) {
		// 	foreach (Session::__()['data'] as $k => $v) {
		// 		global ${$k};
		// 		${$k} = $v;
		// 	}

		// 	Session::__(['data' => null]);
		// 	// unset($_SESSION['__']['data']);
		// }
		if (gettype($_target) !== gettype('')) { // if target is a Closure
			$_target(...$_args);
			exit(0);
		}

		$_target = explode('::', $_target);
		$action = $_target[1];
		$_target = str_replace('.', '\\', $_target[0]);
		$_target = self::CNS . $_target;
		$controller = new $_target();
		$controller->{$action}(...$_args);
		exit(0);
	}
}

class __DoAction
{
	private $isFinal;

	/**
	 * get level of runing
	 */
	function __construct(bool $_isFinal = true)
	{
		$this->isFinal = $_isFinal;
	}

	/**
	 * run function located in controller of target
	 */
	public function run()
	{
		if ($this->isFinal)
			return [
				__K::response()->status => __K::response()->runTarget,
			];
		else
			return [
				__K::response()->status => __K::response()->nextMiddleware,
			];
	}

	/**
	 * run next middleware
	 */
	public function next()
	{
		return [
			__K::response()->status => __K::response()->nextMiddleware,
		];
	}

	/**
	 * show data with http code
	 */
	public function fail($_data, int $_statusCode = 400, string $_statusMessage = null)
	{
		Response::httpCode($_statusCode, $_statusMessage);
		Response::show($_data);
	}

	/**
	 * send data with http code in pervious page
	 */
	public function failBack($_data, int $_statusCode = 400)
	{
		Response::redirect(Session::__()['pervPage'], $_data, $_statusCode);
	}
}
