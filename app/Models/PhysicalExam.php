<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalExam extends Model
{
    protected $table = 'physical_exams';

    protected $fillable = [
        'patient_id',
        'user_id',
        'fecha',
        'antecedentes',
        'condiciones_generales',
        'pa',
        'pulso',
        'so2',
        'peso',
        'talla',
        'imc',
        'circunferencia_abdominal',
        'grasa_pct',
        'agua_pct',
        'masa_muscular',
        'tasa_fisica',
        'calorias',
        'edad_metabolica',
        'masa_osea',
        'grasa_visceral',
        'conclusion_balance',
        'examen_piel',
        'examen_pupilas',
        'examen_cuello',
        'examen_torax',
        'examen_pulmonar',
        'examen_abdomen',
        'examen_miembros',
        'examen_neurologico',
        'ecg',
        'rayos_x_torax',
        'espirometria',
        'ecosonograma',
        'hematologia',
        'serologia',
        'perfil_lipidico',
        'electrolitos',
        'orina',
        'riesgo_cardiovascular',
        'diagnostico_global',
        'dieta_habitos',
        'plan_ejercicios',
        'tratamiento_farmacologico',
        'proximos_estudios',
    ];

    protected $casts = [
        'fecha' => 'date',
        'antecedentes' => 'array',
        'examen_piel' => 'array',
        'examen_pupilas' => 'array',
        'examen_cuello' => 'array',
        'examen_torax' => 'array',
        'examen_pulmonar' => 'array',
        'examen_abdomen' => 'array',
        'examen_miembros' => 'array',
        'examen_neurologico' => 'array',
        'ecg' => 'array',
        'espirometria' => 'array',
        'hematologia' => 'array',
        'serologia' => 'array',
        'perfil_lipidico' => 'array',
        'electrolitos' => 'array',
        'orina' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
