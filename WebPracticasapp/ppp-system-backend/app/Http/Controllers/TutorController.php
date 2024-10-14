<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\Alumno;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    // Obtener todos los tutores con sus alumnos asignados
    public function index()
    {
        try {
            $tutores = Tutor::with('alumnos')->get();
            return response()->json($tutores, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Crear un nuevo tutor
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:tutores,user_id',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:tutores,email',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            $tutor = Tutor::create($request->all());
            return response()->json($tutor, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el tutor: ' . $e->getMessage()], 500);
        }
    }

    // Asignar un alumno a un tutor
    public function assignAlumno(Request $request, $tutorId)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'estado' => 'required|in:pendiente,aceptado,rechazado',
        ]);

        try {
            $tutor = Tutor::findOrFail($tutorId);
            $tutor->alumnos()->syncWithoutDetaching([$request->alumno_id => ['estado' => $request->estado]]);
            return response()->json(['message' => 'Alumno asignado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al asignar alumno: ' . $e->getMessage()], 500);
        }
    }

    // Obtener alumnos asignados a un tutor con estado 'aceptado'
    public function getAssignedAlumnos($tutorId)
    {
        try {
            $tutor = Tutor::with(['alumnos' => function ($query) {
                $query->wherePivot('estado', 'aceptado');
            }])->findOrFail($tutorId);

            return response()->json($tutor->alumnos, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener alumnos asignados: ' . $e->getMessage()], 500);
        }
    }

    // Actualizar el estado de la relación entre tutor y alumno
    public function updateAlumnoEstado(Request $request, $tutorId, $alumnoId)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aceptado,rechazado',
        ]);

        try {
            $tutor = Tutor::findOrFail($tutorId);
            $tutor->alumnos()->updateExistingPivot($alumnoId, ['estado' => $request->estado]);
            return response()->json(['message' => 'Estado del alumno actualizado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el estado del alumno: ' . $e->getMessage()], 500);
        }
    }
}
