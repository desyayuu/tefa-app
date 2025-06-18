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
        Schema::create('d_portofolio', function (Blueprint $table) {
            $table->string('portofolio_id', 36)->primary();
            $table->string('mahasiswa_id', 36);
            $table->string('nama_kegiatan');
            $table->enum('jenis_kegiatan', ['Magang', 'Pelatihan', 'Lomba', 'Penelitian', 'Pengabdian', 'Lainnya']);
            $table->string('deskripsi_kegiatan')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->enum('tingkat_kegiatan', ['Internasional', 'Nasional', 'Regional', 'Lainnya']);
            $table->string('link_kegiatan')->nullable();
            $table->string('peran_dalam_kegiatan')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_portofolio');
    }
};
