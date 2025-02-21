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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id');
            $table->unsignedInteger('number')->default(0);
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('country', 5);
            $table->string('city', 255)->nullable();
            $table->string('street', 255)->nullable();
            $table->decimal('latitude', 9, 7);
            $table->decimal('longitude', 10, 7);
            // number of minutes it takes to reach the point
            // (starting from the previous point)
            $table->unsignedInteger('travel_time')->nullable();
            $table->unsignedInteger('travel_time_cap')->nullable();
            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
