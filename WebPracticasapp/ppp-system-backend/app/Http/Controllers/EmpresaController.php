<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    // Listar todas las empresas
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
    }

    // Guardar una nueva empresa
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:empresas',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:empresas,email',
            'website' => 'nullable|url',
            'contacto_nombre' => 'nullable|string|max:255',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email',
            'notas' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo'
        ]);

        $empresa = Empresa::create($validatedData);
        return response()->json($empresa, 201);
    }

    // Mostrar una empresa específica
    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json($empresa);
    }

    // Actualizar una empresa
    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:empresas,nombre,' . $id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:empresas,email,' . $id,
            'website' => 'nullable|url',
            'contacto_nombre' => 'nullable|string|max:255',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email',
            'notas' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo'
        ]);

        $empresa->update($validatedData);
        return response()->json($empresa);
    }

    // Eliminar una empresa
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();
        return response()->json(null, 204);
    }
}
