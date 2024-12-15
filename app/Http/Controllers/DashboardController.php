<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch the latest announcement
        $today = now()->format('Y-m-d');
        $announcements = Announcement::where('start_date', '<=', $today)
                                     ->where('end_date', '>=', $today)
                                     ->get();
        return view('dashboard', compact('announcements'));
    }
}
