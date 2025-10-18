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
        Schema::create('user_qualifications', function (Blueprint $table) {
            $table->id('user_qualification_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('training_id')->nullable();
            $table->dateTime('date_obtained')
                ->nullable()
                ->comment('Erforderlich wenn keine Ausbildung vorliegt');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_qualifications');
    }
};
