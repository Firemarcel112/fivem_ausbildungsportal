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
        Schema::create('user_fractions', function (Blueprint $table) {
            $table->id('user_fraction_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fraction_id');
            $table->tinyInteger('default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_fractions');
    }
};
