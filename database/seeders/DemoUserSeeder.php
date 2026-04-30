<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Donation;
use App\Models\Location;
use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Donor
        $user = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'phone'         => '+250 788 123 456',
                'date_of_birth' => '1995-06-15',
                'gender'        => 'male',
                'blood_type'    => 'O+',
                'password'      => Hash::make('password'),
                'locale'        => 'en',
            ]
        );

        // 2. Add dynamic demo donations (Detecting current year)
        $location = Location::first();
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $types = ['whole_blood', 'platelets', 'whole_blood'];
        
        // Dynamic dates based on the current year (2026)
        $dates = [
            $currentYear . '-01-15', 
            $lastYear . '-11-10', 
            $lastYear . '-09-05'
        ];

        foreach ($types as $i => $type) {
            Donation::firstOrCreate(
                ['user_id' => $user->id, 'donation_date' => $dates[$i]],
                [
                    'location_id' => $location->id, 
                    'donation_type' => $type, 
                    'status' => 'completed'
                ]
            );
        }

        // 3. Unlock first achievements
        $achievements = Achievement::whereIn('required_donations', [1])->get();
        foreach ($achievements as $a) {
            $user->achievements()->syncWithoutDetaching([
                $a->id => ['unlocked_at' => now()]
            ]);
        }

        // 4. Create Admin user
        User::firstOrCreate(
            ['email' => 'admin@bloodconnect.rw'],
            [
                'first_name'    => 'Admin',
                'last_name'     => 'User',
                'password'      => Hash::make('password'),
                'role'          => 'admin',
                'locale'        => 'en',
                'date_of_birth' => '1990-01-01',
                'gender'        => 'male',
            ]
        );

        // 5. Create Doctor user
        User::firstOrCreate(
            ['email' => 'doctor@bloodconnect.rw'],
            [
                'first_name'    => 'Dr. Marie',
                'last_name'     => 'Uwimana',
                'password'      => Hash::make('password'),
                'role'          => 'doctor',
                'locale'        => 'en',
                'date_of_birth' => '1985-03-15',
                'gender'        => 'female',
            ]
        );
    }
}