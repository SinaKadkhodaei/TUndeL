<?php

namespace __DataLayer\__Migration\__Tables;

class CourseTimes extends \__DataLayer\__Driver\__Migration
{
	public $priority = 13;

	public function make()
	{
		$this->table('course_times');

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
			->field('day_of_week')
			->tinyInt(1)
			->unsigned()
			->notNull();
		$this
			->field('time_of_day')
			->time()
			->notNull();
		$this
			->field('duration_hour')
			->time()
			->notNull();

		$this->endTable();
	}
}
