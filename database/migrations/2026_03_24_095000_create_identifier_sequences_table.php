<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identifier_sequences', function (Blueprint $table) {
            $table->string('name', 32)->primary();
            $table->unsignedBigInteger('next_value')->default(1);
        });

        DB::table('identifier_sequences')->insert([
            ['name' => 'teacher', 'next_value' => 1],
            ['name' => 'student', 'next_value' => 1],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('identifier_sequences');
    }
};
