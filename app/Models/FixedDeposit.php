<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FixedDeposit extends Model
{
    protected $table = 'fixed_deposit_records';

    protected $primaryKey = 'fd_id';

    public $timestamps = false;

    protected $fillable = [
        'acc_number', 'referral_number', 'deposit_amount', 'interest_rate', 'start_date', 'tenure',
        'maturity_date', 'fd_cert', 'cert_no', 'fd_type', 'status', 'bank_id', 'reminder_maturity',
        'reminder_incomplete', 'previous_fd_id', 'remaining_balance', 'total_withdrawn',
    ];

    protected function casts(): array
    {
        return [
            'deposit_amount' => 'decimal:2',
            'interest_rate' => 'decimal:3',
            'start_date' => 'date',
            'maturity_date' => 'date',
            'tenure' => 'integer',
            'remaining_balance' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
        ];
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }

    public function freeFd(): HasOne
    {
        return $this->hasOne(FreeFd::class, 'fd_id', 'fd_id');
    }

    public function pledgeFd(): HasOne
    {
        return $this->hasOne(PledgeFd::class, 'fd_id', 'fd_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FdTransaction::class, 'fd_id', 'fd_id');
    }

    public function previousFd(): BelongsTo
    {
        return $this->belongsTo(FixedDeposit::class, 'previous_fd_id', 'fd_id');
    }

    public function getBankNameAttribute(): ?string
    {
        return $this->bank?->bank_name;
    }

    /**
     * Simple interest = principal * rate * months / 1200, rounded half-up to 2 decimals
     * (same formula as the Java version).
     */
    public static function interestFor($principal, $rate, int $tenure): string
    {
        $raw = bcdiv(bcmul(bcmul((string) $principal, (string) $rate, 10), (string) $tenure, 10), '1200', 10);

        return bcadd($raw, '0.005', 2); // half-up for positive amounts
    }

    /** Principal + interest. */
    public static function maturityAmountFor($principal, $rate, int $tenure): string
    {
        return bcadd((string) $principal, self::interestFor($principal, $rate, $tenure), 2);
    }

    public function getInterestAmountAttribute(): string
    {
        return self::interestFor($this->deposit_amount, $this->interest_rate, (int) $this->tenure);
    }

    public function getMaturityAmountAttribute(): string
    {
        return self::maturityAmountFor($this->deposit_amount, $this->interest_rate, (int) $this->tenure);
    }

    public function getDisplayStatusAttribute(): string
    {
        return $this->status ? ucfirst(strtolower($this->status)) : '';
    }

    public function getStatusClassAttribute(): string
    {
        return strtolower((string) $this->status);
    }

    public function isFullyWithdrawn(): bool
    {
        return $this->remaining_balance !== null && bccomp((string) $this->remaining_balance, '0', 2) === 0;
    }

    public function hasWithdrawals(): bool
    {
        return $this->total_withdrawn !== null && bccomp((string) $this->total_withdrawn, '0', 2) > 0;
    }

    public function hasCertificateFile(): bool
    {
        return ! empty($this->fd_cert);
    }

    /** Staff who created this FD (from the CREATE transaction). */
    public function creatorTransaction(): HasOne
    {
        return $this->hasOne(FdTransaction::class, 'fd_id', 'fd_id')
            ->where('transaction_type', 'CREATE')
            ->oldest('transaction_date');
    }
}
