<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Site;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use League\Csv\Reader;
use App\Services\WordPressService;
use Corcel\Model\Page;
use Illuminate\Support\Facades\DB;

class BulkSeoController extends Controller
{
    protected $wordPressService;

    public function __construct(WordpressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    public function index()
    {
        $user = auth()->user();
        $creationRole = Role::where('name', 'creation')->first();
        $permissions = $creationRole ? $creationRole->permissions : collect();
        return view('creation.seo.index', compact('user', 'permissions'));
    }

    public function bulkSeoUpdate(Request $request)
    {

        // Check if CSV file is uploaded
        $csvUploaded = $request->hasFile('csv_file');

        if ($csvUploaded) {
            $csv = Reader::createFromPath($request->file('csv_file')->getRealPath(), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
            foreach ($records as $key => $record) {
                $site = Site::where('site_url', 'like', '%' . $record['Website'] . '%')->first();
                // return $site->server;
                try {
                    if ($site != null) {
                        $connectionName = $this->wordPressService->connectToWp($site->id);
                        $page = explode('/', $record['Pages']);
                        if (isset($page[1])) {
                            $post = Page::on($connectionName)->where('post_name', $page[1])->first();
                        } else {
                            $post = Page::on($connectionName)->where('post_name', $record['Pages'])->first();
                        }
                        if (isset($post->ID)) {
                            $this->wordPressService->updateRankMathMetaDescriptionWithConn($post->ID, $record['Meta Description'], $connectionName);
                            $this->wordPressService->updateRankMathMetaTagsWithConn($post->ID, $record['Keywords'], $connectionName);
                            $this->wordPressService->updatePostMetaWithConn($post->ID, 'rank_math_title', $record['Meta Title'], $connectionName);
                        }
                    }
                } catch (\Exception $e) {
                    // Log the error or handle it as needed
                    // \Log::error("Error updating SEO for site: {$site->id} - {$e->getMessage()}");
                } finally {
                    // Ensure the connection is closed
                    DB::disconnect($connectionName);
                }
            }
        }
        return redirect()->back()->with('success', 'Seo updated on all sites.');
    }
}
