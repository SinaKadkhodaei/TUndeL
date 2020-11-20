<?php

namespace __App\__Middlewares;

use __Bootstrap\__DoAction as __Do;

class test
{
	public function handle(__Do $_do)
	{
		if (isset($_GET['a']))
			return $_do->next();

		$_do->fail(['status' => false, 'level' => 'test'], 400);
	}
}
