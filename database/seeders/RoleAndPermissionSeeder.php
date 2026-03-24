<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'admin.access',
            'users.manage',
            'users.view',
            'roles.manage',
            'settings.manage',
            'teacher.manage',
            'teacher.view',
            'student.manage',
            'student.view',
            'parent.manage',
            'parent.view',
            'supervisor.manage',
            'slot.manage',
            'schedule.manage',
            'attendance.manage',
            'lesson.manage',
            'task.manage',
            'leave.review',
            'leave.request',
            'salary.manage',
            'salary.view',
            'invoice.manage',
            'payment.manage',
            'reports.view',
            'notifications.manage',
            'violations.manage',
            'profile.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $rolePermissions = [
            'Admin' => $permissions,
            'HR' => [
                'dashboard.view',
                'users.manage',
                'teacher.manage',
                'teacher.view',
                'student.manage',
                'student.view',
                'parent.manage',
                'parent.view',
                'supervisor.manage',
                'profile.view',
            ],
            'Supervisor' => [
                'dashboard.view',
                'teacher.view',
                'student.view',
                'parent.view',
                'slot.manage',
                'schedule.manage',
                'attendance.manage',
                'lesson.manage',
                'task.manage',
                'leave.review',
                'profile.view',
            ],
            'Teacher' => [
                'dashboard.view',
                'attendance.manage',
                'lesson.manage',
                'task.manage',
                'leave.request',
                'salary.view',
                'profile.view',
            ],
            'Student' => [
                'dashboard.view',
                'profile.view',
            ],
            'Parent' => [
                'dashboard.view',
                'profile.view',
            ],
            'Accountant' => [
                'dashboard.view',
                'invoice.manage',
                'payment.manage',
                'reports.view',
                'profile.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $assignedPermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($assignedPermissions);
        }
    }
}
