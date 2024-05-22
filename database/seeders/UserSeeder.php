<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count()) {
            return;
        }

        User::create([
            'name' => 'Doc Demo',
            'email' => 'admin@ims.co.zw',
            'avatar' => '/images/empty-user.jpg',
            'password' => Hash::make('admin1234'),
            'country_id' => 1,
            'email_verified_at' => now(),
            'role' => 'admin'
        ]);

        $user = User::create([
            'name' => 'Patient Demo',
            'email' => 'patient@ims.co.zw',
            'avatar' => '/images/empty-user.jpg',
            'password' => Hash::make('patient1234'),
            'country_id' => 1,
            'email_verified_at' => now(),
            'role' => 'patient'
        ]);

        //patient info
        $user->patientInformation()->create([
            'gender' => 'male',
            'dob' => Carbon::now()->subYears(20),
            'class' => 'adult',
            'address' => '1234 Main St',
        ]);

        $bmi = 78.82 / (1.82 * 1.82);

        $bmi_category = match (true) {
            $bmi <= 18 => 'Underweight',
            $bmi > 18 && $bmi <= 25 => 'Normal weight',
            $bmi > 25 && $bmi <= 30 => 'Overweight',
            default => 'Obese',
        };

        //patient athrometric
        $user->patientAthrometric()->create([
            'height' => '1.82',
            'weight' => '78.82',
            'bmi' => $bmi,
            'bmi_category' => $bmi_category,
        ]);

        $heightCubed = 1.82 * 1.82 * 1.82;
        $tbv = 0.3669 * $heightCubed + (0.03219 * 78.82) + 0.6041;

        $user->patientPhysiology()->create([
            // 'tbv' => round($tbv, 5),
            'tbv' => '2',
            'cbgr' => round((0.00556 / (0.6 * $tbv)), 5),
            'isf' => 2,
            'dia' => 0.5,
        ]);

    }
}
