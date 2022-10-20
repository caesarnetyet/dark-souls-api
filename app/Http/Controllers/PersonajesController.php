<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\Personaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonajesController extends Controller
{
    public function index() {
        $personajes = Personaje::all();
        return response()->json($personajes);
    }
    public function agregarPersonaje(Request $request, Validator $validator){
        $personaje = new Personaje();
        $validator = Validator::make($request->all(),
        [
            "nombre" => "required | string | max:50",
            "clase_id"=> "required | integer | exists:clases,id",
        ],
        [
            "nombre.required" => "El nombre es requerido",
            "nombre.string" => "El nombre debe ser una cadena de caracteres",
            "nombre.max" => "El nombre debe tener como máximo 50 caracteres",
            "clase_id.required" => "La clase es requerida",
            "clase_id.integer" => "La clase debe ser un número entero",
            "clase_id.exists" => "La clase no existe"
        ]);
        if($validator->fails()){
            return response()->json(["errores" => $validator->errors()], 400);
        }
        $clase =Clase::find($request->clase_id);
        if($personaje->where("nombre", $request->nombre)->first())
            return response()->json(["error" => "El personaje ya existe"], 400);
        $personaje->nombre = $request->nombre;
        $personaje->clase_id = $request->clase_id;
        $personaje->save();
        
        if($personaje){
            return response()->json([
                "mensaje" => "Personaje agregado correctamente",
                "clase" => $clase->nombre,
                "personaje" => $personaje
            ], 201);
        }else{
            return response()->json([
                "mensaje" => "Error al agregar personaje"
            ], 500);
        }
    }
    public function borrarPorId($id)
    {
        $personaje = Personaje::find($id);
        if ($personaje) {
            $personaje->delete();
            return response()->json([
                'mensaje' => 'personaje eliminada correctamente',
                'personaje' => $personaje
            ], 201);
        } else {
            return response()->json([
                'mensaje' => 'No se encontro la personaje'
            ], 500);
        }
    }

    public function actualizarPersonaje(Request $request, $id)
    {
        $personaje = Personaje::find($id);
        if (!$personaje) {
            return response()->json([
                'mensaje' => 'personaje no encontrado'
            ], 400);}
        
            $validator = Validator::make(
            $request->all(),
            [
                'nombre' => 'required | string | max:50',
                "clase_id"=> "required | integer | exists:clases,id"
            ],
            [
                'nombre.required' => 'El nombre es requerido',
                'nombre.string' => 'El nombre debe ser una cadena de caracteres',
                'nombre.max' => 'El nombre debe tener como máximo 50 caracteres',
                "clase_id.required" => "La clase es requerida",
                "clase_id.integer" => "La clase debe ser un número entero",
                "clase_id.exists" => "La clase no existe"
            ]
        );

        if ($validator->fails()) {
            return response()->json(["errores" => $validator->errors()], 400);
        }
       
        $personaje->nombre = $request->nombre;
        $personaje->clase_id = $request->clase_id;
        $personaje->save();
        $clase =Clase::find($request->clase_id);
        return response()->json([
            'mensaje' => 'personaje actualizado correctamente',
            'personaje' => [
                "nombre" => $personaje->nombre,
                "updated_at" => $personaje->updated_at,
                "clase"=> $clase->nombre
            ]
        ], 201);
    }

    public function personajeConArmas() {
        $personajes = Personaje::with('armas')->get();
        return response()->json($personajes);
    }

    public function personajeConCaracteristicas(){
        $personajes = Personaje::with('caracteristica')->get();
        return response()->json($personajes);
    }


}
