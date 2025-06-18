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
        Schema::create('d_mahasiswa', function (Blueprint $table) {
            $table->string('mahasiswa_id', 36)->primary();
            $table->string('user_id', 36)->unique();
            $table->string('bidang_keahlian_id', 36)->nullable();
            $table->string('bahasa_pemrograman_id', 36)->nullable();
            $table->string('tool_id', 36)->nullable();
            $table->string('nama_mahasiswa');
            $table->enum('jenis_kelamin_mahasiswa', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir_mahasiswa')->nullable();
            $table->string('telepon_mahasiswa')->nullable();
            $table->string('profile_img_mahasiswa')->nullable();
            $table->string('nim_mahasiswa')->unique();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('doc_cv')->nullable();
            $table->string('doc_ktm')->nullable();
            $table->string('doc_ktp')->nullable();
            $table->text('deskripsi_diri')->nullable();
            $table->text('kelebihan_diri')->nullable();
            $table->text('kekurangan_diri')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();
            
            $table->foreign('user_id')->references('user_id')->on('d_user');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_mahasiswa');
    }
};
