<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,'.$request->user()->id,
                'type' => ['required', Rule::in(['income', 'expense'])],
                'from_transaction' => 'sometimes|boolean'
            ]);

            $category = $request->user()->categories()->create($validated);

            if ($request->from_transaction) {
                return response()->json([
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type
                ]);
            }

            return back()->with('success', '✅ Categoría creada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al crear categoría');
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            if (!Gate::allows('update', $category)) {
                abort(403, 'No tienes permiso para actualizar esta categoría');
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($category->id)->where('user_id', $request->user()->id)
                ],
                'type' => ['required', Rule::in(['income', 'expense'])]
            ]);

            $category->update($validated);

            return back()->with('success', '✅ Categoría actualizada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar categoría');
        }
    }

    public function destroy(Category $category)
    {
        try {
            if (!Gate::allows('delete', $category)) {
                abort(403, 'No tienes permiso para eliminar esta categoría');
            }

            DB::transaction(function () use ($category) {
                if ($category->transactions()->exists()) {
                    return back()->with('error', '❌ No puedes eliminar una categoría con transacciones asociadas');
                }

                $category->delete();
            });

            return back()->with('success', '✅ Categoría eliminada correctamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar categoría');
        }
    }

    public function getCategories(Request $request)
    {
        try {
            $categories = $request->user()->categories()
                ->when($request->filled('type'), function ($query) use ($request) {
                    $query->where('type', $request->type);
                })
                ->orderBy('name')
                ->get();

            return response()->json($categories);

        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}