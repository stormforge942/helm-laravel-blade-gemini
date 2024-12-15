<?php

use App\Http\Controllers\Api\NicheController;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\WordPressService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Reporting\KixieWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // move the new routes here later
});

Route::group(['prefix' => 'logo'], function () {
    Route::get('/', [LogoController::class, 'show']);
    Route::post('/upload', [LogoController::class, 'upload']);
    Route::delete('/delete', [LogoController::class, 'destroy']);
});

Route::group(['prefix' => 'media'], function () {
    Route::get('/', [MediaController::class, 'index']);
});

Route::group(['prefix' => 'images'], function () {
    Route::get('/{image}', [ImageController::class, 'show']);
    Route::put('/{image}', [ImageController::class, 'update']);
    Route::post('/', [ImageController::class, 'store']);
    Route::get('/niche/{nicheId}', [ImageController::class, 'indexByNiche']);
});

Route::get('/niches', [NicheController::class, 'index']);

Route::get('/api/creation/connect-to-db/{id}', function (Request $request, WordPressService $wordPressService, $siteId) {
    try {
        $success = $wordPressService->connectToDb($siteId);

        if ($success) {
            // Optionally retrieve data after successful connection (e.g., posts)
            $posts = Post::on('wordpress')->status('publish')->get();
            $formattedPosts = $posts->map(function ($post) {
                return ['title' => $post->post_title];
            });

            return response()->json([
                'success' => true,
                'message' => 'Connection successful!',
                'data' => $formattedPosts,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to WordPress database.'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error connecting to database: ' . $e->getMessage()
        ], 500);
    }
});

Route::post("/webhook/kixie-call", [KixieWebhookController::class, 'handleCallLog']);
Route::post('/webhook/kixie-sms', [KixieWebhookController::class, 'handleSMSLog']);
