<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
                {{ __('Forge: Bulk SEO Insert/Update') }}
            </h2>

            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900 text-sm">
                <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1 px-2 rounded inline-flex items-center text-sm">
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
    @if(session('success'))
    <div id="post-success-alert" class="p-4 mb-2 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($permissions->isEmpty() || $permissions->every(fn($permission) => empty($permission->link)))
                    <p>Coming soon!</p>
                    @else
                    @if (Auth::user()->hasRole(['administrator', 'super_admin']) || Auth::user()->hasAnyPermission(['generate-site']))
                    <div class="container mx-auto p-6 bg-white rounded-md">
                        <h1 class="text-2xl font-bold text-center mb-6">Bulk SEO Insert/Update</h1>
                        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-3 mt-3 rounded relative" role="alert" style="display: none;">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">Something went wrong on the server. Fix errors and try
                                again.</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                </svg>
                            </span>
                        </div>

                        <form class="seo-update-form" id="seo-update-form" method="POST" enctype="multipart/form-data" action="{{ url('/creation/seo-bulk-update') }}">
                            @csrf

                            <!-- Clear Form Fields Button -->
                            <div class="flex justify-end mb-4">
                                <button type="reset" class="px-2 py-1 bg-red-500 text-white text-sm font-semibold rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75">
                                    Clear form fields
                                </button>
                            </div>

                            <!-- Option 1: Upload CSV -->
                            <div class="mb-6">
                                <h2 class="text-xl font-semibold mb-2">Option 1: Upload CSV File</h2>
                                <div class="border p-4 rounded-md bg-gray-50">
                                    <label class="block mb-2 text-gray-700">Upload CSV:</label>
                                    <input type="file" name="csv_file" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <a href="{{ asset('generate-content/seo-template.csv') }}" class="text-blue-500 hover:underline mt-2 text-xs inline-block">Download
                                        CSV Template</a>
                                    <p class="text-red-500 text-xs mt-1" id="csv_file-error"></p>
                                </div>
                            </div>



                            <!-- Generate Site Button -->
                            <div class="text-center">
                                <x-primary-button class="ms-4 bg-blue" type="submit" id="generate-content-btn">
                                    <span id="generate-content-text">SEO Update</span>
                                    <span id="loader" class="ml-2" style="display: none;">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
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
</x-app-layout>