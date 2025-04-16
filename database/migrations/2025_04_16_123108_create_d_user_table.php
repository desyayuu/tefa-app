<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('d_user', function (Blueprint $table) {
            $table->string('user_id', 36)->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Mahasiswa', 'Dosen', 'Koordinator']);
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_user');
    }
};
