{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Roles & Permissions
        </h2>

        <form id="userSelectionForm" action="{{ route('admin.users.edit') }}" method="GET">
            <select name="user_id" onchange="document.getElementById('userSelectionForm').submit();">
                <option value="">Select a User</option>
                @foreach ($users as $userOption)
                    <option value="{{ $userOption->id }}" @if (request('user_id') == $userOption->id) selected @endif>{{ $userOption->name }}</option>
                @endforeach
            </select>
        </form>

        @if ($user)
            <form class="mt-5" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Permissions --}}
                <div class="mb-5">
                    <label>Permissions:</label><br>
                    @php
                    // Retrieve only direct permissions assigned to the user
                    $userDirectPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
                    @endphp

                    <ul>
                        @foreach ($user->getDirectPermissions() as $permission)

                        <li>{{$permission->name}}</li>

                        @endforeach

                    </ul>
                    <ul>
                        @foreach ($user->getAllPermissions() as $permission)

                        <li>{{$permission->name}}</li>

                        @endforeach

                    </ul>
                    @foreach ($user->getPermissionsViaRoles() as $permission)

                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               @if (in_array($permission->name, $userDirectPermissions)) checked @endif> {{ $permission->name }}<br>
                    @endforeach
                </div>
                

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        @endif
    </div>
</x-app-layout>