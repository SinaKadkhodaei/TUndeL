<?php

namespace __DataLayer\__Migration\__Tables;

class CourseExams extends \__DataLayer\__Driver\__Migration
{
	public $priority = 19;

	public function make()
	{
		$this->table('course_exams');

		$this
			->field('id_course_exams')
			->int(10)
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
			->field('name')
			->string(100)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();
		$this
			->field('started_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->nullable();
		$this
			->field('closed_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->nullable();

		$this->endTable();
	}
}
