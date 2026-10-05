<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    protected $table = 'staff';

    protected $primaryKey = 'staff_id';

    public $timestamps = false;

    // "Remember me" is not used by this system
    protected $rememberTokenName = '';

    protected $fillable = [
        'staff_id_prefix', 'staff_name', 'staff_phone', 'staff_address', 'staff_email',
        'password', 'staff_picture', 'staff_role', 'staff_status', 'reason', 'manager_id',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'manager_id', 'staff_id');
    }

    public function isActive(): bool
    {
        return strcasecmp((string) $this->staff_status, 'Active') === 0;
    }

    public function isSeniorFinanceManager(): bool
    {
        return strcasecmp((string) $this->staff_role, 'Senior Finance Manager') === 0;
    }

    /** e.g. FinanceE07 */
    public function getFormattedStaffIdAttribute(): string
    {
        return $this->staff_id_prefix
            ? sprintf('%s%02d', $this->staff_id_prefix, $this->staff_id)
            : (string) $this->staff_id;
    }

    /** Prefix used for the formatted staff ID, based on role. */
    public static function prefixForRole(?string $role): string
    {
        if ($role === null) {
            return 'STF';
        }

        return match (strtoupper(trim($role))) {
            'FINANCE EXECUTIVE' => 'FinanceE',
            'SENIOR FINANCE MANAGER', 'FINANCE MANAGER' => 'FinanceM',
            'SENIOR MANAGER' => 'SeniorM',
            'MANAGER' => 'Manager',
            'OFFICER' => 'Officer',
            'CLERK' => 'Clerk',
            'ADMIN', 'ADMINISTRATOR' => 'Admin',
            'ASSISTANT' => 'Assistant',
            'SUPERVISOR' => 'Supervisor',
            'DIRECTOR' => 'Director',
            default => 'Staff',
        };
    }
}
