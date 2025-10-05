<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Service\CurrencyService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();

            // Moneda del usuario
            $userCurrency = $user->currency ?? 'NIO';
            $currencySymbol = CurrencyService::getSymbol($userCurrency);

            // Montos en NIO (base de datos)
            $currentBalanceNIO = Account::where('user_id', $user->id)->sum('balance');
            $monthlyIncomeNIO = Transaction::where('user_id', $user->id)
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->sum('amount');
            $monthlyExpensesNIO = Transaction::where('user_id', $user->id)
                ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->sum('amount');

            // Convertir a moneda del usuario
            $currentBalance = CurrencyService::convert($currentBalanceNIO, 'NIO', $userCurrency);
            $monthlyIncome = CurrencyService::convert($monthlyIncomeNIO, 'NIO', $userCurrency);
            $monthlyExpenses = CurrencyService::convert($monthlyExpensesNIO, 'NIO', $userCurrency);

            // Transacciones recientes
            $recentTransactions = Transaction::with(['category', 'account'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            // Datos para gráficos (últimos 6 meses)
            $monthlySummary = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i);
                $monthName = $date->format('M Y');

                $incomeNIO = Transaction::where('user_id', $user->id)
                    ->whereHas('category', fn($q) => $q->where('type', 'income'))
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount');

                $expensesNIO = Transaction::where('user_id', $user->id)
                    ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount');

                $monthlySummary[$monthName] = [
                    'income' => CurrencyService::convert($incomeNIO, 'NIO', $userCurrency),
                    'expenses' => CurrencyService::convert($expensesNIO, 'NIO', $userCurrency),
                ];
            }

            return view('dashboard', compact(
                'currentBalance',
                'monthlyIncome',
                'monthlyExpenses',
                'recentTransactions',
                'monthlySummary',
                'currencySymbol',
                'userCurrency'
            ));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar el dashboard');
        }
    }
}