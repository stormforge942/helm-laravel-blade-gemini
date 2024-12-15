<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Corcel\Model\Option;
use Corcel\Model\Attachment;
use App\Models\Site;
use App\Modles\Server;
use App\Models\Post;
use Exception;

class WordPressApiService
{
    protected $siteUrl;
    protected $username;
    protected $applicationPassword;


    public function __construct($siteUrl, $username, $applicationPassword)
    {
        $this->siteUrl = $siteUrl;
        $this->username = $username;
        $this->applicationPassword = $applicationPassword;
    }

    public function uploadMedia($filePath, $fileName)
    {
        $url = $this->siteUrl . '/wp-json/wp/v2/media';
        $response = Http::withBasicAuth($this->username, $this->applicationPassword)
                    ->attach('file', fopen($file->getRealPath(), 'r'), $file->getOriginalClientName())
                    ->post($url);
        
        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to upload media: ' . $response->body());
    }


    // public function __construct($baseUri = null, $username = null, $password = null)
    // {
    //     if ($baseUri && $username && $password) {
    //         $this->client = new Client([
    //             'base_uri' => $baseUri,
    //             'auth' => [$username, $password],  // Basic Auth
    //             'http_errors' => false  // Do not throw exceptions for HTTP error responses
    //         ]);
    //     }
    // }

    // /**
    //  * Dynamically set the client configuration.
    //  */
    // public function setClient($baseUri, $username, $password)
    // {
    //     $this->client = new Client([
    //         'base_uri' => $baseUri,
    //         'auth' => [$username, $password],
    //         'http_errors' => false
    //     ]);
    // }

    // /**
    //  * Make a GET request to fetch posts from WordPress.
    //  * Basic Authentication is used directly in each request.
    //  */
    // public function getPosts()
    // {
    //     try {
    //         $response = $this->client->request('POST', '/wp-json/wp/v2/pages');
    //         $contents = $response->getBody()->getContents();
    //         \Log::info('WP Pages Response:', ['response' => json_decode($contents, true)]);
    //         return json_decode($contents, true);
    //     } catch (GuzzleException $e) {
    //         \Log::error('Failed to fetch WP Pages:', ['error' => $e->getMessage()]);

    //         return null;
    //     }
    // }
}