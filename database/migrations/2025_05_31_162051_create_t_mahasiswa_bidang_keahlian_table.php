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
        Schema::create('t_mahasiswa_bidang_keahlian', function (Blueprint $table) {
            $table->string('mahasiswa_bidang_keahlian_id', 36)->primary();
            $table->string('mahasiswa_id', 36);
            $table->string('bidang_keahlian_id', 36);
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            // Foreign key constraints
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('d_mahasiswa');
            $table->foreign('bidang_keahlian_id')->references('bidang_keahlian_id')->on('m_bidang_keahlian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_mahasiswa_bidang_keahlian');
    }
};
