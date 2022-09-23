<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create new admin
        $data['name'] = "Shivang Pandit";
        $data['email'] = "admin@gmail.com";
        $data['password'] = bcrypt("adminT@123");

        Admin::create($data);
    }
}
