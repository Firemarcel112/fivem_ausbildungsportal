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
		Schema::create('training_requests', function (Blueprint $table) {
			$table->id('training_request_id');
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->bigInteger('qualification_id')->unsigned();
			$table->date('date');
			$table->time('time');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('training_requests');
	}
};
