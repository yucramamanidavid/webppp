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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Asegurarse de que el nombre sea único
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable()->unique(); // Email único para contacto
            $table->string('website')->nullable(); // Página web de la empresa
            $table->string('contacto_nombre')->nullable(); // Nombre de contacto principal
            $table->string('contacto_telefono')->nullable(); // Teléfono de contacto
            $table->string('contacto_email')->nullable(); // Email de contacto
            $table->text('notas')->nullable(); // Notas adicionales sobre la empresa
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado de la empresa
            $table->timestamps();

            // Índices adicionales
            $table->index(['nombre', 'telefono']); // Índice conjunto para optimizar consultas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
