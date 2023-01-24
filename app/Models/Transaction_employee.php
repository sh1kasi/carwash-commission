<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction_employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_transaction';
    protected $guarded = [];
    public $timestamps = true;

    public function employee_products()
    {
        return $this->belongsTo(Product::class, 'product_id','id')->withTrashed();
    }
    
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id','id')->withTrashed();
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

}
