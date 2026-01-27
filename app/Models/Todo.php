<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_at',
        'reminder_at',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'reminder_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Mark the todo as completed and set the timestamp.
     */
    public function markAsCompleted(): void
    {
        $this->forceFill([
            'is_completed' => true,
            'completed_at' => now(),
        ])->save();
    }

    /**
     * Reset the completion state for the todo.
     */
    public function markAsPending(): void
    {
        $this->forceFill([
            'is_completed' => false,
            'completed_at' => null,
        ])->save();
    }
}
