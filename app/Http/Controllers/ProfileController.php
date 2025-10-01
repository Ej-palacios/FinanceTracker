<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the profile settings page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('perfil', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (!empty($validatedData['new_password'])) {
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Update the user's preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
      
    public function updatePreferences(Request $request)
{
    /** @var User $user */
    $user = Auth::user();
    //$user = auth()->user();

    // Validar los datos del formulario
    $validated = $request->validate(rules: [
        'currency' => 'required|in:NIO,USD,EUR',
        'dark_mode' => 'nullable|boolean',
    ]);

    // Actualizar la moneda
    $user->currency = $validated['currency'];

    // Actualizar el modo oscuro
    $user->dark_mode = $request->has('dark_mode') ? true : false;

    $user->save();

    // Mensaje de éxito
    return redirect()->back()->with('success', 'Preferencias guardadas correctamente.');
}

  
}