<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role; 
use App\Models\CustomPermission;
use App\Models\User;


class MaintenanceUserController extends Controller
{
    public function index()
    {
        $user = auth()->user(); 
        $maintenanceRole = Role::where('name', 'maintenance')->first();
        $permissions = $maintenanceRole ? $maintenanceRole->permissions : collect();
        return view('maintenance.index', compact('user', 'permissions')); 
    }

}
