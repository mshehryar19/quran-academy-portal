<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_DEFAULT_NAME', 'Portal Admin');
        $email = env('ADMIN_DEFAULT_EMAIL', 'admin@quranacademy.local');
        $password = env('ADMIN_DEFAULT_PASSWORD', 'Admin@12345');

        /** @var User $admin */
        $admin = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $admin->forceFill([
            'name' => $name,
        ])->save();

        if (! Hash::check($password, $admin->password)) {
            $admin->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }

        $admin->syncRoles(['Admin']);

        activity()
            ->causedBy($admin)
            ->event('bootstrap.admin')
            ->log('Default admin bootstrap ensured');
    }
}
