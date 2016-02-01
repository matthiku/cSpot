<?php

# (C) 2016 Matthias Kuhs, Ireland

// see https://tuts.codingo.me/laravel-social-and-email-authentication

use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $adminRole = Role::whereName('administrator')->first();
        $userRole = Role::whereName('user')->first();

        $user = User::find(1);
        $user->assignRole($adminRole);

        $user = User::find(2);
        $user->assignRole($userRole);
    }
}
