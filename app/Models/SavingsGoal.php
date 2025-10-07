<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $status
 * @property string|null $description
 * @property string $target_amount
 * @property string $current_amount
 * @property \Illuminate\Support\Carbon|null $target_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read float $progress_percentage
 * @property-read float $remaining_amount
 * @property-read int|null $days_remaining
 * @property-read int|null $next_milestone
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SavingsGoal active()
 * @method static \Illuminate\Database\Eloquent\Builder|SavingsGoal completed()
 * @method static \Illuminate\Database\Eloquent\Builder|SavingsGoal overdue()
 * @mixin \Eloquent
 */
class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'description',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAUSED = 'paused';

    // Milestone percentages
    const MILESTONES = [25, 50, 75, 100];

    /**
     * Get the user that owns the savings goal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0.0;
        }

        return min(100.0, floatval($this->current_amount) / floatval($this->target_amount) * 100.0);
    }

    /**
     * Get the remaining amount needed to reach the goal.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Check if the goal is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED || $this->current_amount >= $this->target_amount;
    }

    /**
     * Check if the goal is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the goal is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->target_date && \Illuminate\Support\Carbon::parse($this->target_date)->isPast() && !$this->isCompleted();
    }

    /**
     * Get the days remaining until target date.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->target_date) {
            return null;
        }

        return now()->diffInDays($this->target_date, false);
    }

    /**
     * Get the next milestone percentage.
     */
    public function getNextMilestoneAttribute(): ?int
    {
        $currentProgress = $this->progress_percentage;

        foreach (self::MILESTONES as $milestone) {
            if ($currentProgress < $milestone) {
                return $milestone;
            }
        }

        return null;
    }

    /**
     * Add amount to current savings.
     */
    public function addSavings(float $amount): void
    {
        $this->current_amount += $amount;

        if ($this->current_amount >= $this->target_amount) {
            $this->status = self::STATUS_COMPLETED;
        }

        $this->save();
    }

    /**
     * Scope for active goals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for completed goals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for overdue goals.
     */
    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
                    ->where('status', self::STATUS_ACTIVE)
                    ->whereRaw('current_amount < target_amount');
    }
}
