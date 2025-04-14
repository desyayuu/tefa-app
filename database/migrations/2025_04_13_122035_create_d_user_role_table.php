<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('d_user_role', function (Blueprint $table) {
            $table->id('user_role_id');
            $table->string('user_id', 36);
            $table->unsignedBigInteger('role_id');
            
            $table->foreign('user_id')->references('user_id')->on('d_user');
            $table->foreign('role_id')->references('role_id')->on('r_role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('d_user_role');
    }
};