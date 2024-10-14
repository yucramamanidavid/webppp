<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutoresTable extends Migration
{
    public function up()
    {
        Schema::create('tutores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // Relación con la tabla users
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Tabla pivot para la relación entre tutores y alumnos
        Schema::create('alumno_tutor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('tutor_id');
            $table->string('estado')->default('pendiente'); // 'pendiente', 'aceptado' o 'rechazado'
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('tutor_id')->references('id')->on('tutores')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alumno_tutor');
        Schema::dropIfExists('tutores');
    }
}

