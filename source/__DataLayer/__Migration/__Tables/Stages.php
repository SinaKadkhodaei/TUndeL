<?php

namespace __DataLayer\__Migration\__Tables;

class Stages extends \__DataLayer\__Driver\__Migration
{
	public $priority = 2;

	public function make()
	{
		$this->table('stages');

		$this
			->field('id_stages')
			->tinyInt()
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('name')
			->string(50)
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
