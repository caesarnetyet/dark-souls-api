<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\URL;
class UsersController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required | string | max:50',
            'email' => 'required | email | max:50 | unique:users',
            'password' => 'required | string | min:8 | max:50',
            'role_id' => 'required| integer | exists:roles,id',
            'numero_telefono'=> 'required | string | size:10',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre debe tener como máximo 50 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser un email válido',
            'email.max' => 'El email debe tener como máximo 50 caracteres',
            'email.unique' => 'El email ya existe',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de caracteres',
            'password.min' => 'La contraseña debe tener como mínimo 8 caracteres',
            'password.max' => 'La contraseña debe tener como máximo 50 caracteres',
            'role_id.required' => 'El rol es requerido',
            'role_id.exist' => 'El rol no existe',
            'role_id.integer' => 'El rol debe ser un número entero',
            'numero_telefono.required' => 'El número de teléfono es requerido',
            'numero_telefono.string' => 'El número de teléfono debe ser una cadena de caracteres',
            'numero_telefono.size' => 'El número de teléfono debe tener 10 caracteres',
        ]);
        if($validator->fails()) {
            return response()->json(["errores" => $validator->errors()], 400);
        }

            $user =  new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role_id = $request->role_id;
            $user->numero_telefono = $request->numero_telefono;
            $user->save();
            // Mail::to($request->email)->send(new SendMail($user));
        return URL::temporarySignedRoute('verify', now()->addMinutes(2), ['numero_telefono' => $user->numero_telefono]);
    
    }
    


    public function login(Request $request) {
        $user = User::where("email", $request->email)->first();
            if ($user) 
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(["token" => $token, "Autentificacion correcta"], 200);

            return response()->json("Contraseña o correo incorrectos", 400);
        
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

    public function usuariosConRoles(){
        $users = User::with('roles')->get();
        return response()->json($users, 200);
    }

    public function verified(Request $request){

        // dd($user);
        $numero = intval($request->numero_telefono);
        
        $response = Http::get('https://api.nexmo.com/verify/json', [
            'api_key' => 'e630d1a8',
            'api_secret' => 'cL5tFVfss1mWz9St',
            'number' => 52 .$numero,
            'brand' => "pippipipipipi",
        ]);

        if ($response->ok()) {
            return response()->json($response->json(), 200);
        } else {
            return response()->json($response->json(), 400);
        }
    }
   
}
