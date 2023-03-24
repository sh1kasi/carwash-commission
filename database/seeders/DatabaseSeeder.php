<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ProductsTableSeeder;
use Database\Seeders\EmployeesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(ProductsTableSeeder::class);
        // $this->call(EmployeesTableSeeder::class);

        $data = [
            ['name' => 'admin', 'email' => 'admin00@gmail.com', 'password' => Hash::make('admin12345')]
        ];

        User::insert($data);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
