<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => ['required','string','max:255'],
            'apellido'  => ['required','string','max:100'],
            'telefono'  => ['required','string','max:20'],
            'email'     => ['required','string','lowercase','email','max:255','unique:'.User::class],
            'password'  => ['required','confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user)); // Dispara el mail de verificación
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
