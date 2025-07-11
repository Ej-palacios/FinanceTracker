<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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
            // Aplicar filtros
            if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
                $query->whereHas('category', fn($q) => $q->where('type', $request->type));
            }

            if ($request->filled('account_id')) {
                $query->where('account_id', $request->account_id);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $transactions = $query->paginate(15)->withQueryString();
            $accounts = Account::where('user_id', $user->id)->get();
            $categories = Category::where('user_id', $user->id)->get();

            return view('transacciones', compact('transactions', 'accounts', 'categories'));

        } catch (\Exception $e) {
            Log::error('Error en TransactionsController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar transacciones');
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
            Log::error('Error en TransactionsController@create: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar formulario');
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
                
                $transaction = new Transaction($validated);
                $transaction->user_id = $user->id;
                $transaction->save();
                
                $account = Account::where('id', $validated['account_id'])
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                    
                $account->balance += $validated['type'] === 'income' 
                    ? $validated['amount'] 
                    : -$validated['amount'];
                $account->save();

                return redirect()->route('transacciones.index')
                    ->with('success', 'Transacción creada exitosamente');
            });

        } catch (\Exception $e) {
            Log::error('Error en TransactionsController@store: ' . $e->getMessage());
            return back()->with('error', 'Error al crear transacción')->withInput();
        }
    }

    public function edit(Request $request, Transaction $transaction)
    {
        try {
            if ($request->user()->id !== $transaction->user_id) {
                abort(403, 'No tienes permiso para editar esta transacción');
            }
            
            return view('transactions.create', [
                'transaction' => $transaction,
                'categories' => Category::where('user_id', $request->user()->id)->get(),
                'accounts' => Account::where('user_id', $request->user()->id)->get(),
                'editMode' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error en TransactionsController@edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar transacción para editar');
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
                $oldAmount = $transaction->amount;
                $oldType = $transaction->type;

                $transaction->update($validated);
                
                if ($oldAccount->id !== $validated['account_id']) {
                    $oldAccount->balance -= $oldType === 'income' ? $oldAmount : -$oldAmount;
                    $oldAccount->save();
                    
                    $newAccount = Account::where('id', $validated['account_id'])
                        ->where('user_id', $transaction->user_id)
                        ->lockForUpdate()
                        ->firstOrFail();
                        
                    $newAccount->balance += $validated['type'] === 'income' 
                        ? $validated['amount'] 
                        : -$validated['amount'];
                    $newAccount->save();
                } else {
                    $difference = ($validated['type'] === 'income' ? $validated['amount'] : -$validated['amount']) 
                                - ($oldType === 'income' ? $oldAmount : -$oldAmount);
                    $oldAccount->balance += $difference;
                    $oldAccount->save();
                }

                return redirect()->route('transacciones.index')
                    ->with('success', 'Transacción actualizada exitosamente');
            });

        } catch (\Exception $e) {
            Log::error('Error en TransactionsController@update: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar transacción')->withInput();
        }
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        try {
            if ($request->user()->id !== $transaction->user_id) {
                abort(403, 'No tienes permiso para eliminar esta transacción');
            }

            return DB::transaction(function () use ($transaction) {
                $account = $transaction->account;
                $account->balance -= $transaction->type === 'income' 
                    ? $transaction->amount 
                    : -$transaction->amount;
                $account->save();

                $transaction->delete();

                return back()->with('success', 'Transacción eliminada correctamente');
            });

        } catch (\Exception $e) {
            Log::error('Error en TransactionsController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar transacción');
        }
    }
}