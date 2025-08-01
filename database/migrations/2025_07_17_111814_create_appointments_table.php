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
      Schema::create('appointments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained()->onDelete('cascade');
    $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
    $table->date('appointment_date');
    $table->time('appointment_time');
    $table->enum('status', ['Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No-Show'])->default('Scheduled');
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
