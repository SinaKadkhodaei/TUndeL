<?php

namespace __DataLayer;

class SecondTests extends __Driver\__QueryBuilder
{
	protected static
		$table = 'second_test',
		$primaryKey = 'id_second_test',
		$fieldCasting =
		[
			'id_second_test' => 'secondTestId',
			'id_test' => 'testId',
			'id_test_new' => 'testNewId',
			'second_test_name' => 'secondTestName',
		],
		$fieldTypes =
		[
			'id_second_test' => TypeInt,
			'id_test' => TypeInt,
			'id_test_new' => TypeInt,
			'second_test_name' => TypeString,
		];

	public static function showSimple(int $_id)
	{
		return
			self::select(['id_second_test', 'second_test_name', 'id_test', '(id_second_test/2) as testType'])
			->where('id_second_test<?', [$_id])
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
