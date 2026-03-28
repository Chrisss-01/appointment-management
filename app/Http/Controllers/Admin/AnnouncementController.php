<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * List all announcements.
     */
    public function index()
    {
        $announcements = Announcement::with('author')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements', compact('announcements'));
    }

    /**
     * Store a new announcement.
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $validated = $request->validated();
        $validated['author_id'] = $request->user()->id;

        if ($request->boolean('is_published')) {
            $validated['is_published'] = true;
            $validated['published_at'] = now();
        }

        Announcement::create($validated);

        return back()->with('success', 'Announcement created successfully.');
    }

    /**
     * Update an announcement.
     */
    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $validated = $request->validated();

        if ($request->boolean('is_published') && !$announcement->is_published) {
            $validated['is_published'] = true;
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully.');
    }

    /**
     * Publish/unpublish an announcement.
     */
    public function togglePublish(Announcement $announcement)
    {
        $announcement->update([
            'is_published' => !$announcement->is_published,
            'published_at' => !$announcement->is_published ? now() : $announcement->published_at,
        ]);

        $status = $announcement->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Announcement {$status}.");
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
