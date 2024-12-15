<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Models\CustomPermission;
use App\Models\User;


class ReportingUserController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        // Check if the user has the necessary roles
        if (!$user->hasAnyRole(['super_admin','administrator', 'sales_manager', 'sales_person', 'leads_manager','partner'])) {
            \Log::warning('Unauthorized access attempt: User lacks necessary roles', ['user_id' => $user->id]);
            abort(403, 'Unauthorized');
        }

        // Initialize permission collections
        $permissions = collect();

        // Collect sales permissions if the user has the sales_manager role
        if ($user->hasAnyRole(['super_admin','administrator', 'sales_manager'])) {
            $salesRole = Role::where('name', 'sales_manager')->first();
            if ($salesRole) {
                $salesPermissions = $salesRole->permissions;
                $permissions = $permissions->merge($salesPermissions);
            }
        } else if ($user->hasAnyRole(['sales_person'])) {
            $salesRole = Role::where('name', 'sales_person')->first();
            if ($salesRole) {
                $salesPermissions = $salesRole->permissions;
                $permissions = $permissions->merge($salesPermissions);
            }
        }

        if ($user->hasAnyRole(['super_admin','administrator', 'leads_manager'])) {
            $leadsPermissions = Role::where('name', 'leads_manager')->first()->permissions;
            $permissions = $permissions->merge($leadsPermissions);
        }

        if ($user->hasAnyRole(['partner'])) {
            $partnerPermissions = Role::where('name', 'partner')->first()->permissions;
            $permissions = $permissions->merge($partnerPermissions);
        }

        return view('reporting.index', compact('user', 'permissions'));
    }


}
