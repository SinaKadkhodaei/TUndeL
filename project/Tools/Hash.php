<?php

namespace Tools;

class Hash
{
	public static function make($_text)
	{
		return password_hash($_text, PASSWORD_DEFAULT);
	}

	public static function check($_text, $_hash)
	{
		return password_verify($_text, $_hash);
	}
}
