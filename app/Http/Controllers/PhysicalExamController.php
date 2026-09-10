<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhysicalExam;

class PhysicalExamController extends Controller
{
    public function index($patientId)
    {
        $exams = PhysicalExam::where('patient_id', $patientId)
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($exams, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fecha' => 'required|date',
            'antecedentes' => 'nullable|array',
            'condiciones_generales' => 'nullable|string|max:50',
            'pa' => 'nullable|string|max:50',
            'pulso' => 'nullable|string|max:50',
            'so2' => 'nullable|string|max:50',
            'peso' => 'nullable|numeric|min:0.5|max:500',
            'talla' => 'nullable|numeric|min:10|max:250',
            'imc' => 'nullable|numeric|min:0|max:100',
            'circunferencia_abdominal' => 'nullable|numeric',
            'grasa_pct' => 'nullable|numeric',
            'agua_pct' => 'nullable|numeric',
            'masa_muscular' => 'nullable|numeric',
            'tasa_fisica' => 'nullable|integer',
            'calorias' => 'nullable|numeric',
            'edad_metabolica' => 'nullable|integer',
            'masa_osea' => 'nullable|numeric',
            'grasa_visceral' => 'nullable|integer',
            'conclusion_balance' => 'nullable|string',
            'examen_piel' => 'nullable|array',
            'examen_pupilas' => 'nullable|array',
            'examen_cuello' => 'nullable|array',
            'examen_torax' => 'nullable|array',
            'examen_pulmonar' => 'nullable|array',
            'examen_abdomen' => 'nullable|array',
            'examen_miembros' => 'nullable|array',
            'examen_neurologico' => 'nullable|array',
            'ecg' => 'nullable|array',
            'rayos_x_torax' => 'nullable|string',
            'espirometria' => 'nullable|array',
            'ecosonograma' => 'nullable|string',
            'hematologia' => 'nullable|array',
            'serologia' => 'nullable|array',
            'perfil_lipidico' => 'nullable|array',
            'electrolitos' => 'nullable|array',
            'orina' => 'nullable|array',
            'riesgo_cardiovascular' => 'nullable|string|max:255',
            'diagnostico_global' => 'nullable|string',
            'dieta_habitos' => 'nullable|string',
            'plan_ejercicios' => 'nullable|string',
            'tratamiento_farmacologico' => 'nullable|string',
            'proximos_estudios' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $exam = PhysicalExam::create($validated);
        return response()->json($exam, 201);
    }

    public function show($id)
    {
        $exam = PhysicalExam::with('patient', 'user')->findOrFail($id);
        return response()->json($exam, 200);
    }

    public function update(Request $request, $id)
    {
        $exam = PhysicalExam::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fecha' => 'required|date',
            'antecedentes' => 'nullable|array',
            'condiciones_generales' => 'nullable|string|max:50',
            'pa' => 'nullable|string|max:50',
            'pulso' => 'nullable|string|max:50',
            'so2' => 'nullable|string|max:50',
            'peso' => 'nullable|numeric|min:0.5|max:500',
            'talla' => 'nullable|numeric|min:10|max:250',
            'imc' => 'nullable|numeric|min:0|max:100',
            'circunferencia_abdominal' => 'nullable|numeric',
            'grasa_pct' => 'nullable|numeric',
            'agua_pct' => 'nullable|numeric',
            'masa_muscular' => 'nullable|numeric',
            'tasa_fisica' => 'nullable|integer',
            'calorias' => 'nullable|numeric',
            'edad_metabolica' => 'nullable|integer',
            'masa_osea' => 'nullable|numeric',
            'grasa_visceral' => 'nullable|integer',
            'conclusion_balance' => 'nullable|string',
            'examen_piel' => 'nullable|array',
            'examen_pupilas' => 'nullable|array',
            'examen_cuello' => 'nullable|array',
            'examen_torax' => 'nullable|array',
            'examen_pulmonar' => 'nullable|array',
            'examen_abdomen' => 'nullable|array',
            'examen_miembros' => 'nullable|array',
            'examen_neurologico' => 'nullable|array',
            'ecg' => 'nullable|array',
            'rayos_x_torax' => 'nullable|string',
            'espirometria' => 'nullable|array',
            'ecosonograma' => 'nullable|string',
            'hematologia' => 'nullable|array',
            'serologia' => 'nullable|array',
            'perfil_lipidico' => 'nullable|array',
            'electrolitos' => 'nullable|array',
            'orina' => 'nullable|array',
            'riesgo_cardiovascular' => 'nullable|string|max:255',
            'diagnostico_global' => 'nullable|string',
            'dieta_habitos' => 'nullable|string',
            'plan_ejercicios' => 'nullable|string',
            'tratamiento_farmacologico' => 'nullable|string',
            'proximos_estudios' => 'nullable|string',
        ]);

        $exam->update($validated);
        return response()->json($exam, 200);
    }

    public function destroy($id)
    {
        $exam = PhysicalExam::findOrFail($id);
        $exam->delete();
        return response()->json(['message' => 'Examen eliminado'], 200);
    }
}
