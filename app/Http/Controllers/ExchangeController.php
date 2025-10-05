<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Events\ExchangeRequestCreated;
use App\Events\ExchangeApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExchangeController extends Controller
{
    // Tasas de cambio estáticas
    private $exchangeRates = [
        'NIO' => ['USD' => 0.027, 'EUR' => 0.024],
        'USD' => ['NIO' => 36.72, 'EUR' => 0.92],
        'EUR' => ['NIO' => 41.50, 'USD' => 1.08]
    ];

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $sentRequests = ExchangeRequest::with('toUser')
                ->where('from_user_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'sent_page');

            $receivedRequests = ExchangeRequest::with('fromUser')
                ->where('to_user_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'received_page');

            $users = User::where('id', '!=', $user->id)->get();

            return view('exchanges.index', compact('sentRequests', 'receivedRequests', 'users'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar intercambios: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $user = $request->user();
            $search = $request->get('search', '');
            $users = collect();
            
            if ($search) {
                $users = User::where('id', '!=', $user->id)
                    ->search($search)
                    ->get(['id', 'user_id', 'name', 'email', 'currency']);
            }
            
            return view('exchanges.create', compact('users', 'search'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar formulario de intercambio');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'to_user_id' => 'required|exists:users,id',
                'from_amount' => 'required|numeric|min:0.01|max:10000',
                'from_currency' => 'required|in:NIO,USD,EUR',
                'notes' => 'nullable|string|max:500'
            ]);

            return DB::transaction(function () use ($validated, $request) {
                /** @var User $user */
                $user = $request->user();
                /** @var User $toUser */
                $toUser = User::find($validated['to_user_id']);

                // Verificar que no sea el mismo usuario
                if ($user->id == $validated['to_user_id']) {
                    return back()->with('error', '❌ No puedes hacer una transferencia a ti mismo');
                }

                // Para transferencias directas, usar la misma moneda y monto
                $amount = $validated['from_amount'];
                $currency = $validated['from_currency'];

                // Verificar saldo del usuario que envía
                $fromUserAccount = $this->getOrCreateAccount($user, $currency);
                if ($fromUserAccount->balance < $amount) {
                    return back()->with('error', '❌ Saldo insuficiente para realizar la transferencia');
                }

                // 1. Usuario que RECIBE (to_user) - INGRESO
                $toUserAccount = $this->getOrCreateAccount($toUser, $currency);

                $toTransaction = Transaction::create([
                    'user_id' => $toUser->id,
                    'account_id' => $toUserAccount->id,
                    'category_id' => $this->getOrCreateCategory($toUser, 'income'),
                    'type' => 'income',
                    'amount' => $amount,
                    'date' => now(),
                    'description' => "Transferencia recibida de {$user->name}" . ($validated['notes'] ? " - {$validated['notes']}" : "")
                ]);

                // SUMAR al que recibe
                $toUserAccount->balance += $amount;
                $toUserAccount->save();

                // 2. Usuario que ENVÍA (from_user) - GASTO
                $fromTransaction = Transaction::create([
                    'user_id' => $user->id,
                    'account_id' => $fromUserAccount->id,
                    'category_id' => $this->getOrCreateCategory($user, 'expense'),
                    'type' => 'expense',
                    'amount' => $amount,
                    'date' => now(),
                    'description' => "Transferencia enviada a {$toUser->name}" . ($validated['notes'] ? " - {$validated['notes']}" : "")
                ]);

                // RESTAR al que envía
                $fromUserAccount->balance -= $amount;
                $fromUserAccount->save();

                // Crear registro de la transferencia completada (opcional, para historial)
                $exchangeRequest = ExchangeRequest::create([
                    'from_user_id' => $user->id,
                    'to_user_id' => $validated['to_user_id'],
                    'from_currency' => $currency,
                    'to_currency' => $currency,
                    'from_amount' => $amount,
                    'to_amount' => $amount,
                    'exchange_rate' => 1,
                    'status' => ExchangeRequest::STATUS_COMPLETED,
                    'transaction_number' => ExchangeRequest::generateTransactionNumber(),
                    'notes' => $validated['notes'],
                    'completed_at' => now(),
                    'from_transaction_id' => $fromTransaction->id,
                    'to_transaction_id' => $toTransaction->id
                ]);

                // Disparar evento para notificaciones (usar el mismo evento de aprobación)
                event(new ExchangeApproved($exchangeRequest, $user, $toUser));

                return redirect()->route('deposits.index')
                    ->with('success', "✅ Transferencia realizada correctamente! Se transfirieron {$amount} {$currency}");

            });

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al realizar la transferencia: ' . $e->getMessage())->withInput();
        }
    }

    public function approve(Request $request, ExchangeRequest $exchange)
    {
        try {
            // Verificar permisos
            if ($exchange->to_user_id !== $request->user()->id) {
                return back()->with('error', '❌ No tienes permiso para aprobar este depósito');
            }

            // Verificar estado
            if ($exchange->status !== ExchangeRequest::STATUS_PENDING) {
                return back()->with('error', '⚠️ Esta solicitud ya fue procesada');
            }

            return DB::transaction(function () use ($exchange) {
                // 1. Usuario que RECIBE (to_user) - INGRESO
                $toUserAccount = $this->getOrCreateAccount($exchange->toUser, $exchange->to_currency);

                $toTransaction = Transaction::create([
                    'user_id' => $exchange->toUser->id,
                    'account_id' => $toUserAccount->id,
                    'category_id' => $this->getOrCreateCategory($exchange->toUser, 'income'),
                    'type' => 'income',
                    'amount' => $exchange->to_amount,
                    'date' => now(),
                    'description' => "Depósito recibido de {$exchange->fromUser->name}"
                ]);

                // SUMAR al que recibe
                $toUserAccount->balance += $exchange->to_amount;
                $toUserAccount->save();

                // 2. Usuario que ENVÍA (from_user) - GASTO
                $fromUserAccount = $this->getOrCreateAccount($exchange->fromUser, $exchange->from_currency);

                $fromTransaction = Transaction::create([
                    'user_id' => $exchange->fromUser->id,
                    'account_id' => $fromUserAccount->id,
                    'category_id' => $this->getOrCreateCategory($exchange->fromUser, 'expense'),
                    'type' => 'expense',
                    'amount' => $exchange->from_amount,
                    'date' => now(),
                    'description' => "Depósito enviado a {$exchange->toUser->name}"
                ]);

                // RESTAR al que envía
                $fromUserAccount->balance -= $exchange->from_amount;
                $fromUserAccount->save();

                // 3. Actualizar depósito
                $exchange->update([
                    'status' => ExchangeRequest::STATUS_COMPLETED,
                    'completed_at' => now(),
                    'from_transaction_id' => $fromTransaction->id,
                    'to_transaction_id' => $toTransaction->id
                ]);

                // Disparar evento para notificaciones
                event(new ExchangeApproved($exchange, $exchange->fromUser, $exchange->toUser));

                return back()->with('success',
                    "✅ Depósito completado exitosamente! Se transfirieron {$exchange->from_amount} {$exchange->from_currency}"
                );
            });

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al aprobar depósito: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ExchangeRequest $exchange)
    {
        try {
            if ($exchange->to_user_id !== $request->user()->id) {
                return back()->with('error', '❌ No tienes permiso para rechazar este depósito');
            }

            $exchange->update(['status' => ExchangeRequest::STATUS_REJECTED]);

            return back()->with('success', '✅ Depósito rechazado correctamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al rechazar depósito');
        }
    }

    public function show(ExchangeRequest $exchange)
    {
        try {
            $exchange->load(['fromUser', 'toUser']);
            return view('exchanges.show', compact('exchange'));

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al cargar intercambio');
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $user = $request->user();
            $search = $request->get('q');

            if (empty($search) || strlen($search) < 2) {
                return response()->json([]);
            }

            $users = User::where('id', '!=', $user->id)
                ->search($search)
                ->limit(10)
                ->get(['id', 'user_id', 'name', 'email', 'currency'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'currency' => $user->currency,
                        'display_text' => "{$user->name} (ID: {$user->user_id}) - {$user->email} - Moneda: {$user->currency}"
                    ];
                });

            return response()->json($users);

        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function calculateExchange(Request $request)
    {
        try {
            $fromAmount = $request->input('from_amount');
            $fromCurrency = $request->input('from_currency');
            $toCurrency = $request->input('to_currency');

            $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);
            $toAmount = $fromAmount * $exchangeRate;

            return response()->json([
                'to_amount' => number_format($toAmount, 2),
                'exchange_rate' => number_format($exchangeRate, 4),
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Error al calcular depósito'], 500);
        }
    }

    public function calculateDeposit(Request $request)
    {
        // Alias for calculateExchange to keep route consistent with deposits
        return $this->calculateExchange($request);
    }

    private function getExchangeRate($fromCurrency, $toCurrency)
    {
        return $this->exchangeRates[$fromCurrency][$toCurrency] ?? 1;
    }

    private function getOrCreateAccount(User $user, $currency)
    {
        // Buscar cualquier cuenta del usuario
        $account = $user->accounts()->first();
        
        if (!$account) {
            // Crear cuenta si no existe
            $account = $user->accounts()->create([
                'name' => 'Cuenta Principal',
                'type' => 'cash',
                'initial_balance' => 1000,
                'balance' => 1000,
                'currency' => $currency
            ]);
        }
        
        return $account;
    }

    private function getOrCreateCategory(User $user, $type)
    {
        $category = $user->categories()
            ->where('type', $type)
            ->where('name', 'Depósitos')
            ->first();

        if (!$category) {
            $category = $user->categories()->create([
                'name' => 'Depósitos',
                'type' => $type
            ]);
        }

        return $category->id;
    }
}