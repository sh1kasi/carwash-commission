<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee_kasbon extends Model
{
    use HasFactory;

    protected $table = 'employee_kasbon';
    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
