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
        Schema::create('d_dokumentasi_proyek', function (Blueprint $table) {
            $table->string('dokumentasi_proyek_id', 36)->primary();
            $table->string('luaran_proyek_id', 36);
            $table->string('proyek_id', 36);
            $table->string('nama_file');
            $table->string('path_file');

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_dokumentasi_proyek');
    }
};
