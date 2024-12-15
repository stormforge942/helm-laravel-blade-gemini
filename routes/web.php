<?php

use App\Http\Controllers\Reporting\KixieReprotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CreationUserController;
use App\Http\Controllers\MaintenanceUserController;
use App\Http\Controllers\ReportingUserController;
use App\Http\Controllers\WordpressController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\ContentGeneratorController;
use App\Http\Controllers\Reporting\SalesFormController;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\SiteGeneratorController;
use App\Http\Controllers\NewsGeneratorController;
use App\Http\Controllers\AnnouncementController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\BulkContent\BulkContentController;
use App\Http\Controllers\GeminiApiController;
use App\Http\Controllers\Leads\LeadsUserController;
use App\Http\Controllers\BulkSeoController;
use App\Http\Controllers\Creation\HomepageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\DnsSetupController;
use App\Http\Controllers\UpdateDnsController;

use App\Models\User;
use App\Models\CustomPermission;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('welcome');
});

Route::get('/assign-permissions', function () {
    $user = Auth::user();

    if (!$user) {
        return 'User not found';
    }

    $roles = ['super_admin', 'administrator'];
    $permissionsData = [
        ['name' => 'add_announcement'],
        ['name' => 'remove_announcement']
    ];

    foreach ($roles as $roleName) {
        $role = Role::findByName($roleName);

        if (!$role) {
            return "Role {$roleName} not found";
        }

        $user->assignRole($role);

        foreach ($permissionsData as $permissionData) {
            $permission = Permission::updateOrCreate(['name' => $permissionData['name']], $permissionData);
            $role->givePermissionTo($permission);
        }
    }

    $permissions = $user->getAllPermissions();

    return response()->json([
        'permissions' => $permissions,
    ]);
});

Route::middleware('guest')->group(function () {
    if (config('custom.dev_env')) {
        Route::get('login/azure', [LoginController::class, 'handleAzureCallback'])->name('login.azure');
    } else {
        //        Route::get('/login', function () {
        //            return redirect()->route('login.azure');
        //        })->name('login');

        Route::get('login/azure', [LoginController::class, 'redirectToAzure'])->name('login.azure');
        Route::get('login/azure/callback', [LoginController::class, 'handleAzureCallback']);

        Route::get('login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
        Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);
    }
});

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('login', [LoginController::class, 'login']);

Route::get('/login', function () {
    return redirect('/');
})->name('login');


Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('policies');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'can:create_neighborhoods_maps'])->group(function () {
    Route::get('/creation', [CreationUserController::class, 'index'])->name('creation.index');
    Route::get('/niche', [CreationUserController::class, 'niche'])->name('niche');

    Route::get('/creation/neighborhoods', [NeighborhoodController::class, 'index'])->name('creation.neighborhoods.index');
    Route::get('/creation/neighborhoods/server', [WordpressController::class, 'getSitesByServer'])->name('creation.neighborhoods.byServer');
    Route::post('/creation/neighborhoods/generate-neigborhoods', [ContentGeneratorController::class, 'generateNeighborhoods'])->name('fetch.creation.generate-neighborhoods');
    Route::post('/creation/neighborhoods/generate-content', [ContentGeneratorController::class, 'generateNeighborhoodsContent'])->name('fetch.creation.generate-neighborhoods-content');
    Route::post('/creation/neighborhoods/generate-places', [ContentGeneratorController::class, 'generatePlaces'])->name('fetch.creation.generate-places');
    Route::post('/creation/neighborhoods/generate-directions', [MapsController::class, 'getDirections'])->name('fetch.creation.generate-directions');
    Route::get('/niche-content/{nicheId}', [NeighborhoodController::class, 'getStaticContentByNiche'])->name('creation.neighborhoods.byNiche');
    Route::post('/posts/update-neighborhoods', [NeighborhoodController::class, 'createNeighborhoodPosts'])->name('creation.neighborhood-posts.store');

    Route::get('/creation/neighborhoods/google-poi', [WordpressController::class, 'googlePoi'])->name('creation.neighborhoods.googlePoi');
    Route::post('/options/update-poi', [NeighborhoodController::class, 'updatePoiOptions'])->name('creation.poi.store');
    Route::get('/options/get-poi', [NeighborhoodController::class, 'getPoiOptions'])->name('fetch.poi.options');

    Route::post('/creation/upload-file', [NeighborhoodController::class, 'uploadFile'])->name('wordpress.upload.file');

    Route::get('/connect-db/{siteId}', [WordpressController::class, 'connectDb'])->name('wordpress.connect.db');

    Route::get('/creation/generate-content', [ContentGeneratorController::class, 'showForm'])->name('creation.generate-content.form');
    Route::post('/creation/generate-content', [ContentGeneratorController::class, 'generate'])->name('creation.generate-content.post');
    Route::post('/creation/generate-iframe', [ContentGeneratorController::class, 'generateIframe'])->name('fetch.creation.generate-iframe');

    Route::get('/creation/site-generator', [SiteGeneratorController::class, 'index'])->name('creation.site-generator');
    Route::post('/creation/generate-site', [SiteGeneratorController::class, 'generate'])->name('creation.generate-site');
    Route::get('/creation/generated-site', [SiteGeneratorController::class, 'viewGeneratedSite'])->name('generated.site.view');

    Route::get('/creation/pages/service', [WordpressController::class, 'servicePage'])->name('creation.service-pages');
    Route::post('/pages/update-service', [WordpressController::class, 'createServicePage'])->name('creation.service-pages.store');
    Route::put('/pages/update-service/{id}', [WordpressController::class, 'updateServicePage'])->name('creation.service-pages.update');
    Route::get('/creation/get-service-pages', [WordpressController::class, 'getPages'])->name('fetch.pages.service');

    Route::get('/creation/posts/blog', [WordpressController::class, 'blogPost'])->name('creation.blog-posts');
    Route::get('/creation/get-blog-posts', [WordpressController::class, 'getPosts'])->name('fetch.posts.blog');
    Route::get('/creation/get-post-categories', [WordpressController::class, 'getCategories'])->name('fetch.posts.categories');
    Route::post('/posts/update-blog', [WordpressController::class, 'createBlogPost'])->name('creation.blog-posts.store');
    Route::get('/creation/blog-posts', [WordpressController::class, 'getAllBlogPosts'])->name('fetch.all-posts.blog');
    Route::put('/creation/blog-posts/{id}', [WordpressController::class, 'updateBlogPost'])->name('creation.blog-posts.update');

    Route::post('/creation/generate-headlines', [BulkContentController::class, 'generateHeadlines'])->name('creation.generateHeadline.get');
    Route::post('/creation/generate-headline-content', [BulkContentController::class, 'generateTextBasedOnHeadlines'])->name('creation.generateTextBasedOnHeadlines.get');

    Route::get('/creation/generate-content/bulk', [BulkContentController::class, 'showForm'])->name('creation.bulk-content.form');
    Route::post('/creation/generate-content/bulk', [BulkContentController::class, 'generate'])->name('creation.bulk-content.get');
    Route::get('/wp-sites', [BulkContentController::class, 'getSitesByNiche'])->name('creation.neighborhoods.byNiche.sites');
    Route::post('/creation/bulk-content/blogs',  [BulkContentController::class, 'postBlogContent'])->name('creation.bulk-content.post');
    Route::get('/creation/seo-bulk-update',  [BulkSeoController::class, 'index'])->name('creation.seo-bulk.get');
    Route::post('/creation/seo-bulk-update',  [BulkSeoController::class, 'bulkSeoUpdate'])->name('creation.seo-bulk-update');

    Route::get('/creation/generate-content/homepage', [HomepageController::class, 'showForm'])->name('generate-content.homepage.showForm');
    Route::get('/creation/generate-content/homepage/{siteId}', [HomepageController::class, 'getHomepageSections'])->name('generate-content.homepage.getSections');
    Route::post('/creation/generate-content/homepage', [HomepageController::class, 'generate'])->name('generate-content.homepage.get');
    Route::post('/creation/homepage/update', [HomepageController::class, 'updateHomepage'])->name('generate-content.homepage.update');
    Route::get('/wp-sites/images', [HomepageController::class, 'getSiteLogos'])->name('site-logos.fetch');
});

Route::middleware(['auth', 'check.any.roles:super_admin,administrator,maintenance'])->group(function () {
    Route::get('/maintenance', [MaintenanceUserController::class, 'index'])->name('maintenance.index');
});

Route::middleware(['auth', 'can:create_edit_form'])->group(function () {
    Route::get('/maintenance/form-creation', [FormController::class, 'index'])->name('maintenance.form-creation');
    Route::post('/maintenance/form-creation', [FormController::class, 'store']);
    Route::get('/maintenance/form-creation/edit/{id}', [FormController::class, 'edit']);
    Route::post('/maintenance/form-creation/update/{id}', [FormController::class, 'update']);
});

Route::middleware(['auth', 'check.any.roles:super_admin,administrator,sales_manager,sales_person,leads_manager,partner'])->group(function () {
    Route::get('/reporting', [ReportingUserController::class, 'index'])->name('reporting.index');
});

Route::middleware(['auth', 'can:view_rental_report'])->group(function () {
    Route::get('/reporting/rental-report', [KixieReprotController::class, 'index'])->name('kixie.reports');
});

Route::middleware(['auth', 'can:create_rental_sale'])->group(function () {
    Route::get('/reporting/sales-form', [SalesFormController::class, 'index'])->name('sales.form.index');
    Route::post('/reporting/sales-form', [SalesFormController::class, 'store'])->name('sales.form.store');
});

Route::middleware(['auth', 'can:view_rental_sale'])->group(function () {
    Route::get('/reporting/pipeline-report', [SalesFormController::class, 'pipelineReporting'])->name('pipeline.reporting');
});

Route::middleware(['auth', 'can:update_rental_sale'])->group(function () {
    Route::put('/sales-form/update', [SalesFormController::class, 'update'])->name('sales.form.update');
    Route::get('/sales-form/{id}', [SalesFormController::class, 'edit'])->name('sales.form.edit');
});

Route::middleware(['auth', 'can:access_leads_portal'])->group(function () {
    Route::get('/reporting/clearvue/redirect', [LeadsUserController::class, 'redirectToLeadsSite']);
});

Route::post('/admin/users/store', [AdminUserController::class, 'store'])->name('admin.users.store');

Route::middleware(['auth', 'can:add_users'])->group(function () {
    Route::get('/admin/dashboard', [AdminUserController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/creation/generate-content/review', [ContentGeneratorController::class, 'reviewForm'])->name('creation.generate-content.review');
    Route::post('/creation/generate-reviews', [ContentGeneratorController::class, 'generateReviews'])->name('fetch.creation.generate-reviews');

    Route::get('/creation/news-generator', [NewsGeneratorController::class, 'index'])->name('creation.news-generator');
    Route::post('/creation/generate-news', [NewsGeneratorController::class, 'generate'])->name('creation.generate-news');
    Route::get('/creation/generated-news', [NewsGeneratorController::class, 'viewGeneratedContent'])->name('generated.news.view');

    Route::get('/admin/update-dns', [UpdateDnsController::class, 'index'])->name('update-dns');
    Route::post('/admin/update-dns', [UpdateDnsController::class, 'UpdateDns'])->name('update-dns');

    Route::get('/dns/create', [DnsSetupController::class, 'create'])->name('dns.create');
    Route::post('/dns/store', [DnsSetupController::class, 'import'])->name('dns.import');
});

Route::get('/admin/users/register', function () {
    $roles = Role::all();
    return view('admin.users.register', compact('roles'));
})->middleware(['auth', 'can:add_users'])->name('admin.users.register');

Route::get('/admin/users/remove', function () {
    $users = User::orderBy('name', 'asc')->get();
    return view('admin.users.remove', compact('users'));
})->middleware(['auth', 'can:remove_users'])->name('admin.users.remove');

Route::get('/announcements/create', [AnnouncementController::class, 'create'])->middleware(['auth', 'can:add_announcement'])->name('announcement.create');
Route::post('/announcements/store', [AnnouncementController::class, 'store'])->name('announcement.store');
Route::get('/announcements', [AnnouncementController::class, 'show'])->middleware(['auth', 'can:remove_announcement'])->name('announcements.index');
Route::post('/announcements/remove', [AnnouncementController::class, 'delete'])->middleware(['auth', 'can:remove_announcement'])->name('announcements.delete');

Route::get('/gemini/list-models', [GeminiApiController::class, 'listModels']);
Route::get('/gemini/generate-content', [GeminiApiController::class, 'verifyGoogle']);
Route::get('/generate-content/callback', [GeminiApiController::class, 'callback']);
Route::get('/generate-content/execute', [GeminiApiController::class, 'generateContent']);

Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
Route::delete('/admin/users', [AdminUserController::class, 'destroyMany'])->name('admin.users.destroyMany');
Route::post('/admin/users/update/status', [AdminUserController::class, 'updateStatus'])->name('admin.update.status')->middleware(['auth', 'can:remove_users']);

Route::middleware(['auth', 'check.any.roles:super_admin,administrator'])->group(function () {
    Route::get('/get-all-forms', [HomepageController::class, 'getAllForms']);
    Route::get('/get-form-data/{id}', [HomepageController::class, 'getFormData']);
    Route::post('/check-form-name', [FormController::class, 'checkFormName'])->name('check.form.name');
    Route::post('/maintenance/forms-by-niche', [FormController::class, 'getFormsByNiche'])->name('get.forms.by.niche');
    Route::get('/maintenance/forms-by-niche', [FormController::class, 'getFormsByNiche']);
});


// Route::middleware(['auth'])->group(function () {
//     Route::middleware(['role:administrator|super_admin'])->group(function () {
//         Route::get('/admin/dashboard', [AdminUserController::class, 'dashboard'])->name('admin.dashboard');
//     });

//     Route::middleware(['role:administrator|super_admin', 'can:add_users'])->group(function () {
//         Route::get('/admin/register', function () {
//             $roles = Spatie\Permission\Models\Role::all();
//             return view('admin.register', compact('roles'));
//         })->name('admin.register');
//     });

// });

require __DIR__ . '/auth.php';
