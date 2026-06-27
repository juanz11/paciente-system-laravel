<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecipeFormatController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'doctor_nombre'       => $user->doctor_nombre,
            'doctor_especialidad' => $user->doctor_especialidad,
            'doctor_codigo_mmps'  => $user->doctor_codigo_mmps,
            'doctor_codigo_cm'    => $user->doctor_codigo_cm,
            'doctor_ci'           => $user->doctor_ci,
            'doctor_logo'         => $user->doctor_logo ? asset('storage/' . $user->doctor_logo) : null,
            'doctor_fondo_agua'   => $user->doctor_fondo_agua ? asset('storage/' . $user->doctor_fondo_agua) : null,
            'doctor_direccion'    => $user->doctor_direccion,
            'doctor_telefono'     => $user->doctor_telefono,
        ], 200);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'doctor_nombre'       => 'nullable|string|max:255',
            'doctor_especialidad' => 'nullable|string|max:255',
            'doctor_codigo_mmps'  => 'nullable|string|max:100',
            'doctor_codigo_cm'    => 'nullable|string|max:100',
            'doctor_ci'           => 'nullable|string|max:100',
            'doctor_logo'         => 'nullable|file|image|max:2048',
            'doctor_fondo_agua'   => 'nullable|file|image|max:2048',
            'doctor_direccion'    => 'nullable|string|max:500',
            'doctor_telefono'     => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('doctor_logo')) {
            if ($user->doctor_logo) {
                Storage::disk('public')->delete($user->doctor_logo);
            }
            $validated['doctor_logo'] = $request->file('doctor_logo')->store('recipe', 'public');
        } else {
            unset($validated['doctor_logo']);
        }

        if ($request->hasFile('doctor_fondo_agua')) {
            if ($user->doctor_fondo_agua) {
                Storage::disk('public')->delete($user->doctor_fondo_agua);
            }
            $validated['doctor_fondo_agua'] = $request->file('doctor_fondo_agua')->store('recipe', 'public');
        } else {
            unset($validated['doctor_fondo_agua']);
        }

        $user->update($validated);

        return response()->json([
            'message'             => 'Formato actualizado',
            'doctor_nombre'       => $user->fresh()->doctor_nombre,
            'doctor_especialidad' => $user->fresh()->doctor_especialidad,
            'doctor_codigo_mmps'  => $user->fresh()->doctor_codigo_mmps,
            'doctor_codigo_cm'    => $user->fresh()->doctor_codigo_cm,
            'doctor_ci'           => $user->fresh()->doctor_ci,
            'doctor_logo'         => $user->fresh()->doctor_logo ? asset('storage/' . $user->fresh()->doctor_logo) : null,
            'doctor_fondo_agua'   => $user->fresh()->doctor_fondo_agua ? asset('storage/' . $user->fresh()->doctor_fondo_agua) : null,
            'doctor_direccion'    => $user->fresh()->doctor_direccion,
            'doctor_telefono'     => $user->fresh()->doctor_telefono,
        ], 200);
    }
}
