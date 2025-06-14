<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('guest_credits', function (Blueprint $table) {
            $table->id();
            $table->string('device_signature')->unique();
            $table->integer('credits')->default(10);
            $table->date('last_reset')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('guest_credits');
    }
};
