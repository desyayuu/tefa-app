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
            $table->string('nama');
            $table->string('email');
            $table->string('password');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->date('tanggal_lahir');
            $table->string('telepon');
            $table->string('profile_img')->nullable();
            $table->string('nidn');
            $table->dateTime('created_at');
            $table->integer('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
            
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
