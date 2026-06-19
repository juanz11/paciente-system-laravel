<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'lugar_nacimiento',
        'nacionalidad',
        'cedula_identidad',
        'edad',
        'telefono',
        'ciudad',
        'estado',
        'direccion',
        'emergencia_nombre',
        'emergencia_parentesco',
        'emergencia_telefono',
        'emergencia_direccion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class);
    }
}
