<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        try {
            DB::table('role_user')->truncate();
            DB::table('exam_results')->truncate();
            User::truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $adminRole = Role::where('name','admin')->first();
        $moderatorRole = Role::where('name','moderator')->first();
        $userRole = Role::where('name','user')->first();
        $seedPassword = Hash::make(env('SEED_USER_PASSWORD', 'Password123!'));

        $admin = User::create([
            'name' => 'CrazyExam Admin',
            'email' => 'admin@crazyexam.test',
            'email_verified_at' => now(),
            'country' => 'Nigeria',
            'state' => 'Nigeria',
            'county' => 'Lagos',
            'level' => 'Senior Secondary',
            'grade' => 'SS 3',
            'school' => 'FGGC',
            'school_level' => 'Senior Secondary',
            'class_year' => 'SS 3',
            'country_of_study' => 'Nigeria',
            'city_town' => 'Lagos',
            'password' => $seedPassword,
        ]);

        $moderator = User::create([
            'name' => 'CrazyExam Moderator',
            'email' => 'moderator@crazyexam.test',
            'email_verified_at' => now(),
            'country' => 'Ghana',
            'state' => 'Ghana',
            'county' => 'Accra',
            'level' => 'University / Higher Institution',
            'grade' => '200 Level',
            'school' => 'FGGC',
            'school_level' => 'University / Higher Institution',
            'class_year' => '200 Level',
            'country_of_study' => 'Ghana',
            'city_town' => 'Accra',
            'password' => $seedPassword,
        ]);
            
        $user = User::create([
            'name' => 'CrazyExam Student',
            'email' => 'student@crazyexam.test',
            'email_verified_at' => now(),
            'country' => 'Nigeria',
            'state' => 'Nigeria',
            'county' => 'Abuja',
            'level' => 'Senior Secondary',
            'grade' => 'SS 2',
            'school' => 'FGGC',
            'school_level' => 'Senior Secondary',
            'class_year' => 'SS 2',
            'country_of_study' => 'Nigeria',
            'city_town' => 'Abuja',
            'password' => $seedPassword,
        ]);

        $admin->roles()->attach($adminRole);
        $moderator->roles()->attach($moderatorRole);
        $user->roles()->attach($userRole);
    }
}
