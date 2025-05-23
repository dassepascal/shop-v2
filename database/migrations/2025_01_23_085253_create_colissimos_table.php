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
        Schema::create('colissimos', function (Blueprint $table) {
            $table->id();
            $table->decimal('price');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('range_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models_colissimos');
    }
};
