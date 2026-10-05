<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\FdTransaction;
use App\Models\FixedDeposit;
use App\Models\FreeFd;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FixedDepositFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function manager(): Staff
    {
        return Staff::create([
            'staff_id_prefix' => 'FinanceM',
            'staff_name' => 'Manager',
            'staff_phone' => '0123456789',
            'staff_address' => 'Johor',
            'staff_email' => 'manager@test.com',
            'password' => 'abc12345',
            'staff_role' => 'Senior Finance Manager',
            'staff_status' => 'Active',
        ]);
    }

    private function executive(Staff $manager): Staff
    {
        return Staff::create([
            'staff_id_prefix' => 'FinanceE',
            'staff_name' => 'Executive',
            'staff_phone' => '0111111111',
            'staff_address' => 'Johor',
            'staff_email' => 'exec@test.com',
            'password' => 'exec1234',
            'staff_role' => 'Finance Executive',
            'staff_status' => 'Active',
            'manager_id' => $manager->staff_id,
        ]);
    }

    private function maturedFreeFd(string $withdrawable = 'Full', string $autoRenewal = 'N'): FixedDeposit
    {
        $bank = Bank::create(['bank_name' => 'Maybank']);
        $fd = FixedDeposit::create([
            'acc_number' => '1234567890',
            'deposit_amount' => '10000',
            'interest_rate' => '3.5',
            'start_date' => '2026-01-01',
            'tenure' => 3,
            'maturity_date' => '2026-04-01',
            'fd_type' => 'FREEFD',
            'status' => 'MATURED',
            'bank_id' => $bank->bank_id,
            'remaining_balance' => '10087.50',
            'total_withdrawn' => 0,
        ]);
        FreeFd::create(['fd_id' => $fd->fd_id, 'auto_renewal_status' => $autoRenewal, 'withdrawable_status' => $withdrawable]);

        return $fd;
    }

    private function updatePayload(FixedDeposit $fd, array $extra = []): array
    {
        return array_merge([
            'fdID' => 'FD'.$fd->fd_id,
            'accountNumber' => $fd->acc_number,
            'bankName' => 'Maybank',
            'depositAmount' => '10000',
            'interestRate' => '3.5',
            'startDate' => '2026-01-01',
            'tenure' => '3',
            'maturityDate' => '2026-04-01',
            'fdType' => 'Free',
            'fdStatus' => 'Matured',
            'autoRenewalStatus' => 'No',
            'withdrawableStatus' => 'Full',
        ], $extra);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
        $this->get(route('signup'))->assertOk();
    }

    public function test_sign_up_hashes_password_and_allows_login(): void
    {
        $this->post(route('signup.submit'), [
            'name' => 'Nor Azlina',
            'phone' => '0123456789',
            'email' => 'Manager@Test.com',
            'password' => 'abc12345',
            'confirmPassword' => 'abc12345',
            'address' => 'Johor Bahru',
            'role' => 'Senior Finance Manager',
            'profilePicture' => UploadedFile::fake()->image('me.png'),
        ])->assertRedirect(route('login', ['signup' => 'success']));

        $staff = Staff::firstOrFail();
        $this->assertSame('manager@test.com', $staff->staff_email);
        $this->assertSame('FinanceM01', $staff->formatted_staff_id);
        $this->assertNotSame('abc12345', $staff->password);

        $this->post(route('login.submit'), ['email' => 'manager@test.com', 'password' => 'abc12345'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($staff);
    }

    public function test_inactive_staff_cannot_log_in(): void
    {
        $this->manager()->update(['staff_status' => 'Inactive']);

        $this->post(route('login.submit'), ['email' => 'manager@test.com', 'password' => 'abc12345'])
            ->assertSessionHas('error', 'Your account is not active. Please contact admin.');
        $this->assertGuest();
    }

    public function test_bank_and_user_pages_are_for_senior_finance_managers_only(): void
    {
        $manager = $this->manager();
        $exec = $this->executive($manager);

        $this->actingAs($exec)->get(route('banks.list'))->assertRedirect(route('dashboard'));
        $this->actingAs($exec)->get(route('users.list'))->assertRedirect(route('dashboard'));
        $this->actingAs($manager)->get(route('banks.list'))->assertOk();
        $this->actingAs($manager)->get(route('users.list'))->assertOk()->assertSee('FinanceE02');
    }

    public function test_create_fd_through_draft_and_application_form(): void
    {
        $manager = $this->manager();
        Bank::create(['bank_name' => 'Maybank']);

        $this->actingAs($manager)->post(route('fd.create.submit'), [
            'accountNumber' => '1234567890',
            'bankName' => 'Maybank',
            'depositAmount' => '10000',
            'interestRate' => '3.5',
            'startDate' => '2026-10-01',
            'tenure' => '3',
            'maturityDate' => '2027-01-01',
            'fdCertificateNo' => 'CERT-001',
            'fdType' => 'Free',
            'autoRenewalStatus' => 'Y',
            'withdrawableStatus' => 'Partial',
            'fdCertificate' => UploadedFile::fake()->create('cert.pdf', 10, 'application/pdf'),
        ])->assertRedirect(route('fd.application'));

        $this->get(route('fd.application'))->assertOk()->assertSee('Maybank');
        $this->post(route('fd.submit'))->assertRedirect(route('fd.list'));

        $fd = FixedDeposit::firstOrFail();
        $this->assertSame('PENDING', $fd->status);
        $this->assertSame('10000.00', $fd->remaining_balance);
        $this->assertSame('87.50', $fd->interest_amount);
        $this->assertTrue(Storage::exists($fd->fd_cert));
        $this->assertSame('Y', $fd->freeFd->auto_renewal_status);
        $this->assertSame('10087.50', FdTransaction::where('transaction_type', 'CREATE')->value('calc_total_profit'));

        // Same account number again is rejected
        $this->post(route('fd.create.submit'), [
            'accountNumber' => '1234567890', 'bankName' => 'Maybank', 'depositAmount' => '1', 'interestRate' => '1',
            'startDate' => '2026-10-01', 'tenure' => '1', 'maturityDate' => '2026-11-01', 'fdType' => 'Free',
        ]);
        $this->post(route('fd.submit'))->assertSessionHas('error', 'Account Number 1234567890 already exists.');
    }

    public function test_marking_matured_sets_balance_to_maturity_amount(): void
    {
        $manager = $this->manager();
        $fd = $this->maturedFreeFd();
        $fd->update(['status' => 'ONGOING', 'remaining_balance' => '10000']);

        $this->actingAs($manager)->post(route('fd.update.submit'), $this->updatePayload($fd))
            ->assertRedirect(route('fd.list'));

        $this->assertSame('10087.50', $fd->fresh()->remaining_balance);
    }

    public function test_partial_withdrawal_is_limited_to_half_of_balance(): void
    {
        $manager = $this->manager();
        $fd = $this->maturedFreeFd('Partial');

        $this->actingAs($manager)->post(route('fd.update.submit'), $this->updatePayload($fd, [
            'withdrawableStatus' => 'Partial', 'transactionType' => 'Withdraw',
            'transactionDate' => ['', '2026-04-05'], 'withdrawAmount' => '6000',
        ]))->assertSessionHas('errorMessage', 'Partial withdrawal max RM 5043.75 (half of balance)!');

        $this->post(route('fd.update.submit'), $this->updatePayload($fd, [
            'withdrawableStatus' => 'Partial', 'transactionType' => 'Withdraw',
            'transactionDate' => ['', '2026-04-05'], 'withdrawAmount' => '1000.50',
        ]))->assertRedirect(route('fd.list'));

        $fd->refresh();
        $this->assertSame('9087.00', $fd->remaining_balance);
        $this->assertSame('1000.50', $fd->total_withdrawn);
        $this->assertSame('2026-04-05', FdTransaction::where('transaction_type', 'WITHDRAW')->first()->transaction_date->toDateString());
    }

    public function test_reinvest_creates_linked_fd(): void
    {
        $manager = $this->manager();
        $fd = $this->maturedFreeFd();

        $this->actingAs($manager)->post(route('fd.update.submit'), $this->updatePayload($fd, [
            'transactionType' => 'Reinvest', 'transactionDate' => ['2026-04-02', ''],
            'newStartDate' => '2026-04-02', 'newTenure' => '6', 'newMaturityDate' => '2026-10-02',
        ]))->assertRedirect(route('fd.list'));

        $new = FixedDeposit::where('previous_fd_id', $fd->fd_id)->firstOrFail();
        $this->assertSame('ONGOING', $new->status);
        $this->assertSame('10087.50', $new->deposit_amount);
        $this->assertSame('Full', $new->freeFd->withdrawable_status);
    }

    public function test_auto_renew_command_renews_matured_free_fds(): void
    {
        $fd = $this->maturedFreeFd('Full', 'Y');
        $fd->update(['status' => 'ONGOING', 'maturity_date' => today()->subDay()->toDateString()]);

        $this->artisan('fd:auto-renew')->assertSuccessful();

        $this->assertSame('MATURED', $fd->fresh()->status);
        $new = FixedDeposit::where('previous_fd_id', $fd->fd_id)->firstOrFail();
        $this->assertSame(today()->addDay()->toDateString(), $new->start_date->toDateString());
        $this->assertNull(FdTransaction::where('fd_id', $new->fd_id)->value('staff_id'));
    }

    public function test_forgot_password_flow(): void
    {
        $this->manager();

        $this->post(route('password.forgot.submit'), ['email' => 'manager@test.com'])
            ->assertRedirect(route('password.verify', ['sent' => 'true']));

        $otp = session('fp_otp');
        $this->post(route('password.verify.submit'), ['otp' => '000000'])
            ->assertSessionHas('error', 'Invalid verification code. Please try again.');
        $this->post(route('password.verify.submit'), ['otp' => $otp])
            ->assertRedirect(route('password.reset', ['verified' => 'true']));
        $this->post(route('password.reset.submit'), ['password' => 'newpass99', 'confirm' => 'newpass99'])
            ->assertRedirect(route('login', ['reset' => 'success']));

        $this->post(route('login.submit'), ['email' => 'manager@test.com', 'password' => 'newpass99'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_all_pages_render_for_manager(): void
    {
        $manager = $this->manager();
        $fd = $this->maturedFreeFd();

        foreach ([
            route('dashboard'), route('fd.list'), route('fd.create'), route('fd.report'),
            route('fd.view', ['id' => $fd->fd_id]), route('fd.update', ['id' => 'FD'.$fd->fd_id]),
            route('fd.application.view', ['id' => $fd->fd_id]), route('banks.list'), route('banks.create'),
            route('banks.edit', ['id' => $fd->bank_id]), route('users.list'), route('profile'),
        ] as $url) {
            $this->actingAs($manager)->get($url)->assertOk();
        }
    }
}
