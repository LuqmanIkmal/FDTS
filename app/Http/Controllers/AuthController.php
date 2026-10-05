<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Login, sign up and logout (replaces LoginServlet and SignUpServlet).
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = trim((string) $request->input('email'));
        $password = trim((string) $request->input('password'));

        $back = fn (string $error) => back()->withInput($request->only('email'))->with('error', $error);

        if ($email === '' || $password === '') {
            return $back('Please enter email and password.');
        }

        $staff = Staff::whereRaw('LOWER(staff_email) = ?', [strtolower($email)])->first();

        if (! $staff || ! Hash::check($password, $staff->password)) {
            return $back('Invalid email address or password. Please try again.');
        }

        if ($staff->staff_status !== null && ! $staff->isActive()) {
            return $back('Your account is not active. Please contact admin.');
        }

        Auth::login($staff);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showSignUp()
    {
        // Manager dropdown: active Senior Finance Managers who have no manager
        $managers = Staff::where('staff_role', 'Senior Finance Manager')
            ->whereRaw('LOWER(staff_status) = ?', ['active'])
            ->whereNull('manager_id')
            ->orderBy('staff_id')
            ->get();

        return view('auth.signup', compact('managers'));
    }

    public function signUp(Request $request)
    {
        $fail = fn (string $error) => back()->with('error', $error);

        try {
            $name = trim((string) $request->input('name'));
            $phone = trim((string) $request->input('phone'));
            $email = trim((string) $request->input('email'));
            $password = (string) $request->input('password');
            $confirmPassword = (string) $request->input('confirmPassword');
            $address = trim((string) $request->input('address'));
            $role = trim((string) $request->input('role'));
            $managerIdStr = trim((string) $request->input('managerId'));
            $picture = $request->file('profilePicture');

            if ($name === '' || $phone === '' || $email === '' || trim($password) === '' || trim($confirmPassword) === ''
                || $address === '' || $role === '' || ! $picture || $picture->getSize() === 0) {
                return $fail('Please fill in all required fields.');
            }

            if (strlen(trim($password)) < 8 || ! preg_match('/[a-zA-Z]/', $password) || ! preg_match('/[0-9]/', $password)) {
                return $fail('Password does not meet the security criteria.');
            }

            if ($password !== $confirmPassword) {
                return $fail('Passwords do not match.');
            }

            $managerId = null; // "I'm Manager" / no manager
            if ($managerIdStr !== '') {
                if (! ctype_digit($managerIdStr)) {
                    return $fail('Invalid manager selected.');
                }
                $managerId = (int) $managerIdStr > 0 ? (int) $managerIdStr : null;
                if ($managerId !== null && ! Staff::whereKey($managerId)->exists()) {
                    return $fail('Invalid manager selected.');
                }
            }

            if (! $picture->isValid() || ! in_array($picture->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'], true)) {
                return $fail('Invalid file format. Only JPEG and PNG files are accepted.');
            }

            if ($picture->getSize() > 5 * 1024 * 1024) {
                return $fail('File size must be less than 5MB');
            }

            if (Staff::whereRaw('LOWER(staff_email) = ?', [strtolower($email)])->exists()) {
                return $fail('An account with this email address already exists.');
            }

            Staff::create([
                'staff_id_prefix' => Staff::prefixForRole($role),
                'staff_name' => $name,
                'staff_phone' => $phone,
                'staff_address' => $address,
                'staff_email' => strtolower($email),
                'password' => trim($password),
                'staff_picture' => $picture->store('profile-pictures'),
                'staff_role' => $role,
                'staff_status' => 'Active',
                'manager_id' => $managerId,
            ]);

            return redirect()->route('login', ['signup' => 'success']);
        } catch (\Throwable $e) {
            Log::error('Sign up failed: '.$e->getMessage());

            return $fail('An error occurred: '.$e->getMessage());
        }
    }
}
