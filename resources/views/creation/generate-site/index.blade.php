<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
                {{ __('Forge: Site Generator') }}
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
                        @if (Auth::user()->hasRole(['administrator', 'super_admin']) || Auth::user()->hasAnyPermission(['generate-site']))
                            <div class="container mx-auto p-6 bg-white rounded-md dark:bg-gray-800">
                                <h1 class="text-2xl font-bold text-center mb-6">Site Generator</h1>
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

                                <form class="site-generator-form" id="site-generator-form" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Clear Form Fields Button -->
                                    <div class="flex justify-end mb-4">
                                        <button type="reset"
                                            class="px-2 py-1 bg-red-500 text-white text-sm font-semibold rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75">
                                            Clear form fields
                                        </button>
                                    </div>

                                    <!-- Option 1: Upload CSV -->
                                    <div class="mb-6">
                                        <h2 class="text-xl font-semibold mb-2">Option 1: Upload CSV File</h2>
                                        <div class="border p-4 rounded-md bg-gray-50">
                                            <label class="block mb-2 text-gray-700">Upload CSV:</label>
                                            <input type="file" name="csv_file"
                                                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                            <a href="{{ asset('generate-content/template.csv') }}"
                                                class="text-blue-500 hover:underline mt-2 text-xs inline-block">Download
                                                CSV Template</a>
                                            <p class="text-red-500 text-xs mt-1" id="csv_file-error"></p>
                                        </div>
                                    </div>

                                    <!-- If CSV is uploaded, these fields will not be used -->
                                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6"
                                        role="alert">
                                        <p class="font-bold">Note:</p>
                                        <p>If you upload a CSV file, the fields below will not be used for generating
                                            content.</p>
                                    </div>

                                    <!-- Option 2: Manual Input -->
                                    <div class="mb-6">
                                        <h2 class="text-xl font-semibold mb-2">Option 2: Manual Input</h2>
                                        <div
                                            class="border p-4 rounded-md bg-gray-50 grid gap-4 grid-cols-1 md:grid-cols-2">
                                            <div>
                                                <label class="block mb-2 text-gray-700">Base Site URL:</label>
                                                <input type="text" name="base_url"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter URL of the site to base this on" />
                                                <p class="text-red-500 text-sm" id="base_url-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Keywords
                                                    (comma-separated):</label>
                                                <input type="text" name="keywords"
                                                    class="w-full p-2 border rounded-md" placeholder="Enter keywords" />
                                                <p class="text-red-500 text-sm" id="keywords-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">City:</label>
                                                <input type="text" name="city"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the city name" />
                                                <p class="text-red-500 text-sm" id="city-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Core Focus of the
                                                    Site:</label>
                                                <input type="text" name="core_focus"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the core focus of the site" />
                                                <p class="text-red-500 text-sm" id="core_focus-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">First Keyword
                                                    Frequency:</label>
                                                <input type="number" min="1" name="first_keyword_frequency"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter frequency for first keyword" />
                                                <p class="text-red-500 text-sm" id="first_keyword_frequency-error">
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Second Keyword
                                                    Frequency:</label>
                                                <input type="number" min="1" name="second_keyword_frequency"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter frequency for second keyword" />
                                                <p class="text-red-500 text-sm" id="second_keyword_frequency-error">
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Third Keyword
                                                    Frequency:</label>
                                                <input type="number" min="1" name="third_keyword_frequency"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter frequency for third keyword" />
                                                <p class="text-red-500 text-sm" id="third_keyword_frequency-error">
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Additional Keyword
                                                    Frequency:</label>
                                                <input type="number" min="1"
                                                    name="additional_keyword_frequency"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter additional keyword frequency" />
                                                <p class="text-red-500 text-sm"
                                                    id="additional_keyword_frequency-error">
                                                </p>
                                            </div>
                                            <div class="col-span-1 md:col-span-2">
                                                <label class="block mb-2 text-gray-700">Additional Prompt
                                                    Instructions:</label>
                                                <textarea name="additional_instructions" class="w-full p-2 border rounded-md"
                                                    placeholder="Enter additional/custom instructions"></textarea>
                                                <p class="text-red-500 text-sm" id="additional_instructions-error">
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Required (For Both Options) -->
                                    <div class="mb-6">
                                        <h2 class="text-xl font-semibold mb-2">Required (For Both Options)</h2>
                                        <div
                                            class="border p-4 rounded-md bg-gray-50 grid gap-4 grid-cols-1 md:grid-cols-2">
                                            <div>
                                                <label class="block mb-2 text-gray-700">Format Like Site:</label>
                                                <input type="url" name="format_like_site"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the format like site" />
                                                <p class="text-red-500 text-sm" id="format_like_site-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Number of Copies:</label>
                                                <input type="number" min="1" name="number_of_copies"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the number of copies" />
                                                <p class="text-red-500 text-sm" id="number_of_copies-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Minimum Word Count:</label>
                                                <input type="number" min="1" max="2500" name="min_word_count"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the minimum word count" />
                                                <p class="text-red-500 text-sm" id="min_word_count-error"></p>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-gray-700">Maximum Word Count:</label>
                                                <input type="number" min="1" max="3000" name="max_word_count"
                                                    class="w-full p-2 border rounded-md"
                                                    placeholder="Enter the maximum word count" />
                                                <p class="text-red-500 text-sm" id="max_word_count-error"></p>
                                            </div>
                                            <div class="col-span-1 md:col-span-2">
                                                <label class="block mb-2 text-gray-700">Content Style:</label>
                                                <div class="flex items-center space-x-4">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="content_style[]"
                                                            class="form-checkbox" value="bullet-points">
                                                        <span class="ml-2 text-gray-700">Bullet Points</span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="content_style[]"
                                                            class="form-checkbox" value="numbering">
                                                        <span class="ml-2 text-gray-700">Numbering</span>
                                                    </label>
                                                </div>
                                                <p class="text-red-500 text-sm" id="content_style-error"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Generate Site Button -->
                                    <div class="text-center">
                                        <x-primary-button class="ms-4 bg-blue" type="submit"
                                            id="generate-content-btn">
                                            <span id="generate-content-text">Generate Site</span>
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

        document.getElementById('site-generator-form').onsubmit = function(event) {
            event.preventDefault();
            loader.style.display = 'block';
            document.getElementById('generate-content-text').textContent = 'Generating...';
            document.getElementById('generate-content-btn').disabled = true;

            const formData = new FormData(this);
            console.log('Form Data:', [...formData.entries()]);

            fetch("{{ route('creation.generate-site') }}", {
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

                    // Redirect to the generated site URL
                    window.location.href = data.redirect_url;
                })
                .catch(errors => {
                    console.error('Error generating content:', errors);

                    // Hide previous error alert
                    alertBox.style.display = 'none';
                    loader.style.display = 'none';

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
</x-app-layout>
