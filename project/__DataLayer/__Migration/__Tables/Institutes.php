<?php

namespace __DataLayer\__Migration\__Tables;

class Institutes extends \__DataLayer\__Driver\__Migration
{
	public $priority = 3;

	public function make()
	{
		$this->table('institutes');

		$this
			->field('id_institutes')
			->tinyInt()
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_stage')
			->tinyInt()
			->unsigned()
			->notNull()
			->foreignKey()->references('stages', 'id_stages')->onAction()->cascade();
		$this
			->field('name')
			->string(50)
			->notNull();
		$this
			->field('is_public')
			->bool()
			->notNull()
			->defaultValue(0);
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
