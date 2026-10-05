<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Bank list, register and edit (replaces BankServlet and BankList.jsp's data loading).
 */
class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::orderByDesc('bank_id')->get()->map(fn (Bank $b) => [
            'id' => $b->bank_id,
            'name' => $b->bank_name ?? '',
            'phone' => $b->bank_phone ?? '',
            'address' => $b->bank_address ?? '',
        ]);

        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $name = trim((string) $request->input('bankName'));
        $address = trim((string) $request->input('bankAddress'));
        $phone = trim((string) $request->input('bankPhone'));

        $fail = fn (string $error) => back()->withInput()->with('error', $error);

        if ($name === '' || $address === '' || $phone === '') {
            return $fail('Please fill in all fields.');
        }

        if (Bank::whereRaw('LOWER(bank_name) = ?', [strtolower($name)])->exists()) {
            return $fail('This bank already exists.');
        }

        try {
            Bank::create(['bank_name' => $name, 'bank_address' => $address, 'bank_phone' => $phone]);
        } catch (\Throwable $e) {
            Log::error('Error inserting bank: '.$e->getMessage());

            return $fail('Failed to create bank. Please try again.');
        }

        return redirect()->route('banks.list', ['msg' => 'created']);
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');

        if ($id === null || trim($id) === '') {
            return redirect()->route('banks.list', ['error' => 'missing_id']);
        }
        if (! ctype_digit((string) $id)) {
            return redirect()->route('banks.list', ['error' => 'invalid_id']);
        }

        $bank = Bank::find($id);

        if (! $bank) {
            return redirect()->route('banks.list', ['error' => 'not_found']);
        }

        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request)
    {
        $id = (string) $request->input('bankId');
        $address = trim((string) $request->input('bankAddress'));
        $phone = trim((string) $request->input('bankPhone'));

        if (! ctype_digit($id)) {
            return redirect()->route('banks.list', ['error' => 'invalid_id']);
        }

        $bank = Bank::find($id);

        if (! $bank) {
            return redirect()->route('banks.list', ['error' => 'not_found']);
        }

        if ($address === '' || $phone === '') {
            return redirect()->route('banks.edit', ['id' => $id])->withInput()->with('error', 'Please fill in all fields.');
        }

        try {
            // Bank name cannot be changed
            $bank->update(['bank_address' => $address, 'bank_phone' => $phone]);
        } catch (\Throwable $e) {
            Log::error('Error updating bank: '.$e->getMessage());

            return redirect()->route('banks.edit', ['id' => $id])->withInput()
                ->with('error', 'Failed to update bank. Please try again.');
        }

        return redirect()->route('banks.list', ['msg' => 'updated', 'bankId' => $bank->bank_id]);
    }
}
