<?php

namespace __DataLayer\__Migration;

use __DataLayer\RolePermissions;
use __DataLayer\Roles;
use __DataLayer\Users;

class __Seeder
{
	public static function make()
	{
		$row = Roles::insert(
			[
				'id_roles' => 1,
				'name' => 'مدیر',
				'state' => true
			]
		)->run(false, 1, true);

		$row = $row['insertedKeys'];

		$rolePermissions = [];
		foreach (Roles::$permissionsSchema as $controller => $cProps)
			foreach ($cProps['abilities'] as $action => $aProps)
				$rolePermissions[] = [
					'id_role' => $row[0],
					'controller' => $controller,
					'action' => $action,
					'type' => 1
				];

		RolePermissions::delete()->where('id_role=1')->run();
		RolePermissions::insert($rolePermissions)->run();

		Users::insert(
			[
				'id_users' => 1,
				'id_role' => $row[0],
				'national_code' => 123456,
				'personal_code' => 654321,
				'first_name' => 'مدیر',
				'last_name' => 'سامانه',
				'password' => null,
				'photo' => null,
				'state' => true,
			]
		)->run();
	}
}
