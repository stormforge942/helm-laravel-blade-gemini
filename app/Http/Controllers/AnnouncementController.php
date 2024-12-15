<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class AnnouncementController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $creationRole = Role::where('name', 'creation')->first();
        $permissions = $creationRole ? $creationRole->permissions : collect();
        return view('admin.announcement.create', compact('user', 'permissions'));
    }

    public function store(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'announcement_content' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');

            // Create the announcement
            $announcement = Announcement::create([
                'content' => $validated['announcement_content'],
                'start_date' =>  $startDate,
                'end_date' => $endDate,
                'user_id' => Auth::id()
            ]);

            // Return a success message
            return response()->json([
                'success' => 'Announcement created successfully!',
                'redirect_url' => url('/announcements')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error in generate method: ' . $e->getMessage());
            return response()->json(['error' => 'Error adding announcement: ' . $e->getMessage()], 500);
        }
    }

    public function show() {
        $user = auth()->user();
        $creationRole = Role::where('name', 'creation')->first();
        $permissions = $creationRole ? $creationRole->permissions : collect();
        $today = now()->format('Y-m-d');
        $announcements = Announcement::where('start_date', '<=', $today)
                                        ->where('end_date', '>=', $today)
                                        ->get();
        return view('admin.announcement.show', compact('announcements', 'user', 'permissions'));   
    }


    public function delete(Request $request)
    {
        try {
            $announcementIds = $request->input('announcements', []);

            if (!empty($announcementIds)) {
                Announcement::whereIn('id', $announcementIds)->delete();
            }

            return response()->json([
                'message' => 'Selected announcements have been deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in delete method: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting announcements: ' . $e->getMessage()], 500);
        }
    }
    

}
