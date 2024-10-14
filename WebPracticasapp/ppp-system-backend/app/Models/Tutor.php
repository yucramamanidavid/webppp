<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nombre', 'email', 'telefono'];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_tutor')
                    ->withPivot('estado')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
