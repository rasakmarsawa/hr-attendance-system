<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->integer('total_present')->default(0);
            $table->integer('total_absent')->default(0);
            $table->integer('total_late')->default(0);
            $table->decimal('daily_rate', 12, 2);
            $table->decimal('total_pay', 12, 2);
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->string('issued_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'month', 'year']); // ensure one payroll per employee per month
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
