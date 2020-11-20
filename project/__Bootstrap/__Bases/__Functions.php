<?php

use Tools\Response;

/**
 * convert /1/2 to localhost/1/2
 * @return string
 */
function url(string $_route)
{
	return OriginUrl . '/' . trim($_route, '/');
}

/**
 * var_dump $_data and then exit()
 */
function fire(...$_data)
{
	if (count(ob_list_handlers()) > 1) //1:"Closure::__invoke"
		if (error_reporting() !== 0)
			ob_get_clean();

	if (count($_data) < 2)
		$_data = $_data[0];

	if (class_exists('\Tools\Response'))
		Response::show($_data);
	else
		var_dump($_data);
	exit(0);
}

/**
 * echo $_text with filtering html entities
 */
function escXSS($_text)
{
	return htmlentities($_text);
}

/**
 * echo $_text with filtering html entities
 */
function e($_text)
{
	echo htmlentities($_text);
}

/**
 * echo $_text as raw
 */
function r($_text)
{
	echo $_text;
}

/**
 * @param array|stdClass $_data
 */
function json($_data)
{
	return json_encode($_data);
}

/**
 * binding array to a string
 * @return string
 */
function bind(string $_text, array $_replaces, $_search = '?', bool $_searchIsRaw = false)
{
	if (gettype($_search) === gettype([]))
		return str_replace($_search, $_replaces, $_text);
	else {
		if ($_searchIsRaw === false)
			$_search = '/' . preg_quote($_search) . '/';

		foreach ($_replaces as $el)
			$_text = preg_replace($_search, $el, $_text, 1);
		return $_text;
	}
}

/**
 * binding array to a string
 * @return string
 */
function bindSql($_sql, array $_replaces = null, $_search = '?', bool $_searchIsRaw = false)
{
	if (is_array($_sql)) {
		$_replaces = $_sql[1];
		$_sql = $_sql[0];
	}

	array_walk($_replaces, function (&$v, $k) {
		if (is_bool($v))
			$v = ((int) $v);
		elseif (!is_numeric($v))
			$v = '"' . $v . '"';
	});

	if (gettype($_search) === gettype([]))
		return str_replace($_search, $_replaces, $_sql);
	else {
		if ($_searchIsRaw === false)
			$_search = '/' . preg_quote($_search) . '/';

		foreach ($_replaces as $el)
			$_sql = preg_replace($_search, $el, $_sql, 1);
		return $_sql;
	}
}

/**
 * get route by name
 * @return string
 */
function route(string $_name, array $_params = [])
{
	return \__Bootstrap\__Routes::getRoute($_name, $_params);
}

/**
 * get csrf
 * @return string
 */
function csrf(bool $_dontPrint = false)
{
	$csrf = \__Bootstrap\__Security::makeCsrf();
	if ($_dontPrint)
		return $csrf;
	else
		echo $csrf;
}

/**
 * @return array
 */
function toArray(...$_data)
{
	if (count($_data) > 1)
		$_data = array_merge(...$_data);
	else
		$_data = $_data[0];

	return json_decode(json_encode($_data), true);
}

/**
 * @return stdClass
 */
function toObject($_data)
{
	return json_decode(json_encode($_data), false);
}

function stop($_message = null)
{
	if ($_message === null)
		$_message = 'Stop for stack trace';
	throw new Exception($_message);
}
