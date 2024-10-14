<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{
    use HasFactory;
    protected $fillable = [
        'alumno_id',
        'empresa',
        'horas',
        'fecha',
        'descripcion',
        'estado',
        'evidencia',
        'estado_evidencia'
    ];

    // Relación con el modelo Alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }


    public function historialEvidencias()
    {
        return $this->hasMany(HistorialEvidencia::class);
    }
}
