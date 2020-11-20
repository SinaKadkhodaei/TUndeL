<?php

namespace Tools;

use __Bootstrap\__Keywords as __K;
use __Bootstrap\__Security;
use __Events\__Errors;
use __Events\__System;
use Session;

class Response
{
	public static
		$sections = [];

	const types = [
			'js' => 'application/javascript',
			'css' => 'text/css',
			'array' => 'application/json',
			'object' => 'application/json',
			'string' => 'text/plain',
			'null' => 'text/plain',
			'integer' => 'text/plain',
			'html' => 'text/html',
			'xml' => 'application/xml',
		], // content types
		VENS = '/__App/__Views/__Errors/', // error view namespace
		VNS = '/__App/__Views/'; // view namespace;

	/**
	 * @param mixed $_data everything that can show in browser
	 * @param string $_type one of types that has in Response::types
	 * @return mixed dont return anything and just displaying data
	 */
	public static function show($_data, string $_type = null)
	{
		if ($_data === null)
			$_data = 'NULL Value';

		$type = gettype($_data);
		self::makeNormallyValue($_data, $type);
		header('content-type:' . ($_type ?? self::types[$type]));

		echo $_data;
		exit(0);
	}

	/**
	 * @param mixed $_data everything that can show in browser
	 * @param string $_type one of types that has in Response::types
	 * @return mixed dont return anything and just displaying data
	 */
	public static function openFile($_path, $_return = false)
	{
		if (file_exists($_path)) {
			$ext = explode('.', basename($_path));

			if (isset(\Tools\Response::types[$ext[count($ext) - 1]]))
				header('Content-type: ' . \Tools\Response::types[$ext[count($ext) - 1]]);
			else
				header('Content-type: ' . mime_content_type($_path));

			readfile($_path);
		} else {
			if ($_return === true)
				return false;
			else
				\Tools\Response::errorHandle(404);
		}
		exit(0);
	}


	/**
	 * @param mixed $_data everything that can show in browser
	 * @param string $_type one of types that has in Response::types
	 */
	private static function makeNormallyValue(&$_data, string &$_type = null)
	{
		switch (strtolower($_type)) {
			case 'boolean':
				$_data = json_encode(['status' => $_data]);
				$_type = 'array';
				break;

			case 'object':
			case 'array':
				$_data = json_encode($_data);
				$merge = __System::mergeResponse();
				if (!empty($merge)) {
					$_data = array_merge(json_decode($_data, true), $merge);
					$_data = json_encode($_data);
				}
				$_type = 'array';
				break;
		}
	}
	/**
	 * @param int $_statusCode like 200 , 404 and ...
	 * @param string $_statusMessage a message that show side of code
	 * @return null dont return anything and just set status code for page
	 */
	public static function httpCode(int $_statusCode, string $_statusMessage = null)
	{
		if ($_statusMessage === null)
			http_response_code($_statusCode);
		else
			header('HTTP/1.1 ' . $_statusCode . ' ' . $_statusMessage);
	}

	/**
	 * @param string $_name name of view that locate in __App\__Views (if it located in a folder seprate it with dot)
	 * @param mixed $_data a data that you want to send to it
	 * @param int $_statusCode http code for target
	 * @param string $_statusMessage a message that show side of code
	 * @return mixed dont return anything and just display target view
	 */
	public static function view(string $_TUndeLViewName, $_TUndeLViewData = null)
	{

		if ($_TUndeLViewData !== null)
			foreach ($_TUndeLViewData as $k => $v)
				${$k} = $v;

		if (isset(Session::__()['data'])) {
			foreach (Session::__()['data'] as $k => $v)
				${$k} = $v;
			Session::__(['data' => null]);
			// unset($_SESSION['__']['data']);
		}
		unset($k, $v, $_TUndeLViewData);

		$_TUndeLViewName = str_replace('.', '/', $_TUndeLViewName);
		$_TUndeLViewName = PrjDir . self::VNS . $_TUndeLViewName . '.php';
		$GLOBALS['isView'] = true;

		require_once($_TUndeLViewName);

		exit(0);
	}

	/**
	 * @param string $_TUndeLViewName name of view that locate in __App\__Views (if it located in a folder seprate it with dot)
	 * @param bool $_TUndeLViewExit exit program?
	 * @return mixed dont return anything and just display target view
	 */
	public static function loadView(string $_TUndeLViewName, $_TUndeLViewData = null, bool $_TUndeLViewExit = false)
	{
		$params = array_keys(get_defined_vars());
		if ($_TUndeLViewData !== null)
			foreach ($_TUndeLViewData as $k => $v)
				if (!in_array($k, $params))
					${$k} = $v;

		unset($k, $v, $_TUndeLViewData);

		$_TUndeLViewName = str_replace('.', '/', $_TUndeLViewName);
		$_TUndeLViewName = PrjDir . self::VNS . $_TUndeLViewName . '.php';
		$GLOBALS['isView'] = true;

		require_once($_TUndeLViewName);

		if ($_TUndeLViewExit === true)
			exit(0);
	}


	/**
	 * @param int $_statusCode http code for target
	 * @param string $_statusMessage a message that show side of code
	 * @return mixed dont return anything and just display target view with two variable ($httpCode & $httpMessage)
	 */
	public static function errorHandle(int $_statusCode = 404, $_TUndeLViewData = null, string $_statusMessage = null)
	{
		__Errors::handle($_statusCode);
		$params = array_keys(get_defined_vars());
		if ($_TUndeLViewData !== null)
			foreach ($_TUndeLViewData as $k => $v)
				if (!in_array($k, $params))
					${$k} = $v;

		$name = PrjDir . self::VENS . $_statusCode . '.php';
		${'httpCode'} = $_statusCode;
		${'httpMessage'} = $_statusMessage;
		self::httpCode($_statusCode, $_statusMessage);

		unset($k, $v, $_TUndeLViewData);
		require_once($name);
		exit(0);
	}

	/**
	 * @param array $_data everything that can show in browser
	 * @return mixed dont return anything and just displaying data
	 */
	public static function back(array $_data = [], int $_statusCode = 302)
	{
		Session::__(['data' => $_data]);
		$_data = [];
		Response::redirect(Session::__()['pervPage'], $_data, $_statusCode);
	}

	/**
	 * @param string $_url
	 * @param array $_data everything that can show in browser
	 * @return mixed dont return anything and just displaying data
	 */
	public static function redirect(string $_url, array $_data = [], int $_statusCode = 302)
	{
		if (count($_data) > 0)
			Session::__(['data' => $_data]);

		header('Location: ' . $_url, true);
		// echo '<script type="text/javascript">window.location.href="' . $_url . '";</script><noscript><meta http-equiv="refresh" content=0;url="' . $_url . '" /></noscript>';;
		exit(0);
	}

	/**
	 * @param string $_url
	 * @param array $_data everything that can show in browser
	 * @return mixed dont return anything and just displaying data
	 */
	public static function redirectHead(string $_url, array $_data = [], int $_statusCode = 302)
	{
		if (count($_data) > 0)
			Session::__(['data' => $_data]);

		$rnd = '';
		// __Security::csrfUnlock();
		// $rnd = '?redId=' . rand(1000, 9999);

		header('Location: ' . $_url .  $rnd, false);
		exit(0);
	}
}
