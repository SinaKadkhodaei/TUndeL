<?php

namespace __DataLayer\__Migration\__Tables;

class Roles extends \__DataLayer\__Driver\__Migration
{
	public $priority = 6;

	public function make()
	{
		$this->table('roles');

		$this
			->field('id_roles')
			->tinyInt(3)
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
