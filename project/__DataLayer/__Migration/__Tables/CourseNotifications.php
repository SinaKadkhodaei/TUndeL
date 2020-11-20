<?php

namespace __DataLayer\__Migration\__Tables;

class CourseNotifications extends \__DataLayer\__Driver\__Migration
{
	public $priority = 15;

	public function make()
	{
		$this->table('course_notifications');

		$this
			->field('id_course_notifications')
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
			->field('text')
			->string(500)
			->notNull();
		$this
			->field('created_at')
			->timestamp()
			->defaultValue('CURRENT_TIMESTAMP', true)
			->notNull();

		$this->endTable();
	}
}
