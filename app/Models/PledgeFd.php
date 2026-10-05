<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PledgeFd extends Model
{
    protected $table = 'pledge_fds';

    protected $primaryKey = 'fd_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['fd_id', 'collateral_status', 'pledge_value'];

    protected function casts(): array
    {
        return ['pledge_value' => 'decimal:2'];
    }
}
