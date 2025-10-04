<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

   public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'currency' => 'required|in:NIO,USD,EUR', // Agregar este campo
    ]);

    // El user_id se generará automáticamente en el modelo
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'currency' => $validated['currency'],
        'date_format' => 'd/m/Y', // Valor por defecto
        'dark_mode' => false,
        'notifications' => true,
    ]);

    // Resto del código de registro...
    Auth::login($user);

    return redirect()->route('dashboard')
        ->with('success', '¡Registro exitoso! Tu ID de usuario es: ' . $user->user_id);
}
}