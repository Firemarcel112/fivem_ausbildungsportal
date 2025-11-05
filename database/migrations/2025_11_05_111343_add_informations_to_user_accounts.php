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
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->enum('gender', ['M', 'W', 'D'])
                ->default('M')
                ->comment('Bestimmt die Anrede auf dem Zertifikat M = Herr / W = Frau / D = Ohne Anrede');
            $table->string('birth_location')
                ->nullable()
                ->comment('Bestimmt den Geburtsort auf dem Zertifikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('birth_location');
        });
    }
};
