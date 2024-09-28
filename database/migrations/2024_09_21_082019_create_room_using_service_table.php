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
        Schema::create('room_using_service', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            // $table->uuid('uuid')->primary();
            // $table->unsignedBigInteger('room_using_id');
            // $table->unsignedBigInteger('service_id');
            // $table->date('service_using_date');
            // $table->timestamps();
            // $table->unsignedBigInteger('created_by')->nullable();
            // $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_using_service');
    }
};
