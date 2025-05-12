<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
