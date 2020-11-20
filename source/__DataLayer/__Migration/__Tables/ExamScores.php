<?php

namespace __DataLayer\__Migration\__Tables;

class ExamScores extends \__DataLayer\__Driver\__Migration
{
	public $priority = 21;

	public function make()
	{
		$this->table('exam_scores');

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
			->field('score')
			->float(3, 2)
			->unsigned()
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
