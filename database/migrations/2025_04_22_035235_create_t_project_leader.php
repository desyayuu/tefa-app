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
        Schema::create('t_project_leader', function (Blueprint $table) {
            $table->string('project_leader_id', 36)->primary();
            $table->string('proyek_id', 36);
            $table->enum('leader_type', ['Dosen', 'Profesional']);
            $table->string('leader_id', 36);
            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek');
            
            // Add a unique constraint to prevent duplicate leaders
            $table->unique(['proyek_id', 'leader_type', 'leader_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_project_leader');
    }
};