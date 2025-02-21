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
        Schema::create('route_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id');
            $table->foreignId('beg_point_id')->nullable();
            $table->foreignId('end_point_id')->nullable();
            $table->string('name', 50)->nullable();
            $table->string('description')->nullable();
            $table->string('unit', 25);
            $table->decimal('from', 10);
            $table->decimal('to', 10)->nullable();
            $table->string('currency', 5);
            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');

            $table->foreign('beg_point_id')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');

            $table->foreign('end_point_id')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_prices');
    }
};
