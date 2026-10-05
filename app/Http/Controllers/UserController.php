<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * User list and status updates (replaces UserList.jsp's data loading and UpdateUserServlet).
 */
class UserController extends Controller
{
    public function index()
    {
        $me = auth()->user();

        // Show only users managed by the logged-in user
        $users = Staff::with('manager')
            ->where('manager_id', $me->staff_id)
            ->orderBy('staff_id')
            ->get()
            ->map(fn (Staff $s) => [
                'staffId' => $s->formatted_staff_id,
                'numericId' => $s->staff_id,
                'name' => $s->staff_name ?? '',
                'role' => $s->staff_role ?? '',
                'status' => $s->staff_status ?? '',
                'manageBy' => $s->manager?->staff_name ?? '',
                'email' => $s->staff_email ?? '',
                'phone' => $s->staff_phone ?? '',
                'address' => str_replace(["\r", "\n"], ' ', $s->staff_address ?? ''),
                'reason' => $s->reason,
            ]);

        return view('users.index', compact('users'));
    }

    public function update(Request $request)
    {
        try {
            $staffId = (string) $request->input('editStaffId');
            $status = (string) $request->input('editStatus');
            $reason = $request->input('editReason');

            // An uploaded reason file is recorded by its file name
            if ($request->hasFile('editReasonFile') && $request->file('editReasonFile')->isValid()) {
                $reason = $request->file('editReasonFile')->getClientOriginalName();
            }

            if ($staffId === '') {
                return redirect()->route('users.list', ['error' => 'missing_id']);
            }
            if (! ctype_digit($staffId)) {
                return redirect()->route('users.list', ['error' => 'invalid_id']);
            }
            if ($status === '') {
                return redirect()->route('users.list', ['error' => 'missing_status']);
            }

            // Managers can only update the users they manage
            $staff = Staff::where('staff_id', $staffId)
                ->where('manager_id', auth()->id())
                ->first();

            if (! $staff) {
                return redirect()->route('users.list', ['error' => 'update_failed']);
            }

            $staff->update([
                'staff_status' => $status,
                'reason' => strcasecmp($status, 'Inactive') === 0
                    ? (($reason !== null && $reason !== '') ? $reason : 'No reason provided')
                    : null,
            ]);

            return redirect()->route('users.list', ['success' => 'true', 'staffId' => $staff->staff_id]);
        } catch (\Throwable $e) {
            Log::error('Update user failed: '.$e->getMessage());

            return redirect()->route('users.list', ['error' => 'exception']);
        }
    }
}
