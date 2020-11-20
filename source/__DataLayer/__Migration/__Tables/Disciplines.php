<?php

namespace __DataLayer\__Migration\__Tables;

class Disciplines extends \__DataLayer\__Driver\__Migration
{
	public $priority = 4;

	public function make()
	{
		$this->table('disciplines');

		$this
			->field('id_disciplines')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_institute')
			->tinyInt()
			->unsigned()
			->notNull()
			->foreignKey()
			->references('institutes', 'id_institutes')
			->onAction()
			->cascade();
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
