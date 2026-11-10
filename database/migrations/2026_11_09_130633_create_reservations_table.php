<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('borrower_name', 100);
            $table->string('class_institution', 100);
            $table->string('phone_number', 20);
            $table->unsignedSmallInteger('participant_count');
            $table->string('purpose', 100);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();

            $table->index(['court_id', 'reservation_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
