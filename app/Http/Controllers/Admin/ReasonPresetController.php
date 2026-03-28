<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReasonPreset;
use App\Models\Service;
use Illuminate\Http\Request;

class ReasonPresetController extends Controller
{
    /**
     * Manage reason presets for services.
     */
    public function index()
    {
        $services = Service::with('reasonPresets')->get();
        return view('admin.reason-presets', compact('services'));
    }

    /**
     * Store a new reason preset.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'label' => 'required|string|max:255',
        ]);

        ReasonPreset::create($validated);

        return back()->with('success', 'Reason preset added.');
    }

    /**
     * Update a reason preset.
     */
    public function update(Request $request, ReasonPreset $reasonPreset)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $reasonPreset->update($validated);

        return back()->with('success', 'Reason preset updated.');
    }

    /**
     * Delete a reason preset.
     */
    public function destroy(ReasonPreset $reasonPreset)
    {
        $reasonPreset->delete();

        return back()->with('success', 'Reason preset removed.');
    }
}
