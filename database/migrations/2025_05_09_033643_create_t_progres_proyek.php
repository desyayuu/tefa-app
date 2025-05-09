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
        Schema::create('t_progres_proyek', function (Blueprint $table) {
            $table->string('progres_proyek_id', 36)->primary();
            $table->string('proyek_id');
            $table->string('project_leader_id', 36)->nullable();
            $table->string('project_member_dosen_id', 36)->nullable();
            $table->string('project_member_profesional_id', 36)->nullable();
            $table->string('project_member_mahasiswa_id', 36)->nullable();
            $table->string('assigned_to', 36)->nullable();
            $table->string('nama_progres');
            $table->text('deskripsi_progres')->nullable();
            $table->enum('status_progres', ['Inisiasi', 'In Progress', 'Done']);
            $table->integer('persentase_progres');

            $table->dateTime('created_at')->nullable();
            $table->string('created_by', 36)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('proyek_id')->references('proyek_id')->on('m_proyek');
            $table->foreign('project_leader_id')->references('project_leader_id')->on('t_project_leader');
            $table->foreign('project_member_dosen_id')->references('project_member_dosen_id')->on('t_project_member_dosen');
            $table->foreign('project_member_profesional_id')->references('project_member_profesional_id')->on('t_project_member_profesional');
            $table->foreign('project_member_mahasiswa_id')->references('project_member_mahasiswa_id')->on('t_project_member_mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_progres_proyek');
    }
};