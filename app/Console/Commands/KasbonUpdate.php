<?php

namespace App\Console\Commands;

use App\Models\Kasbon;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class KasbonUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kasbon:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee kasbon reset';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employee_tetap = Employee::where('role', 'Tetap')->get();
        $now = Carbon::now()->timezone('Asia/Jakarta');
        $currentDate = Carbon::parse($now)->format('Y-m-d');
        foreach ($employee_tetap as $data) {
            $employee = Kasbon::where('employee_id', $data->id)->first();
             $resetDate = Carbon::parse($employee->reset_date)->format('Y-m-d');
             $month = Carbon::parse($resetDate)->diffInMonths($currentDate);
             if ($month > 0) {
                $employee->reset_date = $now->setTimeFromTimeString($now->toTimeString());
                $employee->sisa_nominal = $data->kasbon;
                $employee->kasbon_input = null;
                $employee->save();
             }
        }
    }
}
