<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remittance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'remittance_code',
        'pin_code',
        'sender_id',
        'sender_type',
        'sender_name',
        'sender_phone',
        'recipient_name',
        'recipient_phone',
        'recipient_id_type',
        'recipient_id_number',
        'amount',
        'fee',
        'agent_commission',
        'currency',
        'status',
        'paid_by_agent_id',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'agent_commission' => 'decimal:2',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship with the Sender (User)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship with the Payout Agent
     */
    public function payingAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'paid_by_agent_id');
    }

    /**
     * Generate unique 8-digit remittance code
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'REM' . mt_rand(10000000, 99999999);
        } while (self::where('remittance_code', $code)->exists());

        return $code;
    }

    /**
     * Generate 4-digit secret PIN code
     */
    public static function generatePinCode(): string
    {
        return str_pad((string) mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if remittance is pending and can be paid
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if remittance is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if remittance is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
