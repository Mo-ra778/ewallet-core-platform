<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'buy_rate',
        'sell_rate',
        'custom_fee_percent',
        'min_exchange_amount',
        'max_exchange_amount',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:6',
            'buy_rate' => 'decimal:6',
            'sell_rate' => 'decimal:6',
            'custom_fee_percent' => 'decimal:2',
            'min_exchange_amount' => 'decimal:2',
            'max_exchange_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get active exchange rate pair record
     */
    public static function getPair(string $from, string $to): ?self
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        return self::where('from_currency', $from)
            ->where('to_currency', $to)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get effective fee percent for this pair (uses custom fee if set, otherwise system default)
     */
    public static function getFeePercent(string $from, string $to): float
    {
        $pair = self::getPair($from, $to);
        if ($pair && $pair->custom_fee_percent !== null) {
            return (float) $pair->custom_fee_percent;
        }

        return SystemSetting::getFloat('exchange_fee_percent', 0.25);
    }

    /**
     * Get active exchange rate between two currencies
     */
    public static function getRate(string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $direct = self::where('from_currency', $from)
            ->where('to_currency', $to)
            ->where('is_active', true)
            ->first();

        if ($direct) {
            return (float) $direct->rate;
        }

        // Check reverse rate fallback
        $reverse = self::where('from_currency', $to)
            ->where('to_currency', $from)
            ->where('is_active', true)
            ->first();

        if ($reverse && (float) $reverse->rate > 0) {
            return round(1 / (float) $reverse->rate, 6);
        }

        return null;
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert(float $amount, string $from, string $to): ?float
    {
        $rate = self::getRate($from, $to);
        if ($rate === null) {
            return null;
        }

        return round($amount * $rate, 2);
    }
}
