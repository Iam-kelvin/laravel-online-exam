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
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => $seedPassword,
        ]);

        $moderator = User::create([
            'name' => 'CrazyExam Moderator',
            'email' => 'moderator@crazyexam.test',
            'email_verified_at' => now(),
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => $seedPassword,
        ]);
            
        $user = User::create([
            'name' => 'CrazyExam Student',
            'email' => 'student@crazyexam.test',
            'email_verified_at' => now(),
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => $seedPassword,
        ]);

        $admin->roles()->attach($adminRole);
        $moderator->roles()->attach($moderatorRole);
        $user->roles()->attach($userRole);
    }
}
