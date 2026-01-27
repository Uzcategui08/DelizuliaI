<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Payee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
        'contact_info',
        'notes',
    ];

    /**
     * Retrieve the payments associated with the payee.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
