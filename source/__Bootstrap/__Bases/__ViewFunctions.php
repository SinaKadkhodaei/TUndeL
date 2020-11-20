<?php

// -------------------------------*** View Functions ***-------------------------------

use __Bootstrap\__Caches;
use Tools\Response;

/**
 * print metatag csrf
 * @param string $_tagName name & id for metatag
 * @return string html DOM meta tag
 */
function metaCsrf(string $_tagName = '__token')
{
	$csrf = \__Bootstrap\__Security::makeCsrf();
	$csrf = '<meta name="' . $_tagName . '" id="' . $_tagName . '" content="' . $csrf . '">';
	echo $csrf;
}

/**
 * print metatag csrf
 * @param string $_tagName name & id for metatag
 * @return string html DOM input hidden tag
 */
function putCsrf(string $_tagName = '__token')
{
	$csrf = \__Bootstrap\__Security::makeCsrf();
	$csrf = '<input name="' . $_tagName . '" id="' . $_tagName . '" type="hidden" value="' . $csrf . '">';
	echo $csrf;
}

function valueSection(string $_name, $_value)
{
	Response::$sections[$_name] = $_value;
}

function startSection(string $_name)
{
	ob_start(
		function ($_buffer) use ($_name) {
			Response::$sections[$_name] = $_buffer;
			return '';
		}
	);
}

function endSection()
{
	ob_end_flush();
}

function loadView(string $_name, $_data = null)
{
	Response::loadView($_name, $_data);
}

function eXSS($_value)
{
	return htmlentities($_value);
}

function partialContent(string $_content, $_params = null, $_skipParts = null, $_repeatSkip = null)
{
	if ($_skipParts !== null && $_repeatSkip === null)
		$_repeatSkip = 1;

	if ($_repeatSkip !== null) {
		if ($_skipParts !== null) {
			if ($_repeatSkip > 1) {
				$tmp = [];
				for ($i = 1; $i <= $_repeatSkip; $i++)
					$tmp[] = '/(<!-- @' . $_skipParts . $i . ' -->[\s\S]+<!-- @' . $_skipParts . $i . ' -->)/';
				$_content = preg_replace($tmp, '', $_content);
			} else
				$_content = preg_replace('/(<!-- @' . $_skipParts . ' -->[\s\S]+<!-- @' . $_skipParts . ' -->)/', '', $_content);
		} else {
			if ($_repeatSkip > 1) {
				$tmp = [];
				for ($i = 1; $i <= $_repeatSkip; $i++)
					$tmp[] = '/(<!-- @(\w+)Else' . $i . ' -->[\s\S]+<!-- @(\w+)Else' . $i . ' -->)/';
				$_content = preg_replace($tmp, '', $_content);
			} else
				$_content = preg_replace('/(<!-- @(\w+)Else' . ' -->[\s\S]+<!-- @(\w+)Else' . ' -->)/', '', $_content);
		}
	}

	$_content = str_replace(["\r\n", "\n"], [' ', ' '], $_content);

	if ($_params !== false) {
		if ($_params !== null) {
			$tmp = []; //keys
			uksort(
				$_params,
				function ($_a, $_b) {
					return strlen($_b) - strlen($_a);
				}
			); // restrict to change the @stateIcon to trueIcon cause of existing the @state

			$dCX = [in_array('dontCheckXSS', $_params), false];
			if (!$dCX[0])
				$dCX = [$_params['dontCheckXSS'] ?? false, true];

			unset($_params['dontCheckXSS']);

			if ($dCX[0] === false || $dCX[1] === true)
				array_walk(
					$_params,
					function (&$_value, $_key) use (&$tmp, &$dCX) {
						$e = true;
						if (is_array($dCX[0]))
							if (in_array($_key, $dCX[0]))
								$e = false;

						if ($e)
							$_value = eXSS($_value);
						$tmp[] = '/\@' . $_key . '/';
					}
				);
			else
				array_walk(
					$_params,
					function ($_value, $_key) use (&$tmp) {
						$tmp[] = '/\@' . $_key . '/';
					}
				);

			$_content = preg_replace($tmp, array_values($_params), $_content);
		} else
			$_content = addslashes($_content);
	}

	return $_content;
}

function loadPartial(string $_name, $_params = null, $_skipParts = null, $_repeatSkip = null)
{
	$_name = str_replace('.', '/', $_name);

	if (__Caches::exists($_name))
		$res = __Caches::{$_name}();
	else {
		$res = file_get_contents(PrjDir . '/__App/__Views/__Partials/' . $_name . '.php');
		__Caches::{$_name}($res);
	}

	echo partialContent($res, $_params, $_skipParts, $_repeatSkip);
}

function loadSection(string $_name, bool $_required = true)
{
	if (isset(Response::$sections[$_name]))
		echo (Response::$sections[$_name]);
	else
		if ($_required === true)
		throw new Exception('there is no section named ' . $_name);
}


function perviousPage(bool $_echo = true)
{
	if ($_echo === true)
		echo Session::__()['pervPage'];
	else
		return Session::__()['pervPage'];
}
