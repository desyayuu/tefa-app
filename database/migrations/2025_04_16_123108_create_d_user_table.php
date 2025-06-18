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
        Schema::create('d_user', function (Blueprint $table) {
            $table->string('user_id', 36)->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Mahasiswa', 'Dosen', 'Koordinator', 'Profesional']);
            $table->enum('status', ['Pending', 'Active', 'Rejected', 'Disabled'])->default('Pending');
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
        Schema::dropIfExists('d_user');
    }
};
