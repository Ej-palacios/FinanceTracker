<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function profile()
    {
        try {
            $user = Auth::user();
            $mainAccount = $user->getMainAccount();
            $savingsAccount = $user->getSavingsAccount();
            $userCurrency = $user->currency ?? 'NIO';
            $currencySymbol = \App\Service\CurrencyService::getSymbol($userCurrency);

            // Convertir saldos a la moneda del usuario
            $mainBalanceConverted = \App\Service\CurrencyService::convert((float)$mainAccount->balance, 'NIO', $userCurrency);
            $savingsBalanceConverted = \App\Service\CurrencyService::convert((float)$savingsAccount->balance, 'NIO', $userCurrency);

            return view('perfil', compact('user', 'mainAccount', 'savingsAccount', 'userCurrency', 'currencySymbol', 'mainBalanceConverted', 'savingsBalanceConverted'));
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
                'dark_mode' => 'nullable',
                'notifications' => 'nullable',
            ]);

            $user->currency = $validated['currency'];
            $user->date_format = $validated['date_format'];
            // Acepta 'on', '1', true y ausente como false
            $user->dark_mode = $request->boolean('dark_mode');
            $user->notifications = $request->boolean('notifications');
            $user->save();

            return redirect()->back()->with('success', '✅ Preferencias guardadas correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al guardar preferencias');
        }
    }

    public function addSavings(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);

            $user = Auth::user();
            $main = $user->getMainAccount();
            $savings = $user->getSavingsAccount();

            if (!$main) {
                return back()->with('error', '❌ No tienes una cuenta principal');
            }

            // El usuario ingresa montos en su moneda preferida; convertir a NIO para operar en cuentas
            $userCurrency = $user->currency ?? 'NIO';
            $amountUser = (float) $validated['amount'];
            $amountNIO = \App\Service\CurrencyService::convert($amountUser, $userCurrency, 'NIO');

            if ($main->balance < $amountNIO) {
                return back()->with('error', '❌ Saldo insuficiente en la cuenta principal');
            }

            DB::transaction(function () use ($main, $savings, $amountNIO) {
                $main->balance -= $amountNIO;
                $savings->balance += $amountNIO;
                $main->save();
                $savings->save();
            });

            return back()->with('success', '✅ Ahorros agregados exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al agregar ahorros');
        }
    }

    public function releaseSavings(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);

            $user = Auth::user();
            $savings = $user->getSavingsAccount();
            $main = $user->getMainAccount();

            if (!$main) {
                return back()->with('error', '❌ No tienes una cuenta principal');
            }

            // Convertir desde moneda del usuario a NIO para operar en cuentas
            $userCurrency = $user->currency ?? 'NIO';
            $amountUser = (float) $validated['amount'];
            $amountNIO = \App\Service\CurrencyService::convert($amountUser, $userCurrency, 'NIO');

            if ($savings->balance < $amountNIO) {
                return back()->with('error', '❌ Saldo insuficiente en ahorros');
            }

            DB::transaction(function () use ($savings, $main, $amountNIO) {
                $savings->balance -= $amountNIO;
                $main->balance += $amountNIO;
                $savings->save();
                $main->save();
            });

            return back()->with('success', '✅ Ahorros liberados exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al liberar ahorros');
        }
    }
}
