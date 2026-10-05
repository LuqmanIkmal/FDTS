<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Services\Mailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Forgot password flow: email OTP -> verify code -> reset password
 * (replaces ForgotPasswordServlet, VerifyCodeServlet and ResetPasswordServlet).
 */
class PasswordResetController extends Controller
{
    private const OTP_MINUTES = 5;

    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));

        if ($email === '') {
            return back()->with('error', 'Email is required.');
        }

        $staff = Staff::whereRaw('LOWER(staff_email) = ?', [$email])
            ->where('staff_status', 'Active')
            ->first();

        if (! $staff) {
            return back()->withInput()->with('error', 'Email not found or account is not active.');
        }

        $otp = (string) random_int(100000, 999999);

        session([
            'fp_staffId' => $staff->staff_id,
            'fp_email' => $email,
            'fp_otp' => $otp,
            'fp_expiry' => now()->addMinutes(self::OTP_MINUTES)->timestamp,
            'fp_verified' => false,
        ]);

        try {
            Mailer::send(
                $email,
                'Fixed Deposit Tracking System - Password Reset OTP',
                "Your verification code is: {$otp}\n\n".
                "This code will expire in 5 minutes.\n\n".
                'If you did not request this, please ignore this email.'
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Unable to send verification email. Please try again.');
        }

        return redirect()->route('password.verify', ['sent' => 'true']);
    }

    public function showVerify()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $sessionOtp = session('fp_otp');
        $expiry = session('fp_expiry');

        if (! $sessionOtp || ! $expiry) {
            return redirect()->route('password.forgot')->with('error', 'Session expired. Please try again.');
        }

        if (now()->timestamp > $expiry) {
            $this->clearResetSession();

            return redirect()->route('password.forgot')->with('error', 'Verification code expired. Please request a new one.');
        }

        $userOtp = trim((string) $request->input('otp'));

        if ($userOtp === '') {
            return back()->with('error', 'Please enter the verification code.');
        }

        if (! hash_equals($sessionOtp, $userOtp)) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        session(['fp_verified' => true]);

        return redirect()->route('password.reset', ['verified' => 'true']);
    }

    public function showReset()
    {
        if (! session('fp_verified')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (! session('fp_verified')) {
            return redirect()->route('password.forgot');
        }

        $newPassword = trim((string) $request->input('password'));
        $confirmPassword = trim((string) $request->input('confirm'));
        $fail = fn (string $error) => redirect()->route('password.reset', ['verified' => 'true'])->with('error', $error);

        if ($newPassword === '' || $confirmPassword === '') {
            return $fail('Please fill in all fields.');
        }

        if ($newPassword !== $confirmPassword) {
            return $fail('Password and Confirm Password do not match!');
        }

        if (strlen($newPassword) < 6) {
            return $fail('Password must be at least 6 characters long.');
        }

        $staff = Staff::find(session('fp_staffId'));

        if (! $staff) {
            return $fail('User not found. Please try again.');
        }

        if (Hash::check($newPassword, $staff->password)) {
            return $fail('New password cannot be the same as the old password.');
        }

        $staff->password = $newPassword;
        $staff->save();

        $this->clearResetSession();

        return redirect()->route('login', ['reset' => 'success']);
    }

    private function clearResetSession(): void
    {
        session()->forget(['fp_staffId', 'fp_email', 'fp_otp', 'fp_expiry', 'fp_verified']);
    }
}
