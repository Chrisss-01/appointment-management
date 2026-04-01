<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * List all announcements visible to staff (management view).
     */
    public function index()
    {
        $announcements = Announcement::with('author')
            ->where(function ($q) {
                // Always show all staff-authored announcements (regardless of audience)
                $q->whereHas('author', fn($u) => $u->where('role', 'staff'))
                  // Show admin announcements only when targeted at 'all' or 'staff'
                  ->orWhere(fn($q2) =>
                      $q2->whereHas('author', fn($u) => $u->where('role', 'admin'))
                         ->whereIn('target_audience', ['all', 'staff'])
                  );
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('staff.announcements', compact('announcements'));
    }

    /**
     * Store a new announcement authored by the staff user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'expires_at'   => 'nullable|date|after:now',
        ]);

        // Staff announcements always target students
        $validated['target_audience'] = 'students';
        $validated['author_id'] = $request->user()->id;

        if ($request->boolean('is_published')) {
            $validated['is_published'] = true;
            $validated['published_at'] = now();
        }

        Announcement::create($validated);

        return back()->with('success', 'Announcement created successfully.');
    }

    /**
     * Update an announcement (only if authored by the current staff user).
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeOwnership($announcement);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'expires_at'   => 'nullable|date|after:now',
        ]);

        // Staff announcements always target students — preserve it on update
        $validated['target_audience'] = 'students';

        if ($request->boolean('is_published') && !$announcement->is_published) {
            $validated['is_published'] = true;
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully.');
    }

    /**
     * Toggle publish/unpublish (only if authored by the current staff user).
     */
    public function togglePublish(Announcement $announcement)
    {
        $this->authorizeOwnership($announcement);

        $announcement->update([
            'is_published' => !$announcement->is_published,
            'published_at' => !$announcement->is_published ? now() : $announcement->published_at,
        ]);

        $status = $announcement->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Announcement {$status}.");
    }

    /**
     * Delete an announcement (only if authored by the current staff user).
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorizeOwnership($announcement);

        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    private function authorizeOwnership(Announcement $announcement): void
    {
        if ($announcement->author_id !== auth()->id()) {
            abort(403, 'You can only manage your own announcements.');
        }
    }
}
