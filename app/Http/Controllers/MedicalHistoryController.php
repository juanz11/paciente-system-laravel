<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory;

class MedicalHistoryController extends Controller
{
    public function index($patientId)
    {
        $histories = MedicalHistory::where('patient_id', $patientId)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        return response()->json($histories, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'motivo_consulta' => 'nullable|string',
            'enfermedad_actual' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'antecedentes_personales' => 'nullable|string',
            'examen_neurologico' => 'nullable|string',
            'examen_laboratorio' => 'nullable|string',
            'examen_complementario' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'plan_tratamiento' => 'nullable|string',
            'observacion' => 'nullable|string',
        ]);

        $history = MedicalHistory::create($validated);
        return response()->json($history, 201);
    }

    public function show($id)
    {
        $history = MedicalHistory::with('patient')->findOrFail($id);
        return response()->json($history, 200);
    }

    public function update(Request $request, $id)
    {
        $history = MedicalHistory::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'motivo_consulta' => 'nullable|string',
            'enfermedad_actual' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'antecedentes_personales' => 'nullable|string',
            'examen_neurologico' => 'nullable|string',
            'examen_laboratorio' => 'nullable|string',
            'examen_complementario' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'plan_tratamiento' => 'nullable|string',
            'observacion' => 'nullable|string',
        ]);

        $history->update($validated);
        return response()->json($history, 200);
    }

    public function destroy($id)
    {
        $history = MedicalHistory::findOrFail($id);
        $history->delete();
        return response()->json(['message' => 'Medical history deleted'], 200);
    }
}
