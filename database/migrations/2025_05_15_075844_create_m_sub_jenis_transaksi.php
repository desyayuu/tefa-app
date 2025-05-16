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
        Schema::create('m_sub_jenis_transaksi', function (Blueprint $table) {
            $table->string('sub_jenis_transaksi_id', 36)->primary(); 
            $table->string('jenis_transaksi_id', 36); //Masuk atau Keluar
            $table->string('jenis_keuangan_tefa_id', 36); //Proyek atau Non Proyek
            $table->string('nama_sub_jenis_transaksi');
            $table->string('deskripsi_sub_jenis_transaksi')->nullable();   

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('jenis_transaksi_id')->references('jenis_transaksi_id')->on('m_jenis_transaksi');
            $table->foreign('jenis_keuangan_tefa_id')->references('jenis_keuangan_tefa_id')->on('m_jenis_keuangan_tefa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_sub_jenis_transaksi');
    }
};
