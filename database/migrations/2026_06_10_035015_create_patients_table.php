<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('sexo');
            $table->string('lugar_nacimiento')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('cedula_identidad')->unique()->nullable();
            $table->integer('edad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->text('direccion')->nullable();
            $table->string('emergencia_nombre')->nullable();
            $table->string('emergencia_parentesco')->nullable();
            $table->string('emergencia_telefono')->nullable();
            $table->text('emergencia_direccion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
