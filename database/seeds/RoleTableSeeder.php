<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_superadmin = new Role();
        $role_superadmin->role_name = 'Super Admin';
        $role_superadmin->save();
        $role_admin = new Role();
        $role_admin->role_name = 'Admin';
        $role_admin->save();
        $role_user = new Role();
        $role_user->role_name = 'User';
        $role_user->save();
    }
}
