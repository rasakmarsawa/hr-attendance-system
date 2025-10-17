<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payroll extends Model
{
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
    ];

    protected $casts = [
        'issued_at' => 'datetime',        
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
