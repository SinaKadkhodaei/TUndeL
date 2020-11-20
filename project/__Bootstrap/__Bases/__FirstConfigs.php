<?php

//-----------------------------*** Start load files ***-----------------------------
spl_autoload_register(
	function (string $_class) {
		$class = trim(str_replace('\\', '/', $_class), '/');
		// if (!file_exists(PrjDir . '/' . $class . '.php')) {
		// 	echo 'class ' . $_class . ' doesn\'t exists!!!';
		// 	die();
		// }
		require_once(PrjDir . '/' . $class . '.php');
	}
);

$files = glob(__DIR__ . '/*.php');

foreach ($files as $file) // load all bases file
	if (basename($file) !== '__FirstConfig.php')
		require_once($file);

if (file_exists(PrjDir . '/vendor/autoload.php'))
	require_once(PrjDir . '/vendor/autoload.php');

//-----------------------------*** End load files ***-----------------------------

//-----------------------------*** Check request  ***-----------------------------

if (php_sapi_name() === 'cli-server') {
	if ($_SERVER['REQUEST_URI'] === 'favicon.ico')
		\Tools\Response::errorHandle(404);

	$file = PublicDir . $_SERVER['REQUEST_URI'];
	if (isset($_SERVER['QUERY_STRING']))
		$file = str_replace('?' . $_SERVER['QUERY_STRING'], '', $file);

	if (is_file($file)) {
		if (!preg_match('/^(\.htaccess|__Run.php)$/i', trim($_SERVER['REQUEST_URI'], '/')))
			if (file_exists($file)) {
				$ext = explode('.', basename($file));

				if (isset(\Tools\Response::types[$ext[count($ext) - 1]]))
					header('Content-type: ' . \Tools\Response::types[$ext[count($ext) - 1]]);
				else
					header('Content-type: ' . mime_content_type($file));

				readfile($file);
				exit(0);
			}
	}
}

unset($files, $file, $ext);

//-----------------------------*** End check request -----------------------------

if (\__Bootstrap\__Configs::app()->debugMode === false)
	error_reporting(0);

if (!isset($_SESSION))
	session_start();

if (!($__fastBoot ?? false)) {
	require_once(PrjDir . '/Tools/__RootFunctions.php');
	\__Events\__System::boot();
}
//-----------------------------*** Shutdown script ***-----------------------------

register_shutdown_function(
	function () {
		if (count(ob_list_handlers()) > 1) //1:"Closure::__invoke"
			if (error_reporting() !== 0)
				if (error_get_last()) {
					ob_get_clean();
					fire(error_get_last()['message']);
				}

		if (error_reporting() === 0)
			if (error_get_last())
				\Tools\Response::errorHandle(500);

		\__Events\__System::shutdown();

		if (RequestMethod === 'get' && http_response_code() == 200)
			if ($GLOBALS['isView'] ?? false)
				Session::__(['pervPage' => CurrentUrl]); // example : http://localhost/OOS/Posts/574
	}
); // when script runing out set current url as pervious page
