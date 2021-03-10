<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions for admin
        Permission::create(['name' => 'users.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'admin']);
        Permission::create(['name' => 'users.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'admin']);

        // create permission for user
        Permission::create(['name' => 'users.view', 'guard_name' => 'user']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'user']);
        Permission::create(['name' => 'users.update', 'guard_name' => 'user']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'user']);


        Permission::create(['name' => 'admins.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'admins.create', 'guard_name' => 'admin']);
        Permission::create(['name' => 'admins.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'admins.delete', 'guard_name' => 'admin']);


        Permission::create(['name' => 'merchants.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'merchants.create', 'guard_name' => 'admin']);
        Permission::create(['name' => 'merchants.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'merchants.delete', 'guard_name' => 'admin']);


        Permission::create(['name' => 'payments.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'payments.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'payments.missed', 'guard_name' => 'admin']);
        Permission::create(['name' => 'payments.search', 'guard_name' => 'admin']);


        Permission::create(['name' => 'payments.view', 'guard_name' => 'user']);
        Permission::create(['name' => 'payments.update', 'guard_name' => 'user']);
        Permission::create(['name' => 'payments.missed', 'guard_name' => 'user']);
        Permission::create(['name' => 'payments.search', 'guard_name' => 'user']);


        Permission::create(['name' => 'roles.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'admin']);
        Permission::create(['name' => 'roles.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'admin']);

        Permission::create(['name' => 'permissions.view', 'guard_name' => 'admin']);
        Permission::create(['name' => 'permissions.create', 'guard_name' => 'admin']);
        Permission::create(['name' => 'permissions.update', 'guard_name' => 'admin']);
        Permission::create(['name' => 'permissions.delete', 'guard_name' => 'admin']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'Super Admin', 'guard_name'=>'admin']);
        $role->syncPermissions([
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'admins.view',
            'admins.create',
            'admins.update',
            'admins.delete',

            'merchants.view',
            'merchants.create',
            'merchants.update',
            'merchants.delete',

            'payments.view',
            'payments.update',
            'payments.missed',
            'payments.search',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
        ]);
    }
}
