<?php

namespace __DataLayer\__Migration\__Tables;

class Lessons extends \__DataLayer\__Driver\__Migration
{
	public $priority = 10;

	public function make()
	{
		$this->table('lessons');

		$this
			->field('id_lessons')
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
			->field('id_lessons_custom')
			->mediumInt()
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('name')
			->string(50)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
