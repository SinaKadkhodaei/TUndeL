<?php

$prjDir = dirname(__DIR__, 2); // without last two directory , substr(__DIR__, 0, strlen(__DIR__) - strlen(dirname(__DIR__, 2))); //'__Bootstrap/__Bases'
$prefixDir = '';

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
	$prefixDir = '/';

define('PrjDir', $prefixDir . trim($prjDir, '/'));
define('PublicDir', PrjDir . '/Public');

if (php_sapi_name() === 'cli-server') {
	$redirectURL = preg_replace('/(\?.+)$/', '', $_SERVER['REQUEST_URI']);
	$requestScheme = 'http';
	$curRoute = $redirectURL;
} else {
	$redirectURL = $_SERVER['REDIRECT_URL'];
	$requestScheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
	$curRoute = $_GET['__URL'];
}

$curNormalRoute = \__Bootstrap\__Routes::getNormalUrl($curRoute);

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
	$redirectURL = preg_replace('/^(Public)/', '', trim($redirectURL, '/'), 1);

$curUrl =
	$requestScheme . '://' .
	trim($_SERVER['HTTP_HOST'], '/') . '/' .
	trim($redirectURL, '/');

define('CurrentRoute', $curNormalRoute);
define('CurrentUrl', $curUrl);

define('OriginUrl',  trim(str_replace((CurrentRoute == '' ? '' : '/' . trim($curRoute, '/')), '', CurrentUrl), '/'));
define('RequestMethod', strtolower($_SERVER['REQUEST_METHOD']));

unset($curUrl, $_GET['__URL'], $prefixDir, $prjDir, $curRoute, $curNormalRoute, $redirectURL, $requestScheme);

// --------------------*** Data Type Constants ***--------------------

define('TypeTimeStamp', 'ts');
define('TypeInt', 'int');
define('TypeArray', 'array');
define('TypeString', 'string');
define('TypeBool', 'bool');
define('TypeFloat', 'float');
define('TypeFile', 'file');

// --------------------***   Request Methods   ***--------------------

define('MethodGet', 1);
define('MethodPost', 2);
