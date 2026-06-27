<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('doctor_nombre')->nullable();
            $table->string('doctor_especialidad')->nullable();
            $table->string('doctor_codigo_mmps')->nullable();
            $table->string('doctor_codigo_cm')->nullable();
            $table->string('doctor_ci')->nullable();
            $table->string('doctor_logo')->nullable();
            $table->string('doctor_fondo_agua')->nullable();
            $table->string('doctor_direccion')->nullable();
            $table->string('doctor_telefono')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'doctor_nombre',
                'doctor_especialidad',
                'doctor_codigo_mmps',
                'doctor_codigo_cm',
                'doctor_ci',
                'doctor_logo',
                'doctor_fondo_agua',
                'doctor_direccion',
                'doctor_telefono',
            ]);
        });
    }
};
