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
        Schema::create('m_pesan', function (Blueprint $table) {
            $table->string('pesan_id', 36)->primary();
            $table->string('nama_pengirim', 36);
            $table->string('perusahaan_pengirim');
            $table->string('email_pengirim');
            $table->string('telepon_pengirim', 15)->nullable();
            $table->text('pesan_pengirim');

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
        Schema::dropIfExists('m_pesan');
    }
};
