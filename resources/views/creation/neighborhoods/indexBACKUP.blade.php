<x-app-layout>
    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Create New Neighborhoods
        </h1>
    </div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-8">
            <a href="{{route('creation.neighborhoods.googlePoi')}}" class=" text-blue-500 hover:text-gray-300">
                {{ __('Modify Google Points of Interests') }}
            </a>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-8">
        <!-- Server selection dropdown -->
        <div class="mb-4">
            <label for="server_option" class="block text-gray-700 font-bold mb-2">Server</label>
            <select id="serverSelect" name="server" class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
            <option value="">Select server</option>
            @foreach ($servers as $server)
                <option value="{{ $server }}">{{ $server }}</option>
            @endforeach
        </select>
        <p class="text-red-600 text-xs italic">Select the where site is located.</p>

        </div>
        

        <!-- Dynamic dropdown for site list -->
        <div>
            <label for="site_option" class="block text-gray-700 font-bold mb-2">Site</label>
             <select name="siteListDropdown" id="siteListDropdown"
             class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
            <option value="">Select site</option>
        </select>
        <p class="text-red-600 text-xs italic">Select site you want to modify.</p>

        </div>
       

        <div id="siteDetails" class="mb-4"></div>


        <div class="mb-4">
            <form id="neighborhoodForm"  style="display:none" method="POST" action="{{ route('creation.neighborhoods.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="siteId" id="siteIdInput" value="">

                <div class="mb-4">
                    <label for="post_title" class="block text-gray-700 font-bold mb-2">Title</label>
                    <input type="text" name="post_title" id="post_title"
                        placeholder="Add a post title"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="post_content" class="block text-gray-700 font-bold mb-2">Content</label>
                    
                    <textarea id="post_content" name="post_content" class="text-normal shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;"></textarea>
                <div id="quill-content"></div>
              </div>

                <div class="my-5">
                    <label for="post_status" class="block text-gray-700 font-bold mb-2">Post Status</label>
                    <select name="post_status" id="post_status" onchange="logValue(this)"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select Status</option>
                        <option value="draft">Draft</option>
                        <option value="publish">Publish</option>
                    </select>
                </div>

                <hr>

                <!-- Custom Repeater Fields -->
                <div class="custom-repeater-fields mt-5 mb-2">
                    <label for="custom-repeater-field" class="block text-gray-700 font-bold mb-2">Neighborhoods</label>
                    <div class="custom-repeater-field">
                       
                        <input type="text" name="text[]" placeholder="Name" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div class="image-meta-group mt-3">
                            <div class="form-group mt-3">
                                <input type="file" class="custom-file-input w-half text-md text-gray-900 rounded border border-gray-300 cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">   
                            </div>
                            <div class="form-group mt-3">
                                <label for="imageTitle">Image Title</label>
                                <input type="text" class="form-control image-title" placeholder="Enter image title">
                            </div>
                            <div class="form-group mt-3">
                                <label for="imageAlt">Image Alt Tag</label>
                                <input type="text" class="form-control image-alt" placeholder="Enter image alt tag">
                            </div>
                        </div>
                        <div class="mt-3 flex items-center">
                            <button type="button" class="upload-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Upload</button>
                                
                            <div role="status" style="display:none" class="ml-2 uploadSpinner">
                            <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                       
                        <div class="upload-status"></div>
                    </div>
                </div>
                <input type="hidden" name="custom_repeater_field" id="custom_repeater_field">
                <x-secondary-button type="button" class="add-custom-repeater-field mb-4">Add More</x-secondary-button>
                    

                <!-- Weather Code -->
                <div class="mb-4">
                    <label for="weather" class="block text-gray-700 font-bold mb-2">Weather Embed Code</label>
                    <input type="text" name="weather" id="weather" placeholder="HTML code"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Map Repeater Fields -->
                <div class="map-repeater-fields mb-4">
                    <label for="map-repeater-field" class="block text-gray-700 font-bold mb-2">Maps</label>
                    <div class="map-repeater-field mb-2">
                        <input type="text" name="map_text[]" placeholder="Map Text"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <x-secondary-button type="button" class="add-map-repeater-field">Add More</x-secondary-button>
                </div>

                <!-- Keywords -->
                <div class="keywords-fields mb-4">
                    <label for="keyword-field" class="block text-gray-700 font-bold mb-2">Keywords</label>
                    <div class="keyword-field mb-2">
                        <input type="text" name="keywords[]" placeholder="Keyword"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <x-secondary-button type="button" class="add-keyword-repeater-field">Add More</x-secondary-button>
                </div>

                <div class="things-repeater-fields mb-2">
                    <label for="things-repeater-field" class="block text-gray-700 font-bold mb-2">Things to do</label>
                    <div class="things-repeater-field">
                       
                        <input type="text" name="thingsText[]" placeholder="Something to do" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div class="image-meta-group mt-3">
                            <div class="form-group mt-3">
                                <input type="file" class="things-file-input w-half text-md text-gray-900 rounded border border-gray-300 cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">   
                            </div>
                            <div class="form-group mt-3">
                                <label for="imageTitle">Image Title</label>
                                <input type="text" class="form-control things-image-title" placeholder="Enter image title">
                            </div>
                            <div class="form-group mt-3">
                                <label for="imageAlt">Image Alt Tag</label>
                                <input type="text" class="form-control things-image-alt" placeholder="Enter image alt tag">
                            </div>
                        </div>
                        <div class="mt-3 flex items-center">
                            <button type="button" class="upload-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Upload</button>
                                 
                            <div role="status" style="display:none" class="ml-2 uploadSpinner">
                            <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                       
                        <div class="upload-status"></div>
                    </div>
                </div>
                <input type="hidden" name="custom_things_field" id="custom_things_field">
                <x-secondary-button type="button" class="add-things-repeater-field mb-4">Add More</x-secondary-button>


                <div class="max-w-7xl flex items-center justify-center mb-4">
                    <x-primary-button type="submit">
                        {{ __('Create') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>


</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>

    function logValue(selectObject) {
        console.log(selectObject.value);
    }
    document.getElementById('serverSelect').addEventListener('change', function() {
        var server = this.value;
        var siteListDropdown = document.getElementById('siteListDropdown');

        // Prepare the URL with query parameters
        var url = new URL('{{ route('creation.neighborhoods.byServer') }}');
        url.searchParams.append('server', server);

        // Make an AJAX GET request to the server
        fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(sites => {
                siteListDropdown.innerHTML = '<option value="">Select Site</option>'; // Reset the dropdown

                // Append new site options to the dropdown
                sites.forEach(site => {
                    let option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = site.site_url;
                    siteListDropdown.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    document.getElementById('siteListDropdown').addEventListener('change', function() {
        var siteId = this.value;
        console.log(siteId);
        var siteDetails = document.getElementById('siteDetails');
        var neighborhoodForm = document.getElementById('neighborhoodForm');
        var siteIdInput = document.getElementById('siteIdInput');

        if (!siteId) {
            siteDetails.textContent = 'Please select a site.';
            neighborhoodForm.style.display = 'none';
            siteIdInput.value = '';
            return;
        } else {
            siteIdInput.value = siteId;
            neighborhoodForm.style.display = 'block';

        }

        // Generate the URL dynamically using the siteId
        var url = '{{ route('wordpress.connect.db', ':id') }}'; // Placeholder URL
        url = url.replace(':id', siteId); // Replace placeholder with actual siteId


        // Make an AJAX GET request to the server
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
            .then(function(data) {
                if (data.success) {
                    siteDetails.innerHTML = 'Connection successful'; // Clear previous data
                    console.log(data.data)
                    data.data.forEach(function(post) {
                        var p = document.createElement('p');
                        p.textContent = post.title; // Assuming 'title' is a property of the post
                        siteDetails.append(p);
                    });
                } else {
                    siteDetails.textContent = 'Failed to load posts: ' + data.message;
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                siteDetails.textContent = 'Error loading posts.';
            });
    });


    // Custom Repeater Fields
    $(document).ready(function() {
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

    const siteId = $('#siteIdInput').val();
    let uploadedCustomRepeaterFieldFiles = [];
    let uploadedThingsRepeaterFieldFiles = [];

    function handleUploadButtonClick(parentFieldSelector, inputFileClass, textFieldName, titleFieldClass, altFieldClass, uploadStatusSelector, uploadedFilesArray) {
        $(document).on('click', parentFieldSelector + ' .upload-button', async function () {
            const parentField = $(this).closest(parentFieldSelector);
            const input = parentField.find(inputFileClass)[0]; // The file input
            const text = parentField.find(`input[name="${textFieldName}"]`).val(); // The text input value
            const file = input.files[0];
            const title = parentField.find(titleFieldClass).val(); // Image title
            const alt = parentField.find(altFieldClass).val(); // Image alt tag
            const uploadStatus = parentField.find(uploadStatusSelector); // The upload-status div
            const spinner = parentField.find('.uploadSpinner');

            // Check if text input is required and empty
            if (!text) {
                uploadStatus.html('<span class="text-red-500">Please enter a name before uploading</span>').fadeIn().delay(3000).fadeOut();
                return;
            }

            // Show the spinner
            spinner.show();

            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('title', title);
                formData.append('alt', alt);
                formData.append('siteId', $('#siteIdInput').val());

                try {
                    spinner.show();
                    console.log('Starting file upload...'); // Log the start of the upload
                    const response = await fetch('{{ route('wordpress.upload.file') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const responseText = await response.text(); // Get the raw response text

                    // Try to parse the response as JSON
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        throw new Error('Failed to parse JSON response');
                    }

                    console.log('Parsed response:', result);

                    if (result.success) {
                        uploadStatus.html('<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">' +
                            '<span class="font-medium">Upload successful!</span></div>').fadeIn().delay(3000).fadeOut();
                        uploadedFilesArray.push({
                            'text': text,
                            'image_id': result.data.id
                        });
                        console.log(uploadedFilesArray);
                    } else {
                        uploadStatus.html('<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">' +
                            '<span class="font-medium">Upload failed: </span>' + result.message + '</div>').fadeIn().delay(3000).fadeOut();
                    }
                } catch (error) {
                    uploadStatus.html('<span class="text-red-500">There was an issue uploading the file.</span>').fadeIn().delay(3000).fadeOut();
                    console.error('Error:', error); // Log the error
                } finally {
                    spinner.hide();
                }
            } else {
                uploadStatus.html('<span class="text-red-500">Please select a file to upload</span>').fadeIn().delay(3000).fadeOut();
            }
        });
    }

    handleUploadButtonClick('.custom-repeater-field', '.custom-file-input', 'text[]', '.image-title', '.image-alt', '.upload-status', uploadedCustomRepeaterFieldFiles);
    handleUploadButtonClick('.things-repeater-field', '.things-file-input', 'thingsText[]', '.things-image-title', '.things-image-alt', '.upload-status', uploadedThingsRepeaterFieldFiles);

    $('#neighborhoodForm').on('submit', function (event) {
        event.preventDefault();
        // Collect all uploaded file URLs
        const serializedCustomRepeaterData = JSON.stringify(uploadedCustomRepeaterFieldFiles);
        $('#custom_repeater_field').val(serializedCustomRepeaterData);

        const serializedThingsRepeaterData = JSON.stringify(uploadedThingsRepeaterFieldFiles);
        $('#custom_things_field').val(serializedThingsRepeaterData);

        // Get the HTML content from the Quill editor
         let content = quill.root.innerHTML;
        // Set the content to the hidden textarea
        $('#post_content').val(content);
        postContentValue = $('#post_content').val();
        if (!postContentValue) {
            alert('No post content');
        } else{
            // Submit the form after setting the content
            this.submit();
        }
    });

    $('.add-custom-repeater-field').click(function() {
        $('.custom-repeater-fields').append(`
            <div class="custom-repeater-field mt-5">
                <input type="text" name="text[]" placeholder="Name" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <div class="image-meta-group mt-3">
                    <div class="form-group mt-3">
                        <input type="file" class="custom-file-input w-half text-md text-gray-900 rounded border border-gray-300 cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">   
                    </div>
                    <div class="form-group mt-3">
                        <label for="imageTitle">Image Title</label>
                        <input type="text" class="form-control image-title" placeholder="Enter image title">
                    </div>
                    <div class="form-group mt-3">
                        <label for="imageAlt">Image Alt Tag</label>
                        <input type="text" class="form-control image-alt" placeholder="Enter image alt tag">
                    </div>
                </div>
                <div class="mt-3 flex items-center">
                    <button type="button" class="upload-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Upload</button>
                    <div role="status" style="display:none" class="ml-2 uploadSpinner">
                        <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                    </div>
                </div>
                <div class="upload-status"></div>
                <button type="button" class="bg-red-700 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg remove-custom-repeater-field">Remove</button>
            </div>
        `);
    });

    $('.custom-repeater-fields').on('click', '.remove-custom-repeater-field', function() {
        $(this).parent().remove();
    });

    // Map Repeater Fields
    $('.add-map-repeater-field').click(function() {
        $('.map-repeater-fields').append(`
            <div class="map-repeater-field mt-3">
                <input type="text" name="map_text[]" placeholder="Map Text">
                <button type="button" class="remove-map-repeater-field bg-red-700 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg">Remove</button>
            </div>
        `);
    });

    $('.map-repeater-fields').on('click', '.remove-map-repeater-field', function() {
        $(this).parent().remove();
    });

    // Keywords Fields
    $('.add-keyword-repeater-field').click(function() {
        $('.keywords-fields').append(`
            <div class="keyword-field mt-3">
                <input type="text" name="keywords[]" placeholder="Keyword">
                <button type="button" class="remove-keyword-field bg-red-700 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg">Remove</button>
            </div>
        `);
    });

    $('.keywords-fields').on('click', '.remove-keyword-field', function() {
        $(this).parent().remove();
    });

    // Things Repeater Fields
    $('.add-things-repeater-field').click(function() {
        $('.things-repeater-fields').append(`
            <div class="things-repeater-field">
                <input type="text" name="thingsText[]" placeholder="Something to do" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <div class="image-meta-group mt-3">
                    <div class="form-group mt-3">
                        <input type="file" class="things-file-input w-half text-md text-gray-900 rounded border border-gray-300 cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">   
                    </div>
                    <div class="form-group mt-3">
                        <label for="imageTitle">Image Title</label>
                        <input type="text" class="form-control things-image-title" placeholder="Enter image title">
                    </div>
                    <div class="form-group mt-3">
                        <label for="imageAlt">Image Alt Tag</label>
                        <input type="text" class="form-control things-image-alt" placeholder="Enter image alt tag">
                    </div>
                </div>
                <div class="mt-3 flex items-center">
                    <button type="button" class="upload-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Upload</button>
                    <div role="status" style="display:none" class="ml-2 uploadSpinner">
                        <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                    </div>
                </div>
                <div class="upload-status"></div>
                <button type="button" class="remove-thing-repeater-field bg-red-700 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg">Remove</button>
            </div>
        `);
    });

    $('.things-repeater-fields').on('click', '.remove-thing-repeater-field', function() {
        $(this).parent().remove();
    });
});


</script>
