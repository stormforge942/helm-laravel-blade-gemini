<x-app-layout>
    <style>
        .spinner {
            width: 20px;
            height: 20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        input:focus,
        select:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
    <link href='https://cdn.datatables.net/2.1.8/css/dataTables.tailwindcss.css'>
    <div class="my-5 max-w-sm mx-auto flex items-center justify-center">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Remove Users</h1>
    </div>

    <!-- Start of the form -->
    <form id="userDeletionForm" class="mt-5 max-w-8xl mx-auto sm:px-6 lg:px-8" method="POST"
        action="{{ route('admin.users.destroyMany') }}">
        @csrf
        @method('DELETE')
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="search-table">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-3 py-3">
                            Select
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Role
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Owner
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Last Login
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <input id="user_{{ $user->id }}" type="checkbox" name="user_ids[]"
                                value="{{ $user->id }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="user_{{ $user->id }}" class="text-gray-600 hover:text-gray-500 dark:text-gray-300 text-lg ml-2 block text-sm hover:cursor-pointer">
                            </label>
                        </th>
                        <th class="px-3 py-2">
                            {{ $user->name }}
                            @if($user->account_type === 'google')
                            <span class="text-sm text-green-500">[G]</span>
                            @elseif($user->account_type === 'microsoft')
                            <span class="text-sm text-blue-500">[MS]</span>
                            @endif
                        </th>
                        <td class="px-3 py-2">
                            {{ $user->email }}
                        </td>
                        <td class="px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $user->getJWTCustomClaims()['role'])) }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $user->owner }}
                        </td>
                        <td class="px-3 py-2">
                            @if($user->last_login)
                            {{ \Carbon\Carbon::parse($user->last_login)->setTimezone('America/New_York')->format('m/d/Y h:i:s A') }}
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <select onchange='updateStatus({{ $user->id }})' name="status" id="status-{{$user->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div id="loader-{{$user->id}}" style="display:none" class="mt-2">
                                <div class="spinner"></div>
                            </div>
                            <span class="status-{{$user->id}} hidden mt-2">
                                <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Submit button -->
        <div class="flex items-center justify-center mt-4">
            <x-primary-button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" type="submit">
                {{ __('Delete Selected Users') }}
            </x-primary-button>
        </div>
    </form>
</x-app-layout>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

<script>
    $(document).ready(function() {
        Object.assign(DataTable.defaults, {
            initComplete: function() {
                // Add custom classes to buttons and pagination
                $('.col-start-2.justify-self-end').addClass('flex justify-end');
            }
        });
        new DataTable('#search-table');
    });


    document.getElementById('userDeletionForm').onsubmit = function(event) {
        const result = confirm("Are you sure you want to remove these users?");
        if (!result) {
            event.preventDefault();
        }
    };

    async function updateStatus(userId) {
        $('#status-' + userId).attr('disabled', 'disabled');
        $('#loader-' + userId).show();
        $('.status').hide();
        const formData = new FormData();
        formData.append('status', $('#status-' + userId).val());
        formData.append('user_id', userId);

        try {
            const response = await fetch('{{ route("admin.update.status") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const result = await response.json();
            console.log(result);
            if (result.success) {
                console.log('Status updated successfully for user ID:', userId);
                $('.status-' + userId).show();
            } else {
                $('.status-' + userId).append('Something went wrong');
            }
        } catch (error) {
            console.error('Request failed:', error.message);
        } finally {
            $('#loader-' + userId).hide();
            $('#status-' + userId).removeAttr('disabled');
        }
    }
</script>