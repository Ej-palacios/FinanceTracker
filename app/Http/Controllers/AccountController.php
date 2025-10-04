<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:accounts,name,NULL,id,user_id,'.$request->user()->id,
                'type' => ['required', Rule::in(['cash', 'bank', 'credit', 'savings'])],
                'initial_balance' => 'required|numeric|min:0|max:9999999.99',
                'from_transaction' => 'sometimes|boolean'
            ]);

            $account = $request->user()->accounts()->create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'initial_balance' => $validated['initial_balance'],
                'balance' => $validated['initial_balance']
            ]);

            if ($request->from_transaction) {
                return response()->json([
                    'id' => $account->id,
                    'name' => $account->name,
                    'balance' => $account->balance,
                    'type' => $account->type
                ]);
            }

            return back()->with('success', '✅ Cuenta creada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al crear cuenta');
        }
    }

    public function update(Request $request, Account $account)
    {
        try {
            if (!Gate::allows('update', $account)) {
                abort(403, 'No tienes permiso para actualizar esta cuenta');
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('accounts')->ignore($account->id)->where('user_id', $request->user()->id)
                ],
                'type' => ['required', Rule::in(['cash', 'bank', 'credit', 'savings'])]
            ]);

            $account->update($validated);

            return back()->with('success', '✅ Cuenta actualizada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar cuenta');
        }
    }

    public function destroy(Account $account)
    {
        try {
            if (!Gate::allows('delete', $account)) {
                abort(403, 'No tienes permiso para eliminar esta cuenta');
            }

            if ($account->transactions()->exists()) {
                return back()->with('error', '❌ No puedes eliminar una cuenta con transacciones asociadas');
            }

            $account->delete();

            return back()->with('success', '✅ Cuenta eliminada correctamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar cuenta');
        }
    }

    public function getAccounts(Request $request)
    {
        try {
            $accounts = $request->user()->accounts()
                ->orderBy('name')
                ->get(['id', 'name', 'balance', 'type']);

            return response()->json($accounts);

        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}