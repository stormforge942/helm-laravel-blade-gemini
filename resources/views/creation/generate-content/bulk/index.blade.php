<x-app-layout>
    <link href="{{ asset('styles/generate-content.css') }}" rel="stylesheet">
    <style>
        #content-area-bulk,
        #template-editor-container {
            width: 100%;
            background-color: white;
        }

        #sites-container {
            max-height: 60vh;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }

        .generated-content-container {
            max-height: 60vh;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }
    </style>
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Bulk Blogs Creator
        </h1>
    </div>
    <div class="container">
        <div class="form-area">
            <form class="content-generation-form" id="content-generation-form" method="POST">
                @csrf
                <!-- Article Content Generation Fields -->

                <div class="mb-4">

                    <div class="flex gap-4 mb-4">

                        <div class="left-heading w-2/4">
                            <x-label-with-tooltip label="Number of Headings" id="num_headings_label" for="num_headings" text="Select the number of headings you want to create. The number of headings must be between 1 and 6." />
                            <input type="text" name="num_headings" id="num_headings" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                    </div>

                    <div class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <x-label-with-tooltip label="Keywords for Heading" id="keywords_label" for="keywords" text="Enter relevant keywords that will be used to generate the headings. You can update the keywords and regenerate the headings as needed." />

                        <div class="flex items-center gap-4">
                            <input type="text" name="keywords" id="keywords" placeholder="Enter keywords for heading" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" onclick="generateAIHeading()">Generate Heading</button>
                        </div>
                    </div>

                    <!-- Center the Generate Template Content button and add a tooltip -->
                    <div class="flex justify-center mb-4">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap mb-4" id="generate-bulk" style="display:none;" title="This button will generate article content for each heading based on the keywords and headings you've provided.">
                            Generate Template Content
                        </button>
                    </div>

                    <div id="loader_1" class="flex items-center mb-2 hidden pl-6 pt-10 h-0 justify-center">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
                        </svg>
                        <span class="sr-only">Generating content...</span>
                    </div>
                </div>


                <div id="headings-container">
                </div>

                <div style="display:none" id="template-editor-container" class="generated-content-container my-4 overflow-y-auto border border-gray-200 p-2 rounded">
                    <h3 class="text-3xl font-bold dark:text-white my-4">Modify Template Content</h3>
                    <!-- This is where the template content will be loaded into the Quill editor -->
                    <div id="template-quill-editor" class="quill-editor"></div>
                    <textarea id="template-content-textarea" name="template_content" class="hidden"></textarea>
                </div>


                <div id="generate-bulk-content-controllers" style="display:none;">
                    <div class="flex gap-4 mb-4">
                        <div class="right-heading left-heading w-2/4">
                            <x-label-with-tooltip label="Number of Copies" id="num_copy_label" for="num_copies" text="Select the number of headings you want to create. The number of headings must be between 1 and 6." />
                            <input type="text" name="num_copies" id="num_copies" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4 button-container" id="button-container">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap" id="generate-content-btn">
                            Generate Bulk Content
                        </button>
                    </div>
                </div>

            </form>

            <div id="content-area-bulk" class="content-area mb-4" style="display:none">
                <div id="content-pagination-controls" class="flex items-center mb-4">
                    <button id="prev-page" disabled type="button" class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Previous
                    </button>
                    <span id="page-info" class="text-sm font-sm mx-4"></span>
                    <button id="next-page" type="button" class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Next
                    </button>
                    <div role="status" id="loader" class="hidden ml-12 mt-4 flex items-end">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                        <span class="sr-only">Generating content...</span>
                    </div>
                </div>
                <h2 class="text-base my-4">Generated Content</h2>
                <div id="generated-content" class="generated-content-container mb-4 overflow-y-auto border border-gray-200 p-2 rounded"></div>
            </div>
        </div>

        <div class="mx-4 form-area">
            <!-- New Section for Selecting Sites and Number of Posts -->

            <form class="bulk-blog-post-form" id="bulk-blog-post-form" method="POST">
                @csrf
                <!--  Niche Field -->
                <div class="mb-4 inline-flex items-center gap-4">
                    <x-label-with-tooltip label="Niche" id="niche" text="Select the niche for the sites that you want to create posts for. After selecting niche, you will see a list of sites with that niche. This field is required." />
                    <select name="niche" id="niche" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select topic</option>
                        @foreach($niches as $niche)
                        <option value="{{ $niche->niche }}">{{ $niche->niche }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="site-selection-container" class="mb-4" style="display: none;">
                    <x-label-with-tooltip for="sites" label="Select Sites and Number of Posts" id="sites_label" text="Add a topic or niche for the site that you are generating content for. This field is required." />

                    <div class="flex items-center mb-2">
                        <button type="button" id="toggle-select-btn" class="icon-button mr-2" title="Select/Deselect All">
                            <svg id="toggle-select-icon" class="h-8 w-8 text-blue-700 transform hover:scale-110 transition duration-200" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path id="toggle-select-path" stroke="none" d="M0 0h24v24H0z" />
                                <rect x="4" y="4" width="16" height="16" rx="2" />
                                <path d="M9 12l2 2l4 -4" />
                            </svg>
                        </button>
                        <input type="number" id="set-all-num-posts" min="1" class="num-posts-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 p-2 mr-2" placeholder="Number of posts">

                        <button type="button" id="set-all-btn" class="icon-button text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700" title="Set Number for All">
                            Set All
                        </button>
                    </div>

                    <div id="sites-container" class="mb-4 overflow-y-auto border border-gray-200 bg-white p-2 rounded">
                        <!-- Sites checkboxes will be dynamically added here -->
                    </div>

                    <div class="mb-4">
                        <x-label-with-tooltip for="post_category" label="Post Category" id="post_category_label" text="Categories for this post. This is an optional field. The categories you can select from will reflect the categories available from the site. You can select multiple categories." />
                        <div id="post_category">
                            <!-- Categories checkboxes will be populated here -->
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label-with-tooltip for="rank_math_focus_keyword" label="SEO Focus Keywords" id="rank-math-focus-keyword-label" text="Add the keywords you want to rank for in the Rank Math SEO plugin. This field is optional." />
                        <input type="text" id="rank_math_focus_keyword" name="rank_math_focus_keyword" placeholder="Enter focus keywords, separated by commas" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <x-label-with-tooltip for="rank_math_description" label="SEO Meta Description" id="rank-math-description-label" text="This is what will appear as the description when this post shows up in the search results. This field is optional." />
                        <textarea id="rank_math_description" name="rank_math_description" placeholder="Enter SEO description" class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                </div>

                <!-- New Section for Submitting Posts -->
                <div id="post-selection-container" class="mb-4" style="display: none;">
                    <button id="post-content-btn" type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap mt-4 mb-4">Publish</button>
                </div>
            </form>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script>
        initializeQuillEditor('#template-quill-editor');

        const itemsPerPage = 10;
        let generatedContentArray = [];
        const divsPerPage = 5;
        let currentPage = 1;
        document.getElementById('num_headings').addEventListener('keyup', handleKeyUpEvent);

        function handleKeyUpEvent() {

            const container = document.getElementById('headings-container');
            container.innerHTML = '';
            const numHeadings = parseInt(document.getElementById('num_headings').value);
            const copies = 1; // Only one template

            // const copies = parseInt(document.getElementById('num_copies').value);
            for (let a = 1; a <= copies; a++) {
                const headingGroup = document.createElement('div');
                headingGroup.className = 'mb-4 p-4 border border-gray-300 rounded-lg';

                const flexDiv = document.createElement('div');
                flexDiv.className = 'flex justify-between';

                const groupHeading = document.createElement('h1');
                groupHeading.className = 'block text-gray-700 text-lg font-bold mt-4 mb-2 dark:text-gray-400';
                groupHeading.appendChild(document.createTextNode("Template"));
                flexDiv.appendChild(groupHeading);

                headingGroup.appendChild(flexDiv);


                for (let i = 1; i <= numHeadings; i++) {


                    const headingLabel = document.createElement('label');
                    headingLabel.htmlFor = `heading_${a}${i}`;
                    headingLabel.className = 'block text-gray-700 text-sm font-bold mt-4 mb-2 dark:text-gray-300';
                    headingLabel.textContent = `Heading ${i}`;
                    headingGroup.appendChild(headingLabel);

                    const headingInput = document.createElement('input');
                    headingInput.type = 'text';
                    headingInput.name = `heading_${a}${i}`;
                    headingInput.id = `heading_${a}${i}`;
                    headingInput.placeholder = `Enter heading ${i}`;
                    headingInput.className = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline generate_ai';
                    headingInput.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            showToneAndContentArea(`${a}${i}`, i);
                        }
                    });
                    headingGroup.appendChild(headingInput);

                    const toneRow = document.createElement('div');
                    toneRow.className = 'flex items-center gap-4 mt-4 hidden';
                    toneRow.id = `toneRow_${a}${i}`;

                    const toneLabel = document.createElement('label');
                    toneLabel.htmlFor = `tone_${a}${i}`;
                    toneLabel.className = 'block text-gray-700 text-sm font-bold';
                    toneLabel.textContent = 'Tone';
                    toneRow.appendChild(toneLabel);

                    const toneSelect = document.createElement('select');
                    toneSelect.name = `tone_${a}${i}`;
                    toneSelect.id = `tone_${a}${i}`;
                    toneSelect.className = 'shadow appearance-none border rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
                    const tones = ['Professional', 'Friendly', 'Authoritative', 'Informative', 'Persuasive'];
                    tones.forEach(tone => {
                        const option = document.createElement('option');
                        option.value = tone;
                        option.textContent = tone;
                        if (tone === 'Professional') option.selected = true;
                        toneSelect.appendChild(option);
                    });
                    toneRow.appendChild(toneSelect);

                    const generateContentButton = document.createElement('button');
                    generateContentButton.type = 'button';
                    generateContentButton.textContent = 'Generate Content';
                    generateContentButton.className = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap generate-content';
                    generateContentButton.addEventListener('click', function() {
                        generateContent(`${a}${i}`);
                    });
                    toneRow.appendChild(generateContentButton);
                    headingGroup.appendChild(toneRow);

                    container.appendChild(headingGroup);
                }
                // Add the "Use Template Content" button
                const useTemplateButton = document.createElement('button');
                useTemplateButton.type = 'button';
                useTemplateButton.className = 'bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap mt-4 use-template-content';
                useTemplateButton.textContent = 'Use Template Content';
                useTemplateButton.style.display = 'none';
                useTemplateButton.addEventListener('click', function() {
                    moveTemplateContentToQuill(a, numHeadings);
                });
                headingGroup.appendChild(useTemplateButton);

                container.appendChild(headingGroup);
            }

            showDivs(currentPage);
        };

        function moveTemplateContentToQuill(templateIndex, numHeadings) {
            let combinedContent = '';

            // Combine all content directly from the generated content areas
            for (let i = 1; i <= numHeadings; i++) {
                // Get the content from the generated content div (which already contains the header)
                const generatedContentDiv = document.getElementById(`article_content_${templateIndex}${i}`);
                const articleContent = generatedContentDiv ? generatedContentDiv.value : ''; // Get the value if it's in a textarea or div

                // Combine the content (already contains headers and paragraphs)
                combinedContent += articleContent;
            }

            // Use the existing Quill editor
            const editorSelector = '#template-quill-editor'; // Use the existing editor ID
            let quillEditor = Quill.find(document.querySelector(editorSelector));

            if (quillEditor) {
                // Use Quill's dangerouslyPasteHTML to insert the HTML content
                quillEditor.clipboard.dangerouslyPasteHTML(combinedContent);
            } else {
                // Initialize Quill editor if not yet initialized
                quillEditor = initializeQuillEditor(editorSelector);
                quillEditor.clipboard.dangerouslyPasteHTML(combinedContent);
            }
        }


        document.getElementById('generate-bulk').addEventListener('click', function() {
            // Select all buttons with the specified class name
            const generateButtons = document.querySelectorAll('.generate-content');

            // Trigger click event on each button
            generateButtons.forEach(button => button.click());
        });


        function showToneAndContentArea(index, child) {
            const toneRow = document.getElementById(`toneRow_${index}`);
            const headingGroup = document.querySelector(`#headings-container #toneRow_${index}`);

            if (headingGroup && !(headingGroup.nextElementSibling && headingGroup.nextElementSibling.getAttribute('for') && headingGroup.nextElementSibling.getAttribute('for').includes('article_content'))) {
                // Insert loader
                const loader = document.createElement('div');
                loader.id = `loader_${index}`;
                loader.className = 'flex items-center mb-2 hidden pl-6 pt-10 h-0';
                loader.innerHTML = `
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Generating content...</span>
                `;
                headingGroup.insertAdjacentElement('afterend', loader);

                // Create the textarea and Quill editor div
                const textareaContainer = document.createElement('div');
                textareaContainer.className = 'field mb-4 content-textarea';

                const textarea = document.createElement('textarea');
                textarea.id = `article_content_${index}`;
                textarea.name = `article_content_${index}`;
                textarea.style.display = 'none'; // Hidden initially
                textareaContainer.appendChild(textarea);

                const quillDiv = document.createElement('div');
                quillDiv.id = `quill-content_${index}`;
                quillDiv.style.maxHeight = '100px';
                quillDiv.style.overflow = 'hidden';
                textareaContainer.appendChild(quillDiv);

                const toggleButton = document.createElement('button');
                toggleButton.id = `toggleButton_${index}`;
                toggleButton.className = "text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-2.5 py-1 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700";
                toggleButton.textContent = 'Read More';
                toggleButton.style.display = 'none';
                toggleButton.type = 'button';
                textareaContainer.appendChild(toggleButton);

                headingGroup.insertAdjacentElement('afterend', textareaContainer);

                const contentLabel = document.createElement('label');
                contentLabel.htmlFor = `article_content_${index}`;
                contentLabel.className = 'block text-gray-700 text-sm font-bold mt-4 mb-2';
                contentLabel.textContent = 'Article Content';
                headingGroup.insertAdjacentElement('afterend', contentLabel);

                let expanded = false;
                toggleButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    expanded = !expanded;
                    toggleButton.textContent = expanded ? 'Read Less' : 'Read More';

                    if (expanded) {
                        quillDiv.style.maxHeight = 'none';
                    } else {
                        quillDiv.style.maxHeight = '100px';
                    }
                });

                // Show tone and generate content row
                toneRow.classList.remove('hidden');
            }

            // Ensure loader is properly hidden after content generation
            const loaderElement = document.getElementById(`loader_${index}`);
            if (loaderElement) {
                loaderElement.classList.add('hidden'); // Hide the loader after content is ready
            }
        }


        generateContent = async function(index) {
            try {
                // Show loader icon
                const loader = document.getElementById(`loader_${index}`);
                if (loader) {
                    loader.classList.remove('hidden');
                }

                var dataform = document.forms[['content-generation-form']];
                formData = new FormData(dataform);
                formData.append('index', index);

                const response = await fetch(`{{ route('creation.generateTextBasedOnHeadlines.get') }}`, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }

                const data = await response.json();

                const quillEditor = document.querySelector(`#quill-content_${index}`);
                document.getElementById(`article_content_${index}`).value = data;
                quillEditor.innerHTML = data;

                const toggleButton = document.querySelector(`#toggleButton_${index}`);
                if (toggleButton) {
                    toggleButton.style.display = 'block';
                }

                document.getElementById('generate-bulk-content-controllers').style.display = 'block';
                document.querySelector('#content-area-bulk').style.display = 'block';
                document.querySelector('.use-template-content').style.display = 'block';
                document.getElementById('template-editor-container').style.display = 'block';

            } catch (error) {
                console.error('Error:', error);
            } finally {
                // Hide the loader icon in the finally block to ensure it gets hidden no matter what
                const loader = document.getElementById(`loader_${index}`);
                if (loader) {
                    loader.classList.add('hidden');
                }
            }
        };


        generateAIHeading = async function() {
            try {
                document.getElementById('generate-bulk').style.display = 'block';
                const loader = document.getElementById('loader_1');
                loader.style.display = 'flex';

                const headingInput = document.getElementsByClassName('generate_ai');
                const copies = 1;
                const headings = parseInt(document.getElementById('num_headings').value);

                var dataform = document.forms[['content-generation-form']];
                formData = new FormData(dataform);

                const response = await fetch(`{{ route('creation.generateHeadline.get') }}`, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }

                const data = await response.json();

                if (!data || typeof data !== 'string') {
                    throw new Error('Invalid data received');
                }

                const lines = data.split(/\r?\n|\r/).filter(Boolean);
                finalData = lines.map(line => line.replace(/^\s*(\d+\.\s*|-\s*)/, '').trim());

                Array.from(finalData).forEach((el, i) => {
                    if (headingInput[i]) {
                        headingInput[i].value = el;
                    }
                });

                for (let a = 1; a <= copies; a++) {
                    for (let b = 1; b <= headings; b++) {
                        showToneAndContentArea(`${a}${b}`, a);
                    }
                }

            } catch (error) {
                console.error('Error:', error);
            } finally {
                // Ensure the loader is hidden even if there is an error
                const loader = document.getElementById('loader_1');
                loader.style.display = 'none';
            }
        };

        // Function to show divs of the current page
        function showDivs(page) {
            const divs = document.querySelectorAll('#headings-container > div');
            const totalPages = Math.ceil(divs.length / divsPerPage);

            divs.forEach((div, index) => {
                div.style.display = 'none';
                if (index >= (page - 1) * divsPerPage && index < page * divsPerPage) {
                    div.style.display = 'block';
                }
            });
        }

        function initializeQuillEditor(selector, htmlContent = '') {
            if (document.querySelector(selector)) {
                var quill = new Quill(selector, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{
                                'header': 1
                            }, {
                                'header': 2
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'script': 'sub'
                            }, {
                                'script': 'super'
                            }],
                            [{
                                'indent': '-1'
                            }, {
                                'indent': '+1'
                            }],
                            [{
                                'direction': 'rtl'
                            }],
                            [{
                                'size': ['small', false, 'large', 'huge']
                            }],
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'font': []
                            }],
                            [{
                                'align': []
                            }],
                            ['clean'],
                            ['link']
                        ]
                    }
                });

                // If HTML content is provided, load it into the Quill editor
                if (htmlContent) {
                    quill.clipboard.dangerouslyPasteHTML(htmlContent);
                }

                return quill; // Return the quill instance
            }
            return null;
        }

        // Update the paginateContent function
        function paginateContent(contentArray, page, itemsPerPage) {
            if (contentArray.length === 0) return [];
            const totalPages = Math.max(1, Math.ceil(contentArray.length / itemsPerPage));
            page = Math.min(Math.max(1, page), totalPages);
            const start = (page - 1) * itemsPerPage;
            const end = Math.min(start + itemsPerPage, contentArray.length);
            return contentArray.slice(start, end);
        }

        // Function to hide all content and show only those for the current page
        function showPageContent(page, itemsPerPage, totalItems) {
            const start = (page - 1) * itemsPerPage;
            const end = Math.min(start + itemsPerPage, totalItems);

            // Hide all content blocks
            const allContentBlocks = document.querySelectorAll('.content-block');
            allContentBlocks.forEach(block => block.style.display = 'none');

            // Show the content blocks for the current page
            for (let i = start; i < end; i++) {
                allContentBlocks[i].style.display = 'block';
            }
        }

        function updatePaginationControls(totalItems, itemsPerPage, currentPage) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            document.getElementById('page-info').innerText = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage === totalPages;
        }
        document.getElementById('next-page').addEventListener('click', () => {
            const totalPages = Math.ceil(generatedContentArray.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                showPageContent(currentPage, itemsPerPage, generatedContentArray.length);
                updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);
            }
        });

        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPageContent(currentPage, itemsPerPage, generatedContentArray.length);
                updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);
            }
        });

        function getTemplateContent() {
            const templateContent = [];
            const headingInputs = document.querySelectorAll('.generate_ai');

            headingInputs.forEach((headingInput, index) => {
                const headingText = headingInput.value.trim();

                templateContent.push({
                    heading: headingText,
                });
            });
            return templateContent;
        }


        function displayGeneratedContent(contentArray) {
            const generatedContentDiv = document.getElementById('generated-content');
            generatedContentDiv.innerHTML = ""; // Only clear once when the content is loaded

            if (contentArray.length === 0) {
                generatedContentDiv.innerHTML = "<p>No content to display.</p>";
                updatePaginationControls(0, itemsPerPage, 0);
                return;
            }

            // Render all content blocks at once but hide them
            contentArray.forEach((content, index) => {
                const contentBlock = document.createElement('div');
                contentBlock.className = "content-block";
                contentBlock.setAttribute('data-index', index); // Keep track of the index
                contentBlock.style.display = "none"; // Initially hide all content blocks

                contentBlock.innerHTML = `
            <p class="tab">Copy ${index + 1}</p>
            <button type="button" data-index="${index}" class="remove-content ms-auto -mx-1.5 -my-1.5 bg-white text-red-400 hover:text-red-900 rounded focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-default" aria-label="Close">
                <span class="sr-only">Remove</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
            <h3>${content.title}</h3>
            <input style="display:none" class="generated-heading" value="${content.title}" type="text"></input>
            <div id="generated-content-${index}" style="display:none;">${content.content}</div>
            <div id="editor-container-${index}" class="editor-container"></div>
            <textarea class="hidden-textarea" id="hidden-input-${index}" name="content-${index}" style="display:none;"></textarea>
        `;

                generatedContentDiv.appendChild(contentBlock);
                initializeQuillEditor(`#editor-container-${index}`);

                const editor = Quill.find(document.querySelector(`#editor-container-${index}`));
                if (editor) {
                    editor.clipboard.dangerouslyPasteHTML(content.content, 'api'); // Set the content inside the editor
                }
            });

            // Show the first set of items
            showPageContent(currentPage, itemsPerPage, contentArray.length);

            // Update pagination controls
            updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);

            // Handle the remove button click events
            document.querySelectorAll('.remove-content').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    removeContent(index); // Make sure this function is defined elsewhere
                });
            });
        }



        function removeContent(index) {
            // Remove the content at the specified index from the array
            generatedContentArray.splice(index, 1);

            // Recalculate total pages and ensure currentPage is within bounds
            const totalPages = Math.ceil(generatedContentArray.length / itemsPerPage);
            currentPage = Math.min(currentPage, totalPages);

            // If there are no items left, reset to page 1
            if (currentPage === 0 && generatedContentArray.length > 0) currentPage = 1;

            // Paginate the content after the removal
            showPageContent(generatedContentArray, currentPage, itemsPerPage);

            // Recreate the divs for the updated content
            displayGeneratedContent(generatedContentArray);

            // Update the pagination controls
            updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);
        }






        function handleRemoveClick(event) {
            const index = this.getAttribute('data-index');
            removeContent(index);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('content-generation-form');
            const generateButton = document.getElementById('generate-content-btn');

            if (form && generateButton) {
                // Remove the form's submit event
                form.onsubmit = null;

                // Add click event to the button instead
                generateButton.addEventListener('click', async function(event) {
                    event.preventDefault();

                    console.log('Generate content button clicked');
                    const loader = document.getElementById('loader');

                    if (loader) loader.style.display = 'block';
                    generateButton.disabled = true;

                    try {
                        const formData = new FormData(form);

                        // Collect the heading, tone, and content fields
                        document.querySelectorAll(`[id^=heading_]`).forEach(heading => formData.append(heading.name, heading.value));
                        document.querySelectorAll(`[id^=tone_]`).forEach(tone => formData.append(tone.name, tone.value));

                        const quillEditor = Quill.find(document.querySelector('#template-quill-editor'));
                        if (quillEditor) {
                            const quillContent = quillEditor.root.innerHTML;
                            const templateContentTextarea = document.getElementById('template-content-textarea');
                            if (templateContentTextarea) {
                                templateContentTextarea.value = quillContent;
                                formData.append('template_content', quillContent);
                            }
                        }

                        let totalFetched = 0;
                        const totalCopies = parseInt(formData.get('num_copies') || '1');
                        let batchNumber = 1;

                        // Function to handle each batch request
                        async function fetchBatch(batchNumber) {
                            console.log(batchNumber);
                            // Create a new FormData instance for each batch to avoid overwriting
                            let batchFormData = new FormData();

                            // Append all existing form data to the new batchFormData
                            for (let pair of formData.entries()) {
                                batchFormData.append(pair[0], pair[1]);
                            }

                            batchFormData.append('batch', batchNumber);

                            try {
                                const response = await fetch(`{{ route('creation.bulk-content.get') }}?batch=${batchNumber}`, {
                                    method: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: batchFormData
                                });

                                if (!response.ok) {
                                    throw new Error('Network response was not ok ' + response.statusText);
                                }

                                const data = await response.json();

                                if (data.original && Array.isArray(data.original)) {
                                    generatedContentArray = generatedContentArray.concat(data.original);
                                    displayGeneratedContent(generatedContentArray);
                                    showPageContent(currentPage, itemsPerPage, generatedContentArray.length);
                                    updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);
                                    totalFetched += data.original.length; // Update totalFetched based on actual data length

                                    // Stop further fetching if we've fetched all required copies
                                    if (totalFetched >= totalCopies) {
                                        return true; // Indicate completion
                                    }

                                } else {
                                    console.error('Unexpected data format received:', data);
                                    throw new Error('Unexpected data format received from the server');
                                }
                            } catch (error) {
                                console.error(`Error fetching batch ${batchNumber}:`, error);
                                throw error;
                            }
                        }

                        // Create an array of promises for all batch requests
                        const batchPromises = [];

                        for (let i = 0; i < totalCopies; i++) {
                            batchPromises.push(fetchBatch(batchNumber));
                            batchNumber++;
                        }

                        // Wait for all batch requests to complete
                        await Promise.all(batchPromises);

                    } catch (error) {
                        console.error('Error:', error);
                        // alert('An error occurred while generating content. Please try again.');
                    } finally {
                        if (loader) loader.style.display = 'none';
                        generateButton.disabled = false;
                    }
                });
            } else {
                console.error('Form or generate button not found');
            }
        });
    </script>

    <script>
        let allSelected = false;

        document.getElementById('toggle-select-btn').addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default form submission
            allSelected = !allSelected;
            document.querySelectorAll('#sites-container input[type="checkbox"]').forEach(checkbox => checkbox.checked = allSelected);

            const icon = document.getElementById('toggle-select-icon');
            if (allSelected) {
                icon.classList.remove('text-indigo-500');
                icon.classList.add('text-red-500');
            } else {
                icon.classList.remove('text-red-500');
                icon.classList.add('text-indigo-500');
            }
        });

        document.getElementById('set-all-btn').addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default form submission
            const value = document.getElementById('set-all-num-posts').value;
            if (value) {
                document.querySelectorAll('#sites-container input[type="number"]').forEach(input => input.value = value);
            }
        });

        document.getElementById('niche').addEventListener('change', loadSitesAndCategories);

        function loadSitesAndCategories() {
            // Fetch sites based on the selected niche
            const niche = document.getElementById('niche').value;
            const maxCategoryFetchRetries = 3; // Maximum number of sites to try for categories
            let categoryFetchAttempts = 0;

            if (niche) {
                fetch(`{{ url('/wp-sites?niche=') }}${niche}`)
                    .then(response => response.json())
                    .then(sites => {
                        const sitesContainer = document.getElementById('sites-container');
                        sitesContainer.innerHTML = '';

                        sites.forEach(site => {
                            const siteContainer = document.createElement('div');
                            siteContainer.className = 'site-container mb-4';
                            siteContainer.id = `site-${site.id}`;

                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'sites[]';
                            checkbox.value = site.id;
                            checkbox.className = 'bg-gray-100 border border-gray-800 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-700 dark:border-blue-600 dark:placeholder-blue-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';

                            const label = document.createElement('label');
                            label.htmlFor = `site-${site.id}`;
                            label.textContent = site.site_url;
                            label.className = 'ml-2 text-sm';

                            const numPostsContainer = document.createElement('div');
                            numPostsContainer.className = 'num-posts-container inline-flex items-center ml-4';

                            const numPostsInputLabel = document.createElement('label');
                            numPostsInputLabel.htmlFor = `num-posts-${site.id}`;
                            numPostsInputLabel.textContent = 'Number of posts:';
                            numPostsInputLabel.className = 'text-sm font-medium text-gray-700 dark:text-gray-400 mr-2';

                            const numPostsInput = document.createElement('input');
                            numPostsInput.type = 'number';
                            numPostsInput.name = `num_posts_${site.id}`;
                            numPostsInput.id = `num-posts-${site.id}`;
                            numPostsInput.min = 1;
                            numPostsInput.value = 1;
                            numPostsInput.className = 'num-posts-input bg-gray-100 border border-gray-800 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-14 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';

                            const statusSpan = document.createElement('span');
                            statusSpan.className = 'status ml-2';

                            // Loader
                            const loader = document.createElement('span');
                            loader.className = 'loader hidden';
                            loader.innerHTML = '<svg class="h-6 w-6 text-gray-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 4a6 6 0 100 12 6 6 0 000-12z"></path></svg>';

                            numPostsContainer.appendChild(numPostsInputLabel);
                            numPostsContainer.appendChild(numPostsInput);

                            siteContainer.appendChild(checkbox);
                            siteContainer.appendChild(label);
                            siteContainer.appendChild(numPostsContainer);
                            siteContainer.appendChild(statusSpan); // Append the status span
                            siteContainer.appendChild(loader); // Append the loader

                            sitesContainer.appendChild(siteContainer);
                        });

                        document.getElementById('site-selection-container').style.display = 'block';
                        document.getElementById('post-selection-container').style.display = 'block';

                        // Fetch categories from the first few sites that successfully return them
                        function fetchCategoriesFromSite(siteId) {
                            fetch(`{{ route('fetch.posts.categories') }}?siteId=${siteId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.data.length > 0) {
                                        populateCategories(data.data);
                                        // console.log(data.data);
                                    } else {
                                        if (categoryFetchAttempts < maxCategoryFetchRetries) {
                                            categoryFetchAttempts++;
                                            fetchCategoriesFromSite(sites[categoryFetchAttempts].id);
                                        } else {
                                            console.log('Error retrieving categories');
                                        }
                                    }
                                })
                                .catch(error => {
                                    if (categoryFetchAttempts < maxCategoryFetchRetries) {
                                        categoryFetchAttempts++;
                                        fetchCategoriesFromSite(sites[categoryFetchAttempts].id);
                                    } else {
                                        console.error('Error:', error);
                                    }
                                });
                        }

                        if (sites.length > 0) {
                            fetchCategoriesFromSite(sites[categoryFetchAttempts].id);
                        }
                    })
                    .catch(error => console.error('Error loading sites:', error));
            } else {
                document.getElementById('site-selection-container').style.display = 'none';
                document.getElementById('post-selection-container').style.display = 'none';
            }
        }

        function populateCategories(categories) {
            const container = document.getElementById('post_category');
            container.innerHTML = '';

            if (categories.length === 0) {
                const message = document.createElement('p');
                message.textContent = 'No categories available.';
                container.appendChild(message);
                return;
            }

            categories.forEach(category => {
                if (category) {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'post_category[]';
                    checkbox.value = category.name;
                    checkbox.id = 'category-' + category.id;
                    checkbox.className = 'text-sm';

                    const label = document.createElement('label');
                    label.htmlFor = 'category-' + category.id;
                    label.textContent = category.name;
                    label.className = 'text-sm ml-1 dark:text-gray-300';

                    const div = document.createElement('div');
                    div.appendChild(checkbox);
                    div.appendChild(label);

                    container.appendChild(div);
                }
            });
        }


        document.getElementById('bulk-blog-post-form').onsubmit = async function(event) {
            event.preventDefault();

            const postContent = document.getElementById('post-content-btn');

            // Collect selected sites
            const selectedSites = Array.from(document.querySelectorAll('input[name="sites[]"]:checked')).map(cb => cb.value);
            const numPostsPerSite = {};
            selectedSites.forEach(siteId => {
                numPostsPerSite[siteId] = document.querySelector(`input[name="num_posts_${siteId}"]`).value;
            });

            // Ensure all editor content is saved into the hidden textareas
            document.querySelectorAll('.editor-container').forEach((editorContainer, index) => {
                const quill = Quill.find(editorContainer); // Get the Quill editor instance
                const textarea = document.querySelector(`#hidden-input-${index}`); // Get the corresponding textarea
                if (quill && textarea) {
                    textarea.value = quill.root.innerHTML; // Store Quill content in the hidden textarea
                }
            });

            // Get additional data from form fields (SEO fields, etc.)
            const seoKeywords = document.getElementById('rank_math_focus_keyword').value;
            const seoDescription = document.getElementById('rank_math_description').value;
            const postCategories = Array.from(document.querySelectorAll('#post_category input[type="checkbox"]:checked')).map(cb => cb.value);

            // Get the values from the hidden textareas and headings
            const textareas = document.getElementsByClassName('hidden-textarea');
            const textareaValues = Array.from(textareas).map(textarea => textarea.value);
            const headings = document.getElementsByClassName('generated-heading');
            const headingsValues = Array.from(headings).map(heading => heading.value);

            // Get the number of copies and headings
            const numCopies = document.querySelectorAll('#headings-container > div').length;

            // Prepare the data to send
            const postData = {
                content: textareaValues, // Generated content
                sites: selectedSites, // Selected site IDs
                num_posts_per_site: numPostsPerSite, // Posts per site
                rank_math_focus_keyword: seoKeywords, // SEO keywords
                rank_math_description: seoDescription, // SEO description
                post_category: postCategories, // Post categories
                headings: headingsValues, // Generated headings
                num_copies: numCopies // Number of copies to generate
            };

            try {
                postContent.disabled = true;
                let totalPostedItems = 0;

                const response = await fetch(`{{ url('/creation/bulk-content/blogs') }}`, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(postData)
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }

                const result = await response.json();

                selectedSites.forEach(siteId => {
                    const siteRow = document.querySelector(`#site-${siteId}`);
                    const statusSpan = siteRow.querySelector('.status');
                    statusSpan.innerHTML = ''; // Clear previous status
                    const loader = siteRow.querySelector('.loader');
                    loader.classList.remove('hidden'); // Show loader
                });

                let currentIndex = 0;

                // Process each result and update the UI
                result.forEach(item => {
                    const siteRow = document.querySelector(`#site-${item.site_id}`);
                    if (siteRow) {
                        const statusSpan = siteRow.querySelector('.status');
                        const loader = siteRow.querySelector('.loader');
                        loader.classList.add('hidden'); // Hide loader

                        if (statusSpan) {
                            statusSpan.innerHTML = ''; // Clear previous status

                            if (item.status === 'success') {
                                // Update the UI to indicate success
                                statusSpan.innerHTML = `
                                <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>`;

                                // Remove the posted content from the array
                                const numPosts = parseInt(numPostsPerSite[item.site_id]);
                                totalPostedItems += numPosts;
                            } else if (item.status === 'error') {
                                // Update the UI to indicate an error
                                statusSpan.innerHTML = `
                                <svg class="h-4 w-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                                <span class="error-message text-xs text-red-500">${item.message}</span>`;
                            }
                        }
                    }
                });

                if (totalPostedItems > 0 && totalPostedItems <= generatedContentArray.length) {
                    generatedContentArray = generatedContentArray.slice(totalPostedItems);
                }

                // Recalculate the current page
                const totalPages = Math.max(1, Math.ceil(generatedContentArray.length / itemsPerPage));
                currentPage = Math.min(Math.max(1, currentPage), totalPages);


                showPageContent(currentPage, itemsPerPage, generatedContentArray.length);
                updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);

                // // Re-render the generated content and update pagination after posting
                // displayGeneratedContent(paginateContent(generatedContentArray, currentPage, itemsPerPage));
                // updatePaginationControls(generatedContentArray.length, itemsPerPage, currentPage);

            } catch (error) {
                console.error('Error posting content:', error);
            } finally {
                // Re-enable the submit button after processing
                postContent.disabled = false;
            }
        };
    </script>

</x-app-layout>