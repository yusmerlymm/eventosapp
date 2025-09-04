<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Modules\Auth\Models\User;

class EmailVerificationController
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Link de verificacion invalido'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Correo ya verificado'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Correo verificado correctamente'], 200);
    }

    public function resend(Request $request){
        if($request->user()->hasVerifiedEmail()){
            return response()->json(['message' => 'Ya verificado'], 200);
        }

        // se va a encolar automaticamente
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Correo de verificacion reenviado'], 200);
    }
}
