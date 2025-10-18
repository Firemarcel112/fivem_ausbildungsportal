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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id('training_id');
            $table->unsignedBigInteger('trainer_id');
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('fraktion_id')->nullable();
            $table->string('meeting_point')->nullable();
            $table->text('additional_information')->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('max_participants')->default(10);
            $table->integer('min_participants')->default(2);
            $table->tinyInteger('completed')->default(0);
            $table->tinyInteger('canceled')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
