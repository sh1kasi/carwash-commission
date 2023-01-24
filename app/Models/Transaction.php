<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = "transactions";
    protected $guarded = [];
    public $timestamps = true;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps()->withTrashed();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('status', 'commission', 'product_id')->withTimestamps()->withTrashed();
    }

    public function employee_transaction()
    {
        return $this->hasMany(Transaction_employee::class);
    }
}
