<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function register(Request $request) {
        $response = Http::post("http://192.168.127.135:8000/api/Usuarios/insertar",[
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>$request->password,
        ]);
        
        if($response->successful()) {
            User::create([
                "name"=>$request->name,
                "email"=>$request->email,
                "password"=>bcrypt($request->password),
            ]);
            return response()->json($response->json(), 201);
        } else {
            return response()->json($response->json(), 400);
        }

    }
    


    public function login(Request $request, Response $response) {
        $response = Http::post("http://192.168.127.135:8000/api/Usuarios/login",[
            "email"=>$request->email,
            "password"=>$request->password,
        ]);
        if($response->successful()) {
            $user = User::where("email", $request->email)->first();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(["token" => $token, "token remoto" => $response['acces_token']], 200);
        } else {
            return response()->json($response->json(), 400);
        }
    }



    
    public function loginAsArmero(Request $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Correo eletronico o contraseña no coinciden'
            ], 401);
        }
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token', ['armero'])->plainTextToken;
        return response()->json([
            'tipo_usuario' => 'armero',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
    public function info(Request $request) {
        return $request->user();
    }

    public function logout(Request $request) {
       $request->user()->tokens()->delete();
        return [
            'message' => 'Sesión cerrada'
        ];
    }
}
