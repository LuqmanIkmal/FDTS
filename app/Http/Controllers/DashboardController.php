<?php

namespace App\Http\Controllers;

use App\Models\FixedDeposit;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Load every FD once; the page filters and aggregates in the browser,
     * so changing a filter is instant and needs no page reload.
     */
    public function index()
    {
        $fdData = [];
        $dataLoadFailed = false;

        try {
            $fdData = FixedDeposit::with('bank')->get()->map(fn (FixedDeposit $f) => [
                'id' => $f->fd_id,
                'acc' => $f->acc_number,
                'deposit' => $f->deposit_amount !== null ? (float) $f->deposit_amount : null,
                'rate' => $f->interest_rate !== null ? (float) $f->interest_rate : null,
                'start' => $f->start_date?->toDateString(),
                'tenure' => $f->tenure,
                'maturity' => $f->maturity_date?->toDateString(),
                'type' => $f->fd_type,
                'status' => $f->status,
                'remaining' => $f->remaining_balance !== null ? (float) $f->remaining_balance : null,
                'withdrawn' => $f->total_withdrawn !== null ? (float) $f->total_withdrawn : null,
                'bank' => $f->bank_name,
            ])->all();
        } catch (\Throwable $e) {
            Log::error('Dashboard data load failed: '.$e->getMessage());
            $dataLoadFailed = true;
        }

        return view('dashboard', compact('fdData', 'dataLoadFailed'));
    }
}
