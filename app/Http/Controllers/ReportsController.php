<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $userCurrency = $user->currency ?? 'NIO';
            $currencySymbol = match($userCurrency) {
                'USD' => '$',
                'EUR' => '€',
                default => 'C$'
            };
            
            $now = Carbon::now();
            
            // 1. Datos para gráfico mensual
            $monthlyData = [];
            $currentYear = $now->year;
            
            for ($month = 1; $month <= $now->month; $month++) {
                $date = Carbon::create($currentYear, $month, 1);
                $monthKey = $date->format('Y-m');
                $monthName = $date->translatedFormat('M Y');
                
                $income = Transaction::where('user_id', $user->id)
                    ->whereHas('category', fn($q) => $q->where('type', 'income'))
                    ->whereMonth('date', $month)
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                $expenses = Transaction::where('user_id', $user->id)
                    ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->whereMonth('date', $month)
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                $monthlyData[$monthKey] = [
                    'label' => $monthName,
                    'income' => $this->convertAmount($income, $userCurrency),
                    'expenses' => $this->convertAmount($expenses, $userCurrency)
                ];
            }
            
            ksort($monthlyData);
            
            // 2. Datos para gráfico circular de gastos
            $expenseFilter = $request->input('expense_filter', 'current_month');
            $expenseSelectedMonth = $request->input('expense_month');
            if ($expenseFilter === 'month' && empty($expenseSelectedMonth)) {
                $expenseFilter = 'current_month';
            }
            $expenseData = $this->getExpenseData($user, $expenseFilter, $expenseSelectedMonth, $userCurrency);
            
            // 3. Datos para gráfico de barras de categorías
            $categoryFilter = $request->input('category_filter', 'current_month');
            $categorySelectedMonth = $request->input('category_month');
            if ($categoryFilter === 'month' && empty($categorySelectedMonth)) {
                $categoryFilter = 'current_month';
            }
            $categoryData = $this->getCategoryData($user, $categoryFilter, $categorySelectedMonth, $userCurrency);
            
            // Meses disponibles para filtros
            $availableMonths = Transaction::where('user_id', $user->id)
                ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month')
                ->orderBy('month', 'desc')
                ->pluck('month')
                ->map(fn($month) => [
                    'value' => $month,
                    'label' => Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y')
                ]);
            
            return view('reportes', [
                'monthlyLabels' => array_column($monthlyData, 'label'),
                'monthlyIncome' => array_column($monthlyData, 'income'),
                'monthlyExpenses' => array_column($monthlyData, 'expenses'),
                'expenseChart' => [
                    'labels' => $expenseData['labels'],
                    'data' => $expenseData['data'],
                    'colors' => $this->generateChartColors(count($expenseData['labels'])),
                    'isEmpty' => empty($expenseData['data'])
                ],
                'categoryChart' => [
                    'income' => $categoryData['income'],
                    'expenses' => $categoryData['expenses'],
                    'isEmpty' => empty($categoryData['income']['data']) && empty($categoryData['expenses']['data'])
                ],
                'currentMonth' => $now->format('Y-m'),
                'currencySymbol' => $currencySymbol, 
                'userCurrency' => $userCurrency,
            ]);

        } catch (\Exception $e) {
            Log::error('Error en ReportsController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al generar reportes');
        }
    }

    protected function convertAmount($amount, $currency)
    {
        // Tasas estáticas (Nicaragua)
        $rates = [
            'NIO' => 1,
            'USD' => 36.72, // 1 USD = 36.72 NIO
            'EUR' => 40.50, // 1 EUR = 40.50 NIO
        ];

        if ($currency === 'NIO') {
            return round($amount, 2);
        }

        if ($currency === 'USD') {
            return round($amount / $rates['USD'], 2);
        }

        if ($currency === 'EUR') {
            return round($amount / $rates['EUR'], 2);
        }

        return round($amount, 2);
    }

    protected function generateChartColors($count)
    {
        $baseColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];
        
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }
        
        return $colors;
    }

    protected function getExpenseData($user, $filter, $selectedMonth, $currency)
    {
        $transactionQuery = Transaction::where('user_id', $user->id)
            ->whereHas('category', fn($q) => $q->where('type', 'expense'));
        
        $now = now();
        
        if ($filter === 'week') {
            $transactionQuery->whereBetween('date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
        } elseif ($filter === 'current_month') {
            $transactionQuery->whereMonth('date', $now->month)->whereYear('date', $now->year);
        } elseif ($filter === 'month' && $selectedMonth) {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
            $transactionQuery->whereMonth('date', $monthDate->month)->whereYear('date', $monthDate->year);
        }

        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->with(['transactions' => function($q) use ($transactionQuery) {
                $q->whereIn('id', $transactionQuery->pluck('id'));
            }])
            ->get()
            ->map(fn($category) => [
                'name' => $category->name,
                'amount' => $this->convertAmount($category->transactions->sum('amount'), $currency)
            ])
            ->filter(fn($item) => $item['amount'] > 0);
        
        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('amount')->toArray()
        ];
    }

    protected function getCategoryData($user, $filter, $selectedMonth, $currency)
    {
        $now = now();

        $incomeQuery = Transaction::where('user_id', $user->id)
            ->whereHas('category', fn($q) => $q->where('type', 'income'));
        $expenseQuery = Transaction::where('user_id', $user->id)
            ->whereHas('category', fn($q) => $q->where('type', 'expense'));

        $this->applyFilters($incomeQuery, $filter, $selectedMonth, $now);
        $this->applyFilters($expenseQuery, $filter, $selectedMonth, $now);

        $incomeData = $incomeQuery->with('category')->get()->groupBy('category_id')
            ->map(fn($transactions) => [
                'name' => $transactions->first()->category->name,
                'amount' => $this->convertAmount($transactions->sum('amount'), $currency)
            ])->sortByDesc('amount')->take(10);

        $expenseData = $expenseQuery->with('category')->get()->groupBy('category_id')
            ->map(fn($transactions) => [
                'name' => $transactions->first()->category->name,
                'amount' => $this->convertAmount($transactions->sum('amount'), $currency)
            ])->sortByDesc('amount')->take(10);

        return [
            'income' => [
                'labels' => $incomeData->pluck('name')->toArray(),
                'data' => $incomeData->pluck('amount')->toArray(),
                'colors' => $this->generateChartColors($incomeData->count())
            ],
            'expenses' => [
                'labels' => $expenseData->pluck('name')->toArray(),
                'data' => $expenseData->pluck('amount')->toArray(),
                'colors' => $this->generateChartColors($expenseData->count())
            ]
        ];
    }

    protected function applyFilters($query, $filter, $selectedMonth, $now)
    {
        if ($filter === 'week') {
            $query->whereBetween('date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
        } elseif ($filter === 'current_month') {
            $query->whereMonth('date', $now->month)->whereYear('date', $now->year);
        } elseif ($filter === 'month' && $selectedMonth) {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
            $query->whereMonth('date', $monthDate->month)->whereYear('date', $monthDate->year);
        }
    }

    public function updateCharts(Request $request)
    {
        try {
            $user = Auth::user();
            $userCurrency = $user->currency ?? 'NIO';
            
            $expenseData = $this->getExpenseData(
                $user,
                $request->input('expense_filter', 'current_month'),
                $request->input('expense_month'),
                $userCurrency
            );
            
            $categoryData = $this->getCategoryData(
                $user,
                $request->input('category_filter', 'current_month'),
                $request->input('category_month'),
                $userCurrency
            );
            
            return response()->json([
                'expenseChart' => [
                    'labels' => $expenseData['labels'],
                    'data' => $expenseData['data'],
                    'colors' => $this->generateChartColors(count($expenseData['labels'])),
                    'isEmpty' => empty($expenseData['data'])
                ],
                'categoryChart' => [
                    'income' => $categoryData['income'],
                    'expenses' => $categoryData['expenses'],
                    'isEmpty' => empty($categoryData['income']['data']) && empty($categoryData['expenses']['data'])
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en ReportsController@updateCharts: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar gráficos'], 500);
        }
    }
}