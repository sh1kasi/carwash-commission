<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employees';
    protected $guarded = [];
    protected $appends = ['rest_kasbon'];

    public function kasbons()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function getRestKasbonAttribute(){
        $kasbon = Employee_kasbon::where('employee_id', $this->id)->whereDate('tanggal_input', '>=' ,Carbon::now()->subDays(7))->sum('nominal');
        if($this->role == 'Tetap'){
            $sisa = $this->kasbon -  $kasbon;
        }else{
            $transaction = Transaction_employee::where('employee_id', $this->id)
                            ->whereHas('transactions', function($t){
                                $t->whereDate('created_at', '>=' ,Carbon::now()->subDays(7));
                            })->sum('commission');
            $sisa = $transaction - $kasbon;
        }

        return $sisa;
    }
}
