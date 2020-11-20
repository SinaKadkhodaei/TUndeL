<?php

namespace __DataLayer\__Migration\__Tables;

class Courses extends \__DataLayer\__Driver\__Migration
{
	public $priority = 12;

	public function make()
	{
		$this->table('courses');

		$this
			->field('id_courses')
			->mediumInt(8)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_courses_custom')
			->mediumInt()
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('id_lesson')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('lessons', 'id_lessons')
			->onAction()
			->cascade();
		$this
			->field('id_teacher')
			->smallInt(5)
			->unsigned()
			->nullable()
			->foreignKey()
			->references('users', 'id_users')
			->onAction()
			->cascade();
		$this
			->field('id_term')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('terms', 'id_terms')
			->onAction()
			->cascade();
		$this
			->field('id_place')
			->smallInt(5)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('places', 'id_places')
			->onAction()
			->cascade();
		$this
			->field('id_institute_host')
			->tinyInt()
			->unsigned()
			->nullable()
			->foreignKey()
			->references('institutes', 'id_institutes')
			->onAction()
			->cascade();
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
