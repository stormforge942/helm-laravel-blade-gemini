<x-app-layout>
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Create and Update Blog Posts
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

        <!-- Row with two columns -->
        <div class="flex mb-4">
            <!-- Column for niches -->
            <div class="pr-4">

                <!-- Server selection dropdown -->
                <div class="mb-4">
                    <label for="server_option" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Niche
                        <button data-popover-target="server-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg><span class="sr-only">Show information</span></button>
                    </label>
                    <!-- Tip tool -->
                    <div data-popover id="server-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                        <div class="p-3 space-y-2">
                            <p>Please select a niche, a category of site that you want to work on</p>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg></a>
                        </div>
                        <div data-popper-arrow></div>
                    </div>
                    <select id="nicheSelect" name="server" class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
                        <option value="">Select niche</option>
                        @foreach($niches as $niche)
                        <option value="{{ $niche->niche }}">{{ $niche->niche }}</option>
                        @endforeach
                    </select>

                </div>

            </div>


            <div class="pr-4">

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

            </div>
        </div>

        <div id="siteDetails" class="dark:text-gray-300">
            <h3 style="display: none;" class=" dark:text-gray-400">Recent posts from site:</h3>
            <ul id="recent-pages-list" class="recent-pages-list dark:text-gray-300"></ul>
        </div>

        <button id="new-post-btn" type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">New Post</button>

        <form id="create-wordpress-post-form" class="mt-4" method="POST" action="{{ route('creation.blog-posts.store') }}" style="display:none">
            @csrf
            <input type="hidden" name="_method" value="POST" id="form-method">
            <input type="hidden" name="post_id" id="post_id">
            <input type="hidden" name="siteId" id="siteIdInput" value="">

            <div class="mb-4">
                <label for="post_title" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Post Title
                    <button data-popover-target="post-title-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>
                <!-- Tip tool -->
                <div data-popover id="post-title-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Title for the post. This is a required field.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="post_title" name="post_title" placeholder="Enter post title" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>


            <div class="mb-4">
                <label for="post_content" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Post Content
                    <button data-popover-target="post-content-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>

                </label>
                <!-- Tip tool -->
                <div data-popover id="post-content-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Content for the post. This is a required field. The content will be automatically populated with the a previously created post.</p>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tips</h3>
                        <ul class="space-y-1 text-gray-700 list-disc list-inside dark:text-gray-500">
                            <li>Use the template given or paste in your own.</li>
                            <li>Use the rich text editor to format your content. The content will show up in WordPress exactly how you see it here.</li>
                            <li>To use shortcodes, surround them with { } like you would when editing in WordPress.</li>
                        </ul>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <textarea id="post_content" name="post_content" class="text-normal shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;"></textarea>
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
                        <p>Publish status for post. This is a required field. The post can either be in draft or published status.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <select name="post_status" id="post_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select status</option>
                    <option value="draft">Draft</option>
                    <option value="publish">Publish</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="post_category" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">Category
                    <button data-popover-target="post-category-select-popover-description" data-popover-placement="bottom-end" type="button"><svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg><span class="sr-only">Show information</span></button>
                </label>
                <div data-popover id="post-category-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Categories for this post. This is an optional field. The categories you can select from will reflect the categories available from the site. You can select multiple categories.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg></a>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <div id="post_category" class="dark:text-gray-300">
                    <!-- Categories checkboxes will be populated here -->
                </div>
            </div>

            <div class="mb-4">
                <label for="new_category" class="block font-bold text-gray-700 mb-2 dark:text-gray-300">New Category
                    <button data-popover-target="new-category-select-popover-description" data-popover-placement="bottom-end" type="button">
                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Show information</span>
                    </button>
                </label>
                <!-- Tip tool -->
                <div data-popover id="new-category-select-popover-description" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                    <div class="p-3 space-y-2">
                        <p>Add a new category for this post. This field is optional.</p>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </div>
                    <div data-popper-arrow></div>
                </div>
                <input type="text" id="new_category" name="new_category" placeholder="Enter new category" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
    $('#post-success-alert').delay(3000).fadeOut();
    $('#post-error-alert').delay(3000).fadeOut();

    $(document).ready(function() {
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'], // toggled buttons
            ['blockquote', 'code-block'],
            [{
                'header': 1
            }, {
                'header': 2
            }], // custom button values
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }], // superscript/subscript
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }], // outdent/indent
            [{
                'direction': 'rtl'
            }], // text direction
            [{
                'size': ['small', false, 'large', 'huge']
            }], // custom dropdown
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'color': []
            }, {
                'background': []
            }], // dropdown with defaults from theme
            [{
                'font': []
            }],
            [{
                'align': []
            }],
            ['clean'], // remove formatting button
            ['link', 'image', 'video'] // link and image, video
        ];
        const quill = new Quill('#quill-content', {
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Compose an epic...',
            theme: 'snow'
        });

        $('#new-post-btn').click(function() {
            resetForm();
        });

        function resetForm() {
            $('#create-wordpress-post-form')[0].reset();
            quill.root.innerHTML = '';
            $('input[name="post_category[]"]').prop('checked', false);
            $('#create-wordpress-post-form').attr('action', "{{ route('creation.blog-posts.store') }}");
            $('#create-wordpress-post-form').find('input[name="_method"]').remove();
            $('button[type="submit"]').text('Create');
        }
        $('#create-wordpress-post-form').on('submit', function(e) {
            e.preventDefault();
            // Get the HTML content from the Quill editor
            let content = quill.root.innerHTML;
            // Set the content to the hidden textarea
            $('#post_content').val(content);
            postContentValue = $('#post_content').val();
            if (!postContentValue) {
                alert('No post content');
            } else {
                // Submit the form after setting the content
                this.submit();
            }

        });

        function fetchCategories(siteId) {
            const url = '{{ route('fetch.posts.categories') }}' + '?siteId=' + siteId;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateCategories(data.data);
                    } else {
                        console.log('Error retrieving categories');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function populateCategories(categories) {
            $('#post_category').empty();
            const container = document.getElementById('post_category');

            if (categories.length === 0) {
                const message = document.createElement('p');
                message.textContent = 'No categories available.';
                container.appendChild(message);
                return;
            }

            categories.forEach(category => {
                if (category) {
                    const div = document.createElement('div');

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'post_category[]';
                    checkbox.value = category.id;
                    checkbox.id = 'category-' + category.id;
                    checkbox.dataset.category = JSON.stringify(category);

                    const label = document.createElement('label');
                    label.htmlFor = 'category-' + category.id;
                    label.textContent = category.name;

                    div.appendChild(checkbox);
                    div.appendChild(label);
                    container.appendChild(div);
                }
            });
        }

        $('#nicheSelect').change(function() {
            const niche = this.value;
            const url = new URL('{{ route('creation.neighborhoods.byNiche.sites') }}');
            url.searchParams.append('niche', niche);

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
            siteDetails.empty(); // Clear previous content

            if (!siteId) {
                siteDetails.text('Please select a site.');
                return;
            }

            siteIdInput.val(siteId);
            const url = '{{ route('fetch.all-posts.blog') }}' + '?siteId=' + siteId;

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#create-wordpress-post-form').show();

                        // Create and append the heading
                        const heading = $('<h3 class="my-2 h3 dark:text-gray-400">Recent posts from site (excluding Neighborhoods). Select one if you want to edit an existing post:</h3>');
                        siteDetails.append(heading);

                        // Create and append the list
                        const list = $('<ul class="recent-pages-list m-2 list-disc dark:text-gray-300"></ul>');
                        siteDetails.append(list);

                        let posts = data.data;
                        // console.log(posts);

                        if (posts.length > 0) {
                            posts.forEach(post => {
                                const listItem = $('<li class="recent-page-item dark:text-gray-300"></li>');
                                const link = $('<a href="#" class="cursor-pointer font-medium font-sm text-gray-600 dark:text-gray-500"></a>')
                                    .text(post.post_title)
                                    .attr('data-post-id', post.id)
                                    .on('click', function(e) {
                                        e.preventDefault();
                                        populateForm(post);
                                    });

                                listItem.append(link);
                                list.append(listItem);
                            });

                            fetchCategories(siteId);
                        } else {
                            list.append('<li>No recent posts found (excluding Neighborhoods category).</li>');
                        }
                    } else {
                        $('#create-wordpress-post-form').hide();
                        siteDetails.text('Unable to fetch posts. Please let a developer know.');
                    }
                })
                .catch(error => {
                    siteDetails.text('An error occurred while loading the posts: ' + error.message);
                });
        });

        function populateForm(post) {
            $('#post_id').val(post.id);
            $('#post_title').val(post.post_title);
            quill.root.innerHTML = post.post_content;
            $('#post_status').val(post.post_status);
            $('#rank_math_focus_keyword').val(post.rank_math_focus_keyword);
            $('#rank_math_description').val(post.rank_math_description);

            // Populate categories
            $('input[name="post_category[]"]').prop('checked', false);
            if (post.terms && post.terms.length > 0) {
                post.terms.forEach(term => {
                    $(`#category-${term.term_id}`).prop('checked', true);
                });
            }

            // Change form action and method for updating
            $('#create-wordpress-post-form').attr('action', `{{ route('creation.blog-posts.update', '') }}/${post.id}`);
            $('#create-wordpress-post-form').append('<input type="hidden" name="_method" value="PUT">');

            // Change button text
            $('button[type="submit"]').text('Update');
        }

    });
</script>