<?php

namespace App\Http\Controllers;

use App\Models\Practica;
use App\Models\User;
use App\Notifications\PracticaStatusNotification;
use Illuminate\Http\Request;

class PracticaController extends Controller
{
    // Listar prácticas de un alumno específico
    public function index($alumnoId)
    {
        $practicas = Practica::where('alumno_id', $alumnoId)->get();

        return response()->json($practicas);
    }

    // Registrar nuevas prácticas

public function store(Request $request)
{

    try {
        $validatedData = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'empresa' => 'nullable|string|max:255',
            'horas' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        // Si el usuario es alumno, el estado se establece automáticamente a "pendiente"
        if ($request->user()->role === 'alumno') {
            $validatedData['estado'] = 'pendiente';
        }

        $practica = Practica::create($validatedData);
        return response()->json($practica, 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['validation_errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    // Aprobar o rechazar una práctica
    public function update(Request $request, $id)
    {
        try {
            // Verifica si solo se está pasando el campo 'estado' y aplica validaciones según eso
            if ($request->has('estado') && $request->keys() === ['estado']) {
                // Validar solo el campo 'estado' si es el único presente
                $validatedData = $request->validate([
                    'estado' => 'in:pendiente,aprobado,rechazado'
                ]);
            } else {
                // Validar todos los campos si se está intentando enviar más de un campo
                $validatedData = $request->validate([
                    'empresa' => 'nullable|string|max:255',
                    'horas' => 'required|integer|min:1',
                    'fecha' => 'required|date',
                    'descripcion' => 'nullable|string',
                    'estado' => 'in:pendiente,aprobado,rechazado'
                ]);
            }

            // Buscar y actualizar la práctica
            $practica = Practica::findOrFail($id);
            $practica->update($validatedData);

            // Notificar al alumno si la práctica es aprobada o rechazada
            if (isset($validatedData['estado']) && in_array($validatedData['estado'], ['aprobado', 'rechazado'])) {
                try {
                    $alumno = User::findOrFail($practica->alumno_id);
                    $alumno->notify(new PracticaStatusNotification($validatedData['estado'], $practica));
                } catch (\Exception $notificationError) {
                    // Si hay un error con la notificación, solo registrarlo en los logs
                    \Log::error('Error enviando notificación: ' . $notificationError->getMessage());
                }
            }

            return response()->json($practica, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['validation_errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    // Eliminar un registro de práctica
    public function destroy($id)
    {
        $practica = Practica::findOrFail($id);
        $practica->delete();

        return response()->json(null, 204);
    }

    public function show($id)
    {
        try {
            // Cargar la práctica junto con el alumno asociado
            $practica = Practica::with('alumno')->findOrFail($id);
            return response()->json($practica, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Práctica no encontrada'], 404);
        }
    }


//suir evidencia
public function uploadEvidencia(Request $request, $id)
{
    $practica = Practica::findOrFail($id);

    if ($request->hasFile('evidencia')) {
        $file = $request->file('evidencia');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('evidencias', $filename, 'public');

        $practica->evidencia = $path;
        $practica->estado_evidencia = 'pendiente de revisión';
        $practica->save();

        // Guardar el historial de evidencia
        $practica->historialEvidencias()->create([
            'archivo' => $path,
            'fecha_subida' => now(),
        ]);

        return response()->json(['message' => 'Evidencia subida exitosamente', 'practica' => $practica], 200);
    }

    return response()->json(['error' => 'Archivo de evidencia no encontrado'], 400);
}

// En PracticaController.php

public function updateEstadoEvidencia(Request $request, $id)
{
    $request->validate([
        'estado_evidencia' => 'required|in:aprobado,rechazado,pendiente de revisión',
    ]);

    $practica = Practica::findOrFail($id);
    $practica->estado_evidencia = $request->input('estado_evidencia');
    $practica->save();

    return response()->json(['message' => 'Estado de evidencia actualizado exitosamente', 'practica' => $practica], 200);
}



public function getHistorialEvidencias($id, Request $request)
{
    $practica = Practica::findOrFail($id);

    $query = $practica->historialEvidencias();

    if ($request->has('year')) {
        $query->whereYear('fecha_subida', $request->year);
    }

    if ($request->has('fecha')) {
        $query->whereDate('fecha_subida', $request->fecha);
    }

    $historial = $query->get();

    return response()->json($historial);
}

}
