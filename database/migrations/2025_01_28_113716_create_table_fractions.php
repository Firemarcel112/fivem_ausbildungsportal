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
        Schema::create('fractions', function (Blueprint $table) {
            $table->id('fraction_id');
            $table->string('name');
            $table->string('short_name', 10);
            $table->string('discord_webhook')
                ->nullable();
            $table->string('discord_webhook_completed')
                ->nullable();
            $table->tinyInteger('master')
                ->default(0)
                ->comment('Master = Fraktion ist Hauptfraktion für Abschluss');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fractions');
    }
};
