<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeFd extends Model
{
    protected $table = 'free_fds';

    protected $primaryKey = 'fd_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['fd_id', 'auto_renewal_status', 'withdrawable_status'];
}
