<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CreationUserController extends Controller
{

    public function niche()
    {
        // Define breadcrumbs
        $breadcrumbs = [
            ['name' => 'Creation', 'url' => route('creation.index')],
            ['name' => 'Niche', 'url' => null] // Current page
        ];

        return view('creation.niche', compact('breadcrumbs'));
    }

    public function index()
    {
        $user = auth()->user();
        $creationRole = Role::where('name', 'creation')->first();
        $permissions = $creationRole ? $creationRole->permissions : collect();

        return view('creation.index', compact('user', 'permissions'));
    }
}
