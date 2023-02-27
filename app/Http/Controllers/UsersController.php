<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMail;
use App\Jobs\ProcessPhone;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    //
    public function index(){
        $users = User::all();

        return $users->map(fn ($user)=>
            [
                'id' => $user->id,
                'attributes'=>
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role->name,
                    'active' => $user->active,
                ],
                'actions'=>
                [
                    'edit_url' => URL::signedRoute('user.update ', ['user' => $user]),
                    'delete_url' => URL::signedRoute('user.destroy', ['user' => $user]),
                ]
            ]);
    }

    public function register(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'required|string|numeric',
            ]
        );
        if ($validator->fails())
            return response()->json(['error'=>$validator->errors()], 400);

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);
        $validated['role_id'] = 3;
        $user = User::create($validated);
        $url = URL::signedRoute('user.verify', ['user' => $user]);
        ProcessMail::dispatch($user, $url)->delay(now()->addSeconds(5))->onQueue('emails');
        return response()->json(['message'=>'Usuario creado satisfactoriamente, revisa tu correo electronico.'], 201);
    }

    public function verify(User $user){
        if ($user->active)
            return response()->json(['message'=>'Usuario ya verificado'], 401);
        $code = rand(1000, 9999);
        $user->code = $code;
        $user->save();
        $message = "Dark Souls APP Su codigo de verificacion es: $code";
        ProcessPhone::dispatch($user->phone, $message)->delay(now()->addSeconds(5))->onQueue('sms');
        $url = URL::signedRoute('user.verifyCode', ['user' => $user]);
        return response()->json(['message' => 'Revisa el codigo enviado a tu celular!', 'url'=> $url] );
    }

    public function verifyPhone(Request $request, User $user){
        $validator = Validator::make(
            $request->all(),
            [
                'code' => 'required|numeric',
            ]
        );
        if ($validator->fails())
            return response()->json(['error'=>$validator->errors()], 400);
        $validated = $validator->validated();
        if($validated['code'] == $user->code){
            $user->active = true;
            $user->save();
            return response()->json(['message'=>'Usuario verificado satisfactoriamente']);
        }
        return response()->json(['error'=>'Codigo incorrecto'], 400);
    }

    public function login(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]
        );
        if ($validator->fails())
            return response()->json(['error'=>$validator->errors()], 400);

        $user = User::where('email', $request->email)->first();

        if(!$user)
            return response()->json(['error'=>'Usuario no encontrado'], 404);

        if(!$user->active)
            return response()->json(['error'=>'Usuario no verificado'], 401);

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['message'=> "Inicio de sesion exitoso",'token' => $token], 201);
        }
        return response()->json(['error'=>'Contraseña incorrecta'], 400);
    }

    public function getUser(Request $request){
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'attributes'=>
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->name,
                'active' => $user->active,
            ],
            'actions'=>
            [
                'edit_url' => URL::signedRoute('user.update', ['user' => $user]),
                'delete_url' => URL::signedRoute('user.destroy', ['user' => $user]),
            ]
        ]);
    }

    public function destroy(User $user){
        $user->delete();
        return response()->json(['message'=>'Usuario eliminado satisfactoriamente'], 201);
    }

    public function update(User $user, Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'string',
                'email' => 'email|unique:users',
                'password' => 'string|min:8',
                'phone' => 'string|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }

        $user->update($validator->validated());
        return response()->json(['message'=>'Usuario actualizado satisfactoriamente'], 201);
    }
}
