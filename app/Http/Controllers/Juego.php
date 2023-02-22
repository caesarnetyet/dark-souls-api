<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\juegos;
use Illuminate\Support\Facades\Http;

class Juego extends Controller
{
    public function insertarJuego(Request $request, Response $response){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required
            | string',
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }

        $response = Http::post('http://192.168.123.139:8000/api/Juegos/insertar',[
            'nombre'=>$request->nombre,
        ]);
        if ($response->failed())
        return response()->json($response->json(),400);

            $juego = new juegos;
            $juego->nombre = $request->nombre;
            $juego->save();
            return response()->json([
                'estado'=>$request->nombre." exitosamente agregado",
                'estado remoto'=> $response->json()
            ]);

    }

    public function modificarJuego(Request $request, Response $response, int $id){

        if($request->all()==null){
            return response()->json([
                'No hay nada a modificar',
                'valores modificables'=>'nombre'
            ],400);
        }else{
            $juego = juegos::find($id);
            if($request->nombre != null)$juego->nombre=$request->nombre;
            $juego->save();
            return response()->json([
                'estado'=>'Valor o valores modificados exitosamente'
            ],200);
        }
    }

    public function consultarJuegos(Request $request){
        $response = Http::withToken($request->token)->get('http://192.168.127.135:8000/api/Juegos/consultar');
        return response()->json($response->json(),$response->status());

    }

}
