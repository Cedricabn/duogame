<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('duo_sessions', function (Blueprint $table) {
            $table->json('chat')->nullable()->after('ttt');
        });
    }

    public function down(): void
    {
        Schema::table('duo_sessions', function (Blueprint $table) {
            $table->dropColumn('chat');
        });
    }
};
