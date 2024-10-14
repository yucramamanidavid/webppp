<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEvidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'practica_id',
        'archivo',
        'horas_reportadas',
        'fecha_subida',
        'estado'
    ];

    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}
