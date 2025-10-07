<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $query = Transaction::with([
                    'category' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    },
                    'account' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                ])
                ->where('user_id', $user->id)
                ->latest();

            // Filtro por tipo (income/expense)
            if ($request->filled('transaction_type') && in_array($request->transaction_type, ['income', 'expense'])) {
                $query->whereHas('category', fn($q) => $q->where('type', $request->transaction_type));
            }

            // Filtro por día específico
            if ($request->filled('day')) {
                $query->whereDay('date', $request->day);
            }

            // Filtro por mes específico
            if ($request->filled('month')) {
                $query->whereMonth('date', $request->month);
            }

            // Filtro por rango de fechas
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $transactions = $query->paginate(15)->withQueryString();
            $accounts = Account::where('user_id', $user->id)->get();
            $categories = Category::where('user_id', $user->id)->get();
            $userCurrency = $user->currency ?? 'NIO';

            return view('transacciones', compact('transactions', 'accounts', 'categories', 'userCurrency'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar transacciones');
        }
    }

    public function create(Request $request)
    {
        try {
            $user = $request->user();
            return view('transactions.create', [
                'categories' => Category::where('user_id', $user->id)->get(),
                'accounts' => Account::where('user_id', $user->id)->get()
            ]);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar formulario');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => ['required', Rule::in(['income', 'expense'])],
                'amount' => 'required|numeric|min:0.01|max:9999999.99',
                'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->where('user_id', $request->user()->id)
                ],
                'account_id' => [
                    'required',
                    Rule::exists('accounts', 'id')->where('user_id', $request->user()->id)
                ],
                'date' => 'required|date|before_or_equal:today',
                'description' => 'nullable|string|max:255'
            ]);

            return DB::transaction(function () use ($validated, $request) {
                $user = $request->user();

                $account = Account::where('id', $validated['account_id'])
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                if ($account->type === 'savings') {
                    return back()->with('error', '❌ No se pueden realizar transacciones en la cuenta de ahorros')->withInput();
                }

                // Convertir el monto ingresado (moneda del usuario) a NIO para almacenar y ajustar saldos
                $userCurrency = $user->currency ?? 'NIO';
                $amountUser = (float) $validated['amount'];
                $amountNIO = \App\Service\CurrencyService::convert($amountUser, $userCurrency, 'NIO');

                $transaction = new Transaction();
                $transaction->type = $validated['type'];
                $transaction->amount = $amountNIO;
                $transaction->category_id = $validated['category_id'];
                $transaction->account_id = $validated['account_id'];
                $transaction->date = $validated['date'];
                $transaction->description = $validated['description'] ?? null;
                $transaction->user_id = $user->id;
                $transaction->save();

                $account->balance += $validated['type'] === 'income'
                    ? $amountNIO
                    : -$amountNIO;
                $account->save();

                return redirect()->route('transacciones.index')
                    ->with('success', '✅ Transacción creada exitosamente');
            });

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al crear transacción')->withInput();
        }
    }

    public function edit(Request $request, Transaction $transaction)
    {
        try {
            if ($request->user()->id !== $transaction->user_id) {
                abort(403, 'No tienes permiso para editar esta transacción');
            }
            
            return view('transactions.edit', [
                'transaction' => $transaction,
                'categories' => Category::where('user_id', $request->user()->id)->get(),
                'accounts' => Account::where('user_id', $request->user()->id)->get()
            ]);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar transacción para editar');
        }
    }

    public function update(Request $request, Transaction $transaction)
    {
        try {
            if ($request->user()->id !== $transaction->user_id) {
                abort(403, 'No tienes permiso para actualizar esta transacción');
            }

            $validated = $request->validate([
                'type' => ['required', Rule::in(['income', 'expense'])],
                'amount' => 'required|numeric|min:0.01|max:9999999.99',
                'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->where('user_id', $request->user()->id)
                ],
                'account_id' => [
                    'required',
                    Rule::exists('accounts', 'id')->where('user_id', $request->user()->id)
                ],
                'date' => 'required|date|before_or_equal:today',
                'description' => 'nullable|string|max:255'
            ]);

            return DB::transaction(function () use ($validated, $transaction) {
                $oldAccount = $transaction->account;
                $oldAmount = $transaction->amount; // NIO
                $oldType = $transaction->type;

                if ($oldAccount->id !== $validated['account_id']) {
                    $newAccount = Account::where('id', $validated['account_id'])
                        ->where('user_id', $transaction->user_id)
                        ->firstOrFail();

                    if ($newAccount->type === 'savings') {
                        return back()->with('error', '❌ No se pueden realizar transacciones en la cuenta de ahorros')->withInput();
                    }
                }

                // Convertir el nuevo monto a NIO (desde la moneda del usuario)
                $userCurrency = auth()->user()->currency ?? 'NIO';
                $newAmountNIO = \App\Service\CurrencyService::convert((float) $validated['amount'], $userCurrency, 'NIO');

                // Actualizar transacción con monto en NIO y demás campos
                $transaction->type = $validated['type'];
                $transaction->amount = $newAmountNIO;
                $transaction->category_id = $validated['category_id'];
                $transaction->account_id = $validated['account_id'];
                $transaction->date = $validated['date'];
                $transaction->description = $validated['description'] ?? null;
                $transaction->save();

                if ($oldAccount->id !== $validated['account_id']) {
                    // Revertir en la cuenta anterior
                    $oldAccount->balance -= $oldType === 'income' ? $oldAmount : -$oldAmount;
                    $oldAccount->save();

                    // Aplicar en la nueva cuenta
                    $newAccount = Account::where('id', $validated['account_id'])
                        ->where('user_id', $transaction->user_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $newAccount->balance += $validated['type'] === 'income'
                        ? $newAmountNIO
                        : -$newAmountNIO;
                    $newAccount->save();
                } else {
                    $difference = ($validated['type'] === 'income' ? $newAmountNIO : -$newAmountNIO)
                                - ($oldType === 'income' ? $oldAmount : -$oldAmount);
                    $oldAccount->balance += $difference;
                    $oldAccount->save();
                }

                return redirect()->route('transacciones.index')
                    ->with('success', '✅ Transacción actualizada exitosamente');
            });

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar transacción')->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            
            if ($request->user()->id !== $transaction->user_id) {
                return back()->with('error', '❌ No tienes permiso para eliminar esta transacción');
            }

            DB::transaction(function () use ($transaction) {
                $account = $transaction->account;
                
                if ($account) {
                    if ($transaction->type === 'income') {
                        $account->balance -= $transaction->amount;
                    } else {
                        $account->balance += $transaction->amount;
                    }
                    $account->save();
                }

                $transaction->delete();
            });

            return back()->with('success', '✅ Transacción eliminada correctamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar transacción');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $transaction = Transaction::with(['category', 'account'])
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            return view('transactions.show', compact('transaction'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Transacción no encontrada');
        }
    }
}