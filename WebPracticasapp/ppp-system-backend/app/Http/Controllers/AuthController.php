<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Iniciar sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales no válidas'], 401);
        }

        $token = $user->createToken('token-name')->plainTextToken;


        // Buscar el alumno asociado al usuario si es un alumno
        $alumnoId = null;
        if ($user->role === 'alumno') {
            $alumno = Alumno::where('email', $user->email)->first();
            if ($alumno) {
                $alumnoId = $alumno->id;
            }
        }


        return response()->json([
            'token' => $token,
            'role' => $user->role,
            'userId' => $user->id, // Asegúrate de que se incluye el ID del usuario
            'alumnoId' => $alumnoId // Retornar el ID del alumno si es un alumno
        ]);
    }

    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request)
    {
        // Validación inicial de usuario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:alumno,tutor,admin',
            // Validación adicional solo para 'alumno'
            'nombre' => 'required_if:role,alumno|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'codigo' => 'required_if:role,alumno|string|unique:alumnos,codigo'
        ]);

        if ($validator->fails()) {
            return response()->json(['validation_errors' => $validator->errors()], 422);
        }

        try {
            // Creación del usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

           // Si el rol es alumno, crear también un registro en la tabla alumnos y enlazarlo
        $alumnoId = null;
        if ($request->role === 'alumno') {
            $alumno = Alumno::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'codigo' => $request->codigo
            ]);
            $alumnoId = $alumno->id; // Guardamos el ID del alumno
        }

        // Respuesta con el ID del alumno, si corresponde
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'alumnoId' => $alumnoId
        ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
