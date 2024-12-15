<?php

namespace App\Http\Controllers;

use Corcel\Model\Post;
use Corcel\Model\Option;
use Illuminate\Http\Request;
use App\Services\WordPressService;
use Illuminate\Support\Facades\Http;

class LogoController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    public function show()
    {
        $connectionName = $this->wordPressService->connectToWp(request()->siteId);

        $option = Option::on($connectionName)
            ->where('option_name', 'site_logo')
            ->first();

        return response()->json(['logo_url' => $this->getLogoUrl($option->option_value)]);
    }

    private function getLogoUrl($attachmentId)
    {
        $connectionName = $this->wordPressService->connectToWp(request()->siteId);

        return Post::on($connectionName)->find($attachmentId)->guid ?? null;
    }

    public function upload(Request $request)
    {
        $connectionName = $this->wordPressService->connectToWp($request->siteId);

        $request->validate([
            'logo' => 'required',
        ]);

        $site = $this->wordPressService->getSiteById($request->siteId);

        // WordPress REST API credentials
        $wordpressUrl = $site->wpApiDetails->site_url . '/wp-json/wp/v2/media';
        $authUsername = $site->wpApiDetails->username;
        $authPassword = $site->wpApiDetails->application_password;

        $logoId = $this->handleLogoUploadOrFind($request, $connectionName, $wordpressUrl, $authUsername, $authPassword);

        if (!$logoId) {
            return response()->json(['error' => 'Invalid logo format or media not found.'], 400);
        }

        $this->updateThemeMods($connectionName, $logoId);

        Option::on($connectionName)->updateOrInsert(
            ['option_name' => 'site_logo'],
            ['option_value' => $logoId]
        );

        return response()->json([
            'message' => 'Site logo updated successfully.',
            'logo_url' => $this->getLogoUrl($logoId),
        ]);
    }

    private function handleLogoUploadOrFind($request, $connectionName, $wordpressUrl, $authUsername, $authPassword)
    {
        if ($request->hasFile('logo')) {
            return $this->uploadLogoFile($request->file('logo'), $wordpressUrl, $authUsername, $authPassword);
        } elseif (filter_var($request->logo, FILTER_VALIDATE_URL)) {
            return $this->findLogoByUrl($request->logo, $connectionName);
        }
        return null;
    }

    private function uploadLogoFile($file, $wordpressUrl, $authUsername, $authPassword)
    {
        $response = Http::withBasicAuth($authUsername, $authPassword)
            ->attach('file', fopen($file->path(), 'r'), $file->getClientOriginalName())
            ->post($wordpressUrl);

        if ($response->successful()) {
            return $response->json()['id'];
        }
        return null;
    }

    private function findLogoByUrl($url, $connectionName)
    {
        $postName = pathinfo(basename($url), PATHINFO_FILENAME);

        $wpPost = Post::on($connectionName)
            ->where('post_type', 'attachment')
            ->where('post_name', $postName)
            ->first();

        return $wpPost ? $wpPost->ID : null;
    }

    private function updateThemeMods($connectionName, $logoId)
    {
        $themeModOption = Option::on($connectionName)
            ->where('option_name', 'like', 'theme_mods_%')
            ->where('autoload', 'yes')
            ->first();

        if (!$themeModOption) {
            throw new \Exception('Theme mods option not found.');
        }

        $themeModsValue = @unserialize($themeModOption->option_value);
        if ($themeModsValue === false && $themeModOption->option_value !== 'b:0;') {
            throw new \Exception('Error unserializing theme mods.');
        }

        $themeModsValue['custom_logo'] = $logoId;
        $themeModOption->update(['option_value' => serialize($themeModsValue)]);
    }

    public function destroy()
    {
        // Implement the destroy method if needed
    }
}
