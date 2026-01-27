<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payee;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payee_id',
        'amount',
        'scheduled_for',
        'reminder_at',
        'is_paid',
        'paid_at',
        'description',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'reminder_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    /**
     * Access the payee related to the payment.
     */
    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    /**
     * Toggle the payment's completion status.
     */
    public function toggleStatus(bool $paid): void
    {
        $this->forceFill([
            'is_paid' => $paid,
            'paid_at' => $paid ? now() : null,
        ])->save();
    }
}
