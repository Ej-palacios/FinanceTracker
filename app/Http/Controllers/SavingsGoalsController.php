<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavingsGoalsController extends Controller
{
    /**
     * Display a listing of the user's savings goals.
     */
    public function index()
    {
        $user = Auth::user();
        $goals = $user->savingsGoals()
            ->orderBy('status', 'asc')
            ->orderBy('target_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $userCurrency = $user->currency ?? 'NIO';
        $currencySymbol = \App\Service\CurrencyService::getSymbol($userCurrency);

        return view('savings-goals.index', compact('goals', 'userCurrency', 'currencySymbol'));
    }

    /**
     * Show the form for creating a new savings goal.
     */
    public function create()
    {
        $userCurrency = Auth::user()->currency ?? 'NIO';
        $currencySymbol = \App\Service\CurrencyService::getSymbol($userCurrency);

        return view('savings-goals.create', compact('userCurrency', 'currencySymbol'));
    }

    /**
     * Store a newly created savings goal in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'target_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $goal = $user->savingsGoals()->create([
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0,
            'target_date' => $validated['target_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        return redirect()->route('savings-goals.show', $goal)
            ->with('success', '✅ Meta de ahorro creada exitosamente');
    }

    /**
     * Display the specified savings goal.
     */
    public function show(SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $userCurrency = $user->currency ?? 'NIO';
        $currencySymbol = \App\Service\CurrencyService::getSymbol($userCurrency);

        return view('savings-goals.show', compact('savingsGoal', 'userCurrency', 'currencySymbol'));
    }

    /**
     * Show the form for editing the specified savings goal.
     */
    public function edit(SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $userCurrency = Auth::user()->currency ?? 'NIO';
        $currencySymbol = \App\Service\CurrencyService::getSymbol($userCurrency);

        return view('savings-goals.edit', compact('savingsGoal', 'userCurrency', 'currencySymbol'));
    }

    /**
     * Update the specified savings goal in storage.
     */
    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'target_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,completed,paused',
        ]);

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.show', $savingsGoal)
            ->with('success', '✅ Meta de ahorro actualizada exitosamente');
    }

    /**
     * Remove the specified savings goal from storage.
     */
    public function destroy(SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')
            ->with('success', '✅ Meta de ahorro eliminada exitosamente');
    }

    /**
     * Add savings to a goal.
     */
    public function addSavings(Request $request, SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $validated['amount'];

        // Check if user has enough in savings account
        $user = Auth::user();
        $savingsAccount = $user->getSavingsAccount();

        if ($savingsAccount->balance < $amount) {
            return back()->with('error', '❌ Saldo insuficiente en la cuenta de ahorros');
        }

        DB::transaction(function () use ($savingsGoal, $savingsAccount, $amount) {
            $savingsGoal->addSavings($amount);
            $savingsAccount->balance -= $amount;
            $savingsAccount->save();
        });

        // Check for milestone notifications
        $this->checkMilestoneNotifications($savingsGoal);

        return back()->with('success', '✅ Ahorros agregados a la meta exitosamente');
    }

    /**
     * Pause or resume a goal.
     */
    public function toggleStatus(SavingsGoal $savingsGoal)
    {
        // Ensure user owns this goal
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($savingsGoal->status === SavingsGoal::STATUS_ACTIVE) {
            $savingsGoal->status = SavingsGoal::STATUS_PAUSED;
            $message = '✅ Meta pausada exitosamente';
        } else {
            $savingsGoal->status = SavingsGoal::STATUS_ACTIVE;
            $message = '✅ Meta reactivada exitosamente';
        }

        $savingsGoal->save();

        return back()->with('success', $message);
    }

    /**
     * Check and send milestone notifications.
     */
    private function checkMilestoneNotifications(SavingsGoal $savingsGoal)
    {
        $progress = $savingsGoal->progress_percentage;

        // Check for completion
        if ($savingsGoal->isCompleted()) {
            // Send completion notification
            $savingsGoal->user->notifications()->create([
                'type' => 'goal_completed',
                'title' => '¡Meta de ahorro completada!',
                'message' => "Felicidades! Has completado tu meta '{$savingsGoal->name}'",
                'data' => ['goal_id' => $savingsGoal->id],
            ]);
            return;
        }

        // Check for milestones
        $lastMilestone = $savingsGoal->user->notifications()
            ->where('type', 'goal_milestone')
            ->where('data->goal_id', $savingsGoal->id)
            ->max('data->milestone') ?? 0;

        foreach (SavingsGoal::MILESTONES as $milestone) {
            if ($progress >= $milestone && $milestone > $lastMilestone) {
                $savingsGoal->user->notifications()->create([
                    'type' => 'goal_milestone',
                    'title' => '¡Hito alcanzado!',
                    'message' => "Has alcanzado el {$milestone}% de tu meta '{$savingsGoal->name}'",
                    'data' => ['goal_id' => $savingsGoal->id, 'milestone' => $milestone],
                ]);
                break; // Only send the next milestone
            }
        }
    }
}
