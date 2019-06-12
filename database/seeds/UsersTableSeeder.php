<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin')
            ],

            [
                'name' => 'Some User',
                'email' => 'user@user.com',
                'password' => bcrypt('user')
            ],

        ];

        foreach ($data as $item) {
            \App\User::updateOrCreate($item);
        }
    }
}
