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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->text('motivo_consulta')->nullable();
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('examen_neurologico')->nullable();
            $table->text('examen_laboratorio')->nullable();
            $table->text('examen_complementario')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
