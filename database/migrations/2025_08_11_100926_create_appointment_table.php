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
            $table->String('name');
            $table->date('date');
            $table->time('time');
            $table->String('owner');
            $table->string('email');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('staff_id');
            $table->timestamps();
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
		    $table->boolean('handled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};
