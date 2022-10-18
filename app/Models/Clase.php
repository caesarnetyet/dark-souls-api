<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Personaje;
class Clase extends Model
{
    use HasFactory;
    

    public function personajes()
    {
        return $this->hasMany(Personaje::class);
    }
}
