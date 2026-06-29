<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index($patientId)
    {
        $recipes = Recipe::where('patient_id', $patientId)
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($recipes, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fecha' => 'required|date',
            'indicaciones' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $recipe = Recipe::create($validated);
        return response()->json($recipe, 201);
    }

    public function show($id)
    {
        $recipe = Recipe::with('patient', 'user')->findOrFail($id);
        return response()->json($recipe, 200);
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fecha' => 'required|date',
            'indicaciones' => 'required|string',
        ]);

        $recipe->update($validated);
        return response()->json($recipe, 200);
    }

    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted'], 200);
    }
}
