<?php

namespace __App\__Middlewares;

use __Bootstrap\__DoAction as __Do;

class test1
{
	public function handle(__Do $_do)
	{
		if (isset($_GET['b']))
			return $_do->next();

		$_do->fail(['status' => false, 'level' => 'test1'], 400);
	}
}
