<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NicheContent;

class NicheController extends Controller
{
    public function index()
    {
        $nicheContents = NicheContent::orderBy('niche', 'asc')->get();
        
        return response()->json($nicheContents);
    }
}
