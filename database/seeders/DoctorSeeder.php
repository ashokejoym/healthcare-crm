<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Create a user for the doctor
        $user = User::create([
            'name' => 'Dr. John Doe',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign the 'Doctor' role
        $user->assignRole('Doctor');

        // Create the doctor's profile
        Doctor::create([
            'user_id' => $user->id,
            'specialization' => 'General Medicine',
            'phone' => '1234567890',
            'email' => $user->email,
        ]);
    }
}
