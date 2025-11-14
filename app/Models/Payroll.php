<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class payroll extends Model
{
    use HasFactory;
     
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'total_present',
        'total_absent',
        'total_late',
        'daily_rate',
        'total_pay',
        'status',
        'issued_by',
        'issued_at',
        'payment_datetime',
        'department_name',
    ];

    protected $casts = [
        'issued_at' => 'datetime',        
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
