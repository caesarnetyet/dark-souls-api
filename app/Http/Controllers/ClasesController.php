<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClasesController extends Controller
{
    //
    public function index()
    {


        $clases = Clase::all();
        return response()->json($clases);
    }


    public function agregarClase(Request $request, Validator $validator)
    {
        $clase = new Clase();
        $validator = Validator::make(
            $request->all(),
            [
                'nombre' => 'required | string | max:50'
            ],
            [
                'nombre.required' => 'El nombre es requerido',
                'nombre.string' => 'El nombre debe ser una cadena de caracteres',
                'nombre.max' => 'El nombre debe tener como máximo 50 caracteres'
            ]
        );

        if ($validator->fails()) {
            return response()->json(["errores" => $validator->errors()], 400);
        }
        if ($clase->where('nombre', $request->nombre)->first())
            return response()->json(["error" => "La clase ya existe"], 400);


        $clase->nombre = $request->nombre;
        $clase->save();

        if ($clase) {
            return response()->json([
                'mensaje' => 'Clase agregada correctamente',
                'clase' => $clase
            ], 201);
        } else {
            return response()->json([
                'mensaje' => 'Error al agregar clase'
            ], 500);
        }
    }

    public function borrarPorId($id)
    {
        $clase = Clase::find($id);
        if ($clase) {
            $clase->delete();
            return response()->json([
                'mensaje' => 'Clase eliminada correctamente',
                'clase' => $clase
            ], 200);
        } else {
            return response()->json([
                'mensaje' => 'No se encontro la clase'
            ], 500);
        }
    }
    public function encontrarClase($id)
    {
        $clase = new Clase;
        $clase = $clase->find($id);
        if ($clase) {
            return response()->json(
                [
                    'clase' => [
                        'id' => $clase->id,
                        'nombre' => $clase->nombre
                    ]
                ],
                200
            );
        } else {
            return response()->json([
                'mensaje' => 'Clase no encontrada'
            ], 400);
        }
    }

    public function actualizarClase(Request $request, $id)
    {
        $clase = Clase::find($id);
        if (!$clase) {
            return response()->json([
                'mensaje' => 'Clase no encontrada'
            ], 500);}
        
                $validator = Validator::make(
            $request->all(),
            [
                'nombre' => 'required | string | max:50'
            ],
            [
                'nombre.required' => 'El nombre es requerido',
                'nombre.string' => 'El nombre debe ser una cadena de caracteres',
                'nombre.max' => 'El nombre debe tener como máximo 50 caracteres'
            ]
        );

        if ($validator->fails()) {
            return response()->json(["errores" => $validator->errors()], 400);
        }
        $clase->nombre = $request->nombre;
        $clase->save();
        return response()->json([
            'mensaje' => 'Clase actualizada correctamente',
            'clase' => $clase
        ], 201);
    }
}
