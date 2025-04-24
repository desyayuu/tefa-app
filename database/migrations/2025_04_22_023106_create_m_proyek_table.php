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
        Schema::create('m_proyek', function (Blueprint $table) {
            $table->string('proyek_id', 36)->primary();
            $table->string('mitra_proyek_id', 36);
            $table->string('jenis_proyek_id', 36);
            $table->string('nama_proyek');
            $table->string('deskripsi_proyek');
            $table->enum('status_proyek', ['Initiation', 'In Progress', 'Done']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('dana_pendanaan');
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('mitra_proyek_id')->references('mitra_proyek_id')->on('d_mitra_proyek');
            $table->foreign('jenis_proyek_id')->references('jenis_proyek_id')->on('m_jenis_proyek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_proyek');
    }
};
