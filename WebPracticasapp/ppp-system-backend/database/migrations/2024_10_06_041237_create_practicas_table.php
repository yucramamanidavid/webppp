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
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade'); // ID del alumno, con llave foránea
            $table->string('empresa')->nullable(); // Empresa o proyecto
            $table->integer('horas')->default(0); // Horas acumuladas en esta práctica
            $table->date('fecha'); // Fecha de la práctica
            $table->text('descripcion')->nullable(); // Descripción de las actividades
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente'); // Estado del registro
            $table->string('evidencia')->nullable(); // Ruta del archivo de evidencia
            $table->string('estado_evidencia')->default('pendiente'); // Estado de la evidencia

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};
