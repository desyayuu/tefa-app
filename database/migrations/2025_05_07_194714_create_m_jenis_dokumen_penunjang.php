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
        Schema::create('m_jenis_dokumen_penunjang', function (Blueprint $table) {
            $table->string('jenis_dokumen_penunjang_id', 36)->primary();
            $table->string('nama_jenis_dokumen_penunjang');
            $table->string('keterangan_jenis_dokumen_penunjang')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('created_by')->references('user_id')->on('d_user');
            $table->foreign('updated_by')->references('user_id')->on('d_user');
            $table->foreign('deleted_by')->references('user_id')->on('d_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_jenis_dokumen_penunjang');
    }
};
