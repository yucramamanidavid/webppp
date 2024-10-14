<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('historial_evidencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('practica_id');
            $table->string('archivo'); // Archivo de evidencia (PDF, imágenes, etc.)
            $table->integer('horas_reportadas'); // Horas reportadas con esta evidencia
            $table->date('fecha_subida'); // Fecha de subida de la evidencia
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente'); // Estado de la revisión de la evidencia
            $table->timestamps();

            $table->foreign('practica_id')->references('id')->on('practicas')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_evidencias');
    }
};
