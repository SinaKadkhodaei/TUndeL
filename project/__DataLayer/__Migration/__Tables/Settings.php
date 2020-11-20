<?php

namespace __DataLayer\__Migration\__Tables;

class Settings extends \__DataLayer\__Driver\__Migration
{
	public $priority = 0;

	public function make()
	{
		$this->table('settings');

		$this
			->field('name')
			->string(50)
			->notNull()
			->primaryKey();
		$this
			->field('data')
			->string(500)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
