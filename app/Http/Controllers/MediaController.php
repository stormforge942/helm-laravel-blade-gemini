<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WordPressService;
use Illuminate\Support\Facades\Http;

class MediaController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    public function index(Request $request)
    {
        $site = $this->wordPressService->getSiteById($request->siteId);

        // WordPress REST API credentials
        $wordpressUrl = $site->wpApiDetails->site_url . '/wp-json/wp/v2/media';
        $authUsername = $site->wpApiDetails->username;
        $authPassword = $site->wpApiDetails->application_password;

        // Fetch all media
        $response = Http::withBasicAuth($authUsername, $authPassword)->get($wordpressUrl);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => $response->body()], $response->status());
        }
    }
}
