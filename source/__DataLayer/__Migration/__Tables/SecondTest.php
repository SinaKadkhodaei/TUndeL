<?php

namespace __DataLayer\__Migration\__Tables;

class SecondTest extends \__DataLayer\__Driver\__Migration
{
	public $priority = 2;

	public function make()
	{
		$this->table('second_test');

		$this
			->field('id_second_test')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_test')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('test', 'id_test')
			->onAction()
			->cascade();
		$this
			->field('second_test_name')
			->string(50)
			->notNull();

		$this->endTable();
	}
}
