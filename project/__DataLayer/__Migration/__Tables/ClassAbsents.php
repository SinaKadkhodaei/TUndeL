<?php

namespace __DataLayer\__Migration\__Tables;

class ClassAbsents extends \__DataLayer\__Driver\__Migration
{
	public $priority = 17;

	public function make()
	{
		$this->table('class_absents');

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
			->field('delayed')
			->bool()
			->defaultValue('0', true)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
