<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};