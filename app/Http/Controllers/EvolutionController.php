<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class EvolutionController extends Controller
{
    public function index()
    {
        $patients = Patient::with('medicalHistories')->get();
        return view('evolution.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $patient->load('medicalHistories');
        return view('evolution.show', compact('patient'));
    }
}
