<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder {
    public function run() {
        $roles = ['Admin', 'CRM Agent', 'Doctor', 'Patient', 'Lab Manager'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create default users
        foreach ($roles as $role) {
            $user = User::create([
                'name' => $role . ' User',
                'email' => strtolower(str_replace(' ', '', $role)) . '@test.com',
                'password' => Hash::make('password')
            ]);
            $user->assignRole($role);
        }
    }
}
