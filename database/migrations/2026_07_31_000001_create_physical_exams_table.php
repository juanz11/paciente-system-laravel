<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('fecha');

            // Antecedentes de Importancia (JSON con checkboxes)
            $table->json('antecedentes')->nullable();

            // Examen Físico - Condiciones Generales
            $table->string('condiciones_generales')->nullable(); // Buenas / Regular / Malas
            $table->string('pa')->nullable(); // Presión Arterial
            $table->string('pulso')->nullable(); // ppm
            $table->string('so2')->nullable(); // %

            // Balance Corporal
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();
            $table->decimal('circunferencia_abdominal', 5, 2)->nullable();
            $table->decimal('grasa_pct', 5, 2)->nullable();
            $table->decimal('agua_pct', 5, 2)->nullable();
            $table->decimal('masa_muscular', 5, 2)->nullable();
            $table->integer('tasa_fisica')->nullable();
            $table->decimal('calorias', 6, 2)->nullable();
            $table->integer('edad_metabolica')->nullable();
            $table->decimal('masa_osea', 5, 2)->nullable();
            $table->integer('grasa_visceral')->nullable();
            $table->text('conclusion_balance')->nullable();

            // Examen Físico por Sistemas (JSON)
            $table->json('examen_piel')->nullable();
            $table->json('examen_pupilas')->nullable();
            $table->json('examen_cuello')->nullable();
            $table->json('examen_torax')->nullable();
            $table->json('examen_pulmonar')->nullable();
            $table->json('examen_abdomen')->nullable();
            $table->json('examen_miembros')->nullable();
            $table->json('examen_neurologico')->nullable();

            // Estudios Complementarios
            $table->json('ecg')->nullable();
            $table->text('rayos_x_torax')->nullable();
            $table->json('espirometria')->nullable();
            $table->text('ecosonograma')->nullable();

            // Laboratorio Clínico
            $table->json('hematologia')->nullable();
            $table->json('serologia')->nullable();
            $table->json('perfil_lipidico')->nullable();
            $table->json('electrolitos')->nullable();
            $table->json('orina')->nullable();

            // Evaluación de Riesgo
            $table->string('riesgo_cardiovascular')->nullable();
            $table->text('diagnostico_global')->nullable();

            // Indicaciones y Sugerencias
            $table->text('dieta_habitos')->nullable();
            $table->text('plan_ejercicios')->nullable();
            $table->text('tratamiento_farmacologico')->nullable();
            $table->text('proximos_estudios')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_exams');
    }
};
