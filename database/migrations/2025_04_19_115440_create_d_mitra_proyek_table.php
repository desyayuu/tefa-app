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
        Schema::create('d_mitra_proyek', function (Blueprint $table) {
            $table->string('mitra_proyek_id', 36)->primary();
            $table->string('nama_mitra');
            $table->string('email_mitra');
            $table->string('telepon_mitra')->nullable();
            $table->string('alamat_mitra')->nullable();
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
        Schema::dropIfExists('d_mitra_proyek');
    }
};
