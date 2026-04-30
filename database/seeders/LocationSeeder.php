<?php
namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name'         => 'City Blood Center',
                'address'      => '123 Main Street, Downtown',
                'city'         => 'Kigali',
                'phone'        => '+250 788 000 001',
                'hours'        => '08:00 - 17:00',
                'availability' => 'high',
                'walk_ins'     => true,
            ],
            [
                'name'         => 'University Hospital Blood Bank',
                'address'      => '456 College Avenue, Gikondo',
                'city'         => 'Kigali',
                'phone'        => '+250 788 000 002',
                'hours'        => '07:00 - 19:00',
                'availability' => 'medium',
                'walk_ins'     => true,
            ],
            [
                'name'         => 'Community Center Mobile Drive',
                'address'      => '789 Community Way, Nyamirambo',
                'city'         => 'Kigali',
                'phone'        => '+250 788 000 003',
                'hours'        => 'Closed',
                'availability' => 'high',
                'walk_ins'     => true,
            ],
            [
                'name'         => 'Regional Health Clinic',
                'address'      => '101 Health Road, Remera',
                'city'         => 'Kigali',
                'phone'        => '+250 788 000 004',
                'hours'        => '09:00 - 15:00',
                'availability' => 'low',
                'walk_ins'     => false,
            ],
        ];

        foreach ($locations as $l) {
            Location::firstOrCreate(['name' => $l['name']], $l);
        }

        
    }
}