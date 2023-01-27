<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Argya', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Hendra', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Deni', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Taufik', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Aris', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Miftah', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Nurdin', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Indrawan', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Andreas', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Arif', 'role' => 'Tetap', 'kasbon' => '500000'],
            ['name' => 'Rohman', 'role' => 'Tetap', 'kasbon' => '500000']
        ];

        Employee::insert($data);
    }
}
