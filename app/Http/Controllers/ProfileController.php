<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Profile page, profile update and profile pictures
 * (replaces Profile.jsp's data loading, UpdateProfileServlet and ProfilePictureServlet).
 */
class ProfileController extends Controller
{
    /** Shown in the password field; real passwords are stored hashed. */
    private const PASSWORD_MASK = '********';

    public function show()
    {
        $user = auth()->user()->load('manager');

        $isManager = str_contains((string) $user->staff_role, 'Manager');

        // "Managed By" is only shown for non-managers
        $showManagerField = ! $isManager;
        $managerName = $user->manager?->staff_name ?? 'No Manager';

        return view('profile', [
            'user' => $user,
            'showManagerField' => $showManagerField,
            'managerName' => $managerName,
            'passwordMask' => self::PASSWORD_MASK,
        ]);
    }

    public function update(Request $request)
    {
        $staff = auth()->user();

        try {
            $name = (string) $request->input('name');
            $phone = (string) $request->input('phone');
            $address = (string) $request->input('address');
            $password = (string) $request->input('password');

            $staff->staff_name = $name;
            $staff->staff_phone = $phone;
            $staff->staff_address = $address;

            // Password only changes when a new one was entered
            if ($password !== '' && $password !== self::PASSWORD_MASK) {
                if (strlen($password) < 8 || ! preg_match('/[a-zA-Z]/', $password) || ! preg_match('/[0-9]/', $password)) {
                    return redirect()->route('profile', ['error' => 'password'])
                        ->with('error', 'Password does not meet the security criteria.');
                }
                $staff->password = $password;
            }

            // New picture only if it is a JPEG or PNG
            $picture = $request->file('profilePicture');
            if ($picture && $picture->isValid() && $picture->getSize() > 0
                && in_array($picture->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'], true)
                && $picture->getSize() <= 5 * 1024 * 1024) {
                $old = $staff->staff_picture;
                $staff->staff_picture = $picture->store('profile-pictures');
                if ($old) {
                    Storage::delete($old);
                }
            }

            $staff->save();

            return redirect()->route('profile', ['success' => 'true']);
        } catch (\Throwable $e) {
            Log::error('Error updating profile: '.$e->getMessage());

            return redirect()->route('profile', ['error' => 'exception']);
        }
    }

    public function picture(Request $request)
    {
        $staffId = (string) $request->query('staffId');

        if ($staffId === '') {
            abort(400, 'Staff ID is required');
        }
        if (! ctype_digit($staffId)) {
            abort(400, 'Invalid Staff ID');
        }

        $staff = Staff::find($staffId);

        if (! $staff) {
            abort(404, 'Staff not found');
        }

        if (! $staff->staff_picture || ! Storage::exists($staff->staff_picture)) {
            abort(404, 'No profile picture');
        }

        return Storage::response($staff->staff_picture, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
