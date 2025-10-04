<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric|min:0',
                'period' => ['required', Rule::in(['monthly', 'weekly', 'yearly'])],
            ]);

            // Ensure the category belongs to the user
            $category = $request->user()->categories()->find($validated['category_id']);
            if (!$category) {
                return back()->with('error', '❌ Categoría no encontrada');
            }

            $budget = $request->user()->budgets()->create($validated);

            return back()->with('success', '✅ Presupuesto creado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al crear presupuesto');
        }
    }

    public function update(Request $request, Budget $budget)
    {
        try {
            if (!Gate::allows('update', $budget)) {
                abort(403, 'No tienes permiso para actualizar este presupuesto');
            }

            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric|min:0',
                'period' => ['required', Rule::in(['monthly', 'weekly', 'yearly'])],
            ]);

            // Ensure the category belongs to the user
            $category = $request->user()->categories()->find($validated['category_id']);
            if (!$category) {
                return back()->with('error', '❌ Categoría no encontrada');
            }

            $budget->update($validated);

            return back()->with('success', '✅ Presupuesto actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar presupuesto');
        }
    }

    public function destroy(Budget $budget)
    {
        try {
            if (!Gate::allows('delete', $budget)) {
                abort(403, 'No tienes permiso para eliminar este presupuesto');
            }

            $budget->delete();

            return back()->with('success', '✅ Presupuesto eliminado correctamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar presupuesto');
        }
    }

    public function getBudgets(Request $request)
    {
        try {
            $budgets = $request->user()->budgets()
                ->with('category')
                ->when($request->filled('period'), function ($query) use ($request) {
                    $query->where('period', $request->period);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($budgets);

        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
