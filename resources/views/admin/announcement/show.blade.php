<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
                {{ __('Announcements') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ url('announcements/create') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                    <button
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded inline-flex items-center text-sm">
                        <span>Add Announcement</span>
                    </button>
                </a>
                <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900 text-sm">
                    <button
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1 px-2 rounded inline-flex items-center text-sm">
                        <span>Go Back</span>
                    </button>
                </a>
            </div>
        </div>

    </x-slot>
    <style>
        #loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999;
            /* Ensure the loader appears above all other content */
        }

        .spinner {
            width: 50px;
            height: 50px;
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
    </style>

    <div id="loader" style="display:none">
        <div class="spinner"></div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($permissions->isEmpty() || $permissions->every(fn($permission) => empty($permission->link)))
                        <p>Coming soon!</p>
                    @else
                        @if (Auth::user()->hasRole(['administrator', 'super_admin']) || Auth::user()->hasAnyPermission(['remove-announcement']))
                            <div class="container mx-auto p-6 bg-white rounded-md">
                                <div id="error-alert"
                                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-3 mt-3 rounded relative"
                                    role="alert" style="display: none;">
                                    <strong class="font-bold">Oops!</strong>
                                    <span class="block sm:inline">Something went wrong on the server. Fix errors and try
                                        again.</span>
                                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                        <svg class="fill-current h-6 w-6 text-red-500" role="button"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Close</title>
                                            <path
                                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                        </svg>
                                    </span>
                                </div>

                                <div id="success-alert"
                                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-3 mt-3 rounded relative"
                                    role="alert" style="display: none;">
                                    <strong class="font-bold">Success!</strong>
                                    <span class="block sm:inline">Announcements removed successfully!</span>
                                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                        <svg class="fill-current h-6 w-6 text-green-500" role="button"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Close</title>
                                            <path
                                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                        </svg>
                                    </span>
                                </div>

                                @if (count($announcements) > 0)
                                    <!-- Info Bar -->
                                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4"
                                        role="alert">
                                        <span class="block sm:inline">Select the checkboxes to delete multiple
                                            announcements!</span>
                                    </div>
                                @else
                                    <!-- Info Bar -->
                                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4"
                                        role="alert">
                                        <span class="block sm:inline">No announcements available!</span>
                                    </div>
                                @endif

                                <form class="announcement-form" id="announcement-form" method="POST">
                                    @csrf

                                    <!-- Announcements List with Checkboxes -->
                                    <div class="space-y-4 mt-4 mb-4">
                                        @foreach ($announcements as $announcement)
                                            <div class="relative bg-white shadow-md rounded-lg p-4">
                                                <input type="checkbox" name="announcements[]"
                                                    value="{{ $announcement->id }}"
                                                    class="form-checkbox h-5 w-5 text-blue-600 absolute top-4 right-4">
                                                <div class="text-sm font-medium text-gray-900"
                                                    style="margin-left: 2rem; margin-top: -0.2rem;">
                                                    {!! $announcement->content !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>


                                    <!-- Add Announcement Button -->
                                    <div class="text-center">
                                        <x-primary-button class="ms-4 bg-blue" style="display: none;" type="submit"
                                            id="delete-button-btn">
                                            <span id="delete-button-text">Remove selected announcements</span>
                                            <span id="loader" class="ml-2" style="display: none;">
                                                <svg class="animate-spin h-5 w-5 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                            </span>
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const loader = document.getElementById('loader');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const alertBox = document.getElementById('error-alert');
        const successAlert = document.getElementById('success-alert');
        const checkboxes = document.querySelectorAll('input[name="announcements[]"]');
        const deleteButton = document.getElementById('delete-button-btn');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                deleteButton.style.display = anyChecked ? 'inline-block' : 'none';
            });
        });

        document.getElementById('announcement-form').onsubmit = function(event) {
            event.preventDefault();
            loader.style.display = 'block';
            document.getElementById('delete-button-text').textContent = 'Processing...';
            deleteButton.disabled = true;

            const formData = new FormData(this);
            const selectedAnnouncements = Array.from(formData.entries()).filter(entry => entry[0] === 'announcements[]')
                .map(entry => entry[1]);

            fetch("{{ route('announcements.delete') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        announcements: selectedAnnouncements
                    })
                })
                .then(response => {
                    loader.style.display = 'none';
                    document.getElementById('delete-button-text').textContent = 'Remove selected announcements';
                    deleteButton.disabled = false;

                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    loader.style.display = 'none';
                    successAlert.style.display = 'block';
                    alertBox.style.display = 'none';

                    // Remove deleted announcements from the UI
                    selectedAnnouncements.forEach(id => {
                        const checkbox = document.querySelector(`input[value="${id}"]`);
                        if (checkbox) {
                            checkbox.closest('.relative').remove();
                        }
                    });

                    // Hide the delete button
                    deleteButton.style.display = 'none';

                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                })
                .catch(errors => {
                    console.error('Error deleting announcements:', errors);

                    alertBox.style.display = 'block';
                    alertBox.querySelector('span.block').textContent =
                        'Something went wrong on the server. Fix errors and try again.';

                    loader.style.display = 'none';
                    deleteButton.disabled = false;

                    setTimeout(() => {
                        alertBox.style.display = 'none';
                    }, 5000);
                });
        };
    </script>
</x-app-layout>
