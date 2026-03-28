<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReasonPreset;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * List all services.
     */
    public function index()
    {
        $services = Service::with('reasonPresets')->withCount('appointments')->get();
        return view('admin.services', compact('services'));
    }

    /**
     * Create a new service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string|max:500',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'color' => 'required|string|max:7',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Service::create($validated);

        return back()->with('success', 'Service created successfully.');
    }

    /**
     * Update a service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string|max:500',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'color' => 'required|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $service->update($validated);

        return back()->with('success', 'Service updated successfully.');
    }

    /**
     * Delete a service.
     */
    public function destroy(Service $service)
    {
        if ($service->appointments()->exists()) {
            return back()->with('error', 'Cannot delete service with existing appointments.');
        }

        $service->delete();

        return back()->with('success', 'Service deleted successfully.');
    }

    /**
     * Add a reason preset to a service.
     */
    public function storeReasonPreset(Request $request, Service $service)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $service->reasonPresets()->create($validated);

        return back()->with('success', 'Reason preset added.');
    }

    /**
     * Update a reason preset.
     */
    public function updateReasonPreset(Request $request, ReasonPreset $reasonPreset)
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
    public function destroyReasonPreset(ReasonPreset $reasonPreset)
    {
        $reasonPreset->delete();

        return back()->with('success', 'Reason preset removed.');
    }
}
