<?php

namespace __DataLayer;

class Tests extends __Driver\__QueryBuilder
{
	protected static
		$table = 'test',
		$primaryKey = 'id_test',
		$fieldCasting =
		[
			'id_test' => 'testId',
			'test_name' => 'testName',
		],
		$fieldTypes =
		[
			'id_test' => TypeInt,
			'test_name' => TypeString,
			'state' => TypeBool,
			'testType' => TypeFloat,
		];

	public static function showSimple(int $_id)
	{
		return
			self::select(['id_test', 'test_name', 'state', '(id_second_test/2) as testType'])
			->where('id_test<?', [$_id])
			->get();
	}

	public static function insertSimple()
	{
		return
			self::insert(
				[['سینا خان', false], ['رسول خان', true], ['رضا خان', false], ['آیت خان', true]],
				['test_name', 'state']
			)->run(true);
	}
}
