<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        $search = request()->query('search');
        $patients = Patient::when($search, function ($query, $search) {
            return $query->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->get();
        return response()->json($patients, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'lugar_nacimiento' => 'nullable|string',
            'nacionalidad' => 'nullable|string',
            'cedula_identidad' => 'nullable|string|unique:patients',
            'edad' => 'nullable|integer',
            'telefono' => 'nullable|string',
            'ciudad' => 'nullable|string',
            'estado' => 'nullable|string',
            'direccion' => 'nullable|string',
            'emergencia_nombre' => 'nullable|string',
            'emergencia_parentesco' => 'nullable|string',
            'emergencia_telefono' => 'nullable|string',
            'emergencia_direccion' => 'nullable|string',
        ]);

        // Convert null values to empty strings for text fields
        foreach ($validated as $key => $value) {
            if ($value === null && !in_array($key, ['edad', 'fecha_nacimiento'])) {
                $validated[$key] = '';
            }
        }

        $patient = Patient::create($validated);
        return response()->json($patient, 201);
    }

    public function show($id)
    {
        $patient = Patient::with('medicalHistories')->findOrFail($id);
        return response()->json($patient, 200);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $validated = $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'lugar_nacimiento' => 'nullable|string',
            'nacionalidad' => 'nullable|string',
            'cedula_identidad' => 'nullable|string|unique:patients,cedula_identidad,'.$id,
            'edad' => 'nullable|integer',
            'telefono' => 'nullable|string',
            'ciudad' => 'nullable|string',
            'estado' => 'nullable|string',
            'direccion' => 'nullable|string',
            'emergencia_nombre' => 'nullable|string',
            'emergencia_parentesco' => 'nullable|string',
            'emergencia_telefono' => 'nullable|string',
            'emergencia_direccion' => 'nullable|string',
        ]);

        // Convert null values to empty strings for text fields
        foreach ($validated as $key => $value) {
            if ($value === null && !in_array($key, ['edad', 'fecha_nacimiento'])) {
                $validated[$key] = '';
            }
        }

        $patient->update($validated);
        return response()->json($patient, 200);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return response()->json(['message' => 'Patient deleted'], 200);
    }
}
