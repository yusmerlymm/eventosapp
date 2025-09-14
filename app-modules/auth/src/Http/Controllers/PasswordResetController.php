<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Modules\Auth\Models\User;

class PasswordResetController
{
    public function sendResetLink(Request $request){
        $request->validate(['email'=>'required|email']);

        // laravel encolara el correo automaticamente solo si el worker esta corriendo
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => 'Correo de recuperacion enviado'])
                    : response()->json(['message' => 'No se pudo enviar el correo'], 422);
    }

    public function reset(Request $request){
        $request->validate([
            'email'=>'required|email',
            'token'=>'required|string',
            'password'=>'required|string|confirmed|min:8'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => 'Contraseña restablecida con éxito'])
                    : response()->json(['message' => 'No se pudo restablecer la contraseña'], 422);
    }
}
