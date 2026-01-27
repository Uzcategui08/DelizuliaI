<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@autokeys.com'
        ], [
            'name' => 'Administrador Principal',
            'password' => bcrypt('admin123'),
            'rol' => 'admin'
        ]);

        $user = User::firstOrCreate([
            'email' => 'user@autokeys.com'
        ], [
            'name' => 'Usuario Normal',
            'password' => bcrypt('user123'),
            'rol' => 'user'
        ]);

        $limitedUser = User::firstOrCreate([
            'email' => 'limited@autokeys.com'
        ], [
            'name' => 'Usuario Limitado',
            'password' => bcrypt('limited123'),
            'rol' => 'limited'
        ]);

        $admin->syncRoles('admin');
        $user->syncRoles('user');
        $limitedUser->syncRoles('limited');
    }
}
