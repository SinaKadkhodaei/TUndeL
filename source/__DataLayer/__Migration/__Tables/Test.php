<?php

namespace __DataLayer\__Migration\__Tables;

class Test extends \__DataLayer\__Driver\__Migration
{
	public $priority = 1;

	public function make()
	{
		$this->table('test');

		$this
			->field('id_test')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('test_name')
			->string(50)
			->notNull();
		$this
			->field('state')
			->bool()
			->notNull()
			->defaultValue(1);

		$this->endTable();
	}
}
