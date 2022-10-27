<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\mapas;
use Illuminate\Http\Response;

class Mapa extends Controller
{
    public function insertarMapa(Request $request, Response $response){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required',
            'tipo'=>'required | integer',
            'juego'=>'required | integer',
            'jefe'=>'required | integer'
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }
        else{
            $mapa = new Mapas;
            $mapa->nombre = $request->nombre;
            $mapa->tipo = $request->tipo;
            $mapa->juego = $request->juego;
            $mapa->jefe = $request->jefe;
            $mapa->save();
            return response()->json([
                'estado'=>$request->nombre." exitosamente agregado"
            ]);
        }
    }

    public function modificarMapa(Request $request, Response $response, int $id){


        if($request->all()==null){
            return response()->json([
                'error'=>'No insertaste valores a modificar',
                'valores modificables'=>['nombre','tipo','juego']
            ],400);
        }else{
            $mapa = mapas::find($id);
            if($request->nombre != null)$mapa->nombre=$request->nombre;
            if($request->tipo != null)$mapa->tipo=$request->tipo;
            if($request->juego != null)$mapa->juego=$request->juego;
            $mapa->save();
            return response()->json([
                'estado'=>'Valor o valores modificados exitosamente'
            ],200);
        }
    }

    public function consultarMapas(Request $request, Response $response){
        $mapas = mapas::select()
        ->join('jefes','jefes.id','=','mapas.jefe')
        ->join('juegos','juegos.id','=','mapas.juego')
        ->join('tipos','tipos.id','=','mapas.tipo')
        ->groupBy('mapas.nombre','mapas.id','juegos.id','juegos.nombre','tipos.nombre','jefes.id','jefes.nombre')
        ->select('jefes.nombre as jefe','mapas.nombre as mapa','mapas.id as IdMapa')->get();
        return $mapas;
    }
}
