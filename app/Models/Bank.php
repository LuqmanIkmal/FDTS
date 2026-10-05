<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    protected $primaryKey = 'bank_id';

    public $timestamps = false;

    protected $fillable = ['bank_name', 'bank_phone', 'bank_address'];

    public function fixedDeposits(): HasMany
    {
        return $this->hasMany(FixedDeposit::class, 'bank_id', 'bank_id');
    }
}
