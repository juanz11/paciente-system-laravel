<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    protected $fillable = [
        'patient_id',
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes_familiares',
        'antecedentes_personales',
        'examen_neurologico',
        'examen_laboratorio',
        'examen_complementario',
        'diagnostico',
        'plan_tratamiento',
        'observacion',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
