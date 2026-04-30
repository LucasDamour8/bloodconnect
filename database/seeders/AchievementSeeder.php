<?php
namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['name'=>'First Donation', 'description'=>'You made your first blood donation!',         'icon'=>'🩸', 'required_donations'=>1],
            ['name'=>'5 Donations',    'description'=>'You have donated blood 5 times.',              'icon'=>'⭐', 'required_donations'=>5],
            ['name'=>'10 Donations',   'description'=>'You have donated blood 10 times.',             'icon'=>'🏅', 'required_donations'=>10],
            ['name'=>'Life Saver',     'description'=>'Your donations have saved over 30 lives!',     'icon'=>'❤️', 'required_donations'=>15],
            ['name'=>'Hero',           'description'=>'A true hero — 20 or more donations.',          'icon'=>'🦸', 'required_donations'=>20],
        ];

        foreach ($achievements as $a) {
            Achievement::firstOrCreate(['name' => $a['name']], $a);
        }
    }
}