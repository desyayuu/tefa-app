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
        Schema::create('m_dokumen_penunjang_proyek', function (Blueprint $table) {
            $table->string('dokumen_penunjang_proyek_id', 36)->primary;
            $table->string('proyek_id', 36);
            $table->string('jenis_dokumen_penunjang_id', 36);
            $table->string('nama_dokumen_penunjang');
            $table->string('file_dokumen_penunjang');

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek');
            $table->foreign('jenis_dokumen_penunjang_id')->references('jenis_dokumen_penunjang_id')->on('m_jenis_dokumen_penunjang');
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
        Schema::dropIfExists('m_dokumen_penunjang_proyek');
    }
};
