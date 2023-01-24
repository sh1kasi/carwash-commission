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
            ['service' => 'Mobil Biasa', 'price' => '45000', 'type_commission' => 'persentase', 'commission_value' => '30', 'status' => '0'],
            ['service' => 'Mesin', 'price' => '25000', 'type_commission' => 'nominal', 'commission_value' => '7500', 'status' => '0'],
            ['service' => 'WAX', 'price' => '55000', 'type_commission' => 'nominal', 'commission_value' => '16500', 'status' => '1'],
            ['service' => 'WAX PRO', 'price' => '72000', 'type_commission' => 'nominal', 'commission_value' => '28000', 'status' => '1'],
        ];

        Product::insert($data);

    }
}
