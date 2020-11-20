<?php

namespace __DataLayer\__Migration\__Tables;

class ClassScores extends \__DataLayer\__Driver\__Migration
{
	public $priority = 18;

	public function make()
	{
		$this->table('class_scores');

		$this
			->field('id_presented_class')
			->bigInt()
			->unsigned()
			->notNull()
			->foreignKey()
			->references('presented_classes', 'id_presented_classes')
			->onAction()
			->cascade();
		$this
			->field('id_student')
			->int(10)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('students', 'id_students')
			->onAction()
			->cascade();
		$this
			->field('score')
			->float(3, 2)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
