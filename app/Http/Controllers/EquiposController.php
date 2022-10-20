<?php

namespace App\Http\Controllers;

use App\Models\Arma;
use App\Models\Equipo;
use App\Models\Personaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquiposController extends Controller
{
    public function index(){
       $equipo =  Equipo::all();
       return response()->json($equipo);

    }

    public function agregarEquipo(Request $request, Validator $validator){
        $validator = Validator::make($request->all(), [
            'personaje_id' => 'required | integer',
            'arma_id' => 'required | integer',
        ], [
            'personaje_id.required' => 'El personaje es requerido',
            'personaje_id.integer' => 'El personaje debe ser un numero entero',
            'arma_id.required' => 'El arma es requerido',
            'arma_id.integer' => 'El arma debe ser un numero entero',
        ]);
       if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $personaje = Personaje::find($request->personaje_id);
        $personaje->armas()->attach($request->arma_id);
        if($personaje)
            return response()->json([
                'mensaje' => 'Equipo agregado correctamente',
                'equipo' => $personaje
            ], 201);
        else
            return response()->json([
                'mensaje' => 'Error al agregar equipo'
            ], 500);
    }
    public function eliminarEquipo(Request $request, Validator $validator){
        $validator = Validator::make($request->all(), [
            'personaje_id' => 'required | integer | exists:personajes,id',
            'arma_id' => 'required | integer | exists:armas,id',
        ], [
            'personaje_id.required' => 'El personaje es requerido',
            'personaje_id.exists' => 'El personaje no existe',
            'arma_id.exists' => 'El arma no existe',
            'personaje_id.integer' => 'El personaje debe ser un numero entero',
            'arma_id.required' => 'El arma es requerido',
            'arma_id.integer' => 'El arma debe ser un numero entero',
        ]);
       if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $personaje = Personaje::find($request->personaje_id);
        $personaje->armas()->detach($request->arma_id);
        $arma = Arma::find($request->arma_id);
        if($personaje)
            return response()->json([
                'mensaje' => 'Equipo eliminado correctamente',
                'equipo' => ["Personaje"=> $personaje->nombre, "Arma"=> $arma->nombre]
            ], 201);
        else
            return response()->json([
                'mensaje' => 'Error al eliminar equipo'
            ], 500);
    }
}
