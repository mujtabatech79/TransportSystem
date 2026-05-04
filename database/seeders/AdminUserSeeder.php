<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use App\Models\Userr;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()   
    {
        Userr::create([
            'name' => 'Admin',
            'email' => 'admin@goodsmover.com',
            'cnic' => '12345-6789012-3',
            'role' => 'admin',
            'password' => Hash::make('obaid123'), // password ko encrypt kar rahe hain
        ]);
    }
}
