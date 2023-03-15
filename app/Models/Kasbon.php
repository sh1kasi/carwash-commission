<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }

    public function worker()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
        
    }



}
