<?php

namespace __DataLayer\__Migration\__Tables;

class UserInstitutes extends \__DataLayer\__Driver\__Migration
{
	public $priority = 9;

	public function make()
	{
		$this->table('user_institutes');

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
			->field('id_institute')
			->tinyInt()
			->unsigned()
			->notNull()
			->foreignKey()
			->references('institutes', 'id_institutes')
			->onAction()
			->cascade();
		$this
			->field('role')
			->tinyInt(1)
			->notNull()
			->defaultValue(0);

		$this->endTable();
	}
}
