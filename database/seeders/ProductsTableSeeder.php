<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['service' => 'Mobil Biasa', 'price' => '45000', 'status' => '0'],
            ['service' => 'Mobil Besar', 'price' => '50000', 'status' => '0'],
            ['service' => 'Motor', 'price' => '15000', 'status' => '0'], 
            ['service' => 'Motor Besar', 'price' => '20000', 'status' => '0'],
            ['service' => 'Mesin', 'price' => '25000', 'status' => '0'],
            ['service' => 'CUCI WAX', 'price' => '100000', 'status' => '1'],
            ['service' => 'Poles Body Kecil', 'price' => '100000', 'status' => '1'],
            ['service' => 'Poles Body Medium', 'price' => '125000', 'status' => '1'],
            ['service' => 'Poles Body Besar', 'price' => '150000', 'status' => '1'],
            ['service' => 'Poles Kaca', 'price' => '50000', 'status' => '1']
        ];

        Product::insert($data);

    }
}
