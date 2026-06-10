@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('evolution.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">← Volver a la lista</a>
        <h1 class="text-3xl font-bold">Evolución: {{ $patient->nombres }} {{ $patient->apellidos }}</h1>
        <p class="text-gray-600 mt-2">Cédula: {{ $patient->cedula_identidad }}</p>
    </div>

    @if($patient->medicalHistories->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
            <p class="font-bold">Sin consultas</p>
            <p>Este paciente no tiene consultas médicas registradas.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($patient->medicalHistories->sortBy('created_at') as $index => $history)
                <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Consulta #{{ $index + 1 }}</h2>
                        <span class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($history->motivo_consulta)
                        <div>
                            <h3 class="font-medium text-gray-700">Motivo de Consulta:</h3>
                            <p class="text-gray-600">{{ $history->motivo_consulta }}</p>
                        </div>
                        @endif

                        @if($history->diagnostico)
                        <div>
                            <h3 class="font-medium text-gray-700">Diagnóstico:</h3>
                            <p class="text-gray-600">{{ $history->diagnostico }}</p>
                        </div>
                        @endif

                        @if($history->plan_tratamiento)
                        <div>
                            <h3 class="font-medium text-gray-700">Plan de Tratamiento:</h3>
                            <p class="text-gray-600">{{ $history->plan_tratamiento }}</p>
                        </div>
                        @endif

                        @if($history->evolucion)
                        <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-medium text-blue-800">Evolución:</h3>
                            <p class="text-blue-700">{{ $history->evolucion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
