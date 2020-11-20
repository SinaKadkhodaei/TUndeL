<?php

namespace __DataLayer\__Migration\__Tables;

class Users extends \__DataLayer\__Driver\__Migration
{
	public $priority = 8;

	public function make()
	{
		$this->table('users');

		$this
			->field('id_users')
			->smallInt(5)
			->unsigned()
			->increment()
			->notNull()
			->primaryKey();
		$this
			->field('id_role')
			->tinyInt(3)
			->unsigned()
			->notNull()
			->foreignKey()
			->references('roles', 'id_roles')
			->onAction()
			->cascade();
		$this
			->field('national_code')
			->int(10)
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('personal_code')
			->int(10)
			->unsigned()
			->notNull()
			->unique();
		$this
			->field('first_name')
			->string(50)
			->notNull();
		$this
			->field('last_name')
			->string(50)
			->notNull();
		$this
			->field('password')
			->string(60)
			->nullable();
		$this
			->field('photo')
			->string()
			->nullable();
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
