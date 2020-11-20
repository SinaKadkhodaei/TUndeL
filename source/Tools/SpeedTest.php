<?php

namespace Tools;

class SpeedTest
{
	public static function getTime()
	{
		$m = microtime();
		$m = explode(' ', $m);
		$m = substr($m[1], -2) . substr($m[0], 1);
		return $m;
	}

	public static function calcTime($start, $end)
	{
		$end = explode('.', $end);
		$start = explode('.', $start);
		$len = strlen($start[1]) > strlen($end[1]) ? strlen($end[1]) : strlen($start[1]);
		return (($end[0] - $start[0]) . '.' . str_pad($end[1] - $start[1], $len, '0', STR_PAD_LEFT));
	}
}
