<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('d_koordinator', function (Blueprint $table) {
            $table->string('koordinator_id', 36)->primary();
            $table->string('user_id', 36)->unique();
            $table->string('nama_koordinator');
            $table->enum('jenis_kelamin_koordinator', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir_koordinator')->nullable();
            $table->string('telepon_koordinator')->nullable();
            $table->string('profile_img_koordinator')->nullable();
            $table->string('nidn_koordinator')->nullable();
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
        Schema::dropIfExists('d_koordinator');
    }
};
