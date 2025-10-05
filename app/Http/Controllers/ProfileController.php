<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        try {
            $user = Auth::user();
            return view('perfil', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar perfil');
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->getKey(),
                'current_password' => 'nullable|required_with:new_password|current_password',
                'new_password' => 'nullable|min:8|confirmed',
            ]);

            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            if (!empty($validatedData['new_password'])) {
                $user->password = Hash::make($validatedData['new_password']);
            }

            $user->save();

            return back()->with('success', '✅ Perfil actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar perfil');
        }
    }

    public function updatePreferences(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'currency' => 'required|in:NIO,USD,EUR',
                'date_format' => 'required|in:d/m/Y,m/d/Y,Y-m-d',
                'dark_mode' => 'nullable|boolean',
                'notifications' => 'nullable|boolean',
            ]);

            $user->currency = $validated['currency'];
            $user->date_format = $validated['date_format'];
            $user->dark_mode = $request->has('dark_mode') ? true : false;
            $user->notifications = $request->has('notifications') ? true : false;
            $user->save();

            return redirect()->back()->with('success', '✅ Preferencias guardadas correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al guardar preferencias');
        }
    }
}