<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('patient_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->string('action'); // created, updated, deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('patient_audits');
    }
};

