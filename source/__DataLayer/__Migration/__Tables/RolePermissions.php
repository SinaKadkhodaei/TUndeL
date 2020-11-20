<?php

namespace __DataLayer\__Migration\__Tables;

class RolePermissions extends \__DataLayer\__Driver\__Migration
{
	public $priority = 7;

	public function make()
	{
		$this->table('role_permissions');

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
			->field('controller')
			->tinyInt(2)
			->unsigned()
			->notNull();
		$this
			->field('action')
			->tinyInt(1)
			->unsigned()
			->notNull();
		$this
			->field('type')
			->tinyInt(3)
			->unsigned()
			->notNull();

		$this->endTable();
	}
}
