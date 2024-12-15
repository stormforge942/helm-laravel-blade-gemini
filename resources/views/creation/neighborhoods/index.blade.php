<x-app-layout>
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Create New Neighborhoods
        </h1>
    </div>


    @if(session('success'))
    <div id="post-success-alert" class="p-4 mb-2 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div id="post-error-alert" class="p-4 mb-2 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-8">
        <a href="{{ route('creation.neighborhoods.googlePoi') }}" class="text-blue-500 hover:text-gray-300">
            {{ __('Modify Google Points of Interests') }}
        </a>
    </div>

    <div id="loader" style="display:none">
        <div class="spinner"></div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-8">
        <form id="neighborhoodForm" method="POST" enctype="multipart/form-data" action="{{ route('creation.neighborhood-posts.store') }}">
            @csrf

            <!-- City Field -->
            <div class="mb-4">
                <x-label-with-tooltip label="City" id="city" text="Enter a city. This field is required." />
                <input type="text" name="city" id="city" placeholder="Enter a city to begin"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <!-- State Field -->
            <div class="mb-4">
                <x-label-with-tooltip label="State" id="state" text="Select state. This field is required" />
                <select name="state" id="state"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">Select State</option>
                    @foreach(['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'] as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center p-4 my-4 text-sm text-teal-800 rounded-lg bg-teal-100 dark:bg-gray-800 dark:text-teal-400" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Tip</span>
                <div>
                    <span class="font-medium">Tip:</span> To get a list of neighborhoods generated for you, enter the number of neighborhoods and click 'Generate neighborhoods'. If you already have a list of neighborhoods, click the button to 'Add neighborhoods'.
                </div>
            </div>

            <!-- Number of Neighborhoods Field -->
            <div class="mb-4">
                <x-label-with-tooltip label="Number of neighborhoods" id="num_neighborhoods_label"
                    text="Enter the number of neighborhoods you would like to be generated based on location. This field will be set to 1 by default. The maximum number of neighborhoods is 20. This field is not required if you decide to add your own neighborhoods." />
                <div class="field ml-2">
                    <input type="number" id="num_neighborhoods" name="num_neighborhoods" value="1" min="1" max="20"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <!-- Generate or Add Neighborhoods Buttons -->
            <div class="content-area mb-4">
                <div class="max-w-7xl flex items-center justify-center mb-4">
                    <x-primary-button class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button" id="generate-neighborhoods-btn">
                        {{ __('Generate neighborhoods') }}
                    </x-primary-button>
                    <x-primary-button class="ms-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button" id="add-neighborhoods-btn">
                        {{ __('Add neighborhoods') }}
                    </x-primary-button>
                </div>

                <!-- Manual Neighborhood Input -->
                <div id="add-neighborhoods-input" class="mb-4" style="display: none;">
                    <input type="text" id="manual-neighborhood" placeholder="Enter neighborhoods separated by commas"
                        class="mb-4 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <x-primary-button id="confirm-neighborhoods-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button">
                        {{ __('Add') }}
                    </x-primary-button>
                    <x-primary-button id="cancel-neighborhoods-btn" class="ms-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button">
                        {{ __('Cancel') }}
                    </x-primary-button>
                </div>

                <!-- Generated Neighborhoods Display -->
                <h2 class="text-xl font-bold mb-4 dark:text-gray-400">Neighborhoods</h2>

                <div id="generated-neighborhoods" class="space-y-4 mb-4" style="display:none">
                </div>

                <input type="hidden" id="neighborhoods_list" name="neighborhoods_list">
            </div>

            <hr class="py-3 border-t border-gray-300">

            <div class="flex items-center p-4 my-4 text-sm text-teal-800 rounded-lg bg-teal-100 dark:bg-gray-800 dark:text-teal-400" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Tip</span>
                <div>
                    <span class="font-medium">Tip:</span> Next, retrieve AI-generated content by selecting the topic and number of paragraphs to generate. Include keywords for better content.
                </div>
            </div>

            <!-- Niche Field -->
            <div class="my-4">
                <x-label-with-tooltip label="Topic" id="niche" text="Add a topic or niche for the site that you are generating content for. This field is required." />
                <select name="niche" id="niche"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option value="">Select topic</option>
                    @foreach($niches as $niche)
                    <option value="{{ $niche->niche }}">{{ $niche->niche }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Number of Paragraphs Field -->
            <div class="mb-4">
                <x-label-with-tooltip label="Number of paragraphs to generate for each neighborhood on the topic"
                    id="num_paragraphs_label"
                    text="Enter the number of paragraphs you would like to be generated. This field will be set to 1 by default. The maximum number of paragraphs is 20." />
                <div class="field ml-2">
                    <input type="number" id="num_paragraphs" name="num_paragraphs" value="1" min="1" max="20"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <!-- Keywords Field -->
            <div class="mb-4">
                <x-label-with-tooltip label="Keywords" id="keywords"
                    text="Add any keywords you would like neighborhoods content to include, please separate with commas. This field is optional." />
                <div class="field ml-2">
                    <input type="text" id="keywords" name="keywords" value="" placeholder="Separate keywords with commas"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <!-- Generate Content Button -->
            <div class="max-w-7xl flex items-center justify-center mb-4">
                <x-primary-button id="generate-content-btn"
                    class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Generate content') }}
                </x-primary-button>
            </div>

            <!-- Generated Content Display -->
            <h2 class="text-xl font-bold mb-4 dark:text-gray-400">Content</h2>
            <div id="generated-content" class="space-y-4 mb-4"></div>

            <hr class="py-3 border-t border-gray-300">

            <!-- <div class="flex items-center p-4 my-4 text-sm text-teal-800 rounded-lg bg-teal-100 dark:bg-gray-800 dark:text-teal-400" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Tip</span>
                <div>
                    <span class="font-medium">Tip:</span> Click 'Generate places' to get a list of 10 places to go to in the city generated for you. You can click it multiple times to get a new list. If you already have a list available, click the button to 'Add places'.
                </div>
            </div> -->

            <!-- Generate Places Section -->
            <!-- <div class="max-w-7xl flex items-center justify-center my-4">
                <x-primary-button id="generate-places-btn"
                    class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Generate places') }}
                </x-primary-button>

                <x-primary-button class="ms-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button" id="add-places-btn">
                    {{ __('Add places') }}
                </x-primary-button>
            </div> -->

            <!-- Manual Places Input -->
            <div id="add-places-input" class="mb-4" style="display: none;">
                <input type="text" id="manual-places" placeholder="Enter places separated by commas"
                    class="mb-4 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <x-primary-button id="confirm-places-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Add') }}
                </x-primary-button>
                <x-primary-button id="cancel-places-btn" class="ms-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Cancel') }}
                </x-primary-button>
            </div>

            <!-- Generated Places Display -->
            <!-- <h2 class="text-xl font-bold mb-4">Places to go to</h2>
            <div id="generated-places" class="space-y-4 mb-4"></div>

            <input type="hidden" id="places_list" name="places_list">

            <hr class="py-3 border-t border-gray-300">

            <div class="flex items-center p-4 my-4 text-sm text-teal-800 rounded-lg bg-teal-100 dark:bg-gray-800 dark:text-teal-400" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Tip</span>
                <div>
                    <span class="font-medium">Tip:</span> Next, click 'Generate driving directions' to get Google maps directions to the places. If available, include a company address for the start location. The content will be added to the text editor below, modify as needed. This section will be included in all the neighborhood posts for the selected city.
                </div>
            </div> -->

            <!-- Address Field -->
            <div class="my-4">
                <x-label-with-tooltip label="Company address" id="address"
                    text="Add a company address as a starting point for the directions, if available. If you do not have the company address, the directions starting point will be the center of the city. This field is optional." />
                <div class="field ml-2">
                    <input type="text" id="address" name="address" value="" placeholder="Optional company address"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <small class="text-red-600">You can leave this field empty if you do not have the company address.</small>
                </div>
            </div>

            <!-- Generate iframe Button -->
            <div class="max-w-7xl flex items-center justify-center mb-4">
                <x-primary-button id="generate-iframe-btn"
                    class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Generate iframe') }}
                </x-primary-button>
            </div>

            <div id="generated-iframe">

            </div>
            <input type="hidden" id="hidden_iframe" name="hidden_iframe">
            <!-- Generate Directions Button -->
            <!-- <div class="max-w-7xl flex items-center justify-center mb-4">
                <x-primary-button id="generate-directions-btn"
                    class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    {{ __('Generate driving directions') }}
                </x-primary-button>
            </div> -->

            <!-- Generated Directions Display -->
            <!-- <h2 class="text-xl font-bold mb-4">Google Maps driving directions</h2>
            <div id="generated-directions" class="space-y-4 mb-4"></div> -->

            <div class="mb-4" style="display:none">
                <x-label-with-tooltip label="Directions list" id="directions_list_label"
                    text="After clicking Generate directions, the list of Google maps directions will be added to the text editor below. Format the content as desired. This content will be added to every neighborhoods post." />
                <textarea id="directions_list" name="directions_list" class="text-normal shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;"></textarea>
                <div id="directions-quill-container"></div>
            </div>

            <!-- Preview Section -->

            <div class="mb-4" style="display:none">
                <x-label-with-tooltip label="Neighborhoods post content" id="post_content"
                    text="Content for the page. This is a required field. The content will be automatically populated with the a previously created page." />
                <textarea id="page_content" name="page_content" class="text-normal shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;"></textarea>
                <div id="quill-content"></div>
            </div>
            <textarea id="hiddenTextarea" name="combinedNeighborhoodsContent" style="display: none;"></textarea>

            <x-primary-button class="ms-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="button" id="publish-btn">
                {{ __('Publish') }}
            </x-primary-button>

            <!-- Publish Popup -->
            <div id="publish-popup" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start my-3">
                                <div class="mt-3 text-center">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2" id="modal-title">
                                        Publish Content
                                    </h3>
                                    <div class="mt-2">
                                        <!-- Niche selection dropdown -->
                                        <div class="mb-4">

                                            <label for="server_option" class="block text-gray-700 font-bold mb-2">Niche</label>
                                            <select id="nicheSelect" name="server" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2">
                                                <option value="">Select niche</option>
                                                @foreach($siteNiches as $niche)
                                                    <option value="{{ $niche->niche }}">{{ $niche->niche }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <!-- Site selection dropdown -->
                                        <div class="mb-4">
                                            <label for="site_option" class="block text-gray-700 font-bold mb-2">Site</label>
                                            <select name="siteListDropdown" id="siteListDropdown" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2">
                                                <option value="">Select site</option>
                                            </select>
                                        </div>
                                        <!-- Hidden input for siteId -->
                                        <input type="hidden" id="siteId" name="siteId" value="">
                                        <div id="siteDetails" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" id="confirm-publish-btn" class="mr-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Publish
                                </button>
                                <button type="button" id="cancel-publish-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</x-app-layout>

<style>
    #loader {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 999; /* Ensure the loader appears above all other content */
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let quillEditors = [];

    const form = document.getElementById('neighborhoodForm');
    const generateNeighborhoodsButton = document.getElementById('generate-neighborhoods-btn');
    const addNeighborhoodsButton = document.getElementById('add-neighborhoods-btn');
    const cancelNeighborhoodsButton = document.getElementById('cancel-neighborhoods-btn');
    const addNeighborhoodsInputDiv = document.getElementById('add-neighborhoods-input');
    const generateContentButton = document.getElementById('generate-content-btn');
    const generatePlacesButton = document.getElementById('generate-places-btn');
    const addPlacesButton = document.getElementById('add-places-btn');
    const cancelPlacesButton = document.getElementById('cancel-places-btn');
    const addPlacesInputDiv = document.getElementById('add-places-input');
    const generateDirectionsButton = document.getElementById('generate-directions-btn');
    const manualNeighborhoodInput = document.getElementById('manual-neighborhood');
    const confirmNeighborhoodsButton = document.getElementById('confirm-neighborhoods-btn');
    const confirmPlacesButton = document.getElementById('confirm-places-btn');

    const generatedNeighborhoodsDiv = document.getElementById('generated-neighborhoods');
    const generatedContentDiv = document.getElementById('generated-content');
    const generatedPlacesDiv = document.getElementById('generated-places');
    const generatedDirectionsDiv = document.getElementById('generated-directions');
    const loader = document.getElementById('loader');

    const hiddenNeighborhoodsInput = document.getElementById('neighborhoods_list');
    const hiddenPlacesInput = document.getElementById('places_list');
    const hiddenDirectionsInput = document.getElementById('directions_list');
    const siteIdInput = document.getElementById('siteId');

    const quillDirectionsContainer = document.getElementById('directions-quill-container');

    const publishButton = document.getElementById('publish-btn');
    const publishPopup = document.getElementById('publish-popup');
    const cancelPublishButton = document.getElementById('cancel-publish-btn');
    const confirmPublishButton = document.getElementById('confirm-publish-btn');
    const iframeBtn =  document.getElementById('generate-iframe-btn');
    const generatedIframetDiv = document.getElementById('generated-iframe');
    const hiddenIframeInput = document.getElementById('hidden_iframe');
    let neighborhoods = [];
    let places = [];

    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'script': 'sub' }, { 'script': 'super' }],      // superscript/subscript
        [{ 'indent': '-1' }, { 'indent': '+1' }],          // outdent/indent
        [{ 'direction': 'rtl' }],                         // text direction
        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['clean'],                                         // remove formatting button
        ['link', 'image', 'video']                         // link and image, video
    ];

    const quill = new Quill('#quill-content', {
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: 'Compose an epic...',
        theme: 'snow'
    });

    const directionsQuill = new Quill(quillDirectionsContainer, {
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: 'Generate directions...',
        theme: 'snow'
    });

    function copyToEditor(content) {
        const currentContent = quill.root.innerHTML;
        const newContent = currentContent + content;
        quill.clipboard.dangerouslyPasteHTML(newContent);
    }

    function handleResponse(response, container, updateHiddenInput = false, hiddenInputDiv = null) {
        container.innerHTML = ""; // Clear previous results

        if (!Array.isArray(response)) {
            container.innerHTML = `<p>Error: Expected array but got ${typeof response}</p>`;
            return;
        }

        const content = document.createElement('div');
        content.className = "content-container p-4 border rounded-lg shadow-md bg-white";

        const responseList = [];

        response.forEach((item) => {
            const itemBlock = document.createElement('div');
            itemBlock.className = "content-item mb-2";
            itemBlock.innerHTML = `<p>${item}</p>`;
            content.appendChild(itemBlock);

            if (updateHiddenInput) {
                responseList.push(item);
            }
        });

        container.appendChild(content);
        container.style.display = "block";

        if (updateHiddenInput && hiddenInputDiv) {
            hiddenInputDiv.value = JSON.stringify(responseList);
            console.log(hiddenInputDiv.value);
        }
    }

    function handleContentResponse(response, container, updateHiddenInput = false, hiddenInputDiv = null) {
        container.innerHTML = ""; // Clear previous results
        if (!Array.isArray(response)) {
            container.innerHTML = `<p>Error: Expected array but got ${typeof response}</p>`;
            return;
        }

        console.log(response);

        const content = document.createElement('div');
        content.className = "content-container p-4 border rounded-lg shadow-md bg-white";

        response.forEach((item, index) => {
            const itemBlock = document.createElement('div');
            itemBlock.className = "content-item mb-4";

            const neighborhoodTitle = document.createElement('h3');
            neighborhoodTitle.textContent = `Neighborhood: ${item.neighborhood}`;
            neighborhoodTitle.className = 'text-bold';

            const postTitleInput = document.createElement('input');
            postTitleInput.type = 'text';
            postTitleInput.id = `post-title-${index}`;
            postTitleInput.name = `post-title-${index}`;
            postTitleInput.placeholder = 'Enter post title';
            postTitleInput.className = 'w-full py-2 px-3 mb-2 border rounded';
            postTitleInput.required = true;
            postTitleInput.value = item.neighborhood;

            const quillContainer = document.createElement('div');
            quillContainer.id = `quill-${index}`;

            // Create hidden textarea for each neighborhood
            const hiddenTextarea = document.createElement('textarea');
            hiddenTextarea.id = `hidden-quill-${index}`;
            hiddenTextarea.name = `hidden-quill-${index}`;
            hiddenTextarea.style.display = 'none';

            itemBlock.appendChild(neighborhoodTitle);
            itemBlock.appendChild(postTitleInput);
            itemBlock.appendChild(quillContainer);
            itemBlock.appendChild(hiddenTextarea);
            content.appendChild(itemBlock);
        });

        container.appendChild(content);

        // Ensure DOM is updated before initializing Quill editors
        response.forEach((item, index) => {
            const quill = new Quill(`#quill-${index}`, {
                modules: { toolbar: toolbarOptions },
                placeholder: 'Compose your content...',
                theme: 'snow'
            });

            quill.clipboard.dangerouslyPasteHTML(item.content);

            quillEditors.push(quill);

            // Set initial content of hidden textarea
            document.getElementById(`hidden-quill-${index}`).value = quill.root.innerHTML;

            // Update hidden textarea with Quill content on change
            quill.on('text-change', function () {
                document.getElementById(`hidden-quill-${index}`).value = quill.root.innerHTML;
            });

            console.log(document.getElementById(`hidden-quill-${index}`).value);
        });
    }

    function collectAndCombineContent() {
        const nicheId = document.getElementById('niche').value;

        const neighborhoods = [];

        quillEditors.forEach((quill, index) => {
            const generatedContent = document.querySelector(`#hidden-quill-${index}`).value;
            const postTitle = document.querySelector(`#post-title-${index}`).value;
            const neighborhoodTitle = document.querySelector(`#quill-${index}`).parentNode.querySelector('h3').textContent;
            const neighborhood = neighborhoodTitle.replace('Neighborhood: ', '');

            neighborhoods.push({
                neighborhood: neighborhood,
                post_title: postTitle,
                content: generatedContent
            });
        });

        return { nicheId, neighborhoods };
    }

    function submitForm(endpoint, container, updateHiddenInput = false, hiddenInputDiv = null, quillContent = false) {
        loader.style.display = 'block';
        const formData = new FormData(form);

        fetch(endpoint, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                loader.style.display = 'none';
                handleResponse(data, container, updateHiddenInput, hiddenInputDiv);
                if (quillContent) {
                    handleContentResponse(data, container, updateHiddenInput, hiddenInputDiv);
                }
            })
            .catch(error => {
                loader.style.display = 'none';
                console.error(`Error generating ${container.id}:`, error);
                container.innerHTML = `<p>Error: ${error.message}</p>`;
            });
    }

    generateNeighborhoodsButton.onclick = function () {
        submitForm("{{ route('fetch.creation.generate-neighborhoods') }}", generatedNeighborhoodsDiv, true, hiddenNeighborhoodsInput);
    };

    generateContentButton.onclick = function () {
        submitForm("{{ route('fetch.creation.generate-neighborhoods-content') }}", generatedContentDiv, false, null, true);
    };

    iframeBtn.onclick = function(){
        submitForm("{{ route('fetch.creation.generate-iframe') }}", generatedIframetDiv, true,hiddenIframeInput, false);
    };
    /*generatePlacesButton.onclick = function () {
        submitForm("{{ route('fetch.creation.generate-places') }}", generatedPlacesDiv, true, hiddenPlacesInput);
    };*/

    /*generateDirectionsButton.onclick = function () {
        const placesList = JSON.parse(document.getElementById('places_list').value || "[]");
        const address = document.getElementById('address')?.value || '';
        const city = document.getElementById('city').value;
        const state = document.getElementById('state').value;
        generatedDirectionsDiv.innerHTML = ""; // Clear previous results

        if (placesList.length < 1) {
            generatedDirectionsDiv.innerHTML = "<p>Please generate at least one place to get directions.</p>";
            return;
        }

        const cleanedPlacesList = placesList.map(place => place.replace(/<\/?[^>]+(>|$)/g, ""));

        const formData = new FormData();
        formData.append('places_list', JSON.stringify(cleanedPlacesList));
        formData.append('address', address);
        formData.append('city', city);
        formData.append('state', state);

        fetch("{{ route('fetch.creation.generate-directions') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.error) {
                generatedDirectionsDiv.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            let directionContent = '<div>';

            data.forEach((direction, index) => {
                directionContent += `
                    <div style="display: table-cell; padding: 5px; vertical-align: top;">
                        <h4 style="font-weight: bold">${direction.destination}</h4>
                        <iframe
                            width="300"
                            height="400"
                            style="border:0"
                            loading="lazy"
                            allowfullscreen
                            src="${direction.embedLink}">
                        </iframe>
                    </div>
                `;
            });

            directionContent += '</div>';

            // Add the direction content to the Quill editor
            directionsQuill.clipboard.dangerouslyPasteHTML(directionContent, 'api');

            // Store the Quill content in the hidden input field
            hiddenDirectionsInput.value = directionsQuill.root.innerHTML;

        })
        .catch(error => {
            console.error('Error generating directions:', error);
        });
    };*/

    addNeighborhoodsButton.onclick = function () {
        addNeighborhoodsInputDiv.style.display = 'block';
    };

    cancelNeighborhoodsButton.onclick = function () {
        addNeighborhoodsInputDiv.style.display = 'none';
    };

    confirmNeighborhoodsButton.onclick = function () {
        const input = document.getElementById('manual-neighborhood');
        const neighborhoodsStr = input.value.trim();
        if (neighborhoodsStr) {
            const newNeighborhoods = neighborhoodsStr.split(',').map(neighborhood => neighborhood.trim()).filter(neighborhood => neighborhood !== "");
            neighborhoods.push(...newNeighborhoods);
            updateNeighborhoodsDisplay();
            hiddenNeighborhoodsInput.value = JSON.stringify(neighborhoods);
            console.log(hiddenNeighborhoodsInput.value);
            input.value = "";
        }
        addNeighborhoodsInputDiv.style.display = 'none';
    };

    function updateNeighborhoodsDisplay() {
        generatedNeighborhoodsDiv.innerHTML = `
            ${neighborhoods.map(neighborhood => `<div class="bg-white rounded-lg p-4 neighborhood-item mb-2"><p>${neighborhood}</p></div>`).join('')}
        `;
        generatedNeighborhoodsDiv.style.display = "block";
    }

    /*addPlacesButton.onclick = function () {
        addPlacesInputDiv.style.display = 'block';
    };*/

    cancelPlacesButton.onclick = function () {
        addPlacesInputDiv.style.display = 'none';
    };

    confirmPlacesButton.onclick = function () {
        const input = document.getElementById('manual-places');
        const placesStr = input.value.trim();
        if (placesStr) {
            const newPlaces = placesStr.split(',').map(place => place.trim()).filter(place => place !== "");
            places.push(...newPlaces);
            updatePlacesDisplay();
            hiddenPlacesInput.value = JSON.stringify(places);
            input.value = "";
        }
        addPlacesInputDiv.style.display = 'none';
    };

    function updatePlacesDisplay() {
        generatedPlacesDiv.innerHTML = "";

        places.forEach((place, index) => {
            const item = document.createElement('div');
            item.className = "place-item";
            item.innerHTML = `<p>${index + 1}. ${place}</p>`;
            generatedPlacesDiv.appendChild(item);
        });
    }

    publishButton.addEventListener('click', function () {
        const element = document.getElementById('generated-iframe');

        // Check if the element exists and contains an iframe
        if (element && element.querySelector('iframe')) {
            publishPopup.style.display = 'block';
        }
        else{
            alert('Please Generate Iframe Before Publishing.');
        }
    });

    cancelPublishButton.addEventListener('click', function () {
        publishPopup.style.display = 'none';
    });

    $('#nicheSelect').change(function () {
        const server = this.value;
        const url = new URL('{{ route('creation.neighborhoods.byNiche.sites') }}');
        url.searchParams.append('niche', server);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(sites => {
                let siteListDropdown = $('#siteListDropdown');
                siteListDropdown.empty().append('<option value="">Select Site</option>');
                sites.forEach(site => {
                    siteListDropdown.append(`<option value="${site.id}">${site.site_url}</option>`);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    $('#siteListDropdown').change(function () {
        siteId = $(this).val();

        console.log(siteId);
        const siteDetails = $('#siteDetails');
        const siteIdInput = $('#siteIdInput');
        siteDetails.html('');

        if (!siteId) {
            siteDetails.text('Please select a site.');
        } else {
            siteIdInput.val(siteId);
        }

        const url = '{{ route('fetch.pages.service') }}' + '?siteId=' + siteId;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const heading = document.createElement('h3');
                    heading.textContent = 'Connected to site!';
                    siteDetails.append(heading);
                } else {
                    $('#create-wordpress-page-form').hide();
                    siteDetails.text('Unable to connect to database. Please let a developer know.');
                }
            })
            .catch(error => {
                siteDetails.text('An error occurred while loading the pages: ' + error.message);
            });
    });

    confirmPublishButton.addEventListener('click', function () {
        confirmPublishButton.disabled = true;
        const niche = document.getElementById('nicheSelect').value;
        const site = document.getElementById('siteListDropdown').value;

        if (!niche || !site) {
            alert("Please select both a niche and a site.");
            return;
        }

        // Collect and set hidden input values
        const { nicheId, neighborhoods } = collectAndCombineContent();
        hiddenDirectionsInput.value = directionsQuill.root.innerHTML;

        // Set hidden input values in the form
        document.getElementById('niche').value = nicheId;
        document.getElementById('neighborhoods_list').value = JSON.stringify(neighborhoods);

        // Trigger the form submission
        document.getElementById('neighborhoodForm').submit();

    });

});
</script>
