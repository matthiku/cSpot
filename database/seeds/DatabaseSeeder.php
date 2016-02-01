<?php

# (C) 2016 Matthias Kuhs, Ireland

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);

        // see https://tuts.codingo.me/laravel-social-and-email-authentication
        Model::unguard();

        $this->call('RoleSeeder');
        $this->call('UserSeeder');

        Model::reguard();

    }
}
