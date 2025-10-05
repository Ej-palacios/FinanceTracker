<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id', // ID único de 8 dígitos
        'name',
        'email',
        'password',
        'currency',
        'date_format',
        'dark_mode',
        'notifications'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dark_mode' => 'boolean',
        'notifications' => 'boolean',
    ];

    // Boot method para generar ID único automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = self::generateBankUserId();
            }
        });
    }

    // Generar ID único de 8 dígitos al estilo bancario
    public static function generateBankUserId()
    {
        do {
            // Generar número de 8 dígitos que no empiece con 0
            $userId = mt_rand(10000000, 99999999);
        } while (self::where('user_id', $userId)->exists());

        return (string) $userId;
    }

    // Relaciones existentes...
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function hasSettings(): bool
    {
        return !is_null($this->currency) && !is_null($this->date_format);
    }

// Agrega al modelo User
public function sentExchanges()
{
    return $this->hasMany(ExchangeRequest::class, 'from_user_id');
}

public function receivedExchanges()
{
    return $this->hasMany(ExchangeRequest::class, 'to_user_id');
}

public function exchanges()
{
    return $this->hasMany(ExchangeRequest::class, 'from_user_id')
                ->orWhere('to_user_id', $this->id);
}

// Scope para búsqueda (si no lo tienes)
public function scopeSearch($query, $search)
{
    return $query->where(function($q) use ($search) {
        $q->where('user_id', 'LIKE', "%{$search}%")
          ->orWhere('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%");
    });
}

    public function notifications()
{
    return $this->hasMany(Notification::class);
}

public function unreadNotifications()
{
    return $this->notifications()->unread();
}
}