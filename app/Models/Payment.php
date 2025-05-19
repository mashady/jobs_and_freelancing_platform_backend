<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'contract_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
