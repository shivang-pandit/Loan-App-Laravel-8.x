<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create new customer
        $data['name'] = "Shiv Pandit";
        $data['email'] = "customer@gmail.com";
        $data['password'] = bcrypt("customerT@123");

        Customer ::create($data);
    }
}
