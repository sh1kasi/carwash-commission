<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, withHeadingRow{

    public function model(array $row)
    {
        return new Customer([
            "nopol" => $row['nopol']
        ]);
    }
}
