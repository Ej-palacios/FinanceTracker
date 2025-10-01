<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
{
    try {
        $user = Auth::user();
        $now = Carbon::now();

        // Moneda del usuario
        $userCurrency = $user->currency ?? 'NIO';

        // Tasas estáticas de ejemplo (1 NIO = 0.028 USD, 0.026 EUR)
        $exchangeRates = [
            'NIO' => 1,
            'USD' => 0.028,
            'EUR' => 0.026,
        ];

        $rate = $exchangeRates[$userCurrency] ?? 1;

        // Símbolo según moneda
        $currencySymbol = match ($userCurrency) {
            'NIO' => 'C$',
            'USD' => '$',
            'EUR' => '€',
            default => 'C$',
        };

        // --- Montos convertidos ---
        $currentBalanceNIO = Account::where('user_id', $user->id)->sum('balance');
        $currentBalance = $currentBalanceNIO * $rate;

        $monthlyIncomeNIO = Transaction::where('user_id', $user->id)
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');
        $monthlyIncome = $monthlyIncomeNIO * $rate;

        $monthlyExpensesNIO = Transaction::where('user_id', $user->id)
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');
        $monthlyExpenses = $monthlyExpensesNIO * $rate;

        // --- Transacciones recientes ---
        $recentTransactions = Transaction::with(['category', 'account'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // --- Datos para gráficos (últimos 6 meses) ---
        $monthlySummary = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthName = $date->format('M Y');

            $income = Transaction::where('user_id', $user->id)
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount') * $rate;

            $expenses = Transaction::where('user_id', $user->id)
                ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount') * $rate;

            $monthlySummary[$monthName] = [
                'income' => $income,
                'expenses' => $expenses,
            ];
        }

        return view('dashboard', compact(
            'currentBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'recentTransactions',
            'monthlySummary',
            'currencySymbol'
        ));

    } catch (\Exception $e) {
        Log::error('Error en DashboardController@index: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar el dashboard');
    }
}


}