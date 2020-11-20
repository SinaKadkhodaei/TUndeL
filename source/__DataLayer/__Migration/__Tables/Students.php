<?php

namespace __DataLayer\__Migration\__Tables;

class Students extends \__DataLayer\__Driver\__Migration
{
	public $priority = 11;

	public function make()
	{
		$this->table('students');

		$this
			->field('id_students')
			->int(10)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_discipline')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('disciplines', 'id_disciplines')
			->onAction()
			->cascade();
		$this
			->field('year_entry')
			->string(3)
			->notNull();
		$this
			->field('national_code')
			->int(10)
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('student_code')
			->bigInt(20)
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('first_name')
			->string(50)
			->notNull();
		$this
			->field('last_name')
			->string(50)
			->notNull();
		$this
			->field('password')
			->string(60)
			->nullable();
		$this
			->field('photo')
			->string()
			->nullable();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
