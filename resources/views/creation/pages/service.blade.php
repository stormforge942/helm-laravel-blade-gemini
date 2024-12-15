<x-app-layout>
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Create Service Page
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
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 my-8">
        <!-- Server selection dropdown -->
        <div class="mb-4">
            <label for="server_option" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Server
                <button data-popover-target="server-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg><span class="sr-only">Show information</span></button>
            </label>
            <!-- Tip tool -->
            <div data-popover id="server-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                <div class="p-3 space-y-2">
                    <p>Select the server where site is located to get started.</p>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg></a>
                </div>
                <div data-popper-arrow></div>
            </div>
            <select id="serverSelect" name="server" class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
                <option value="">Select server</option>
                @foreach ($servers as $server)
                <option value="{{ $server }}">{{ $server }}</option>
                @endforeach
            </select>

        </div>

        <!-- Site list dynamical dropdown -->
        <div>
            <label for="site_option" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Site
                <button data-popover-target="site-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg><span class="sr-only">Show information</span></button>
            </label>
            <!-- Tip tool -->
            <div data-popover id="site-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                <div class="p-3 space-y-2">
                    <p>Select the url of the site you want to connect to. You will see if the connection was successful beneath this field. The form for making edits will display after you select a site.</p>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg></a>
                </div>
                <div data-popper-arrow></div>
            </div>
            <select name="siteListDropdown" id="siteListDropdown" class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
                <option value="">Select site</option>
            </select>

        </div>

        <div id="siteDetails" class="mt-2"></div>

        <form id="create-wordpress-page-form" class="mt-4" method="POST" action="{{ route('creation.service-pages.store') }}" style="display:none">
            @csrf
            <input type="hidden" name="siteId" id="siteIdInput" value="">

            <div class="mb-4">
                <label for="page_title" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Page Title
                    <button data-popover-target="page-title-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>
                <!-- Tip tool -->
                <div data-popover id="page-title-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Title for the page. This is a required field.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="page_title" name="page_title" placeholder="Enter page title" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>


            <div class="mb-4">
                <label for="page_content" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Page Content
                    <button data-popover-target="page-content-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>
                <!-- Tip tool -->
                <div data-popover id="page-content-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Content for the page. This is a required field. The content will be automatically populated with the a previously created page.</p>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tips</h3>
                        <ul class="space-y-1 text-gray-700 list-disc list-inside dark:text-gray-500">
                            <li>Use the template given or paste in your own.</li>
                            <li>Use the rich text editor to format your content. The content will show up in WordPress how you see it here. Ensure you do not use HTML tags, only the formatting from the editor itself.</li>
                            <li>To use shortcodes, surround them with { } like you would when editing in WordPress.</li>
                        </ul>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                
                <textarea id="page_content" name="page_content" class="text-normal shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;"></textarea>
                <div id="quill-content" class="dark:text-gray-300"></div>
            </div>


            <div class="mb-4">
                <label for="post_status" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Post Status
                    <button data-popover-target="post-status-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>
                </label>
                <!-- Tip tool -->
                <div data-popover id="post-status-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Publish status for page. This is a required field. The page can either be in draft or published status.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <select name="post_status" id="post_status" onchange="logValue(this)" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Status</option>
                    <option value="draft">Draft</option>
                    <option value="publish">Publish</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="page_template" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Template
                    <button data-popover-target="page-template-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>
                <!-- Tip tool -->
                <div data-popover id="page-template-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Template being used for page. You will not be able to edit this field at this time. This is only informational.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="page_template" name="page_template" placeholder="Right side bar template" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>


            <div class="mb-4">
                <label for="page_category" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Category
                    <button data-popover-target="page-category-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>

                <!-- Tip tool -->
                <div data-popover id="page-category-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>The category for the page. You will not be able to edit this field at this time. This is only informational.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="page_category" name="page_category" placeholder="Service" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>

            
            <div class="mb-4">
                <label for="rank_math_focus_keyword" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">{{ __('Rank Math Focus Keywords') }}
                <button data-popover-target="rank-math-focus-keyword-select-popover-description" data-popover-placement="bottom-end" type="button">
                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Show information</span>
                    </button>
                </label>
                  <!-- Tip tool -->
                  <div data-popover id="rank-math-focus-keyword-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Add the keywords you want to rank for in the Rank Math SEO plugin. This field is optional.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="rank_math_focus_keyword" name="rank_math_focus_keyword" placeholder="Enter focus keywords, separated by commas" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="rank_math_description" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">{{ __('Rank Math Meta Description') }}
                <button data-popover-target="rank-math-description-select-popover-description" data-popover-placement="bottom-end" type="button">
                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Show information</span>
                    </button>
                </label>
                  <!-- Tip tool -->
                  <div data-popover id="rank-math-description-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>This is what will appear as the description when this post shows up in the search results. This field is optional.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <textarea id="rank_math_description" name="rank_math_description" placeholder="Enter SEO description" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>

            <div class="flex items-center justify-between mt-4">
                <x-primary-button class="ms-4" type="submit">
                    {{ __('Publish') }}
                </x-primary-button>
            </div>
        </form>

    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    $(document).ready(function() {
        $('#post-success-alert').delay(3000).fadeOut();
        $('#post-error-alert').delay(3000).fadeOut();   

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

        $('#create-wordpress-page-form').on('submit', function (e) {
        e.preventDefault();
        // Get the HTML content from the Quill editor
        let content = quill.root.innerHTML;
        // Set the content to the hidden textarea
        $('#page_content').val(content);
        pageContentValue = $('#page_content').val();
        if (!pageContentValue) {
            alert('No page content');
        } else{
            // Submit the form after setting the content
            this.submit();
        }
       
    });

        $('#serverSelect').change(function() {
            const server = this.value;
            const url = new URL('{{ route('creation.neighborhoods.byServer') }}');
            url.searchParams.append('server', server);

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

        $('#siteListDropdown').change(function() {
            const siteId = $(this).val();
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
                .then(function(response) {
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        $('#create-wordpress-page-form').show();

                        // Create and append the heading
                        const heading = document.createElement('h3');
                        heading.textContent = 'Recent pages from site:';
                        heading.className = ' dark:text-gray-400';
                        siteDetails.append(heading);

                        // Create and append the list
                        const list = document.createElement('ul');
                        list.classList = 'recent-pages-list dark:text-gray-300'; // Add a class for styling

                        let servicePages = data.data;
                        let templateContent = '<h1>Template Title</h1><p>This is a template paragraph. Replace this text with your content.</p>';

                        if (servicePages.length > 0) {
                            servicePages.forEach(page => {
                                const listItem = document.createElement('li');
                                listItem.classList = 'recent-page-item  dark:text-gray-300'; // Add a class for styling

                                const link = document.createElement('a');
                                link.href = '#'; // Replace with the actual URL if needed
                                link.textContent = page.post_title;

                                listItem.append(link);
                                list.append(listItem);

                                // Check for the specific meta_key and set the template content if it exists
                                page.meta.forEach(meta => {
                                    if (meta.meta_key === 'right_side_bar_services') {
                                        templateContent = meta.meta_value;
                                    }
                                });
                            });

                            siteDetails.append(list);
                            quill.clipboard.dangerouslyPasteHTML(templateContent);
                        
                    }
                    } else {
                        $('#create-wordpress-page-form').hide();
                        siteDetails.text('Unable to connect to database. Please let a developer know.');
                    }
                })
                .catch(error => {
                    siteDetails.text('An error occurred while loading the pages: ' + error.message);
                });

        });

    });
</script>