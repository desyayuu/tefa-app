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
        Schema::create('d_profesional', function (Blueprint $table) {
            $table->string('profesional_id', 36)->primary();
            $table->string('user_id', 36)->unique();
            $table->string('nama_profesional');
            $table->enum('jenis_kelamin_profesional', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir_profesional')->nullable();
            $table->string('telepon_profesional')->nullable();
            $table->string('profile_img_profesional')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();
            
            $table->foreign('user_id')->references('user_id')->on('d_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_profesional');
    }
};
