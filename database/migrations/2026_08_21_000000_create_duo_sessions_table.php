<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duo_sessions', function (Blueprint $table) {
            $table->string('code', 6)->primary();
            $table->string('name_a')->nullable();
            $table->string('name_b')->nullable();
            $table->json('answers')->nullable();
            $table->json('game')->nullable();
            $table->json('questions')->nullable();
            $table->json('ttt')->nullable();
            $table->unsignedInteger('idx')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duo_sessions');
    }
};
