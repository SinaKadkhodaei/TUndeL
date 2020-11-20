<?php

namespace __App;

use __Bootstrap\__Routes as Route;
use __Bootstrap\__Security as Security;

class __Routes
{
	public static function make() 
	{
		Route::extension('.html'); // all urls should have .html at the end
		Security::allRoutesShouldUseCsrf(); // all urls should have csrf token in header

		Route::any('Hello/{aNum:num}')
			->run('test', 'hello')
			->middleware('test')
			->csrf(false)
			->name('Hello');

		Route::get('Bye/')
			->middleware('test', true); {

			Route::get('Bye/{aSlug:slug}')
				->run('test', 'bye')
				->middleware('test1')
				->name('Bye');

			Route::addRule('username', '/^\@(?![0-9])([a-zA-Z0-9]+)$/'); // if need extension then add '\.html' after '+'
			Route::any('Bye/{aUsername:username}')
				->csrf(false)
				->middlewareExceptions('test')
				->run('test', 'bye');
		}

		Route::get('/')
			->csrf(false)
			->run(
				function () {
					\Tools\Response::show(csrf(true));
				}
			);

		Route::post('/')
			->run(
				function () {
					\Tools\Response::show(['Test' => 'For Direct Call In POST']);
				}
			);
	}
}
