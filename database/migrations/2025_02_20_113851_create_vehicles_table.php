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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->string('manufacturer');
            $table->string('model');
            $table->year('year');
            $table->string('color', 50);
            $table->string('nickname', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('fuel', 50)->nullable();
            $table->integer('seats')->nullable();
            $table->decimal('cargo_width')->nullable();
            $table->decimal('cargo_length')->nullable();
            $table->decimal('cargo_height')->nullable();
            $table->decimal('cargo_volume', 10, 3)->nullable();
            $table->integer('cargo_capacity')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'nickname']);

            $table->index(['company', 'manufacturer', 'model', 'year', 'nickname']);

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
