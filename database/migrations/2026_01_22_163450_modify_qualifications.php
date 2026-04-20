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
        Schema::table('qualifications', function (Blueprint $table) {
            $table->unsignedTinyInteger('hide')
                ->default(0)
                ->after('rank')
                ->comment('Qualifikation wird in der Ausbidlungsliste angezeigt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropColumn('hide');
        });
    }
};
