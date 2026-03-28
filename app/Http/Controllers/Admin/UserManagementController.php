<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * List students.
     */
    public function students(Request $request)
    {
        $search = $request->get('search');

        $students = User::students()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('student_id', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20);

        return view('admin.students', compact('students', 'search'));
    }

    /**
     * List staff.
     */
    public function staff(Request $request)
    {
        $search = $request->get('search');

        $staff = User::staff()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20);

        return view('admin.staff', compact('staff', 'search'));
    }

    /**
     * Create a new staff user.
     */
    public function createStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:staff,admin',
            'staff_type' => 'nullable|in:doctor,nurse',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'staff_type' => $validated['role'] === 'staff' ? ($validated['staff_type'] ?? 'nurse') : null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Staff account created successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin() && User::admins()->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
