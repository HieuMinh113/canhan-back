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
        Schema::create('booking_hotels', function (Blueprint $table) {
            $table->id();
            $table ->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('check_in');
            $table->date('check_out');
            $table->time('check_in_time');
            $table->time('check_out_time');
            $table->string('email')->nullable();
            $table ->integer('phone');
            $table->decimal('total_price',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_hotels');
    }
};
