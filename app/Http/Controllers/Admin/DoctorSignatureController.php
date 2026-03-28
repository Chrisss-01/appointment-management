<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorSignatureController extends Controller
{
    /**
     * Manage doctor signatures.
     */
    public function index()
    {
        $doctors = User::where('role', 'staff')
            ->where('staff_type', 'doctor')
            ->get();

        return view('admin.doctor-signatures', compact('doctors'));
    }

    /**
     * Update a doctor's signature.
     */
    public function update(Request $request, User $user)
    {
        if (!$user->isDoctor()) {
            return back()->with('error', 'This user is not a doctor.');
        }

        $validated = $request->validate([
            'license_number' => 'nullable|string|max:100',
            'signature_image' => 'nullable|image|max:2048',
        ]);

        $data = ['license_number' => $validated['license_number'] ?? $user->license_number];

        if ($request->hasFile('signature_image')) {
            // Delete old signature
            if ($user->signature_image) {
                Storage::disk('public')->delete($user->signature_image);
            }
            $data['signature_image'] = $request->file('signature_image')
                ->store('signatures', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Doctor signature updated.');
    }
}
