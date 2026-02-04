<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissions = [
            'manager' => [
                'task.create',
                'task.update',
                'task.assign',
                'task.view-all',
                'task.view',
            ],
            'user' => [
                'task.view',
                'task.update-status',
            ]
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
            ]);


            $permissions = Permission::whereIn('name', $permissionNames)->get();

            $role->syncPermissions($permissions);
        }
    }

}
