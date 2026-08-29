<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agent extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'full_name',
        'phone',
        'password_hash',
        'balance',
        'balance_yer',
        'balance_sar',
        'balance_usd',
        'balance_eur',
        'status',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'balance_yer' => 'decimal:2',
            'balance_sar' => 'decimal:2',
            'balance_usd' => 'decimal:2',
            'balance_eur' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get balance for a specific currency
     */
    public function getCurrencyBalance(string $currency = 'YER'): float
    {
        $currency = strtoupper($currency);
        return match ($currency) {
            'SAR' => (float) $this->balance_sar,
            'USD' => (float) $this->balance_usd,
            'EUR' => (float) $this->balance_eur,
            default => (float) ($this->balance_yer > 0 ? $this->balance_yer : $this->balance),
        };
    }

    /**
     * Get all multi-currency balances as an associative array
     */
    public function getAllBalances(): array
    {
        return [
            'YER' => (float) ($this->balance_yer > 0 ? $this->balance_yer : $this->balance),
            'SAR' => (float) $this->balance_sar,
            'USD' => (float) $this->balance_usd,
            'EUR' => (float) $this->balance_eur,
        ];
    }

    /**
     * Check if agent has sufficient funds in specified currency
     */
    public function hasSufficientBalance(float $amount, string $currency = 'YER'): bool
    {
        return $this->getCurrencyBalance($currency) >= $amount;
    }

    /**
     * Decrement balance for a specific currency
     */
    public function decrementCurrency(string $currency, float $amount): void
    {
        $currency = strtoupper($currency);
        $column = match ($currency) {
            'SAR' => 'balance_sar',
            'USD' => 'balance_usd',
            'EUR' => 'balance_eur',
            default => 'balance_yer',
        };

        $new = round(max(0, (float) ($this->{$column} ?? 0) - $amount), 2);
        $updates = [$column => $new];
        if ($column === 'balance_yer') {
            $updates['balance'] = $new;
            $this->balance = $new;
        }
        $this->{$column} = $new;
        $this->update($updates);
    }

    /**
     * Increment balance for a specific currency
     */
    public function incrementCurrency(string $currency, float $amount): void
    {
        $currency = strtoupper($currency);
        $column = match ($currency) {
            'SAR' => 'balance_sar',
            'USD' => 'balance_usd',
            'EUR' => 'balance_eur',
            default => 'balance_yer',
        };

        $new = round((float) ($this->{$column} ?? 0) + $amount, 2);
        $updates = [$column => $new];
        if ($column === 'balance_yer') {
            $updates['balance'] = $new;
            $this->balance = $new;
        }
        $this->{$column} = $new;
        $this->update($updates);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id')
            ->where('recipient_type', 'agent');
    }

    public function paidRemittances(): HasMany
    {
        return $this->hasMany(Remittance::class, 'paid_by_agent_id');
    }
}

