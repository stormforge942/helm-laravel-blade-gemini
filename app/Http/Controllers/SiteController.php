<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Services\WordPressService;

class SiteController extends Controller
{
    protected $wordpressService;

    public function authenticate(Request $request)
    {
        $siteId = $request->input('siteId');
        $site = Site::findOrFail($siteId);

        // Instantiate WordPressService with dynamic credentials
        $this->wordpressService = new WordPressService($site->base_uri, $site->username, $site->password);

        // Directly attempt to fetch posts as a means of "authentication"
        $posts = $this->wordpressService->getPosts();

        if ($posts) {
            return response()->json([
                'success' => true,
                'message' => 'Authenticated and posts fetched successfully.',
                'data' => $posts
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate or fetch posts.'
            ], 401);
        }
    }

    public function index()
    {
        $servers = Site::select('server')->distinct()->pluck('server');
        return view('creation.sites.index', compact('servers'));
    }

    public function getSitesByServer(Request $request)
    {
        if ($request->ajax()) {
            $sites = Site::where('server', $request->input('server'))->orderBy('base_uri', 'asc')->get(['id', 'base_uri']);
            return response()->json($sites);
        }
    }

    public function showSiteDetails($id)
    {
        $site = Site::findOrFail($id);
        $token = $this->wordpressService->authenticate($site->username, $site->password, $site->base_uri);

        if ($token) {
            $posts = $this->wordpressService->getPosts($token);
            return view('creation.sites.details', compact('site', 'posts'));
        } else {
            return redirect()->back()->with('error', 'Failed to authenticate with WordPress API.');
        }
    }
}
