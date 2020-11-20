<?php

namespace __DataLayer\__Migration\__FingerPrintsTables;

class RegisteredStudents extends \__DataLayer\__Driver\__Migration
{
	public $priority = 0;

	public function make()
	{
		$this->table('registered_students');

		$this
			->field('id_student')
			->int(10)
			->unsigned()
			->notNull()
			->primaryKey()
			->foreignKey()
			->references('students', 'id_students', 'db_attendancing')
			->onAction()
			->cascade();
		$this
			->field('id_institute')
			->tinyInt()
			->unsigned()
			->notNull()
			->foreignKey()
			->references('institutes', 'id_institutes', 'db_attendancing')
			->onAction()
			->cascade();

		$this->endTable();
	}
}
