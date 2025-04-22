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
            $table->id('proyek_id');
            $table->string('nama_proyek'); 
            $table->string('deskripsi_proyek'); 
            $table->string('dana_pendanaan'); 
            $table->date('tanggal_mulai'); 
            $table->date('tanggal_akhir');
            $table->datetime('created_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
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
