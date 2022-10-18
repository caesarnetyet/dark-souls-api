<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Arma;
use App\Models\Personaje;

class Equipo extends Model
{
    use HasFactory;
    public function personajes()
    {
        return $this->belongsToMany(Personaje::class);
    }
    public function armas()
    {
        return $this->belongsToMany(Arma::class);
    }

}
