<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PracticaController;
use App\Http\Controllers\TutorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas por Sanctum para usuarios autenticados
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para el usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas de Alumnos
    Route::get('alumnos', [AlumnoController::class, 'index'])->middleware('role:admin,tutor,alumno');
    Route::post('alumnos', [AlumnoController::class, 'store'])->middleware('role:admin');
    Route::get('alumnos/{id}', [AlumnoController::class, 'show'])->middleware('role:admin,alumno,tutor');
    Route::put('alumnos/{id}', [AlumnoController::class, 'update'])->middleware('role:admin');
    Route::delete('alumnos/{id}', [AlumnoController::class, 'destroy'])->middleware('role:admin');
    Route::get('/alumnos/{id}', [AlumnoController::class, 'show']);

    // Búsqueda por código, accesible para `tutor` y `admin`
    Route::get('alumnos/codigo/{codigo}', [AlumnoController::class, 'buscarPorCodigo'])->middleware('role:admin,tutor');

    // Rutas de Practicas
    Route::get('alumnos/{alumnoId}/practicas', [PracticaController::class, 'index'])->middleware('role:admin,tutor,alumno'); // Listar prácticas de un alumno
    Route::post('practicas', [PracticaController::class, 'store'])->middleware('role:admin,tutor,alumno'); // Registrar práctica
    Route::get('practicas/{id}', [PracticaController::class, 'show'])->middleware('role:admin,tutor,alumno'); // Ver una práctica específica
    Route::put('practicas/{id}', [PracticaController::class, 'update'])->middleware('role:admin,tutor'); // Aprobar o rechazar práctica
    Route::delete('practicas/{id}', [PracticaController::class, 'destroy'])->middleware('role:admin'); // Eliminar práctica

    Route::middleware('auth:sanctum')->get('/notificaciones', function (Request $request) {
        return $request->user()->notifications;
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    });

    Route::post('practicas/{id}/upload-evidencia', [PracticaController::class, 'uploadEvidencia'])->middleware('role:alumno,admin,tutor');
    Route::patch('practicas/{id}/estado-evidencia', [PracticaController::class, 'updateEstadoEvidencia'])
    ->middleware('auth:sanctum');

    Route::get('/practicas/{id}/historial-evidencias', [PracticaController::class, 'getHistorialEvidencias']);


    //empresa
    Route::get('/empresas', [EmpresaController::class, 'index']);
    Route::post('/empresas', [EmpresaController::class, 'store'])->middleware('role:admin');
    Route::get('/empresas/{id}', [EmpresaController::class, 'show']);
    Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->middleware('role:admin');
    Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy'])->middleware('role:admin');


    //tutor
    Route::get('/tutores', [TutorController::class, 'index']);
    Route::post('/tutores', [TutorController::class, 'store']);
    Route::post('/tutores/{tutor}/asignar-alumno', [TutorController::class, 'assignAlumno'])->middleware('role:admin,tutor');
    Route::put('/tutores/{tutor}/alumnos/{alumno}/estado', [TutorController::class, 'updateAlumnoEstado'])->middleware('role:admin,tutor');
    Route::get('/tutores/{tutor}/alumnos-asignados', [TutorController::class, 'getAssignedAlumnos'])->middleware('role:admin,tutor');

});
