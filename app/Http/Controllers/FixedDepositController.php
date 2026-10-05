<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\FixedDeposit;
use App\Models\FreeFd;
use App\Models\PledgeFd;
use App\Services\FixedDepositService;
use App\Services\Mailer;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Fixed Deposit pages: list, create (draft -> application form -> submit), view,
 * update (incl. withdraw / reinvest), certificate download and report.
 * Replaces FDListServlet, CreateFDServlet, SubmitFDServlet, ViewFDServlet,
 * ViewApplicationServlet, UpdateFDServlet and GenerateReportServlet.
 */
class FixedDepositController extends Controller
{
    private const ITEMS_PER_PAGE = 7;

    private const CERT_MIMES = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

    public function __construct(private FixedDepositService $fdService)
    {
    }

    // ==================== LIST ====================

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $fdList = FixedDeposit::with('bank')->orderByDesc('fd_id')->get();

        // Search across ALL records before pagination
        if ($search !== '') {
            $needle = strtolower($search);
            $fdList = $fdList->filter(fn (FixedDeposit $f) => str_contains(strtolower((string) $f->acc_number), $needle)
                || str_contains(strtolower((string) $f->bank_name), $needle)
                || str_contains(strtolower((string) $f->fd_type), $needle)
                || str_contains(strtolower((string) $f->status), $needle)
                || str_contains($this->plainNumber($f->deposit_amount), $needle)
                || str_contains((string) $f->fd_id, $needle))->values();
        }

        $totalItems = $fdList->count();
        $totalPages = (int) ceil($totalItems / self::ITEMS_PER_PAGE);
        $currentPage = (int) $request->query('page', 1);
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $startIndex = ($currentPage - 1) * self::ITEMS_PER_PAGE;
        $endIndex = min($startIndex + self::ITEMS_PER_PAGE, $totalItems);
        $pageItems = $fdList->slice($startIndex, self::ITEMS_PER_PAGE);

        return view('fd.index', compact(
            'pageItems', 'search', 'totalItems', 'totalPages', 'currentPage', 'startIndex', 'endIndex'
        ));
    }

    // ==================== CREATE: form -> draft ====================

    public function create()
    {
        $bankList = Bank::orderByDesc('bank_id')->get();

        return view('fd.create', compact('bankList'));
    }

    /** Save the form into the session draft and show the Application Form. */
    public function storeDraft(Request $request)
    {
        $previous = session('fdDraft', []);

        $draft = [
            'accountNumber' => trim((string) $request->input('accountNumber')),
            'referralNumber' => trim((string) $request->input('referralNumber')),
            'bankName' => (string) $request->input('bankName'),
            'depositAmount' => (string) $request->input('depositAmount'),
            'interestRate' => (string) $request->input('interestRate'),
            'startDate' => (string) $request->input('startDate'),
            'tenure' => (string) $request->input('tenure'),
            'maturityDate' => (string) $request->input('maturityDate'),
            'certNo' => trim((string) $request->input('fdCertificateNo')),
            'fdType' => (string) $request->input('fdType'),
            'autoRenewalStatus' => $request->input('autoRenewalStatus'),
            'withdrawableStatus' => $request->input('withdrawableStatus'),
            'pledgeValue' => $request->input('pledgeValue'),
            'collateralStatus' => $request->input('collateralStatus'),
            'reminderMaturity' => $request->input('reminderMaturity'),
            'reminderIncomplete' => $request->input('reminderIncomplete'),
            // Keep a previously uploaded certificate unless a new one is chosen
            'fdCertPath' => $previous['fdCertPath'] ?? null,
            'fdCertFileName' => $previous['fdCertFileName'] ?? null,
        ];

        $file = $request->file('fdCertificate');
        if ($file && $file->getSize() > 0) {
            if (! $file->isValid() || ! in_array($file->getMimeType(), self::CERT_MIMES, true)) {
                session(['fdDraft' => $draft]);

                return redirect()->route('fd.create')
                    ->with('error', 'Invalid file format. Only JPEG, PDF, and PNG files are accepted.');
            }
            if ($file->getSize() > 10 * 1024 * 1024) {
                session(['fdDraft' => $draft]);

                return redirect()->route('fd.create')->with('error', 'File size must be less than 10MB.');
            }
            if (! empty($draft['fdCertPath'])) {
                Storage::delete($draft['fdCertPath']);
            }
            $draft['fdCertPath'] = $file->store('fd-drafts');
            $draft['fdCertFileName'] = $file->getClientOriginalName();
        }

        // Maturity date = start date + tenure (in case the browser did not fill it)
        if ($draft['maturityDate'] === '' && $this->isDate($draft['startDate']) && ctype_digit($draft['tenure'])) {
            $draft['maturityDate'] = Carbon::parse($draft['startDate'])->addMonthsNoOverflow((int) $draft['tenure'])->toDateString();
        }

        session(['fdDraft' => $draft]);

        if ($draft['accountNumber'] === '' || $draft['bankName'] === ''
            || ! is_numeric($draft['depositAmount']) || (float) $draft['depositAmount'] <= 0
            || ! is_numeric($draft['interestRate']) || (float) $draft['interestRate'] <= 0
            || ! $this->isDate($draft['startDate']) || ! ctype_digit($draft['tenure']) || (int) $draft['tenure'] < 1
            || ! $this->isDate($draft['maturityDate']) || ! in_array($draft['fdType'], ['Free', 'Pledge'], true)) {
            return redirect()->route('fd.create')->with('error', 'Please fill in all required fields correctly.');
        }

        return redirect()->route('fd.application');
    }

    // ==================== CREATE: application form -> submit ====================

    public function application()
    {
        $draft = session('fdDraft');

        if (! $draft) {
            return redirect()->route('fd.create');
        }

        return view('fd.application', [
            'app' => $draft,
            'isViewMode' => false,
            'backUrl' => route('fd.create'),
            'bankAddress' => Bank::where('bank_name', $draft['bankName'])->value('bank_address'),
        ]);
    }

    /** Read-only Application Form for an existing FD. */
    public function applicationView(Request $request)
    {
        $fd = $this->findFd($request->query('id'));

        if (! $fd) {
            return redirect()->route('fd.list');
        }

        return view('fd.application', [
            'app' => [
                'accountNumber' => $fd->acc_number,
                'bankName' => $fd->bank_name,
                'depositAmount' => (string) $fd->deposit_amount,
                'interestRate' => $this->plainNumber($fd->interest_rate),
                'tenure' => (string) $fd->tenure,
            ],
            'isViewMode' => true,
            'backUrl' => route('fd.view', ['id' => $fd->fd_id]),
            'bankAddress' => $fd->bank?->bank_address,
        ]);
    }

    /** Save the draft FD to the database. */
    public function submit()
    {
        $draft = session('fdDraft');

        if (! $draft) {
            return redirect()->route('fd.create');
        }

        $staff = auth()->user();

        try {
            $fdType = $draft['fdType'] === 'Free' ? 'FREEFD' : 'PLEDGEFD';
            $depositAmount = (string) $draft['depositAmount'];
            $interestRate = (string) $draft['interestRate'];
            $tenure = (int) $draft['tenure'];
            $certNo = $draft['certNo'] !== '' ? $draft['certNo'] : null;
            $referral = $draft['referralNumber'] !== '' ? $draft['referralNumber'] : null;

            $duplicateError = $this->fdService->checkForDuplicates($draft['accountNumber'], $referral, $certNo);
            if ($duplicateError) {
                return redirect()->route('fd.application')->with('error', $duplicateError);
            }

            $totalProfit = FixedDeposit::maturityAmountFor($depositAmount, $interestRate, $tenure);
            $certMissing = empty($draft['fdCertPath']) || ! Storage::exists($draft['fdCertPath']);

            $fd = DB::transaction(function () use ($draft, $fdType, $depositAmount, $interestRate, $tenure, $certNo, $referral, $totalProfit, $certMissing, $staff) {
                $bankId = $this->fdService->getOrCreateBank($draft['bankName']);

                $certPath = null;
                if (! $certMissing) {
                    $certPath = 'fd-certificates/'.basename($draft['fdCertPath']);
                    Storage::move($draft['fdCertPath'], $certPath);
                }

                $fd = FixedDeposit::create([
                    'acc_number' => $draft['accountNumber'],
                    'referral_number' => $referral,
                    'deposit_amount' => $depositAmount,
                    'interest_rate' => $interestRate,
                    'start_date' => $draft['startDate'],
                    'tenure' => $tenure,
                    'maturity_date' => $draft['maturityDate'],
                    'fd_cert' => $certPath,
                    'cert_no' => $certNo,
                    'fd_type' => $fdType,
                    'status' => 'PENDING',
                    'bank_id' => $bankId,
                    'reminder_maturity' => in_array($draft['reminderMaturity'], ['Y', 'on'], true) ? 'Y' : 'N',
                    'reminder_incomplete' => in_array($draft['reminderIncomplete'], ['Y', 'on'], true) ? 'Y' : 'N',
                    'remaining_balance' => $depositAmount,
                    'total_withdrawn' => 0,
                ]);

                if ($fdType === 'FREEFD') {
                    FreeFd::create([
                        'fd_id' => $fd->fd_id,
                        'auto_renewal_status' => $draft['autoRenewalStatus'] ?: 'N',
                        'withdrawable_status' => $draft['withdrawableStatus'] ?: 'No',
                    ]);
                } else {
                    PledgeFd::create([
                        'fd_id' => $fd->fd_id,
                        'collateral_status' => $draft['collateralStatus'] ?: 'N',
                        'pledge_value' => is_numeric($draft['pledgeValue']) ? $draft['pledgeValue'] : null,
                    ]);
                }

                $this->fdService->recordCreate($fd->fd_id, $staff->staff_id, $totalProfit);

                return $fd;
            });

            $this->sendCreationReminders($draft, $staff, $certMissing, $certNo, $depositAmount, $interestRate, $tenure, $totalProfit);

            session()->forget('fdDraft');

            return redirect()->route('fd.list')->with(['fdSuccess' => true, 'newFdID' => $fd->fd_id]);
        } catch (UniqueConstraintViolationException $e) {
            return redirect()->route('fd.application')->with('error', 'Certificate Number already exists.');
        } catch (\Throwable $e) {
            Log::error('Error saving FD: '.$e->getMessage());

            return redirect()->route('fd.application')->with('error', 'Error saving to database: '.$e->getMessage());
        }
    }

    /** Reminder emails sent right after an FD is created (same as the Java version). */
    private function sendCreationReminders(array $draft, $staff, bool $certMissing, ?string $certNo,
        string $depositAmount, string $interestRate, int $tenure, string $totalProfit): void
    {
        if (! $staff->staff_email) {
            return;
        }

        $certNoMissing = $certNo === null;

        if ($draft['reminderIncomplete'] === 'Y' && ($certMissing || $certNoMissing)) {
            try {
                Mailer::send($staff->staff_email, 'Reminder: Incomplete FD Record - '.$draft['accountNumber'],
                    ReminderService::incompleteEmail($staff->staff_name, $draft['accountNumber'], $draft['bankName'],
                        'PENDING', $certNoMissing, $certMissing));
            } catch (\Throwable $e) {
                // logged by Mailer; FD is already saved
            }
        }

        if ($draft['reminderMaturity'] === 'Y') {
            try {
                Mailer::send($staff->staff_email, 'Reminder: FD Maturing in 7 Days - '.$draft['accountNumber'],
                    ReminderService::maturityEmail($staff->staff_name, $draft['accountNumber'], $draft['bankName'],
                        $depositAmount, $interestRate, $tenure, $draft['startDate'], $draft['maturityDate'], $totalProfit));
            } catch (\Throwable $e) {
                // logged by Mailer; FD is already saved
            }
        }
    }

    // ==================== VIEW ====================

    public function show(Request $request)
    {
        $fd = $this->findFd($request->query('id'));

        if (! $fd) {
            return redirect()->route('fd.list')->with('error', 'Fixed Deposit not found: '.$request->query('id'));
        }

        $autoRenewalStatus = '-';
        $withdrawableStatus = '-';
        $collateralStatus = '-';
        $pledgeValue = null;

        if ($fd->fd_type === 'FREEFD' && $fd->freeFd) {
            $autoRenewalStatus = $fd->freeFd->auto_renewal_status === 'Y' ? 'Yes' : 'No';
            $withdrawableStatus = $fd->freeFd->withdrawable_status ?? '-';
        } elseif ($fd->fd_type === 'PLEDGEFD' && $fd->pledgeFd) {
            $collateralStatus = $fd->pledgeFd->collateral_status === 'Y' ? 'Active' : 'Partial';
            $pledgeValue = $fd->pledgeFd->pledge_value;
        }

        $totalInterest = '-';
        $totalProfit = '-';
        if ($fd->deposit_amount !== null && $fd->interest_rate !== null && $fd->tenure > 0) {
            $totalInterest = number_format((float) $fd->interest_amount, 2);
            $totalProfit = number_format((float) $fd->maturity_amount, 2);
        }

        $certIsPdf = $fd->hasCertificateFile() && Storage::exists($fd->fd_cert)
            && Storage::mimeType($fd->fd_cert) === 'application/pdf';

        $createdBy = $fd->creatorTransaction?->staff?->staff_name;

        return view('fd.view', compact(
            'fd', 'autoRenewalStatus', 'withdrawableStatus', 'collateralStatus', 'pledgeValue',
            'totalInterest', 'totalProfit', 'certIsPdf', 'createdBy'
        ));
    }

    /** Serve the FD certificate file (inline image, or PDF download). */
    public function certificate(Request $request)
    {
        $fd = $this->findFd($request->query('id'));

        if (! $fd || ! $fd->hasCertificateFile() || ! Storage::exists($fd->fd_cert)) {
            abort(404);
        }

        $extension = pathinfo($fd->fd_cert, PATHINFO_EXTENSION);
        $name = 'FD_Certificate_'.$fd->fd_id.($extension ? '.'.$extension : '');

        return $request->boolean('download')
            ? Storage::download($fd->fd_cert, $name)
            : Storage::response($fd->fd_cert, $name);
    }

    // ==================== UPDATE ====================

    public function edit(Request $request)
    {
        $fd = $this->findFd($request->query('id'));

        if (! $fd) {
            return redirect()->route('fd.list');
        }

        $autoRenewalStatus = '';
        $withdrawableStatus = '';
        $collateralStatus = '';
        $pledgeValue = null;

        if ($fd->fd_type === 'FREEFD' && $fd->freeFd) {
            $autoRenewalStatus = $fd->freeFd->auto_renewal_status === 'Y' ? 'Yes' : 'No';
            $withdrawableStatus = $fd->freeFd->withdrawable_status ?? '';
        } elseif ($fd->fd_type === 'PLEDGEFD' && $fd->pledgeFd) {
            $collateralStatus = $fd->pledgeFd->collateral_status === 'Y' ? 'Active' : 'Partial';
            $pledgeValue = $fd->pledgeFd->pledge_value;
        }

        return view('fd.update', [
            'fd' => $fd,
            'bankList' => Bank::orderByDesc('bank_id')->get(),
            'autoRenewalStatus' => $autoRenewalStatus,
            'withdrawableStatus' => $withdrawableStatus,
            'collateralStatus' => $collateralStatus,
            'pledgeValue' => $pledgeValue,
            'reminderMaturity' => $fd->reminder_maturity === 'Y' ? 'on' : 'off',
            'reminderIncomplete' => $fd->reminder_incomplete === 'Y' ? 'on' : 'off',
        ]);
    }

    public function update(Request $request)
    {
        $fd = $this->findFd($request->input('fdID'));

        if (! $fd) {
            return redirect()->route('fd.list');
        }

        $backToForm = fn (string $key, string $message) => redirect()->route('fd.update', ['id' => $fd->fd_id])->with($key, $message);

        try {
            $depositAmount = (string) $request->input('depositAmount');
            $interestRate = (string) $request->input('interestRate');
            $tenure = (string) $request->input('tenure');
            $startDate = (string) $request->input('startDate');
            $maturityDate = (string) $request->input('maturityDate');
            $bankName = (string) $request->input('bankName');
            $status = strtoupper((string) $request->input('fdStatus'));
            $fdType = $request->input('fdType') === 'Free' ? 'FREEFD' : 'PLEDGEFD';
            $certNo = trim((string) $request->input('certificateNo'));
            $referral = trim((string) $request->input('referralNumber'));

            if (! is_numeric($depositAmount) || (float) $depositAmount <= 0 || ! is_numeric($interestRate) || (float) $interestRate <= 0
                || ! ctype_digit($tenure) || (int) $tenure < 1 || ! $this->isDate($startDate) || ! $this->isDate($maturityDate)
                || $bankName === '' || trim((string) $request->input('accountNumber')) === ''
                || ! in_array($status, ['PENDING', 'ONGOING', 'MATURED'], true)) {
                return $backToForm('error', 'Please fill in all required fields correctly.');
            }

            $certFile = $request->file('fdCertificate');
            if ($certFile && $certFile->getSize() > 0
                && (! $certFile->isValid() || ! in_array($certFile->getMimeType(), self::CERT_MIMES, true))) {
                return $backToForm('error', 'Invalid file format. Only JPEG, PDF, and PNG files are accepted.');
            }

            $previousStatus = $fd->status;
            $bankId = $this->fdService->getOrCreateBank($bankName);

            DB::transaction(function () use ($request, $fd, $depositAmount, $interestRate, $tenure, $startDate, $maturityDate,
                $bankId, $status, $fdType, $certNo, $referral, $certFile, $previousStatus) {
                $data = [
                    'acc_number' => trim((string) $request->input('accountNumber')),
                    'referral_number' => $referral !== '' ? $referral : null,
                    'deposit_amount' => $depositAmount,
                    'interest_rate' => $interestRate,
                    'start_date' => $startDate,
                    'tenure' => (int) $tenure,
                    'maturity_date' => $maturityDate,
                    'cert_no' => $certNo !== '' ? $certNo : null,
                    'fd_type' => $fdType,
                    'status' => $status,
                    'bank_id' => $bankId,
                    'reminder_maturity' => in_array($request->input('reminderMaturity'), ['Y', 'on'], true) ? 'Y' : 'N',
                    'reminder_incomplete' => in_array($request->input('reminderIncomplete'), ['Y', 'on'], true) ? 'Y' : 'N',
                ];

                if ($certFile && $certFile->getSize() > 0) {
                    $old = $fd->fd_cert;
                    $data['fd_cert'] = $certFile->store('fd-certificates');
                    if ($old) {
                        Storage::delete($old);
                    }
                }

                // When the FD becomes MATURED, its balance becomes the maturity amount
                if ($status === 'MATURED' && $previousStatus !== 'MATURED') {
                    $data['remaining_balance'] = FixedDeposit::maturityAmountFor($depositAmount, $interestRate, (int) $tenure);
                }

                $fd->update($data);

                if ($fdType === 'FREEFD') {
                    FreeFd::updateOrCreate(['fd_id' => $fd->fd_id], [
                        'auto_renewal_status' => $request->input('autoRenewalStatus') === 'Yes' ? 'Y' : 'N',
                        'withdrawable_status' => $request->input('withdrawableStatus'),
                    ]);
                } else {
                    $collateral = $request->input('collateralStatus');
                    $pledgeValue = $request->input('pledgeValue');
                    PledgeFd::updateOrCreate(['fd_id' => $fd->fd_id], [
                        'collateral_status' => in_array($collateral, ['Yes', 'Active'], true) ? 'Y' : 'N',
                        'pledge_value' => is_numeric($pledgeValue) ? $pledgeValue : null,
                    ]);
                }
            });

            $fd->refresh();

            // Transactions (only for matured FDs)
            $transactionType = (string) $request->input('transactionType');
            if ($status === 'MATURED' && $transactionType !== '') {
                $staffId = auth()->id();
                // transactionDate[0] = reinvest date, transactionDate[1] = withdraw date
                $dates = (array) $request->input('transactionDate', []);
                $transactionDateStr = $transactionType === 'Reinvest'
                    ? ($dates[0] ?? null)
                    : ($dates[1] ?? null);
                $transactionDateStr = $transactionDateStr ?: $this->firstFilled($dates);
                $transactionDate = $this->isDate($transactionDateStr) ? Carbon::parse($transactionDateStr) : now();
                $totalProfit = FixedDeposit::maturityAmountFor($depositAmount, $interestRate, (int) $tenure);

                if ($transactionType === 'Withdraw') {
                    $withdrawAmount = (string) $request->input('withdrawAmount');

                    if (! is_numeric($withdrawAmount) || (float) $withdrawAmount <= 0) {
                        return $backToForm('errorMessage', 'Please enter a valid withdrawal amount.');
                    }
                    $withdrawAmount = bcadd($withdrawAmount, '0', 2);

                    if ($fd->remaining_balance !== null && bccomp($withdrawAmount, (string) $fd->remaining_balance, 2) > 0) {
                        return $backToForm('errorMessage', 'Withdrawal exceeds remaining balance!');
                    }

                    if ($request->input('withdrawableStatus') === 'Partial') {
                        $halfBalance = bcdiv((string) ($fd->remaining_balance ?? '0'), '2', 2); // rounds down
                        if (bccomp($withdrawAmount, $halfBalance, 2) > 0) {
                            return $backToForm('errorMessage', "Partial withdrawal max RM {$halfBalance} (half of balance)!");
                        }
                    }

                    $this->fdService->withdraw($fd, $staffId, $transactionDate, $withdrawAmount, $totalProfit);
                } elseif ($transactionType === 'Reinvest') {
                    $newStart = (string) $request->input('newStartDate');
                    $newTenure = (string) $request->input('newTenure');
                    $newMaturity = (string) $request->input('newMaturityDate');

                    if ($this->isDate($newStart) && ctype_digit($newTenure) && (int) $newTenure > 0 && $this->isDate($newMaturity)) {
                        $newFd = $this->fdService->reinvest($fd, Carbon::parse($newStart), (int) $newTenure,
                            Carbon::parse($newMaturity), $transactionDate, $staffId);

                        session()->flash('reinvestSuccess', true);
                        session()->flash('newFdID', $newFd->fd_id);
                        session()->flash('oldFdID', $fd->fd_id);
                    }
                }
            }

            return redirect()->route('fd.list')->with(['fdUpdateSuccess' => true, 'updatedFdId' => 'FD'.$fd->fd_id]);
        } catch (UniqueConstraintViolationException $e) {
            return $backToForm('error', 'Certificate Number already exists.');
        } catch (\Throwable $e) {
            Log::error('Error updating FD: '.$e->getMessage());

            return $backToForm('error', 'Error: '.$e->getMessage());
        }
    }

    // ==================== REPORT ====================

    public function report()
    {
        $fdList = FixedDeposit::with('bank')->orderByDesc('fd_id')->get();

        $reportData = $fdList->map(fn (FixedDeposit $fd) => [
            'id' => 'FD'.$fd->fd_id,
            'accountNo' => $fd->acc_number,
            'amount' => (float) $fd->deposit_amount,
            'bank' => $fd->bank_name ?? '',
            'tenure' => $fd->tenure,
            'status' => $fd->status,
            'month' => $fd->start_date ? $fd->start_date->format('m') : '',
        ])->values();

        $bankList = Bank::orderByDesc('bank_id')->get();

        return view('fd.report', compact('reportData', 'bankList'));
    }

    // ==================== HELPERS ====================

    /** Accepts "12" or "FD12". */
    private function findFd($id): ?FixedDeposit
    {
        $clean = trim(str_replace('FD', '', (string) $id));

        return ctype_digit($clean) ? FixedDeposit::with(['bank', 'freeFd', 'pledgeFd'])->find($clean) : null;
    }

    private function isDate(?string $value): bool
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1
            && checkdate((int) substr($value, 5, 2), (int) substr($value, 8, 2), (int) substr($value, 0, 4));
    }

    private function plainNumber($value): string
    {
        if ($value === null) {
            return '';
        }
        $s = (string) $value;

        return str_contains($s, '.') ? rtrim(rtrim($s, '0'), '.') : $s;
    }

    /** The update form has two inputs named transactionDate (withdraw and reinvest); use the filled one. */
    private function firstFilled($value): ?string
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v !== null && $v !== '') {
                    return $v;
                }
            }

            return null;
        }

        return $value;
    }
}
