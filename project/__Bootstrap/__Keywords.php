<?php

namespace __Bootstrap;

class __Keywords
{
	public static
		$route =
		[
			'name' => '__nam',
			'address' => '__adr',
			'middleware' => '__mid',
			'middlewareExceptions' => '__mex',
			'root' => '__rot',
			'targets' => '__trg',
			'run' => '__run',
			'method' => '__met',
			'argument' => '__arg',
			'regex' => '__rgx',
			'configs' => '__cfg',
			'caseSensitivity' => '__sns',
			'params' => '__prm',
		],
		$method =
		[
			'any' => 0,
			'get' => 1,
			'post' => 2,
		],
		$middleware =
		[
			'run' => '__run',
			'type' => '__typ',
			'self' => 0,
			'all' => 1,
		],
		$response =
		[
			'status' => '__stu',
			'action' => '__act',
			'data' => '__dta',
			'showView' => 0,
			'showData' => 1,
			'runTarget' => 0,
			'nextMiddleware' => 1,
			'fail' => 2,
		],
		$security =
		[
			'csrf' => 'x-csrf-token',
			'xsrf' => 'x-xsrf-token',
		];

	static function __callStatic($_name, $_args)
	{
		return ((object) self::${$_name});
	}
}
