<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ClassesController extends Controller
{
    public function index(){
        $classes = Classe::all();
        return $classes->map(fn ($class)=>
            [
                'id' => $class->id,
                'attributes'=>
                [
                    'name' => $class->name,
                    'description' => $class->description,
                    'active' => $class->active,
                ],
                'actions'=>
                [
                    'edit_url' => URL::signedRoute('class.update ', ['class' => $class]),
                    'delete_url' => URL::signedRoute('class.destroy', ['class' => $class]),
                ]
            ]);
    }

}
