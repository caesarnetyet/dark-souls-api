<?php

namespace App\Http\Controllers;

use App\Models\jefes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class Jefe extends Controller
{
    public function insertarJefe(Request $request, Response $response){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required',
            'tipo'=>'required | integer',
            'juego'=>'required | integer'
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }
            
            $response = Http::post('http://192.168.123.127:8000/api/Jefes/insertar',[
                'nombre'=>$request->nombre,
                'tipo'=>$request->tipo,
                'juego'=>$request->juego
            ]);
            if ($response->status() == 201)
                $jefe = new jefes;
                $jefe->nombre = $request->nombre;
                $jefe->tipo = $request->tipo;
                $jefe->juego = $request->juego;
                $jefe->save();
                return response()->json([
                'estado'=>$request->nombre." exitosamente agregado",
                'estado remoto'=> $response->json()
                ], $response->status());

            return response()->json([
                'estado'=>$request->nombre." no se pudo agregar",
                'estado remoto'=> $response->json()
                ], $response->status());
           
            
            
            
        
    }

    public function modificarJefe(Request $request, Response $response, int $id){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }
        $response = Http::post('http://192.168.123.139:8000/api/Jefes/insertar',[
            'nombre'=>$request->nombre,
            'tipo'=>$request->tipo,
            'juego'=>$request->juego
        ]);
        if ($response->failed())
            return response()->json([
                'error'=>'No se ha podido insertar el jefe en la base de datos remota'
            ],400);
            $jefe = jefes::find($id);
            if($request->nombre != null)$jefe->nombre=$request->nombre;
            if($request->tipo != null)$jefe->tipo=$request->tipo;
            if($request->juego != null)$jefe->nombre=$request->juego;
            $jefe->save();
            return response()->json([
                'estado'=>'Valor o valores modificados exitosamente'
            ]);
        
    }
    
    public function consultarJefes(Request $request, Response $response){
        $jefes = jefes::select()
        ->join('juegos','juegos.id','=','jefes.juego')
        ->join('tipos','tipos.id','=','jefes.tipo')
        ->select('juegos.nombre as Juego','juegos.id as JuegoId','jefes.nombre as Jefe','jefes.id as JefeId','tipos.nombre as Tipo')->get();
        return $jefes;
    }

    public function consultarJefe(Request $request, Response $response, $id){
        $jefe = jefes::select()->join('juegos','juegos.id','=','jefes.juego')
        ->join('tipos','tipos.id','=','jefes.tipo')
        ->select('juegos.nombre as Juego','juegos.id as IdJuego','jefes.nombre as Jefe','jefes.id as IdJefe','tipos.nombre as Tipo')
        ->where('jefes.id','=',$id)->get();
        return $jefe;
    }
}
