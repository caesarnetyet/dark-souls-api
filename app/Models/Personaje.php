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

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class);
    }

    public function estadistica(){
        return $this->hasOne(Estadistica::class);
    }
}
