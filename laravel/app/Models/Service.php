<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'mobile_number',
        'network',
        'start_date',
        'end_date',
    ];

    public function serviceProduct(): HasOne
    {
        return $this->hasOne(ServiceProduct::class);
    }
}
