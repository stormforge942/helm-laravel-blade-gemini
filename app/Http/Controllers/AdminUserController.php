<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminUserController extends Controller
{
    use HasRoles;
    public function dashboard()
    {
        $user = auth()->user();
        $permissions = collect();

        // Check if the user has 'super_admin' or 'administrator' roles
        if ($user->hasAnyRole(['super_admin', 'administrator'])) {
            // we fetch permissions for 'administrator' which can be assumed to include
            // everything needed for 'super_admin' as well.
            $role = Role::where('name', 'administrator')->first();
            $permissions = $role ? $role->permissions : collect();
        }

        if ($user->hasAnyRole(['super_admin', 'administrator', 'leads_manager'])) {
            $leadsPermissions = Role::where('name', 'leads_manager')->first()->permissions;
            $permissions = $permissions->merge($leadsPermissions);
        }

        return view('admin.dashboard', compact('user', 'permissions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        // Use a transaction to ensure both user creation and role assignment succeed
        \DB::transaction(function () use ($validatedData, $request) {
            // Create the user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'owner' => Auth::user()->name,
                'password' => Hash::make($validatedData['password']), // Hash the password for security
            ]);

            // Assign roles if any are selected
            if (!empty($validatedData['roles'])) {
                $roles = Role::find($validatedData['roles']);
                $user->assignRole($roles);
            }
        });

        // Redirect with a success message
        return redirect()->route('admin.dashboard')->with('success', 'User registered successfully.');
    }

    public function select()
    {
        $users = User::all(); // Get all users to display in the dropdown
        return view('admin.users.select', compact('users'));
    }


    public function edit(Request $request)
    {
        $users = User::all(); // Fetch all users
        $roles = Role::all(); // Fetch all roles
        $permissions = Permission::all(); // Fetch all permissions

        $user = null;
        if ($request->has('user_id')) {
            $user = User::find($request->input('user_id'));
        }

        return view('admin.users.edit', compact('users', 'user', 'roles', 'permissions'));
    }


    public function update(Request $request, User $user)
    {
        $permissions = Permission::find($request->permissions); // Similarly for permissions

        // Sync the user's roles and permissions using the models
        $user->syncPermissions($permissions);
        \Log::info($user->permissions); // Log user's direct permissions

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully');
    }


    public function destroy(Request $request)
    {
        // You can perform an authorization check here to ensure the user has permission to delete users.

        // This grabs an array of user IDs from the request.
        // It assumes your form sends an array of user IDs, whether it's a single ID or multiple.
        $userIds = $request->input('user_ids', []);

        // Delete all users with the provided IDs.
        // You might want to add additional checks here to ensure the user cannot delete themselves or other protected accounts.
        User::whereIn('id', $userIds)->delete();

        // Redirect back with a success message.
        return redirect()->route('admin.dashbaord')->with('success', 'User deleted successfully.');
    }


    public function destroyMany(Request $request)
    {
        // Ensure the current authenticated user has permission to delete users
        if (!auth()->user()->hasPermissionTo('remove_users')) {
            abort(403, 'Unauthorized action.');
        }

        $userIds = $request->input('user_ids', []);

        // Validate that the input is an array and not empty
        if (!is_array($userIds) || empty($userIds)) {
            return back()->with('error', 'No users selected for deletion.');
        }

        // Use a transaction for safe deletion
        \DB::transaction(function () use ($userIds) {
            User::whereIn('id', $userIds)->delete();

            // Log the bulk deletion
            Log::info('Bulk user deletion performed by ' . auth()->user()->name, ['user_ids' => $userIds]);
        });

        return redirect()->route('admin.dashboard')->with('success', 'Users deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'user_id' => 'required|numeric',
        ]);
        try {
            $user = User::find($validated['user_id']);
            $user->status = $validated['status'];
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() . $validated['status']], 500);
        }
        return response()->json(['success' => $validated['status']], 200);
    }
}
