<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('r_role', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('nama_role');
            $table->datetime('created_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('r_role');
    }
};