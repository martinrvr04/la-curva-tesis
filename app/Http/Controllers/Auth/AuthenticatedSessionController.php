<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
//use App\Providers\RouteServiceProvider;


class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autentica con las credenciales de Breeze
        $request->authenticate();

        // Regenera la sesión por seguridad
        $request->session()->regenerate();

        // Si NO está verificado, lo mandamos a verificar y reenviamos link
        if (!$request->user()->hasVerifiedEmail()) {
            // Reenviar correo (opcional: comentar si no lo quieres automático)
            $request->user()->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('status', 'Te enviamos un enlace de verificación a tu correo. Verifica tu email para continuar.');
        }

        // Si está verificado, sigue a su destino
return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}