<x-app-layout>
    <link href="{{ asset('styles/generate-content.css') }}" rel="stylesheet">

    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">Generate Reviews</h1>
    </div>
 
    <div id="loader" class="loader" style="display: none;"></div>
    
    <div class="container mx-auto p-4">
        <div class="form-area">
            <form class="content-generation-form" id="content-generation-form" method="POST">
                @csrf
                <div class="field-container mb-4">
                <x-label-with-tooltip 
                    label="Review Prompt"
                    id="input_review"
                    text="Enter a sample review to get started. You will get the best results if you add at least a couple of words. This field is required."
                />
                
                    <div class="field ml-2">
                        <textarea name="input_review" id="input_review" rows="4" cols="50" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('input_review') }}</textarea>
                    </div>
                </div>

                <div class="field-container mb-4">
                    <x-label-with-tooltip 
                        label="Keywords"
                        id="keywords"
                        text="Add any keywords you would like review to include, please separate with commas. This field is optional."
                    />                    
                <div class="field ml-2">
                        <input type="text" id="keywords" name="keywords" value="{{ old('keywords') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="field-container">
                <x-label-with-tooltip 
                        label="Additional prompt instructions"
                        id="custom_prompt"
                        text="Add any extra instructions you would like to add to the prompt. Things to not include for example. This field is optional."
                    />   
                    <div class="field">
                        <input type="text" id="custom_prompt" name="custom_prompt" value="{{ $custom_prompt}}">
                    </div>
                </div>
              
                <div class="field-container mb-4">
                <x-label-with-tooltip 
                    label="Number of reviews"
                    id="num_review"
                    text="Enter the number of reviews you would like to be generated. This field will be set to 1 by default. The maximum number of reviews is 20."
                />
                    <div class="field ml-2">
                        <input type="number" id="num_reviews" name="num_reviews" value="{{ old('num_reviews',1) }}" min="1" max="20" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4 button-container field-container" id="button-container">
                    <x-primary-button class="ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" id="generate-reviews-btn">
                        Generate Reviews
                    </x-primary-button>
                </div>
            </form>
        </div>
        <div class="content-area">
            <h2 class="text-2xl font-bold mb-4">Generated Reviews:</h2>
            <div id="generated-reviews" class="space-y-4 mb-4"></div>
            <button style="display:none" id="export-btn" class="me-2 mb-2 bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline">Export to Word</button>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('content-generation-form');
        const loader = document.getElementById('loader');
        const generatedReviewsDiv = document.getElementById('generated-reviews');
        const exportBtn = document.getElementById('export-btn');

        form.onsubmit = function (event) {
            event.preventDefault();
            loader.style.display = 'block';

            const formData = new FormData(this);
            console.log('Form Data:', [...formData.entries()]);

            fetch("{{ route('fetch.creation.generate-reviews') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loader.style.display = 'none';
                generatedReviewsDiv.innerHTML = "";
                data.forEach((review, index) => {
                    const reviewBlock = document.createElement('div');
                    reviewBlock.className = "review-block p-4 border rounded-lg shadow-md bg-white flex items-start justify-between";
                    reviewBlock.innerHTML = `
                        <div class="review-content">
                            <p class="font-semibold mb-2">Review ${index + 1}</p>
                            <div class="review-body mb-2">${review}</div>
                        </div>
                        <button class="copy-button bg-blue-500 text-white px-2 py-1 rounded" onclick="copyToClipboard(${index})">Copy</button>
                    `;
                    generatedReviewsDiv.appendChild(reviewBlock);
                });
                exportBtn.style.display = 'inline-block';
            })
            .catch(error => {
                loader.style.display = 'none';
                console.error('Error generating reviews:', error);
            });
        };

        window.copyToClipboard = function (index) {
            const reviewText = document.querySelectorAll('.review-body')[index].innerText;
            navigator.clipboard.writeText(reviewText).then(() => {
                alert('Review copied to clipboard!');
            }).catch(err => {
                console.error('Error copying text: ', err);
            });
        }

        function getCurrentDateFormatted() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based, so we add 1
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}${month}${day}`;
        }

        function exportToWord() {
            if (generatedReviewsDiv.innerHTML.trim() === "") {
                alert("No reviews to export.");
                return;
            }

            // Create a clone of the generated reviews without the copy buttons
            const cloneDiv = generatedReviewsDiv.cloneNode(true);
            const copyButtons = cloneDiv.querySelectorAll('.copy-button');
            copyButtons.forEach(button => button.remove());

            // Convert the cloned HTML content to a Word document
            const content = cloneDiv.innerHTML;
            const blob = htmlDocx.asBlob(content, { orientation: 'portrait' });

            // Generate a timestamp
            const timestamp = getCurrentDateFormatted();

            // Create a link element to trigger the download
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `generated-reviews-${timestamp}.docx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        exportBtn.addEventListener('click', exportToWord);
    });
</script>
