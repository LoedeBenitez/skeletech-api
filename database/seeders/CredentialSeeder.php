<?php

namespace Database\Seeders;

use App\Models\ContactNumber;
use App\Models\EmergencyContact;
use App\Models\EmploymentInformation;
use App\Models\GovernmentInformation;
use App\Models\PersonalInformation;
use App\Models\SystemConfiguration\UserAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Credential;

class CredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // UserAccess::create([
        //     'email' => "superadmin@skeletech.com",
        //     'first_name' => 'Super',
        //     'middle_name' => '',
        //     'last_name' => 'Admin',
        //     'gender' => 'Male',
        //     'birth_date' => '2023-12-25',
        //     'age' => '99',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        Credential::create([
            'email' => "superadmin@skeletech.com",
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'status' => 1,
            'user_access_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        PersonalInformation::create([
            'email' => "superadmin@skeletech.com",
            'first_name' => 'Super',
            'middle_name' => '',
            'last_name' => 'Admin',
            'gender' => 'Male',
            'birth_date' => '2023-12-25',
            'age' => '99',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
