<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ClassesController extends Controller
{
    public function index(){
        $classes = Classe::all();
        return $classes->map(fn ($class)=>
            [
                'id' => $class->id,
                'attributes'=>
                [
                    'name' => $class->name
                ],
                'actions'=>
                [
                    'edit_url' => URL::signedRoute('class.update', ['class' => $class]),
                    'delete_url' => URL::signedRoute('class.destroy', ['class' => $class]),
                ]
            ]);
    }

    public function store(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required | string | unique:classes,name',
        ]);
        if($validate->fails()){
            return response()->json(["error" => $validate->errors()], 400);
        }
        $class = Classe::create($validate->validated());
        return response()->json(["message"=>"Clase creada correctamente"], 201);
    }

    public function show(){
        return Classe::all('id', 'name');
    }

    public function update(Request $request, Classe $class){
        $validator = Validator::make([
            'name' => $request->name,
        ],
            [
            'name' => 'required | string | unique:classes,name',
        ]);
        if($validator->fails())
            return response()->json(["error" => $validator->errors()], 400);

        $class->update($request->all());
        return response()->json($class, 201);
    }

    public function destroy(Classe $class){
        $class->delete();
        return response()->json(["message"=>"Clase borrada satisfactoriamente"], 201);
    }

}
