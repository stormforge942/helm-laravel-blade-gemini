<x-app-layout>
    <link href="{{ asset('styles/generate-content.css') }}" rel="stylesheet">

    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('Generate Content') }}
        </h2>
    </x-slot>
    <div id="loader" class="loader" style="display: none;"></div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="container">
            <!-- Radio Buttons -->
            <div class="mb-4">
                <div class="flex space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="contentOption" value="service"
                            class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Service Page</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="contentOption" value="blog"
                            class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Blog</span>
                    </label>
                </div>
            </div>

            <!-- Content Sections -->
            <div id="homeContent" class="content-section">
                {{-- <p>This is the content for Option 1.</p> --}}
            </div>
        </div>  
    </div>
    <div class="p-6 text-gray-900 dark:text-gray-100 mt-1 content-section hidden" id="serviceContent">
        <div class="container">
            <div class="form-area">
                <form class="content-generation-form" id="content-generation-form" method="POST">
                    @csrf
                    <input type="hidden" value="service" name="type" >
                    <div>
                        <label for="article_content">Article Content:</label>
                        <div class="field mb-4" style="margin-bottom:15px;">
                            <textarea id="article_content" name="article_content"style="display:none;"></textarea>
                            <div id="quill-content"></div>
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="keywords">Keywords: (put keywords in quotes)</label>
                        <div class="field">
                            <input type="text" id="keywords" name="keywords" value="{{ $keywords }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="locality">Locality:</label>
                        <div class="field">
                            <input type="text" id="locality" name="locality" value="{{ $locality }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="tone">Tone:</label>
                        <div class="field">
                            <select name="tone">
                                <option value="professional" {{ $tone == 'professional' ? 'selected' : '' }}>
                                    Professional
                                </option>
                                <option value="friendly" {{ $tone == 'friendly' ? 'selected' : '' }}>Friendly
                                </option>
                                <option value="authoritative" {{ $tone == 'authoritative' ? 'selected' : '' }}>
                                    Authoritative
                                </option>
                                <option value="informative" {{ $tone == 'informative' ? 'selected' : '' }}>
                                    Informative
                                </option>
                                <option value="persuasive" {{ $tone == 'persuasive' ? 'selected' : '' }}>Persuasive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="existing_prompt">Additional prompt instructions:</label>
                        <div class="field">
                            <input type="text" id="existing_prompt" name="existing_prompt"
                                value="{{ $existing_prompt }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="num_copies">Number of Copies:</label>
                        <div class="field">
                            <input type="number" id="num_copies" name="num_copies" value="{{ $num_copies }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="min_words_count">Minimum words count:</label>
                        <div class="field">
                            <input type="number" id="min_words_count" min="1" name="min_words_count"
                                value="{{ $min_words_count }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="max_words_count">Maximum words count:</label>
                        <div class="field">
                            <input type="number" id="max_words_count" min="2" name="max_words_count"
                                value="{{ $max_words_count }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <div></div>
                        <div class="field">
                            <input type="checkbox" id="use-html" name="use_html" value="YES">
                            <label for="vehicle1"> Use HTML</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4 button-container field-container"
                        id="button-container">
                        <x-primary-button class="ms-4 bg-blue" type="submit" id="generate-content-btn">
                            Generate Content
                        </x-primary-button>
                        <button id="download-button"
                            class="px-4 py-2 bg-gray-500 rounded-md font-semibold text-xs text-white cursor-auto uppercase tracking-widest"
                            disabled="disabled">Download Content</button>
                    </div>
                </form>
            </div>
            <div class="content-area">
                <h2>Generated Content:</h2>
                <div id="generated-content"></div>
            </div>
        </div>
    </div>

    <div class="p-6 text-gray-900 dark:text-gray-100 mt-1 content-section hidden" id="blogContent">
        <div class="container">
            <div class="form-area">
                <form class="content-generation-form" id="content-generation-form2" method="POST">
                    @csrf
                    <input type="hidden" value="blog" name="type" >
                    <div>
                        <label for="article_content2">Article Content:</label>
                        <div class="field mb-4" style="margin-bottom:15px;">
                            <textarea id="article_content2" name="article_content"style="display:none;"></textarea>
                            <div id="quill-content2"></div>
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="keywords">Keywords: (put keywords in quotes)</label>
                        <div class="field">
                            <input type="text" id="keywords" name="keywords" value="{{ $keywords }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="locality">Locality:</label>
                        <div class="field">
                            <input type="text" id="locality" name="locality" value="{{ $locality }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="tone">Tone:</label>
                        <div class="field">
                            <select name="tone">
                                <option value="professional" {{ $tone == 'professional' ? 'selected' : '' }}>
                                    Professional
                                </option>
                                <option value="friendly" {{ $tone == 'friendly' ? 'selected' : '' }}>Friendly
                                </option>
                                <option value="authoritative" {{ $tone == 'authoritative' ? 'selected' : '' }}>
                                    Authoritative
                                </option>
                                <option value="informative" {{ $tone == 'informative' ? 'selected' : '' }}>
                                    Informative
                                </option>
                                <option value="persuasive" {{ $tone == 'persuasive' ? 'selected' : '' }}>Persuasive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="existing_prompt">Additional prompt instructions:</label>
                        <div class="field">
                            <input type="text" id="existing_prompt" name="existing_prompt"
                                value="{{ $existing_prompt }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="number_of_paragraphs">Number of Paragraphs:</label>
                        <div class="field">
                            <input type="number" id="number_of_paragraphs" name="number_of_paragraphs" min="1" value="{{ $number_of_paragraphs }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="num_copies">Number of Copies:</label>
                        <div class="field">
                            <input type="number" id="num_copies" name="num_copies" value="{{ $num_copies }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="min_words_count">Minimum words count:</label>
                        <div class="field">
                            <input type="number" id="min_words_count" min="1" name="min_words_count"
                                value="{{ $min_words_count }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <label for="max_words_count">Maximum words count:</label>
                        <div class="field">
                            <input type="number" id="max_words_count" min="2" name="max_words_count"
                                value="{{ $max_words_count }}">
                        </div>
                    </div>
                    <div class="field-container">
                        <div></div>
                        <div class="field">
                            <input type="checkbox" id="use-html" name="use_html" value="YES">
                            <label for="vehicle1"> Use HTML</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4 button-container field-container"
                        id="button-container">
                        <x-primary-button class="ms-4 bg-blue" type="submit" id="generate-content-btn">
                            Generate Content
                        </x-primary-button>
                        <button id="download-button2"
                            class="px-4 py-2 bg-gray-500 rounded-md font-semibold text-xs text-white cursor-auto uppercase tracking-widest"
                            disabled="disabled">Download Content</button>
                    </div>
                </form>
            </div>
            <div class="content-area">
                <h2>Generated Content:</h2>
                <div id="generated-content2"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script>
        document.querySelectorAll('input[name="contentOption"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                // Hide all content sections
                document.querySelectorAll('.content-section').forEach((section) => {
                    section.classList.add('hidden');
                });

                // Show the selected content section
                document.getElementById(this.value + 'Content').classList.remove('hidden');
            });
        });

        function nl2br(str) {
            return str.replace(/\n/g, '<br>');
        }

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
            ['link', 'image', ] // link and image, video
        ];
        const quill = new Quill('#quill-content', {
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Add some content...',
            theme: 'snow'
        });

        const quill2 = new Quill('#quill-content2', {
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Add some content...',
            theme: 'snow'
        });

        document.getElementById('content-generation-form').onsubmit = function(event) {
            event.preventDefault();
            document.getElementById('loader').style.display = 'block';

            // Get the HTML content from the Quill editor
            let content = quill.root.innerHTML;
            // Set the content to the hidden textarea
            $('#article_content').val(content);

            console.log($('#article_content').val());

            const formData = new FormData(this);
            console.log('Form Data:', [...formData.entries()]);

            fetch("{{ route('creation.generate-content.post') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    document.getElementById('loader').style.display = 'none';
                    const generatedContentDiv = document.getElementById('generated-content');
                    generatedContentDiv.innerHTML = "";
                    data.forEach((content, index) => {
                        const contentBlock = document.createElement('div');
                        contentBlock.className = "content-block";
                        contentBlock.innerHTML = `<p class="tab">Copy ${index + 1}</p>`;
                        const contentBody = document.createElement('div');
                        contentBody.className = "content-body";
                        contentBody.innerHTML = nl2br(content);
                        contentBlock.appendChild(contentBody);
                        generatedContentDiv.appendChild(contentBlock);

                        const downloadContentButton = document.getElementById('download-button');
                        downloadContentButton.removeAttribute('disabled');
                        downloadContentButton.classList.remove('bg-gray-500', 'cursor-auto');
                        downloadContentButton.classList.add('bg-gray-800', 'cursor-pointer');
                    });
                })
                .catch(error => {
                    document.getElementById('loader').style.display = 'none';
                    console.error('Error generating content:', error);
                });
        };

        document.getElementById('content-generation-form2').onsubmit = function(event) {
            event.preventDefault();
            document.getElementById('loader').style.display = 'block';

            // Get the HTML content from the Quill editor
            let content = quill2.root.innerHTML;
            // Set the content to the hidden textarea
            $('#article_content2').val(content);

            console.log($('#article_content2').val());

            const formData = new FormData(this);
            console.log('Form Data:', [...formData.entries()]);

            fetch("{{ route('creation.generate-content.post') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    document.getElementById('loader').style.display = 'none';
                    const generatedContentDiv = document.getElementById('generated-content2');
                    generatedContentDiv.innerHTML = "";
                    data.forEach((content, index) => {
                        const contentBlock = document.createElement('div');
                        contentBlock.className = "content-block2";
                        contentBlock.innerHTML = `<p class="tab">Copy ${index + 1}</p>`;
                        const contentBody = document.createElement('div');
                        contentBody.className = "content-body2";
                        contentBody.innerHTML = nl2br(content);
                        contentBlock.appendChild(contentBody);
                        generatedContentDiv.appendChild(contentBlock);

                        const downloadContentButton = document.getElementById('download-button2');
                        downloadContentButton.removeAttribute('disabled');
                        downloadContentButton.classList.remove('bg-gray-500', 'cursor-auto');
                        downloadContentButton.classList.add('bg-gray-800', 'cursor-pointer');
                    });
                })
                .catch(error => {
                    document.getElementById('loader').style.display = 'none';
                    console.error('Error generating content:', error);
                });
        };

        function createHtmlFile(content, filename) {
            const htmlContent = `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>${filename}</title>
                </head>
                <body>
                    ${content}
                </body>
                </html>
            `;

            return new File([htmlContent], filename, {
                type: 'text/html'
            });
        }

        function downloadZip(body) {
            const elements = document.querySelectorAll(body);
            const zip = new JSZip();

            elements.forEach((element, index) => {
                const content = element.innerHTML;
                const filename = `copy_${index + 1}.html`;
                const htmlFile = createHtmlFile(content, filename);
                zip.file(filename, htmlFile);
            });

            zip.generateAsync({
                type: 'blob'
            }).then(function(blob) {
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'content.zip';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }

        document.getElementById('download-button').addEventListener('click', function(event) {
            event.preventDefault();
            downloadZip('.content-body');
        });

        document.getElementById('download-button2').addEventListener('click', function(event) {
            event.preventDefault();
            downloadZip('.content-body2');
        });
    </script>
</x-app-layout>
