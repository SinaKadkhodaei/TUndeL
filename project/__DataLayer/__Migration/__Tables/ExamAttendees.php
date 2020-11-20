<?php

namespace __DataLayer\__Migration\__Tables;

class ExamAttendees extends \__DataLayer\__Driver\__Migration
{
	public $priority = 20;

	public function make()
	{
		$this->table('exam_attendees');

		$this
			->field('id_course_exam')
			->int(10)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('course_exams', 'id_course_exams')
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

		$this->endTable();
	}
}
