<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Jobs\ProcessPhone;
use App\Jobs\ProcessVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Codigo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $data = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'model' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->nombre,
                    'phone_number' => $user->numero_telefono,
                ],
                'actions' => [
                    'delete_url' => route('users.destroy', $user),
                    'update_url' => route('users.update', $user),
                ],
            ];
        });
        return response()->json($data);
    }
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required | string | max:50',
            'email' => 'required | email | max:50 | unique:users',
            'password' => 'required | string | min:8 | max:50',
            'phone_number'=> 'required | string | size:10',
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
            'phone_number.required' => 'El número de teléfono es requerido',
            'phone_number.string' => 'El número de teléfono debe ser una cadena de caracteres',
            'phone_number.size' => 'El número de teléfono debe tener 10 caracteres',
        ]);
        if($validator->fails()) return response()->json(["errores" => $validator->errors()], 400);

            $user =  new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role_id = 3;
            $user->numero_telefono = $request->phone_number;
            $user->save();
            $url = URL::temporarySignedRoute('verify', now()->addHours(30), ['user' => $user->id]);
            // Mail::to($request->email)->send(new SendMail($user, $url));
            $verificationUrl = env("FRONT_URL") . "/verify/?url=" . urlencode($url);
            ProcessMail::dispatch($user, $verificationUrl)
                        ->delay(now()->addSeconds(20))
                        ->onQueue('emails');
        return response()->json(["mensaje" => "Usuario registrado correctamente, espera nuestro mensaje"], 201);

    }





    public function login(Request $request) {

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

        $user = User::where("email", $request->email)->where("active", true)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
           return response()->json(["mensaje" => "Credenciales incorrectas"], 401);

        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(["token" => $token]);


    }

    public function info(Request $request) {
        $user = $request->user();
        return response()->json(
            [
                "name" => $user-> name,
                "email" => $user-> email,
                "role" => $user-> role-> name,
                "password" => $user-> password,
                "phone" => $user-> numero_telefono,
                "active" => $user-> active,
            ]);
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

        $random4Digits = rand(1000, 9999);
        $url = URL::temporarySignedRoute('verifynumber', now()->addMinutes(30), ['user' => $user->id]);
        ProcessPhone::dispatch($user, $random4Digits)->delay(now()->addSeconds(5))->onQueue('phones');
        $codigo = new Codigo;
        $codigo->codigo = $random4Digits;
        $user->codigo()->save($codigo);
        return response()->json($url);


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
