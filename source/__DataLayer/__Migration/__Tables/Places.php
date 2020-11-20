<?php

namespace __DataLayer\__Migration\__Tables;

class Places extends \__DataLayer\__Driver\__Migration
{
	public $priority = 5;

	public function make()
	{
		$this->table('places');

		$this
			->field('id_places')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('name')
			->string(50)
			->notNull();
		$this
			->field('address')
			->string(500)
			->nullable();
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
