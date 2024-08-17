<?php

namespace App\Http\Controllers\Auth\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //validar los datos de entrada
        $request->validate([
            'email'=>'required|email',
            'password'=> 'required'
        ]);

        // intentar autenticacion
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $token=$user->createToken('auth-token')->plainTextToken;

            $data=[
                'user'=> $user,
                'token'=> $token,
            ];

            return ResponseHelper::success($data, 'Login exitoso');
        }
        //Responder con error si las credenciales no son correctas
        return ResponseHelper::error('Credenciales incorrectas', 'Email o contraseña incorrecots',401);
    }
    public function forgotPassword(Request $request)
    {
        //Validar el email
        $request->validate(['email'=>'required|email',]);

        //Verificar si el usuario existe
        $user=DB:: table ('users')->where('email', $request->emil)->first();

        if(!$user){
            return ResponseHelper::error('Email no encontrado', null, 404);
        }

        //Generar el token de reset
        $token= Str::random(60);

        $code = mt_rand(100000, 999999);

        //Guardar el token en la base de datos
        DB::table ('password_reset_tokens')->updateOrInsert(
            [ 'email'=>$request->email],
            [
                'email'=> $request->email,
                'token'=>bcrypt($token),
                'code'=> $code,
                'used'=> false,
                'created_at'=>now(),
            ]

            );

            Mail::to($request->email)->send(new SendCodeResetPassword($code));

            return ResponseHelper::success(['token'=>$token],'Codigo de reestablecimiento enviado');
    }
    public function verifyCodePassword(Request $request)
    {
        $request->validate([
            'code'=>'required|string|exists>password_reset_tokens',
            'tokens' => 'required|string'
        ]);

        $passwordReset=DB::table('password_reset_tokens')
        -> where('code', $request->code)
        ->first();


        if($passwordReset->created_at>now()->addHour()){
            return ResponseHelper::error('Su codigo ya ha expirado solicite otro', 'Inicie de nuevo el proceso ya que ha tardado demasiado.');
        }

        if($passwordReset->used==true){
            return ResponseHelper:: error ('Su codigo ya ha ido usado, solicite otro.', 'Inicie de nuevo el proceso para tener un codigo no usado.');
        }

        DB::table('password_reset_tokens')
        ->where('code', $request->code)
        ->update(['used'=>true]);

        $passwordReset-> token = $request->token;

        return ResponseHelper::success($passwordReset, 'Su codigo es valido');
    }
    
    public function resetPassword(Request $Request)
    {
        $request->validate([
            'token'=>'required',
            'email'=> 'required|email',
            'password'=>'required|min:8|confirmed',
        ]);

        $status=Password::reset(
            $request->only('email','password', 'password_confirmation','token'),
            function($user,$password) use($request){
                $user->forceFill(['password'=>bcrypt($password)]->save());
            }
        );
        if($status === Password::PASSWORD_RESET){
            return ResponseHelper::succes(null,($status));
        }

        return ResponseHelper::error(($status), null, 400);
    }
}
