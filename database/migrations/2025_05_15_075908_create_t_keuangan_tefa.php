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
        Schema::create('t_keuangan_tefa', function (Blueprint $table) {
            $table->string('keuangan_tefa_id', 36)->primary();
            $table->string('jenis_transaksi_id', 36);
            $table->string('jenis_keuangan_tefa_id', 36);
            $table->string('proyek_id', 36)->nullable();
            $table->string('sub_jenis_transaksi_id', 36)->nullable();

            $table->string('nama_transaksi');
            $table->dateTime('tanggal_transaksi');
            $table->decimal('nominal_transaksi', 15, 2);
            $table->string('deskripsi_transaksi')->nullable();
            $table->string('bukti_transaksi')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek');
            $table->foreign('jenis_transaksi_id')->references('jenis_transaksi_id')->on('m_jenis_transaksi');
            $table->foreign('jenis_keuangan_tefa_id')->references('jenis_keuangan_tefa_id')->on('m_jenis_keuangan_tefa');
            $table->foreign('sub_jenis_transaksi_id')->references('sub_jenis_transaksi_id')->on('m_sub_jenis_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_keuangan_tefa');
    }
};
