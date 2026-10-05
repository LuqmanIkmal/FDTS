<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FdTransaction extends Model
{
    protected $table = 'fixed_deposit_transactions';

    protected $primaryKey = 'transaction_id';

    public $timestamps = false;

    protected $fillable = [
        'fd_id', 'staff_id', 'transaction_date', 'calc_total_profit', 'transaction_type', 'withdraw_amount',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'calc_total_profit' => 'decimal:2',
            'withdraw_amount' => 'decimal:2',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    public function fixedDeposit(): BelongsTo
    {
        return $this->belongsTo(FixedDeposit::class, 'fd_id', 'fd_id');
    }
}
