<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Arma;
class Requesito extends Model
{
    use HasFactory;

    public function arma()
    {
        return $this->belongsTo(Arma::class);
    }
}
