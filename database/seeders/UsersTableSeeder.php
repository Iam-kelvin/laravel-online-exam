<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        DB::table('role_user')->truncate();

        $adminRole = Role::where('name','admin')->first();
        $moderatorRole = Role::where('name','moderator')->first();
        $userRole = Role::where('name','user')->first();

        $admin = User::create([
            'name' => 'Kelvin Ajala',
            'email' => 'Kelvinajala007@gmail.com',
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => Hash::make('asdfghjkl'),
        ]);

        $moderator = User::create([
            'name' => 'Gideon Ajala',
            'email' => 'gideonajala007@gmail.com',
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => Hash::make('asdfghjkl'),
        ]);
            
        $user = User::create([
            'name' => 'Kelvin',
            'email' => 'ajala007@gmail.com',
            'country' => 'United States',
            'state' => 'Washington',
            'county' => 'DC',
            'level' => 'High',
            'grade' => '9.0',
            'school' => 'FGGC',
            'password' => Hash::make('asdfghjkl'),
        ]);

        $admin->roles()->attach($adminRole);
        $moderator->roles()->attach($moderatorRole);
        $user->roles()->attach($userRole);
    }
}
