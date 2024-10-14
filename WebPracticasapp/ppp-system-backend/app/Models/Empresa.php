<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'website',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_email',
        'notas',
        'estado'
    ];
}
