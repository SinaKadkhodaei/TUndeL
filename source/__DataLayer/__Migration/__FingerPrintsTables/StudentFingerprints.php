<?php

namespace __DataLayer\__Migration\__FingerPrintsTables;

class StudentFingerprints extends \__DataLayer\__Driver\__Migration
{
	public $priority = 1;

	public function make()
	{
		$this->table('student_fingerprints');

		$this
			->field('id_student')
			->int(10)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('registered_students', 'id_student')
			->onAction()
			->cascade();
		$this
			->field('fingerprint')
			->string()
			->notNull();

		$this->endTable();
	}
}
