<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo;
use App\Models\Requesito;
class Arma extends Model
{
    use HasFactory;

    public function personajes()
    {
        return $this->belongsToMany(Personaje::class, 'equipos', 'personaje_id', 'arma_id');
    }

    public function requesito(){
        return $this->hasOne(Requesito::class);
    }
}
