<?php

namespace __App\__Controllers;

use Tools\Response;

class test
{
	public function hello()
	{
		Response::show(['Welcome' => 'He.Un!']);
	}

	public function bye($_id)
	{
		Response::view('test', ['username' => $_id]);
	}
}
