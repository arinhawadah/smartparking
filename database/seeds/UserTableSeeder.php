<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_superadmin  = Role::where('role_name', 'Super Admin')->first();

        $superadmin = new User();
        $superadmin->unique_id = str_random(25);
        $superadmin->name = 'Admin';
        $superadmin->email = 'ad@min.com';
        $superadmin->car_type = 'Car Admin';
        $superadmin->license_plate_number = 'Plate Admin';
        $superadmin->password = bcrypt('superadminsecret');
        $superadmin->activation_token = str_random(25);
        $superadmin->save();
        $superadmin->roles()->attach($role_superadmin);
    }
}
