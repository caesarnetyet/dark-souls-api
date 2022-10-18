<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo;
use App\Models\Requesito;
class Arma extends Model
{
    use HasFactory;

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class);
    }

    public function requesito(){
        return $this->hasOne(Requesito::class);
    }
}
