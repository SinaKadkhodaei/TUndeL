<?php

namespace __DataLayer\__Migration\__Tables;

class CourseStudents extends \__DataLayer\__Driver\__Migration
{
	public $priority = 14;

	public function make()
	{
		$this->table('course_students');

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
			->field('id_student')
			->int(10)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('students', 'id_students')
			->onAction()
			->cascade();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
