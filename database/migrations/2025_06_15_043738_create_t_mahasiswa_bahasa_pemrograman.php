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
        Schema::create('t_mahasiswa_bahasa_pemrograman', function (Blueprint $table) {
            $table->string('mahasiswa_bahasa_pemrograman_id', 36)->primary();
            $table->string('mahasiswa_id', 36);
            $table->string('bahasa_pemrograman_id', 36);
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            // Foreign key constraints
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('d_mahasiswa');
            $table->foreign('bahasa_pemrograman_id')->references('bahasa_pemrograman_id')->on('m_bahasa_pemrograman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_mahasiswa_bahasa_pemrograman');
    }
};
