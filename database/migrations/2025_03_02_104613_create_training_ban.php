<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('training_bans', function (Blueprint $table) {
            $table->id('training_ban_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date_from');
            $table->date('date_to');
            $table->text('reason');
            $table->unsignedBigInteger('issuer_id');
            $table->text('internal_note')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_bans');
    }
};
