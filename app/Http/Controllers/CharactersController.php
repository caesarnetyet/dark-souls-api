<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class CharactersController extends Controller
{
    public function index(){
        $characters = Character::all();
        return $characters->map(fn ($character)=>

            [
                'id' => $character->id,
                'attributes'=>
                [
                    'name' => $character->name,
                    'class' => $character->classe()->first()->name
                ],
                'actions'=>
                [
                    'edit_url' => URL::signedRoute('character.update', ['character' => $character]),
                    'delete_url' => URL::signedRoute('character.destroy', ['character' => $character]),
                ]
            ]);
    }

    public function store(Request $request){
        $user = $request->user();
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'class_id' => 'required|integer|exists:classes,id',
            ]
        );
        if ($validator->fails())
            return response()->json(['error'=>$validator->errors()], 400);
        $user->characters()->create($validator->validated());
        return response()->json(['message'=>'Personaje creado satisfactoriamente.'], 201);
    }

    public function update(Request $request, Character $character){
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'string',
                'class_id' => 'integer|exists:classes,id',
            ]
        );
        if ($validator->fails())
            return response()->json(['error'=>$validator->errors()], 400);
        $character->update($validator->validated());
        return response()->json(['message'=>'Personaje actualizado satisfactoriamente.'], 201);
    }

    public function destroy(Character $character){
        $character->delete();
        return response()->json(['message'=>'Personaje eliminado satisfactoriamente.'], 201);
    }
}
