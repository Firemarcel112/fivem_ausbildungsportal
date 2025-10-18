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
		Schema::create('discord_accounts', function (Blueprint $table) {
			$table->increments('discord_account_id');
			$table->bigInteger('user_id')->unsigned()->index();
			$table->string('discord_id', 255)->unique();
			$table->string('username', 255)->nullable();
			$table->string('avatar', 255)->nullable();
			$table->string('token', 255)->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('discord_accounts');
	}
};
