<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
                {{ __('Add an announcement') }}
            </h2>
            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900 text-sm">
                <button
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1 px-2 rounded inline-flex items-center text-sm">
                    {{-- <span class="mr-1 flex items-center">
                        <i class="fas fa-arrow-left"></i>
                    </span> --}}
                    <span>Go Back</span>
                </button>
            </a>

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
                        @if (Auth::user()->hasRole(['administrator', 'super_admin']) || Auth::user()->hasAnyPermission(['add-announcement']))
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
                                    <span class="block sm:inline">Announcement created successfully!</span>
                                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                        <svg class="fill-current h-6 w-6 text-green-500" role="button"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Close</title>
                                            <path
                                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                        </svg>
                                    </span>
                                </div>


                                <form class="announcement-form" id="announcement-form" method="POST">
                                    @csrf

                                    <!-- Clear Form Fields Button -->
                                    <div class="flex justify-end mb-4">
                                        <button type="reset"
                                            class="px-2 py-1 bg-red-500 text-white text-sm font-semibold rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75">
                                            Clear form fields
                                        </button>
                                    </div>

                                    <!-- Add an announcement -->
                                    <div class="mb-6">
                                        <h2 class="text-xl font-semibold mb-2">Add an announcement</h2>
                                        <div class="border p-4 rounded-md bg-gray-50">
                                            <div class="col-span-1 md:col-span-2">
                                                <label class="block mb-2 text-gray-700">Announcement Content:</label>
                                                <textarea name="announcement_content" style="display:none;"></textarea>
                                                <div id="quill-content" class="bg-white rounded-md"
                                                    style="height: 200px;"></div>
                                                <p class="text-red-500 text-sm" id="announcement_content-error"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Start Date -->
                                    <div class="mb-4">
                                        <label for="start_date" class="block text-gray-700 dark:text-gray-300">Start Date:</label>
                                        <input type="text" id="start_date" name="start_date" class="w-full mt-1 rounded-md shadow-sm text-gray-800 dark:text-gray-200" placeholder="MM/DD/YYYY" value="{{ old('start_date') }}" readonly>
                                        <p class="text-red-500 text-sm" id="start_date-error"></p>
                                    </div>

                                    <!-- End Date -->
                                    <div class="mb-4">
                                        <label for="end_date" class="block text-gray-700 dark:text-gray-300">End Date:</label>
                                        <input type="text" id="end_date" name="end_date" class="w-full mt-1 rounded-md shadow-sm text-gray-800 dark:text-gray-200" placeholder="MM/DD/YYYY" value="{{ old('end_date') }}" readonly>
                                        <p class="text-red-500 text-sm" id="end_date-error"></p>
                                    </div>

                                    <!-- Add Announcement Button -->
                                    <div class="text-center">
                                        <x-primary-button class="ms-4 bg-blue" type="submit" id="generate-content-btn">
                                            <span id="generate-content-text">Add Announcement</span>
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

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const loader = document.getElementById('loader');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const alertBox = document.getElementById('error-alert');
        const successAlert = document.getElementById('success-alert');

        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'], // toggled buttons
            ['blockquote', 'code-block'],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'size': ['small', false, 'large', 'huge']
            }], // custom dropdown
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'align': []
            }],
            ['link', 'image'] // link and image
        ];

        var quill = new Quill('#quill-content', {
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Add some content...',
            theme: 'snow'
        });

        document.getElementById('announcement-form').onsubmit = function(event) {
            event.preventDefault();
            loader.style.display = 'block';
            document.getElementById('generate-content-text').textContent = 'Generating...';
            document.getElementById('generate-content-btn').disabled = true;

            const formData = new FormData(this);
            const quillContent = quill.root.innerHTML.trim() === '<p><br></p>' ? '' : quill.root.innerHTML;
            formData.append('announcement_content', quillContent);
            console.log('Form Data:', [...formData.entries()]);

            fetch("{{ route('announcement.store') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                })
                .then(response => {
                    document.getElementById('generate-content-text').textContent = 'Generate Site';
                    document.getElementById('generate-content-btn').disabled = false;

                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    loader.style.display = 'none';
                    alertBox.style.display = 'none';
                    successAlert.style.display = 'block';

                    // Redirect to the generated site URL after 2 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                })
                .catch(errors => {
                    console.error('Error generating content:', errors);

                    // Hide previous error alert
                    alertBox.style.display = 'none';
                    loader.style.display = 'none';
                    successAlert.style.display = 'none';

                    // Clear previous error messages
                    document.querySelectorAll('.text-red-500').forEach(el => el.textContent = '');

                    // Show general error alert if there's a server-side error
                    if (errors.error) {
                        alertBox.style.display = 'block';
                        alertBox.querySelector('span.block').textContent =
                            typeof errors.error === 'object' ?
                            'Something went wrong on the server. Fix errors and try again.' : errors.error
                        // Scroll to top
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }

                    // Handle validation errors
                    if (errors.error) {
                        Object.keys(errors.error).forEach(key => {
                            const errorElement = document.getElementById(`${key}-error`);
                            if (errorElement) {
                                errorElement.textContent = errors.error[key][0];
                            }
                        });

                        // Scroll to top
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                });
        };
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#start_date").datepicker({
                dateFormat: "mm/dd/yy",
                onSelect: function(selectedDate) {
                    // Set the minimum date for end_date to the selected start_date
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
            });

            $("#end_date").datepicker({
                dateFormat: "mm/dd/yy",
                onSelect: function(selectedDate) {
                    // Set the maximum date for start_date to the selected end_date
                    $("#start_date").datepicker("option", "maxDate", selectedDate);
                }
            });
        });
    </script>
</x-app-layout>
