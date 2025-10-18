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
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id('training_participant_id');
            $table->unsignedBigInteger('training_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('present')
                ->default(0);
            $table->tinyInteger('logged_out')
                ->default(0);
            $table->tinyInteger('passed')
                ->default(0);
            $table->text('notices')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};
