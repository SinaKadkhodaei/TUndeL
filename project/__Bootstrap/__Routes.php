<?php

namespace __Bootstrap;

use __Bootstrap\__Keywords as __K;
use __Bootstrap\__Run;
use Exception;
use Tools\Response;

class __Routes
{
	// example
	/*
	private static $routes =
	[
		'Auth'=>[
			'Login'=>[
				__K::routes()->method=>__K::methods()->get,

			],
		],
	];
	*/
	const allowedSymbols = '\-';
	private static $urlExtension = null; // extension for urls
	private static $routes = []; // map routes with configs
	private static $routesList = []; // list all routes by name with address
	private static $rules =  // regex rules for routes
	[
		'num' => '/^(\d+)$/',
		'str' => '/^([آ-یa-zA-Z]+)$/u',
		'any' => '/^(.+)$/',
		'slug' => '/^([\w-]+)$/u',
	];

	// --------------------------------------------------------------------------
	// public static function __callStatic($name, $args)
	// {
	// 	return ((object) self::${$name});
	// }!!!!!!!!

	/**
	 * @param string $_url is address for route
	 * @param int $_method the method must run for this route
	 * @return __Route for config current route
	 */
	private static function add(string $_url, int $_method) //array $_params)
	{
		$currentRoute = null;

		$find = [
			'/{((?!\d)(\w+\:\w+))}/ui', // '/Posts/{postId:num}/' to '/Posts/_postId:num/'
			'/((\w+\:\w+)|((?!\w+\:\w+)([\w' . self::allowedSymbols . ']+)))/ui', // '/Posts/_postId:num/' to '/["Posts"]/["_postId:num"]/'
			'/([\/]+)/u', // '/["Posts"]/["_postId:num"]/' to '["Posts"]["_postId:num"]'
			'/^$/', // '' to '["__rot"]
		];
		$replace = [
			'_$1',
			'["$1"]',
			'',
			'["' . __K::route()->root . '"]',
		];
		$routesArrayOfUrl = preg_replace($find, $replace, $_url); // convert '/Posts/{postId:num}/Comments/{commentId:num}' to '["Posts"]["_postId"]["Comments"]["_commentId"]';

		eval('
			self::$routes' . $routesArrayOfUrl . '[__Bootstrap\__Keywords::route()->targets][' . $_method . ']=[];

			$currentRoute=&self::$routes' . $routesArrayOfUrl . '[__Bootstrap\__Keywords::route()->targets][' . $_method . '];
		');

		return (new __Route($_url, $currentRoute, self::$routesList));
	}

	public static function get(string $_url)
	{
		return self::add($_url, __K::method()->get);
	}

	public static function post(string $_url)
	{
		return self::add($_url, __K::method()->post);
	}

	public static function any(string $_url)
	{
		return self::add($_url, __K::method()->any);
	}

	public static function extension(string $_extension)
	{
		return self::$urlExtension = $_extension;
	}

	/**
	 * @param string $_name rule name
	 * @param string $_pattern a regex pattern for run a rule on a route
	 */
	public static function addRule(string $_name, string $_pattern)
	{
		self::$rules[$_name] = $_pattern;
	}

	/**
	 * @param string $_name rule name
	 * @return string regex
	 */
	public static function getRule(string $_name)
	{
		return self::$rules[$_name];
	}

	/**
	 * @param string $_name route name
	 * @param array $_params parameters for this route
	 * @return string url
	 */
	public static function getRoute(string $_name, array $_params = [])
	{
		$route = self::$routesList[$_name];
		if ($route == null && $route !== '')
			throw new Exception('There is no route named "' . $_name . '"');

		if (count($_params) > 0)
			$route = bind($route, $_params, '/\{(\w+:\w+)\}/', true);
		return $route . ($route === '' ? '' : self::$urlExtension);
	}

	/**
	 * @param string $_url url address
	 * @return string normal url
	 */
	public static function getNormalUrl(?string $_url)
	{
		$_url = urldecode($_url);
		return preg_replace(
			[
				'/((^\/+)|(\/+$)|(\?$))/', // convert '/////Post/////{postId:num}//////' to 'Post/////{postId:num}'
				'/(\/+)/', // convert 'Post/////{postId:num}' to 'Post/{postId:num}'
			],
			[
				'',
				'/',
			],
			$_url
		);
	}

	/**
	 * @param string $_url url address
	 * @return string normal url
	 */
	private static function logicUrl(string $_url)
	{
		return preg_replace(
			[
				'/^$/', // '' to '["__rot"]
			],
			[
				__K::route()->root,
			],
			$_url
		);
	}

	/**
	 * @param string $_url url address for run configurations
	 * @return
	 */
	public static function run(string $_url)
	{

		$wrong = true;
		$_url = self::logicUrl($_url);

		if (self::$urlExtension !== null)
			if ($_url !== __K::route()->root) {
				if (!preg_match('/^((.+)' . preg_quote(self::$urlExtension)  . ')$/', $_url))
					goto exitFor404;

				$_url = substr($_url, 0, (-1 * strlen(self::$urlExtension)));
			}

		//------------------------------------------------

		$KW_anyMethod = __K::method()->any;
		$KW_mw = __K::route()->middleware;
		$KW_targets = __K::route()->targets;
		$KW_run = __K::route()->run;
		$KW_params = __K::route()->params;

		//------------------------------------------------

		$requestMethod = RequestMethod;
		$originalRequestMethod = 0;
		if (isset(__K::method()->{$requestMethod})) {
			$originalRequestMethod = __K::method()->{$requestMethod};
			$requestMethod = __K::method()->{$requestMethod};
		}

		$partialUrl = explode('/', $_url);
		$countOfParts = count($partialUrl);
		$ptr = &self::$routes; // a pointer for surf in routes

		$params = [];
		$middlewares = [];

		$addMiddleWare =
			function (&$ptr, $_requestMethod) use (&$middlewares, $KW_targets, $KW_mw) {
				$middlewares =
					array_merge(
						$middlewares,
						array_filter(
							$ptr[$KW_targets][$_requestMethod][$KW_mw],
							function ($_value) { // get middlewares that is for all subroutes
								if ($_value[__K::middleware()->type] === __K::middleware()->all)
									return true;
							}
						)
					);
			};

		$i = 0;
		foreach ($partialUrl as $part) {
			$wrong = true;
			$i++;
			$keys = array_keys($ptr);
			$key = preg_grep('/^((?!' . $KW_targets . ')(' . $part . '))$/ui', $keys);

			if (count($key) > 0) { // if not __cfg and has partial
				$ptr = &$ptr[array_pop($key)];
				$wrong = false;
			} else {
				$key = preg_grep('/^_(\w+):(\w+)/', $keys);
				if (count($key) > 0) {
					foreach ($key as $route) {
						$rule = explode(':', $route);
						$tmp = null;
						if (preg_match(self::getRule($rule[1]), $part)) {
							$ptr = &$ptr[$route];
							$params[] = $part;
							$wrong = false;
							break;
						}
					}
				}
			}

			if ($wrong)
				break;

			if ($i < $countOfParts) // if step is not final
				if (isset($ptr[$KW_targets])) {
					$ptrTarget = &$ptr[$KW_targets];
					if (isset($ptrTarget[$requestMethod]))
						if (isset($ptrTarget[$requestMethod][$KW_mw]))
							$addMiddleWare($ptr, $requestMethod);

					if (isset($ptrTarget[$KW_anyMethod]))
						if (isset($ptrTarget[$KW_anyMethod][$KW_mw]))
							$addMiddleWare($ptr, $KW_anyMethod);
				}
		}

		if (!$wrong) {
			$wrong = true;
			if (isset($ptr[$KW_targets])) {
				if (isset($ptr[$KW_targets][$KW_anyMethod])) {
					if (!isset($ptr[$KW_targets][$KW_anyMethod][$KW_run])) { // if anyMethod doesn't have run index then just use anyMethod middlewares
						if (isset($ptr[$KW_targets][$KW_anyMethod][$KW_mw]))
							$addMiddleWare($ptr, $KW_anyMethod);

						if (isset($ptr[$KW_targets][$requestMethod][$KW_run])) // if requestMethod does have run index then just migrate to requestMethod
							goto run;
					}

					$requestMethod = $KW_anyMethod;
					goto run;
				}
				if (isset($ptr[$KW_targets][$requestMethod]))
					goto run;
			}
		}

		exitFor404: {
			if ($wrong)
				Response::errorHandle(404);
			exit(0);
		}
		exitFor403: {
			Response::errorHandle(403);
			exit(0);
		}

		run: { // run methods and any...
			$ptrTarget = &$ptr[$KW_targets][$requestMethod];

			if (isset($ptrTarget[__K::security()->csrf]) === true) {
				if ($ptrTarget[__K::security()->csrf] === false)
					goto afterCheckCsrf;
			} else {
				$shouldCsrf = __Security::allRoutesShouldUseCsrf(true);

				if ($shouldCsrf === true || $shouldCsrf === $originalRequestMethod)
					goto checkCsrf;

				goto afterCheckCsrf;
			}

			checkCsrf: {
				if (__Security::checkCsrf() === false)
					goto exitFor403;
			}

			afterCheckCsrf: {
				if (isset($ptrTarget[$KW_run])) {

					$middlewares = array_map( // extract target class of middlewares
						function ($_value) use ($KW_run) {
							return $_value[$KW_run];
						},
						$middlewares
					);

					if (isset($ptrTarget[__K::route()->middlewareExceptions]))
						$middlewares = array_diff($middlewares, $ptrTarget[__K::route()->middlewareExceptions]); // $middlewares - __K::route()->middlewareExceptions

					foreach ($middlewares as $middleware) // run pervious middlewares
						__Run::middleware($middleware, false);

					if (isset($ptrTarget[$KW_mw])) {
						$mwPtr = &$ptrTarget[$KW_mw];
						foreach ($mwPtr as $middleware) {
							$do = __Run::middleware($middleware[$KW_run], true);
							if (($do[__K::response()->status] ?? null) === __K::response()->runTarget)
								break;
						}
					}

					if (isset($ptrTarget[$KW_params]))
						$params = array_merge($params, $ptrTarget[$KW_params]);

					__Run::target($ptrTarget[$KW_run], $params);
				}
				goto exitFor404;
			}
		}
	}

	public static function getRoutes()
	{
		return self::$routesList;
		// Response::show(
		// 	[
		// 		'List of routes' => self::$routes,
		// 		'List of routes that have name' =>  self::$routesList
		// 	]
		// );
	}
}

class __Route
{
	private
		$url,
		$route,
		$routeList;

	public function __construct(&$_url, &$_route, &$_routeList)
	{
		$this->url = &$_url;
		$this->route = &$_route;
		$this->routeList = &$_routeList;
	}

	/**
	 * @param mixed $_Controller class name or function for direct call
	 * @param string $_action public non-static function name
	 * @return __Route $this for more actions
	 */
	public function run($_Controller, string $_action = null)
	{
		$this->route[__K::route()->run] = is_callable($_Controller) ? $_Controller : ($_Controller  . '::' . $_action);
		return $this;
	}

	/**
	 * @param bool $_checkCsrf
	 * @return __Route $this for more actions
	 */
	public function csrf(bool $_checkCsrf = true) // run route just with security token
	{
		$this->route[__K::security()->csrf] = $_checkCsrf;
		return $this;
	}

	/**
	 * @param string $_target class name
	 * @param bool $_isForAllSubRoutes is it for all sub routes?
	 * @return __Route $this for more actions
	 */
	public function middleware(string $_target, bool $_isForAllSubRoutes = false)
	{
		// * if __Bootstrap\__Keywords as _K then
		// * 		for all sub-branches : __K::middleware()->all
		// * 		and
		// * 		for default and self route : __K::middleware()->self 

		$type = null;
		if ($_isForAllSubRoutes === true)
			$type = __K::middleware()->all;
		else
			$type = __K::middleware()->self;

		$this->route[__K::route()->middleware][] =
			[
				__K::middleware()->run	=>	$_target,
				__K::middleware()->type	=>	$type,
			];

		return $this;
	}

	/**
	 * if parents have subRouteMiddlware the dont check these middlewares
	 * @param string $_except class names
	 * @return __Route $this for more actions
	 */
	public function middlewareExceptions(string ...$_except)
	{
		$this->route[__K::route()->middlewareExceptions] = $_except;
		return $this;
	}

	/**
	 * @param string $_name name of route
	 */
	public function name(string $_name)
	{
		$this->routeList[$_name] = __Routes::getNormalUrl($this->url);
	}

	/**
	 * @param mixed $_params the parameters for pass to function
	 * @return __Route $this for more actions
	 */
	public function parameters(...$_params)
	{
		$this->route[__K::route()->params] = $_params;
		return $this;
	}
}

/**
 * $_params=
 * 	[
 * 		__K::route()->target	=>
 * 			[
 *				__K::route()->get	=>
 *	 				[
 *						__K::route()->middleware	=>	
 *							[
 * 								[
 * 									__K::middleware()->run	=>	'CheckRules'			,
 * 									__K::middleware()->type	=>	__K::middleware()->all	,
 * 								],
 * 								[
 * 									__K::middleware()->run	=>	'CheckPermissions'		,
 * 									__K::middleware()->type	=>	__K::middleware()->self	,
 * 								],
 * 							] ,
 *						__K::route()->middlewareExceptions	=>	
 *							[
 * 								'Login' ,
 * 								'XSRF' ,
 * 							] ,
 * 						__K::route()->run	=>	'Post::login'
 * 					],
 * 			],
 * 	]
 */
