<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estadistica;
use App\Models\Equipo;
use App\Models\Clase;
class Personaje extends Model
{
    use HasFactory;

    public function clase(){
        return $this->belongsTo(Clase::class);
    }

    public function armas()
    {
        return $this->belongsToMany(Arma::class, 'equipos', 'personaje_id', 'arma_id');
    }

    public function estadistica(){
        return $this->hasOne(Estadistica::class);
    }
}
