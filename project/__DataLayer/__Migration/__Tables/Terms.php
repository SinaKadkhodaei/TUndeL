<?php

namespace __DataLayer\__Migration\__Tables;

class Terms extends \__DataLayer\__Driver\__Migration
{
	public $priority = 1;

	public function make()
	{
		$this->table('terms');

		$this
			->field('id_terms')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('year')
			->smallInt(5)
			->unsigned()
			->notNull();
		$this
			->field('term')
			->tinyInt(2)
			->unsigned()
			->notNull();
		$this
			->field('state')
			->bool()
			->notNull()
			->defaultValue(1);
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
