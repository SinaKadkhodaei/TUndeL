<?php

namespace __DataLayer\__Migration\__Tables;

class PresentedClasses extends \__DataLayer\__Driver\__Migration
{
	public $priority = 16;

	public function make()
	{
		$this->table('presented_classes');

		$this
			->field('id_presented_classes')
			->bigInt()
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_course')
			->mediumInt(8)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('courses', 'id_courses')
			->onAction()
			->cascade();
		$this
			->field('id_user')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('users', 'id_users')
			->onAction()
			->cascade();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();
		$this
			->field('closed_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->nullable();

		$this->endTable();
	}
}
