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
        Schema::table('documents_link', function (Blueprint $table) {
            $table->enum('link_type', ['QUALIFICATION', 'OTHER', 'ACCOUNT'])
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents_link', function (Blueprint $table) {
            $table->enum('link_type', ['QUALIFICATION', 'OTHER'])
                ->change();
        });
    }
};
