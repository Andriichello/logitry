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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id');
            $table->boolean('reversed')->default(false);
            $table->foreignId('vehicle_id')->nullable();
            $table->foreignId('driver_id')->nullable();
            $table->foreignId('contact_id')->nullable();
            $table->string('status', 25);
            $table->json('metadata')->nullable();
            $table->dateTime('departs_at');
            $table->dateTime('arrives_at')->nullable();
            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('cascade');

            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
