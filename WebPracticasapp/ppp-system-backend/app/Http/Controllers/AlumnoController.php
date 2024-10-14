<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    // Obtener todos los alumnos
    public function index()
    {
        try {
            $alumnos = Alumno::all();
            return response()->json($alumnos, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Crear un nuevo alumno
    public function store(Request $request)
    {
        // Validación de los campos necesarios
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:alumnos,email',
            'telefono' => 'nullable|string|max:20',
            'codigo' => 'required|string|unique:alumnos,codigo|max:20'
        ]);

        try {
            $alumno = Alumno::create($validatedData);
            return response()->json($alumno, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el alumno: ' . $e->getMessage()], 500);
        }
    }

    // Obtener un alumno por ID
    public function show($id)
    {
        try {
            $alumno = Alumno::findOrFail($id);
            return response()->json($alumno, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }
    }

    // Actualizar un alumno
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:alumnos,email,'.$id,
            'telefono' => 'nullable|string|max:20',
            'codigo' => 'required|string|unique:alumnos,codigo,'.$id
        ]);

        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->update($validatedData);
            return response()->json($alumno, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el alumno: ' . $e->getMessage()], 500);
        }
    }


    // Eliminar un alumno
    public function destroy($id)
    {
        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el alumno: ' . $e->getMessage()], 500);
        }
    }

    // Buscar un alumno por código
    public function buscarPorCodigo($codigo)
    {
        $alumno = Alumno::where('codigo', $codigo)->first(); // Obtén el primer alumno que coincida con el código
        if ($alumno) {
            return response()->json($alumno, 200);
        } else {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }
    }
}
