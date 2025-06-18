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
        Schema::create('m_bidang_keahlian', function (Blueprint $table) {
            $table->string('bidang_keahlian_id', 36)->primary();
            $table->string('nama_bidang_keahlian');
            $table->text('deskripsi_bidang_keahlian')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();
        });

        Schema::table('d_mahasiswa', function (Blueprint $table) {
            $table->foreign('bidang_keahlian_id')->references('bidang_keahlian_id')->on('m_bidang_keahlian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_bidang_keahlian_');
    }
};
