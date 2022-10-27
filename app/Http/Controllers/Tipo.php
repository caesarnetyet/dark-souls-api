<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\tipos;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class Tipo extends Controller
{
    public function insertarTipo(Request $request, Response $response){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required'
        ],$messages=[
            'required'=>'el atributo es requerido'
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }
        else{
            $tipo = new Tipos;
            $tipo->nombre = $request->nombre;
            $tipo->save();
            return response()->json([
                'estado'=>$request->nombre." exitosamente agregado"
            ]);
        }
    }

    public function modificarTipo(Request $request, Response $response, int $id){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],400);
        }else{
            $tipo = tipos::find($id);
            if($request->nombre != null)$tipo->nombre=$request->nombre;
            $tipo->save();
            return response()->json([
                'estado'=>'Valor o valores modificados exitosamente'
            ]);
        }
    }

    public function consultarTipos(Request $request, Response $response){
        $tipos = tipos::select('id','nombre')->get();
        return $tipos;
    }
    
    public function consultarTipo(Request $request, Response $response, int $id){
        $tipo = tipos::find($id);
        return $tipo;
    }
}
