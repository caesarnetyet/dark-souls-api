<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Codigo;
use Illuminate\Support\Facades\URL;
class UsersController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required | string | max:50',
            'email' => 'required | email | max:50 | unique:users',
            'password' => 'required | string | min:8 | max:50',
           
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

            'numero_telefono.required' => 'El número de teléfono es requerido',
            'numero_telefono.string' => 'El número de teléfono debe ser una cadena de caracteres',
            'numero_telefono.size' => 'El número de teléfono debe tener 10 caracteres',
        ]);
        if($validator->fails()) return response()->json(["errores" => $validator->errors()], 400);

            $user =  new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role_id = 3;
            $user->numero_telefono = $request->numero_telefono;
            $user->save();
            $url = URL::temporarySignedRoute('verify', now()->addMinutes(30), ['user' => $user->id]);
            // Mail::to($request->email)->send(new SendMail($user, $url));

            ProcessMail::dispatch($user, $url)
                        ->delay(now()->addSeconds(20))
                        ->onQueue('emails');
        return response()->json(["mensaje" => "Usuario registrado correctamente, espera nuestro mensaje"], 201);
    
    }
    

   


    public function login(Request $request) {
        if(!Auth::attempt($request->only('email', 'password'))) return response()->json(["mensaje" => "Credenciales incorrectas"], 401);
        $validator = Validator::make($request->all(), [
            'email' => 'required | email ',
            'password' => 'required | string ',
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser un email válido',
            'email.max' => 'El email debe tener como máximo 50 caracteres',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de caracteres',
            
        ]);
        if($validator->fails())return response()->json(["errores" => $validator->errors()], 400);
        
        $user = User::where("email", $request->email)->first();   
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(["token" => $token, "Autentificacion correcta"], 200);

        
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
        
        $user = User::find($request->user);
        if (!!$user->active) return response()->json(["El usuario ya ha sido verificado"], 400); 
        // dd($user->find(1)->codigo->);
        // dd($user->numero_telefono);
        $random4Digits = rand(1000, 9999);
        $url = URL::temporarySignedRoute('verifynumber', now()->addMinutes(30), ['user' => $user->id]);
        
        $response = Http::post('https://rest.nexmo.com/sms/json', [
            "from"=>"Julio Cesar Tovar",
            'api_key' => "e630d1a8",
            'api_secret' => "cL5tFVfss1mWz9St",
            'to' => "52$user->numero_telefono",
            'text' => "Tu codigo de verificacion es: $random4Digits, tienes 30 minutos para insertarlo en la siguiente direccion $url",
        ]);

        if ($response->successful()) {
            $codigo = new Codigo;
            $codigo->codigo = $random4Digits;
            // $codigo->user_id = $user->id;
            // $codigo->save();
            $user->codigo()->save($codigo);

            return response()->json([
                'message' => 'Codigo enviado',
            ], 201);
        } else {
            return response()->json($response->json(),400);
        }
        
    }

    public function verifyNumber(Request $request) {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required | integer | exists:codigos,codigo',
        ], [
            'codigo.required' => 'El codigo es requerido',
            'codigo.integer' => 'El codigo debe ser un número entero',
            'codigo.exist' => 'El codigo es incorrecto',
        ]);
        if($validator->fails()) {
            return response()->json(["errores" => $validator->errors()], 400);
        }
        $user = User::find($request->user);
        $user->active = true;
        $user->save();
        return response()->json("Usuario verificado", 200);
    }
    
}
