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
            ['name' => 'Argya'],
            ['name' => 'Hendra'],
            ['name' => 'Deni'],
            ['name' => 'Taufik'],
            ['name' => 'Aris'],
            ['name' => 'Miftah'],
            ['name' => 'Nurdin'],
            ['name' => 'Indrawan'],
            ['name' => 'Andres'],
            ['name' => 'Arif'],
            ['name' => 'Rohman']
        ];

        Employee::insert($data);
    }
}
