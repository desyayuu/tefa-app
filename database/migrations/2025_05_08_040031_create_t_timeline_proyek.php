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
        Schema::create('t_timeline_proyek', function (Blueprint $table) {
            $table->string('timeline_proyek_id', 36)->primary();
            $table->string('proyek_id', 36);
            $table->string('nama_timeline_proyek');
            $table->dateTime('tanggal_mulai_timeline');
            $table->dateTime('tanggal_selesai_timeline');
            $table->string('deskripsi_timeline')->nullable();
            
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek');           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_timeline_proyek');
    }
};
