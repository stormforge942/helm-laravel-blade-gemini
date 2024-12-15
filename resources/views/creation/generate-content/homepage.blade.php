<x-app-layout>
    <style>
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #1C64F2;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        #post-success-alert,
        #post-error-alert {
            display: none;
            transition: opacity 0.5s ease-in-out;
        }

        #post-success-alert.show,
        #post-error-alert.show {
            display: block;
            opacity: 1;
        }

        .alert {
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        .alert.fade-out {
            opacity: 0;
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
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Edit Homepage
        </h1>
    </div>
    <!-- Success alert message -->
    <div id="post-success-alert"
        class="text-center p-4 mb-2 text-sm text-green-800 rounded-lg bg-green-200 dark:bg-gray-800 dark:text-green-500"
        role="alert" style="display: none;">
        <p></p>
    </div>
    <!-- Fail alert message -->
    <div id="post-error-alert"
        class="text-center p-4 mb-2 text-sm text-red-800 rounded-lg bg-red-200 dark:bg-gray-800 dark:text-red-500"
        role="alert" style="display: none;">
        <p></p>
    </div>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-8 gap-6">
        <form class="homepage-post-form" id="homepage-post-form" method="POST"
            action="{{ route('generate-content.homepage.update')}}">
            @csrf
            <!-- Niche Field -->
            <div class="mb-4 inline-flex items-center gap-4">
                <x-label-with-tooltip label="Niche" id="niche_helm"
                    text="Select the niche for the sites that you want to create posts for. After selecting niche, you will see a list of sites with that niche. This field is required." />
                <select name="niche_helm" id="niche_helm"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">Select topic</option>
                    @foreach($niches as $niche)
                    <option value="{{ $niche->niche }}">{{ $niche->niche }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4 inline-flex items-center gap-4 dark:text-gray-300 text-gray-800">
                <x-label-with-tooltip label="Sites" id="sites_label"
                    text="Select the niche for the sites that you want to create posts for. After selecting niche, you will see a list of sites with that niche. This field is required." />
                <select name="site" id="site"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">Select site</option>
                </select>
            </div>

            <!-- Tone Field -->
            <div class="mb-4 inline-flex items-center gap-4 ">
                <x-label-with-tooltip label="Tone" id="tone_label"
                    text="Add a tone for the site that you are generating content for. The default will be Professional. This field is required." />

                <select name="tone" id="tone"
                    class="shadow appearance-none border rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">Select tone</option>
                    @foreach(['Professional', 'Friendly', 'Authoritative', 'Informative', 'Persuasive'] as $tone)
                    <option value="{{ $tone }}" {{ $tone=='Professional' ? 'selected' : '' }}>{{ $tone }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Homepage Sections will be dynamically loaded here -->
            <div id="wordpress-content" style="display:none">

                <h2 class="text-l font-extrabold dark:text-white">Theme Settings</h2>

                <div id="theme-options-section"></div>

                <h2 class="text-l font-extrabold dark:text-white">SEO Meta</h2>

                <div class="mb-4">
                    <x-label-with-tooltip for="rank_math_focus_keyword" label="SEO Focus Keywords"
                        id="rank-math-focus-keyword-label"
                        text="Add the keywords you want to rank for in the Rank Math SEO plugin. This field is optional." />
                    <input type="text" id="rank_math_focus_keyword" name="rank_math_focus_keyword"
                        placeholder="Enter focus keywords, separated by commas"
                        class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <x-label-with-tooltip for="rank_math_description" label="SEO Meta Description"
                        id="rank-math-description-label"
                        text="This is what will appear as the description when this post shows up in the search results. This field is optional." />
                    <textarea id="rank_math_description" name="rank_math_description"
                        placeholder="Enter SEO description"
                        class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>

                <div class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/2 px-2">
                        <div class="mb-4">
                            <x-label-with-tooltip for="company_logo" label="Site Logo" id="company_logo_label"
                                text="Select a new image for the site or company logo. You will see this logo in the navigation bar on the left hand side. This field is optional." />
                            <img id="site-logo" alt="Site Logo"
                                class="image-preview mb-2 h-36 max-w-xs rounded shadow-md">
                            <input type="text" id="site-logo-alt" name="site-logo-alt" value=""
                                class="word-input w-full mb-2 p-2 border rounded" placeholder="Image Alt Text">

                            <input type="hidden" id="existing-company-logo-id" name="existing-company-logo-id" value="">
                            <input type="hidden" id="new-image-url-company-logo" name="new-image-url-company-logo" value="">
                            <input type="hidden" id="new-image-id-company-logo" name="new-image-id-company-logo" value="">

                            <library-button id="company-logo" section-id="company-logo"></library-button>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 px-2">
                        <div class="mb-4">
                            <x-label-with-tooltip for="favicon_logo" label="Site Favicon" id="favicon_logo_label"
                                text="Select a new image for the site favicon, also referred to as the site icon. The favicon will appear next to the page title in the browser tab. This field is optional." />
                            <img id="site-icon" alt="Site Favicon"
                                class="image-preview mb-2 h-16 max-w-xs rounded shadow-md">
                            <input type="text" id="site-icon-alt" name="site-icon-alt" value=""
                                class="word-input w-full mb-2 p-2 border rounded" placeholder="Image Alt Text">
                            <input type="hidden" id="existing-favicon-logo-id" name="existing-favicon-logo-id" value="">
                            <input type="hidden" id="new-image-url-favicon-logo" name="new-image-url-favicon-logo" value="">
                            <input type="hidden" id="new-image-id-favicon-logo" name="new-image-id-favicon-logo" value="">

                            <library-button id="favicon-logo" section-id="favicon-logo"></library-button>
                        </div>
                    </div>
                </div>

                <h2 class="text-xl font-extrabold dark:text-white">Sections</h2>

                <input type="text" style="display:none" id="postId" name="postId">
                <textarea name="sections" id="sections" style="display:none"></textarea>
                <div id="homepage-sections"></div>

                <div id="total-word-count" class="text-center text-gray-700 font-bold">Total Word Count: 0</div>


                <button type="submit" id="confirm-publish-btn"
                    class="mr-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Publish
                </button>

                <div id="loading"
                    class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-blue-600 motion-reduce:animate-[spin_1.5s_linear_infinite]"
                    role="status" style="display:none">
                    <span
                        class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]"
                        >Loading...</span
                    >
                </div>

            </div>

        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.8/purify.min.js"></script>
    <script>
        window.csrfToken = "{{ csrf_token() }}";
        window.wpSitesBaseUrl = "{{ url('/wp-sites?niche=') }}";
        window.generateContent = "{{ url('/creation/generate-content/homepage') }}";
        window.uploadFileToWordpress = " {{route('wordpress.upload.file')}}";
    </script>
    @vite(['resources/js/wordpress/homepage-handler.js'])

</x-app-layout>
