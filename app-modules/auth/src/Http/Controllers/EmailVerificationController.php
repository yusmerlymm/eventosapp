<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Str;
use Modules\Auth\Models\User;
use Modules\Auth\Models\EmailVerificationToken;

class EmailVerificationController
{
    /**
     * Reenvía el correo de verificación con token temporal.
     */
    public function resend(Request $request)
    {
        $tokenName = $request->user()->currentAccessToken()->name ?? '';

        if($tokenName !== 'pre-verification'){
            return response()->json(['message' => 'Token no autorizado para esta accion.'], 403);
        }

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Ya verificado'], 409);
        }

        // Se encola automáticamente con token temporal
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado'], 200);
    }

    /**
     * Consume el token temporal desde el frontend y genera el access_token real.
     */
    public function consume(Request $request)
    {
        $request->validate(['token' => 'required']);

        $record = EmailVerificationToken::where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return response()->json(['message' => 'Token inválido o expirado.'], 422);
        }

        $user = $record->user;

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Eliminar el token para evitar reuso
        $record->delete();

        // invalidar solo el token temporal
        $user->tokens()->delete();

        // Generar access_token real
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Correo verificado correctamente.',
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}