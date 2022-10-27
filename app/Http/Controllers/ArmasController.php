<?php

namespace App\Http\Controllers;

use App\Models\Arma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ArmasController extends Controller
{
    public function index(){
        $armas = Arma::all();
        return response()->json($armas);
    }

    public function agregarArma(Request $request, Validator $validator){
        $validator = Validator::make($request->all(),
        [
            'nombre' => 'required | string | max:50 | unique:armas',
            'tipo' => 'required | string | max:50',
            'fuerza' => 'required | integer | max:100',
            'magia' => 'required | integer | max:100',
            'peso' => 'required | integer | max:100',
            'estabilidad' => 'required | integer | max:100',
        ],
        [
            'nombre.required' => 'El nombre es requerido',
            'nombre.string' => 'El nombre debe ser una cadena de caracteres',
            'nombre.max' => 'El nombre debe tener como máximo 50 caracteres',
            'nombre.unique' => 'El arma ya existe',
            'tipo.required' => 'El tipo es requerido',
            'tipo.string' => 'El tipo debe ser una cadena de caracteres',
            'tipo.max' => 'El tipo debe tener como máximo 50 caracteres',
            'fuerza.required' => 'La fuerza es requerida',
            'fuerza.integer' => 'La fuerza debe ser un número entero',
            'fuerza.max' => 'La fuerza debe tener como máximo 100',
            'magia.required' => 'La magia es requerida',
            'magia.integer' => 'La magia debe ser un número entero',
            'magia.max' => 'La magia debe tener como máximo 100',
            'peso.required' => 'El peso es requerido',
            'peso.integer' => 'El peso debe ser un número entero',
            'peso.max' => 'El peso debe tener como máximo 100',
            'estabilidad.required' => 'La estabilidad es requerida',
            'estabilidad.integer' => 'La estabilidad debe ser un número entero',
            'estabilidad.max' => 'La estabilidad debe tener como máximo 100'    
        ]
       
        );
        if ($validator->fails())
        return response()->json(["errores" => $validator->errors()], 400);

        $response = Http::post(' http://127.0.0.1:8000/api/v1/armas/agregar',[
            
            "nombre"=>$request->nombre,
            "tipo"=> $request->tipo,
            "fuerza"=> "100",
            "magia"=> 0,
            "peso"=> 3,
            "estabilidad"=>10
        
        
    ]);
    if ($response->failed())
        return response()->json($response->json(),400);

            $arma = new Arma();
            $arma->nombre = $request->nombre;
            $arma->tipo = $request->tipo;
            $arma->fuerza = $request->fuerza;
            $arma->magia = $request->magia;
            $arma->peso = $request->peso;
            $arma->estabilidad = $request->estabilidad;
            $arma->save();
            return response()->json([
                "mensaje" => "Arma agregada correctamente",
                "arma" => $arma
            ], 201);
        
    }


    public function actualizarArma(Request $request){
        $validator = Validator::make($request->all(),
        [
            'id' => 'required | integer | exists:armas',
            'nombre' => 'required | string | max:50',
            'tipo' => 'required | string | max:50',
            'fuerza' => 'required | integer | max:100',
            'magia' => 'required | integer | max:100',
            'peso' => 'required | integer | max:100',
            'estabilidad' => 'required | integer | max:100',
        ],
        [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id debe ser un número entero',
            'id.exists' => 'El arma no existe',
            'nombre.required' => 'El nombre es requerido',
            'nombre.string' => 'El nombre debe ser una cadena de caracteres',
            'nombre.max' => 'El nombre debe tener como máximo 50 caracteres',
            'tipo.required' => 'El tipo es requerido',
            'tipo.string' => 'El tipo debe ser una cadena de caracteres',
            'tipo.max' => 'El tipo debe tener como máximo 50 caracteres',
            'fuerza.required' => 'La fuerza es requerida',
            'fuerza.integer' => 'La fuerza debe ser un número entero',
            'fuerza.max' => 'La fuerza debe tener como máximo 100',
            'magia.required' => 'La magia es requerida',
            'magia.integer' => 'La magia debe ser un número entero',
            'magia.max' => 'La magia debe tener como máximo 100',
            'peso.required' => 'El peso es requerido',
            'peso.integer' => 'El peso debe ser un número entero',
            'peso.max' => 'El peso debe tener como máximo 100',
            'estabilidad.required' => 'La estabilidad es requerida',
            'estabilidad.integer' => 'La estabilidad debe ser un número entero',
            'estabilidad.max' => 'La estabilidad debe tener como máximo 100'
        ]
       
        );
        if ($validator->fails())
        $response = Http::post(' http://127.0.0.1:8000/api/v1/armas/actualizar',[
            
            
            "id"=> 3,
            "nombre"=>"Odachi",
            "tipo"=> "Katana",
            "fuerza"=> 90,
            "magia"=> 0,
            "peso"=> 6,
            "estabilidad"=>12
]);
if ($response->failed())
        return response()->json($response->json(),400);


        $arma = Arma::find($request->id);
        $arma->nombre = $request->nombre;
        $arma->tipo = $request->tipo;
        $arma->fuerza = $request->fuerza;
        $arma->magia = $request->magia;
        $arma->peso = $request->peso;
        $arma->estabilidad = $request->estabilidad;
        $arma->save();

        if($arma)
            return response()->json([
                'mensaje' => 'Arma actualizada correctamente',
                'arma' => $arma
            ], 201);
        else
            return response()->json([
                'mensaje' => 'Error al actualizar arma'
            ], 500);
    }
    public function borrarPorId($id)
    {
        $arma = Arma::find($id);
        if ($arma) {
            $arma->delete();
            return response()->json([
                'mensaje' => 'arma eliminada correctamente',
                'arma' => $arma
            ], 201);
        } else {
            return response()->json([
                'mensaje' => 'No se encontro el arma'
            ], 500);
        }
    }

    public function armaConPersonajes() {
        $armas = Arma::with('personajes')->get();
        return response()->json($armas, 200);

    
    }

    
}
