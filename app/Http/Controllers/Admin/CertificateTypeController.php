<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateType;
use App\Models\CertificateTypeDocument;
use App\Models\CertificatePurposePreset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateTypeController extends Controller
{
    /**
     * Manage certificate types, required documents, and purpose presets.
     */
    public function index()
    {
        $certificateTypes = CertificateType::with(['requiredDocuments', 'purposePresets'])
            ->withCount('certificateRequests')
            ->get();

        return view('admin.certificate-types', compact('certificateTypes'));
    }

    /**
     * Store a new certificate type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:certificate_types,name',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['icon'] = $validated['icon'] ?? 'description';

        CertificateType::create($validated);

        return back()->with('success', 'Certificate type created.');
    }

    /**
     * Update a certificate type.
     */
    public function update(Request $request, CertificateType $certificateType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:certificate_types,name,' . $certificateType->id,
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $certificateType->update($validated);

        return back()->with('success', 'Certificate type updated.');
    }

    /**
     * Delete a certificate type.
     */
    public function destroy(CertificateType $certificateType)
    {
        if ($certificateType->certificateRequests()->exists()) {
            return back()->with('error', 'Cannot delete — certificate requests exist.');
        }

        $certificateType->delete();

        return back()->with('success', 'Certificate type deleted.');
    }

    // ── Required Documents ──────────────────────────────────────────

    public function storeDocument(Request $request, CertificateType $certificateType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);

        $certificateType->requiredDocuments()->create($validated);

        return back()->with('success', 'Required document added.');
    }

    public function destroyDocument(CertificateTypeDocument $document)
    {
        $document->delete();
        return back()->with('success', 'Required document removed.');
    }

    // ── Purpose Presets ─────────────────────────────────────────────

    public function storePurpose(Request $request, CertificateType $certificateType)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $certificateType->purposePresets()->create($validated);

        return back()->with('success', 'Purpose preset added.');
    }

    public function destroyPurpose(CertificatePurposePreset $preset)
    {
        $preset->delete();
        return back()->with('success', 'Purpose preset removed.');
    }
}
