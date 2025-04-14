<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('d_user', function (Blueprint $table) {
            $table->string('user_id', 36)->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('telepon', 15)->nullable();
            $table->integer('nip')->nullable();
            $table->integer('nim')->nullable();
            $table->unsignedBigInteger('bidang_keahlian_id')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('doc_cv')->nullable();
            $table->string('doc_ktm')->nullable();
            $table->string('doc_ktp')->nullable();
            $table->string('profile_img')->nullable();
            $table->datetime('created_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
            
            $table->foreign('bidang_keahlian_id')->references('bidang_keahlian_id')->on('m_bidang_keahlian');
        });
    }

    public function down()
    {
        Schema::dropIfExists('d_user');
    }
};