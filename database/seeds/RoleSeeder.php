<?php

# (C) 2016 Matthias Kuhs, Ireland

use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        // see https://tuts.codingo.me/laravel-social-and-email-authentication
        DB::table('roles')->delete();

        Role::create([
            'name'   => 'user'
        ]);

        Role::create([
            'name'   => 'administrator'
        ]);

    }
}
