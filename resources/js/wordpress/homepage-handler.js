import { createApp } from "vue";
import { createPinia } from 'pinia';
import LogoManager from "../components/LogoManager.vue";
import LibraryButton from "../components/ImageLibrary/LibraryButton.vue";

console.log('homepage-handler.js loaded successfully');

document.addEventListener('DOMContentLoaded', function () {
    const app = createApp({
        components: {
            LogoManager,
            LibraryButton,
        },
    });

    app.use(createPinia());
    app.mount('#wordpress-content');
});

// document.addEventListener('DOMContentLoaded', function () {
const selectedImages = {};
let uploadedImageIds = {};

// Handle the image-selected event emitted from the Vue component
window.addEventListener('image-selected', function (event) {
    const { sectionId, url, title, altText, nicheId } = event.detail.imageData;
    selectedImages[sectionId] = { url, altText, title, nicheId };

    // console.log("Image selected event received:", event.detail);
    // Update hidden fields in the form with the new image details
    const urlField = document.getElementById(`new-image-url-${sectionId}`);
    const idField = document.getElementById(`new-image-id-${sectionId}`);

    if (urlField && idField) {
        urlField.value = url;
        idField.value = event.detail.imageData.id;
    } else {
        console.error(`Fields not found for sectionId: ${sectionId}`);
    }
    // selectedImages.push(event.detail);
});

let spinner;

function populateSiteImages(data) {
    if (data.site_icon) {
        const siteIconImgEl = document.getElementById('site-icon');
        const siteIconAltEl = document.getElementById('site-icon-alt');
        const siteIconIdEl = document.getElementById('existing-favicon-logo-id');
        siteIconImgEl.src = data.site_icon.url;
        siteIconAltEl.value = data.site_icon.alt;
        siteIconIdEl.value = data.site_icon.id;
    }

    if (data.site_logo) {
        const siteLogoImgEl = document.getElementById('site-logo');
        const siteLogoAltEl = document.getElementById('site-logo-alt');
        const siteLogoIdEl = document.getElementById('existing-company-logo-id');

        siteLogoImgEl.src = data.site_logo.url;
        siteLogoAltEl.value = data.site_logo.alt;
        siteLogoIdEl.value = data.site_logo.id;
    }
}

function showAlert(alertId, message) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        alertElement.querySelector('p').textContent = message;
        alertElement.style.display = 'block';

        alertElement.classList.remove('fade-out');

        setTimeout(() => {
            alertElement.classList.add('fade-out');
            setTimeout(() => {
                alertElement.style.display = 'none';
                alertElement.classList.remove('fade-out');
            }, 1000);
        }, 6000);
    }
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function countWords(text) {
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

function updateTotalWordCount() {
    let totalWordCount = 0;

    // Count words in input fields with the class word-input
    document.querySelectorAll('.word-input').forEach(input => {
        const content = input.value || '';
        const wordCount = countWords(content);
        totalWordCount += wordCount;
    });

    // Count words in quill editor
    document.querySelectorAll('.word-count').forEach(div => {
        const content = div.innerText || '';
        const wordCount = countWords(content);
        totalWordCount += wordCount;
    });

    // Display total word count
    const wordCountElement = document.getElementById('total-word-count');
    if (wordCountElement) {
        wordCountElement.textContent = `Total Word Count: ${totalWordCount}`;
    }

}


// Attach event listeners (input and keyup)
document.addEventListener('input', updateTotalWordCount);
document.addEventListener('keyup', updateTotalWordCount);

function populateSeoInfo(post) {
    $('#rank_math_focus_keyword').val(post.rank_math_focus_keyword);
    $('#rank_math_description').val(post.rank_math_description);
}


// Event delegation: Add event listener to the parent container
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.toggle-button')) {
        const fieldId = event.target.getAttribute('data-toggle-field');
        toggleField(fieldId);
    }
});

function toggleField(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        const isVisible = field.style.display === 'block';
        field.style.display = isVisible ? 'none' : 'block';
    }
}

const collapsibleFieldIds = ['body_scripts', 'header_scripts', 'google_scripts','form_html'];

// Function to initialize the fields once they are in the DOM
function initializeCollapsibleFields() {
    collapsibleFieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Optionally hide the field initially
            field.style.display = 'none';

            // Add toggle button logic (this assumes buttons with specific IDs or classes are available)
            const toggleButton = document.querySelector(`button[data-toggle-field="${fieldId}"]`);
            if (toggleButton) {
                toggleButton.addEventListener('click', () => toggleField(fieldId));
            }
        }
    });
}
// Call initializeCollapsibleFields after your fields are added to the DOM

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

// Function to generate Google Maps embed URL
function generateGoogleMapsUrl(city, state, companyAddress) {
    let location = '';
    if (companyAddress) {
        location = `${companyAddress.replace(/\s+/g, '+')}`;
        if (city) location += `+${city.replace(/\s+/g, '+')}`;
        if (state) location += `+${state.replace(/\s+/g, '+')}`;
    } else if (city) {
        location = `${city.replace(/\s+/g, '+')}`;
        if (state) location += `+${state.replace(/\s+/g, '+')}`;
    } else if (state) {
        location = `${state.replace(/\s+/g, '+')}`;
    }
    return `https://maps.google.com/maps?q=${location}&output=embed&zoom=12`;
}

// Function to update Google Map embed URL
function updateGoogleMap() {
    const city = document.getElementById('city')?.value.trim() || '';
    const state = document.getElementById('state')?.value.trim() || '';
    const companyAddress = document.getElementById('company_address')?.value.trim() || '';
    const googleMapField = document.getElementById('footer_map');

    if (googleMapField) {
        const googleMapsUrl = generateGoogleMapsUrl(city, state, companyAddress);
        googleMapField.value = googleMapsUrl;
    }
}

// Function to initialize event listeners
function initializeGoogleMapListeners() {
    const fields = ['city', 'state', 'company_address'];

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', updateGoogleMap);
        }
    });

    // Initial update when the fields are loaded
    updateGoogleMap();
}

function renderFormFields(post) {
    const themeOptions = post.theme_options || {};
    const siteUrl = post.siteUrl || '';

    const fieldMapping = {
        business_name: { display_name: 'Business Name', type: 'input' },
        niche: { display_name: 'Niche', type: 'input' },
        city: { display_name: 'City', type: 'input' },
        state: { display_name: 'State', type: 'input' },
        zip_code: { display_name: 'Zip Code', type: 'input' },
        company_address: { display_name: 'Company Address', type: 'input' },
        phone_number: { display_name: 'Phone Number', type: 'tel' },
        contant_button: { display_name: 'Contact Button', type: 'hidden' },
        footer_map: { display_name: 'Footer Map', type: 'long-input' },
        footer_logo: { display_name: 'Footer Logo', type: 'image' },
        copyright: { display_name: 'Copyright', type: 'hidden' },
        terms_conditions: { display_name: 'Terms & Conditions', type: 'hidden' },
        privacy_policy: { display_name: 'Privacy Policy', type: 'hidden' },
        google_scripts: { display_name: 'Google Scripts', type: 'textarea-collapsible' },
        header_scripts: { display_name: 'Header Scripts', type: 'textarea-collapsible' },
        body_scripts: { display_name: 'Body Scripts', type: 'textarea-collapsible' }, 
        form_html: { display_name: 'Form HTML', type: 'textarea-collapsible' },
        form_selection: { display_name: 'Form Selection', type: 'dropdown' }, 
        linkedin: { display_name: 'LinkedIn', type: 'hidden' },
        data: { display_name: 'Data', type: 'hidden' },
        ringba_phone_number: { display_name: 'Ringba Phone Number', type: 'hidden' },
    };
   
    const formContainer = document.getElementById('theme-options-section');
    formContainer.innerHTML = '';

    for (const [key, fieldConfig] of Object.entries(fieldMapping)) {
        const { display_name, type } = fieldConfig;
        const value = themeOptions[key] || '';
        let escapedValue = escapeHtml(String(value));

        let fieldHtml = '';
        if (type === 'image') {
            const imageUrl = value ? `${siteUrl}/wp-content/uploads${value}` : '';

            fieldHtml = `
               <div class="w-full md:w-1/2 px-2">
            <div class="mb-4">
                <label for="${key}" class="p-1 text-sm block font-bold text-gray-700 mb-2">${display_name}</label>
                <img id="${key}-preview" src="${imageUrl}" alt="${display_name}" class="image-preview mb-2 h-24 max-w-xs rounded shadow-md">
                <input type="hidden" id="existing-${key}-id" name="existing-${key}-id" value="${escapedValue}">
                <input type="hidden" id="new-image-url-${key}" name="new-image-url-${key}" value="">
                <input type="hidden" id="new-image-id-${key}" name="new-image-id-${key}" value="">
                <!-- Hidden input to store the image path in theme_options -->
                <input type="hidden" id="theme_options[${key}]" name="theme_options[${key}]" value="${escapedValue}">

                <div id="${key}-vue-component"></div>
            </div>
        </div>
            `;
        } else if (type === 'tel') {
            escapedValue = formatPhoneNumber(escapedValue);
            fieldHtml = `
                <div class="mb-4 inline-flex m-2">
                    <label for="${key}" class="p-1 text-sm block font-bold text-gray-700 mb-2">${display_name}</label>
                    <input type="tel" name="theme_options[${key}]" id="${key}" value="${escapedValue}" 
                        pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" title="Phone number must be in the format: 000-000-0000"
                        class="font-normal text-sm shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <small class="text-red-500 ml-2">Format: XXX-XXX-XXXX</small>
                </div>
            `;
        } else if (type === 'input') {
            fieldHtml = `
                <div class="mb-4 inline-flex m-2">
                    <label for="${key}" class="p-1 text-sm block font-bold text-gray-700 mb-2">${display_name}</label>
                    <input type="text" name="theme_options[${key}]" id="${key}" value="${escapedValue}" 
                        class="font-normal text-sm shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            `;
        } else if (type === 'long-input') {
            fieldHtml = `
                <div class="mb-4 m-2">
                    <label for="${key}" class="p-1 text-sm block font-bold text-gray-700 mb-2">${display_name}</label>
                    <input type="text" name="theme_options[${key}]" id="${key}" value="${escapedValue}" 
                        class="font-normal text-sm shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            `;
        } else if (type === 'dropdown' && key === 'form_selection') {
            // add the Form Selection dropdown
            fieldHtml = `
                <div class="mb-4 m-2" id="form-selection-container" ">
                    <label id="form-selection-label" for="form_selection" class="ml-2 text-sm block font-bold text-gray-700 mb-2" >${display_name}</label>
                    <select id="form_selection" class="mb-2 font-normal text-sm shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select a Form</option>
                    </select>
                </div>
            `;
        } else if (type === 'textarea') {
            fieldHtml = `
                <div class="mb-4 m-2">
                    <label for="${key}" class="ml-2 text-sm block font-bold text-gray-700 mb-2">${display_name}</label>
                     <!--<select id="form_html_select" class="mb-2 font-normal text-sm shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select a Form HTML</option>
                    </select>-->
                    <textarea rows="3" name="theme_options[${key}]" id="${key}" class="font-normal text-sm shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">${escapedValue}</textarea>
                </div>
            `;
        } else if (type === 'textarea-collapsible') {
            fieldHtml = `
                <div class="mb-4 m-2">
                    <label for="${key}" class="ml-2 text-sm block font-bold text-gray-700 mb-2">
                        ${display_name}
                        <button type="button" class="toggle-button text-sm font-bold ml-2 hover:cursor-pointer" data-toggle-field="${key}">[-]</button>
                    </label>
                    <textarea rows="3" name="theme_options[${key}]" id="${key}" class="font-normal text-sm shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" style="display:none;">${escapedValue}</textarea>
                </div>
            `;
        } else if (type === 'hidden') {
            fieldHtml = `<input type="hidden" name="theme_options[${key}]" id="${key}" value="${escapedValue}">`;
        }

        formContainer.insertAdjacentHTML('beforeend', fieldHtml);
    }

    // Initialize collapsible fields after rendering
    initializeCollapsibleFields();
    initializeGoogleMapListeners();
    initializeImageVueComponents(fieldMapping);
    populateFormSelection();


    const formSelectionDropdown = document.getElementById('form_selection');
    formSelectionDropdown.addEventListener('change', function() {
        const formId = this.value;
        if (formId) {
            fetch(`/get-form-data/${formId}`)
                .then(response => response.json())
                .then(data => {
                    // Get the current content of the 'Header Scripts' field
                    const currentHeaderScripts = document.getElementById('header_scripts').value;
                    const currentBodyScripts = document.getElementById('body_scripts').value;
                    // Append the new 'header_code' and 'body_js' to the existing content
                    document.getElementById('header_scripts').value = currentHeaderScripts + '\n' + data.header_code;
                    document.getElementById('body_scripts').value = currentBodyScripts + '\n' + data.body_js;
                    // Populate the field with form data
                    document.getElementById('form_html').value = data.form_code;
                })
                .catch(error => console.error('Error fetching form data:', error));
        }
    });


}

function populateFormSelection() {
    // Fetch CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const niche = document.getElementById('niche_helm').value;

    // Fetch forms based on the selected niche
    fetch('/maintenance/forms-by-niche', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken  
        },
        body: JSON.stringify({ niche: niche }) 
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    })
    .then(forms => {
        const formSelectionDropdown = document.getElementById('form_selection');
        formSelectionDropdown.innerHTML = ''; // Clear existing options
        formSelectionDropdown.appendChild(new Option('Select a Form', '', true, true)); // Default option

        // Populate the form selection dropdown with the forms for the selected niche
        forms.forEach(form => {
            const option = document.createElement('option');
            option.value = form.id;
            option.textContent = form.name;
            formSelectionDropdown.appendChild(option);
        });

        // Show the form selection dropdown
        document.getElementById('form-selection-container').style.display = 'block';
        document.getElementById('form-selection-label').style.display = 'block';
    })
    .catch(error => {
        console.error('Error fetching forms by niche:', error);
    });
}


function initializeImageVueComponents(fieldMapping) {
    const imageFields = Object.keys(fieldMapping).filter(key => fieldMapping[key].type === 'image');

    imageFields.forEach(fieldKey => {
        const vueComponentPlaceholder = document.getElementById(`${fieldKey}-vue-component`);
        if (vueComponentPlaceholder) {
            const vueApp = createApp({
                components: {
                    LibraryButton
                },
                data() {
                    return {
                        fieldKey: fieldKey
                    };
                },
                methods: {
                    updateImage(imageData) {
                        // Update the image preview
                        const previewImg = document.getElementById(`${fieldKey}-preview`);
                        if (previewImg) {
                            previewImg.src = imageData.url;
                        }

                        // Update the hidden inputs
                        const newImageUrlInput = document.getElementById(`new-image-url-${fieldKey}`);
                        if (newImageUrlInput) {
                            newImageUrlInput.value = imageData.url;
                        }

                        const newImageIdInput = document.getElementById(`new-image-id-${fieldKey}`);
                        if (newImageIdInput) {
                            newImageIdInput.value = imageData.id || '';
                        }

                        // Store the selected image data for form submission
                        selectedImages[fieldKey] = imageData;
                    }
                },
                template: `
                    <library-button id='footer_logo' section-id='footer_logo'></library-button>
                `
            });

            vueApp.use(createPinia());

            vueApp.mount(`#${fieldKey}-vue-component`);
        }
    });
}



function formatPhoneNumber(value) {
    // Remove non-numeric characters
    value = value.replace(/\D/g, '');

    if (value.length === 10) {
        return `${value.substring(0, 3)}-${value.substring(3, 6)}-${value.substring(6, 10)}`;
    }

    return value; // Return the original value if it doesn't match the expected length
}


function parseThemeOptions() {
    const themeOptions = {};
    const themeOptionsFields = document.querySelectorAll('[name^="theme_options["]');

    themeOptionsFields.forEach(field => {
        const key = field.name.match(/\[(.*?)\]/)[1];
        let value = field.value;

        // Special handling for 'data' field
        if (key === 'data' && field.type !== 'hidden') {
            try {
                value = JSON.parse(value);
            } catch (e) {
                console.warn('Failed to parse data field as JSON. Keeping as string.');
            }
        }

        themeOptions[key] = value;
    });

    return themeOptions;
}

function initializeQuillEditor(selector) {
    if (document.querySelector(selector)) {
        new Quill(selector, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'script': 'sub' }, { 'script': 'super' }],
                    [{ 'indent': '-1' }, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'font': [] }],
                    [{ 'align': [] }],
                    ['clean'],
                    ['link']
                ]
            }
        });
    }
}

function loadSites() {
    const niche = document.getElementById('niche_helm').value;
    const siteSelect = document.getElementById('site');
    siteSelect.innerHTML = '<option value="">Select site</option>';

    // Always fetch sites, even if niche is empty
    fetch(`${window.wpSitesBaseUrl}${encodeURIComponent(niche)}`)
        .then(response => response.json())
        .then(sites => {
            sites.forEach(site => {
                const option = document.createElement('option');
                option.value = site.id;
                option.textContent = site.site_url;
                option.setAttribute('data-site-url', site.site_url);
                siteSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading sites:', error));
}

function getSectionInputs(section, index) {
    const sectionId = `section-${index}`;
    const existingImageId = section.content.image?.id || '';

    switch (section.type) {
        case 'Top Banner With Content & Button':
            return `
                    <input type="text" name="sections[${index}][heading]" value="${section.content.heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Heading">
                    <input type="text" name="sections[${index}][subheading]" value="${section.content.subheading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Subheading">
                      <!-- Image display and alt text editing -->
                    <div class="image-section mb-4">
                        <img src="${section.content.image.url}" alt="${section.content.image.alt}" class="image-preview mb-2 w-full max-w-xs rounded shadow-md">
                        <input type="text" name="sections[${index}][image_alt]" value="${section.content.image.alt}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Image Alt Text">
                        
                         <!-- Hidden fields to track the existing image -->
                        <input type="hidden" name="sections[${index}][existing_image_id]" value="${existingImageId}">
                        <input type="hidden" id="new-image-url-${sectionId}" name="sections[${index}][new_image_url]" value="">
                        <input type="hidden" id="new-image-id-${sectionId}" name="sections[${index}][new_image_id]" value="">
                        <!-- Vue Library Button Component -->
                        <div id="library-button-${index}" class="my-2">
                            <library-button :section-id="'${sectionId}'"></library-button>
                        </div>
                    
                    </div>
                </div>
            `;
        case 'Two Blurb Section With Address & Form':
        case 'CTA With Background Image & Button':

            return `
                <div>
                    <input type="text" name="sections[${index}][heading]" value="${section.content.section_heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Heading">
                    <div id="editor-content-${index}" class="word-count mb-2 ai-target">${section.content.section_content}</div>
                    <textarea style="display:none" name="sections[${index}][text]" id="input-text-${index}"></textarea>
                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}">Generate AI Content</button>

                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}" placeholder="Max words" min=0>
                        </div>  
                    
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Submit</button>
                        <div id="spinner-${index}" class="spinner hidden ms-2 inline-flex"></div>

                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Use this content</button>
                    </div>
                </div>
            `;
        case 'Content Block':
        case 'Content Block Full Width':
            const { textContent, imageTags } = extractImageTags(section.content.content);

            console.log("Extracted image tags: ", imageTags);
        
            let hiddenImageInputs = '';
            imageTags.forEach((img, idx) => {
                if (img.html) {
                    // Check if the image is valid and then encode it
                    const encodedImage = btoa(img.html); // Encode the image tag as base64
                    hiddenImageInputs += `<input type="hidden" name="sections[${index}][extracted_image_${idx}]" value="${encodedImage}">`;
                } else {
                    console.warn(`Image tag is empty or invalid at index ${idx}`);
                }
            });

            return `
                <div>
                    <div id="editor-content-${index}" class="word-count mb-2 ai-target">${section.content.content}</div>
                    <textarea style="display:none" name="sections[${index}][text]" id="input-text-${index}"></textarea>
                    ${hiddenImageInputs} <!-- Insert hidden image inputs if they exist -->

                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}">Generate AI Content</button>

                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}" placeholder="Max words" min=0>
                        </div>  
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Submit</button>
                        <div id="spinner-${index}" class="spinner hidden ms-2 inline-flex"></div>

                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Use this content</button>
                    </div>
                </div>
            `;
        case 'Right Image Left Text With Button & Heading':
        case 'Left Image Right Text With Button & Heading':
        case 'Text Block With Background Image & Heading':
        case 'Left Image & Right Text With Accordion':
            return `
                <div>
                    <input type="text" name="sections[${index}][heading]" value="${section.content.heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Section Heading">

                    <div id="editor-content-${index}" class="word-count mb-2 ai-target">${section.content.content}</div>
                    <textarea style="display:none" name="sections[${index}][text]" id="input-text-${index}"></textarea>
                     <!-- Image display and alt text editing -->
                    <div class="image-section mb-4">
                        <img src="${section.content.image.url}" alt="${section.content.image.alt}" class="image-preview mb-2 w-full max-w-xs rounded shadow-md">
                        <input type="text" name="sections[${index}][image_alt]" value="${section.content.image.alt}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Image Alt Text">

                     <!-- Hidden fields to track the existing image -->
                        <input type="hidden" name="sections[${index}][existing_image_id]" value="${existingImageId}">
                        <input type="hidden" id="new-image-url-${sectionId}" name="sections[${index}][new_image_url]" value="">
                        <input type="hidden" id="new-image-id-${sectionId}" name="sections[${index}][new_image_id]" value="">

                        <!-- Vue Library Button Component -->
                        <div id="library-button-${index}" class="my-2">
                            <library-button :section-id="'${sectionId}'"></library-button>
                        </div>
                    </div>
                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}">Generate AI Content</button>

                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}" placeholder="Max words" min=0>
                        </div>  
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Submit</button>
                        <div id="spinner-${index}" class="spinner hidden ms-2 inline-flex"></div>

                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Use this content</button>
                    </div>
                </div>
            `;

        case 'Two Third Text on Left and One Thrid Form on Right':
            return `
                <div>
                    <div id="editor-content-${index}" class="word-count ai-target mb-2">${section.content.content}</div>
                    <textarea style="display:none" name="sections[${index}][text]" id="input-text-${index}"></textarea>
                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}">Generate AI Content</button>
                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}" placeholder="Max words" min=0>
                        </div>  
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Submit</button>
                        <div id="spinner-${index}" class="spinner hidden ms-2 inline-flex"></div>

                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Use this content</button>
                    </div>
                    <form> <!-- Add your form inputs here --> </form>
                </div>
            `;

        case 'Two Side By Side Text Blurb Columns':
            const { image: twoSideBySideImage } = extractImageTag(section.content.content);
            const { image: twoSideBySideImage1 } = extractImageTag(section.content.content_1);

            return `
                <div class="flex-container overflow-scroll">
                    <div class="flex flex-wrap -mx-2">
                        <div class="w-1/2 px-2 mb-4 h-full">
                            <div id="editor-content-${index}-left" class="word-count ai-target" data-field="content">${section.content.content}</div>
                            ${twoSideBySideImage ? `<input type="hidden" name="sections[${index}][extracted_image]" value="${btoa(twoSideBySideImage)}">` : ''}

                            <textarea style="display:none" name="sections[${index}][content]" id="input-text-${index}-left"></textarea>
                            <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-left">Generate AI Content</button>
                            <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-left">
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-left" placeholder="Add any additional instructions for the AI"></textarea>
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-left" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-left" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-left" placeholder="Max words" min=0>
                        </div>  
                                <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-left">Submit</button>
                                <div id="spinner-${index}-left" class="spinner hidden ms-2 inline-flex"></div>

                            </div>
                            <div class="generated-content mt-2 hidden" id="generated-content-${index}-left">
                                <h4 class="font-bold">Generated Content</h4>
                                <div id="generated-text-${index}-left" class="p-2 border rounded"></div>
                                <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-left">Use this content</button>
                            </div>
                        </div>
                        <div class="w-1/2 px-2 mb-4 h-full">
                            <div id="editor-content-${index}-right" class="word-count ai-target" data-field="content_1">${section.content.content_1}</div>
                            ${twoSideBySideImage1 ? `<input type="hidden" name="sections[${index}][extracted_image_1]" value="${btoa(twoSideBySideImage1)}">` : ''}

                            <textarea style="display:none" name="sections[${index}][content_1]" id="input-text-${index}-right"></textarea>
                            <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-right">Generate AI Content</button>

                            <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-right">
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-right" placeholder="Add any additional instructions for the AI"></textarea>
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-right" placeholder="Optional keywords (comma-separated)">
                       <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-right" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-right" placeholder="Max words" min=0>
                        </div>  
                                <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-right">Submit</button>
                                <div id="spinner-${index}-right" class="spinner hidden ms-2 inline-flex"></div>

                            </div>
                            <div class="generated-content mt-2 hidden" id="generated-content-${index}-right">
                                <h4 class="font-bold">Generated Content</h4>
                                <div id="generated-text-${index}-right" class="p-2 border rounded"></div>
                                <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-right">Use this content</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

        case 'Three Side By Side Text Blurb Columns':
            const { image: ctaImage } = extractImageTag(section.content.section_content);
            const { image: ctaImage1 } = extractImageTag(section.content.section_content_1);
            const { image: ctaImage2 } = extractImageTag(section.content.section_content_2);

            return `
                <div class="flex-container overflow-y-auto">
                <input type="text" name="sections[${index}][heading]" value="${section.content.section_heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Heading">
                <div class="flex mx-2 h-full">
          
                        <div class="w-full md:w-1/3 px-2 mb-4 h-full">
                            ${ctaImage ? `<input type="hidden" name="sections[${index}][extracted_image]" value="${btoa(ctaImage)}">` : ''}
                            <div id="editor-content-${index}-first" class="word-count ai-target" data-field="content">${section.content.section_content}</div>
                            <textarea style="display:none" name="sections[${index}][section_content]" id="input-text-${index}-first"></textarea>
                            <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-first">Generate AI Content</button>

                            <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-first">
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-first" placeholder="Add any additional instructions for the AI"></textarea>
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-first" placeholder="Optional keywords (comma-separated)">
                                 <div class="flex">
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-first" placeholder="Min words" min=0>
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-first" placeholder="Max words" min=0>
                                </div>  
                                <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-first">Submit</button>
                                <div id="spinner-${index}-first" class="spinner hidden ms-2 inline-flex"></div>

                            </div>
                            <div class="generated-content mt-2 hidden" id="generated-content-${index}-first">
                                <h4 class="font-bold">Generated Content</h4>
                                <div id="generated-text-${index}-first" class="p-2 border rounded"></div>
                                <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-first">Use this content</button>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 px-2 mb-4 h-full">
                            ${ctaImage1 ? `<input type="hidden" name="sections[${index}][extracted_image_1]" value="${btoa(ctaImage1)}">` : ''}
                            <div id="editor-content-${index}-second" class="word-count ai-target" data-field="content_2">${section.content.section_content_1}</div>
                            <textarea style="display:none" name="sections[${index}][section_content_1]" id="input-text-${index}-second"></textarea>
                            <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-second">Generate AI Content</button>

                            <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-second">
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-second" placeholder="Add any additional instructions for the AI"></textarea>
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-second" placeholder="Optional keywords (comma-separated)">

                                <div class="flex">
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-second" placeholder="Min words" min=0>
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-second" placeholder="Max words" min=0>
                                </div>  
                                <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-second">Submit</button>
                                <div id="spinner-${index}-second" class="spinner hidden ms-2 inline-flex"></div>

                            </div>
                            <div class="generated-content mt-2 hidden" id="generated-content-${index}-second">
                                <h4 class="font-bold">Generated Content</h4>
                                <div id="generated-text-${index}-second" class="p-2 border rounded"></div>
                                <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-second">Use this content</button>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 px-2 mb-4 h-full">
                            ${ctaImage2 ? `<input type="hidden" name="sections[${index}][extracted_image_2]" value="${btoa(ctaImage2)}">` : ''}
                            <div id="editor-content-${index}-third" class="word-count ai-target" data-field="content_3">${section.content.section_content_2}</div>
                            <textarea style="display:none" name="sections[${index}][section_content_2]" id="input-text-${index}-third"></textarea>
                            <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-third">Generate AI Content</button>

                            <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-third">
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-third" placeholder="Add any additional instructions for the AI"></textarea>
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-third" placeholder="Optional keywords (comma-separated)">
                                <div class="flex">
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-third" placeholder="Min words" min=0>
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-third" placeholder="Max words" min=0>
                                </div>  
                                <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-third">Submit</button>
                                <div id="spinner-${index}-third" class="spinner hidden ms-2 inline-flex"></div>

                            </div>
                            <div class="generated-content mt-2 hidden" id="generated-content-${index}-third">
                                <h4 class="font-bold">Generated Content</h4>
                                <div id="generated-text-${index}-third" class="p-2 border rounded"></div>
                                <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-third">Use this content</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

        case 'Image Box Four Columns With Background':
            let imageBoxes = '';
            for (let i = 0; i < section.content.image_boxes.length; i++) {
                imageBoxes += `
                    <div class="mb-2 p-2 border rounded">
                        <h4 class="font-bold">Image Box ${i + 1}</h4>
                        <input type="text" name="sections[${index}][image_boxes][${i}][section_image]" value="${section.content.image_boxes[i].section_image}" class="w-full mb-2 p-2 border rounded" placeholder="Image">
                        <input type="text" name="sections[${index}][image_boxes][${i}][section_heading]" value="${section.content.image_boxes[i].section_heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Heading">
                        <div id="editor-image-box-${index}-${i}" class="word-count ai-target mb-2">${section.content.image_boxes[i].section_content}</div>
                        <textarea style="display:none" name="sections[${index}][image_boxes][${i}][section_content]" id="input-image-box-${index}-${i}"></textarea>
                        <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-${i}">Generate AI Content</button>

                        <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-${i}">
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-${i}" placeholder="Add any additional instructions for the AI"></textarea>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-${i}" placeholder="Optional keywords (comma-separated)">
                            <div class="flex">
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-${i}" placeholder="Min words" min=0>
                                    <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-${i}" placeholder="Max words" min=0>
                                </div>  
                            <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-${i}">Submit</button>
                            <div id="image-box-spinner-${index}-${i}" class="spinner hidden ms-2 inline-flex"></div>

                        </div>
                        <div class="generated-content mt-2 hidden" id="generated-content-${index}-${i}">
                            <h4 class="font-bold">Generated Content</h4>
                            <div id="generated-text-${index}-${i}" class="p-2 border rounded"></div>
                            <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-${i}" data-type="image-box">Use this content</button>
                        </div>
                    </div>
                `;
            }
            return `
                <div>
                    <input type="text" name="sections[${index}][section_heading]" value="${section.content.section_heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Section Heading">
                    <input type="text" name="sections[${index}][section_content]" value="${section.content.section_content}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Section Content">

                    ${imageBoxes}
                </div>
            `;
        case 'Image Accordion without background image':
        case 'Icon Box Section Without Background':
        case 'Icon Box Section With Background':
        case 'Icon Box Four Columns With Background':
            const { image: iconBoxImage } = extractImageTag(section.content.section_content);
            let nestedBoxes = '';
            for (let i = 0; i < section.content.nested_boxes.length; i++) {
                const box = section.content.nested_boxes[i];
                nestedBoxes += `
                <div class="mb-2 p-2 border rounded editor-icon-box">
                    <h4 class="font-bold">Box ${i + 1}</h4>
                    <input type="text" 
                        name="sections[${index}][icon_boxes][${i}][section_heading]" 
                        value="${box.section_heading || ''}" 
                        class="word-input w-full mb-2 p-2 border rounded" 
                        placeholder="Heading">
                        
                    <div id="editor-icon-box-${index}-${i}" class="word-count ai-target mb-2">${box.section_content || ''}</div>
                     ${iconBoxImage ? `
                        <input type="hidden" name="sections[${index}][extracted_image]" value="${btoa(iconBoxImage)}">
                        ` : ''
                    }
                    <textarea style="display:none" 
                            name="sections[${index}][icon_boxes][${i}][section_content]" 
                            id="input-icon-box-${index}-${i}"></textarea>                    
                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}-${i}">Generate AI Content</button>
        
                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}-${i}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}-${i}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}-${i}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}-${i}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}-${i}" placeholder="Max words" min=0>
                        </div>  
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-${i}">Submit</button>
                        <div id="icon-box-spinner-${index}-${i}" class="spinner hidden ms-2 inline-flex"></div>
                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}-${i}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}-${i}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}-${i}" data-type="icon-box">Use this content</button>
                    </div>

                      
                    <!-- Image display and alt text editing -->
                    <div class="image-section mb-4">
                    ${box.image ? `
                            <img src="${box.image.url || ''}" 
                                alt="${box.image.alt || ''}" 
                                class="image-preview mb-2 w-16 h-16 object-cover rounded shadow-sm">
                            <input type="text" 
                                name="sections[${index}][nested_boxes][${i}][image_alt]" 
                                value="${box.image.alt || ''}" 
                                class="word-input w-full mb-2 p-2 border rounded" 
                                placeholder="Image Alt Text">
                            <input type="hidden" 
                                name="sections[${index}][nested_boxes][${i}][existing_image_id]" 
                                value="${box.image.id || ''}">
                        ` : ''}
                        <input type="hidden" 
                            id="new-image-url-${index}-${i}" 
                            name="sections[${index}][nested_boxes][${i}][new_image_url]" 
                            value="">
                        <input type="hidden" 
                            id="new-image-id-${index}-${i}" 
                            name="sections[${index}][nested_boxes][${i}][new_image_id]" 
                            value="">
                        
                        <!-- Vue Library Button Component -->
                        <div id="library-button-${index}-${i}" class="my-2">
                            <library-button :section-id="'${index}-${i}'"></library-button>
                        </div>
                    </div>
                </div>
            `;
            }
            return `
                <div>
                    <input type="text" name="sections[${index}][section_heading]" value="${section.content.section_heading}" class="word-input w-full mb-2 p-2 border rounded" placeholder="Section Heading">
                    
                    <div id="editor-content-${index}" class="word-count mb-2 ai-target">${section.content.section_content}</div>
                    <textarea style="display:none" name="sections[${index}][text]" id="input-text-${index}"></textarea>
                    <button type="button" class="generate-ai-content ms-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-index="${index}">Generate AI Content</button>

                    <div class="ai-input-container mt-2 hidden" id="ai-input-container-${index}">
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" id="additional-prompt-${index}" placeholder="Add any additional instructions for the AI"></textarea>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="keywords-${index}" placeholder="Optional keywords (comma-separated)">
                        <div class="flex">
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="min-${index}" placeholder="Min words" min=0>
                            <input type="number" class="inline-flex mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="max-${index}" placeholder="Max words" min=0>
                        </div>  
                        <button type="button" class="submit-ai-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Submit</button>
                        <div id="spinner-${index}" class="spinner hidden ms-2 inline-flex"></div>

                    </div>
                    <div class="generated-content mt-2 hidden" id="generated-content-${index}">
                        <h4 class="font-bold">Generated Content</h4>
                        <div id="generated-text-${index}" class="p-2 border rounded"></div>
                        <button type="button" class="use-generated-content mt-2 bg-green-500 text-white font-bold py-2 px-4 rounded" data-index="${index}">Use this content</button>
                    </div>
                
                    ${nestedBoxes}
                </div>
            `;

        default:
            return '';
    }
}

function extractExistingImages(content) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(content, 'text/html');
    const images = doc.querySelectorAll('img');
    return Array.from(images).map(img => img.src);
}

function extractImageTag(content) {
    const div = document.createElement('div');
    div.innerHTML = content;

    const imgTag = div.querySelector('img');
    let imgHtml = '';
    if (imgTag) {
        imgHtml = imgTag.outerHTML;
        imgTag.remove();
    }

    return {
        image: imgHtml,
    }
}

function extractImageTags(content) {
    const div = document.createElement('div');
    div.innerHTML = content;

    let imageTags = [];
    let textContent = div.innerHTML;

    div.querySelectorAll('img').forEach((img, index) => {
        const imgPlaceholder = `{{image_${index}}}`;
        imageTags.push({ placeholder: imgPlaceholder, html: img.outerHTML });
        textContent = textContent.replace(img.outerHTML, imgPlaceholder);
    });

    return {
        textContent,
        imageTags
    };
}



function updatePostId(postId) {
    document.getElementById('postId').value = postId;
}

async function loadHomepageSections() {
    const siteSelect = document.getElementById('site');
    const siteId = siteSelect.value;

    const selectedOption = siteSelect.options[siteSelect.selectedIndex];
    const siteUrl = selectedOption ? selectedOption.dataset.siteUrl : '';

    if (siteId) {
        try {
            document.getElementById('homepage-sections').innerHTML = '';
            const url = `${window.generateContent}/${siteId}`;

            const response = await fetch(url);
            const content = await response.json();
            content.siteUrl = siteUrl;

            // console.log(content);

            updatePostId(content.postId);
            displayHomepageSections(content.sections);
            populateSeoInfo(content);
            renderFormFields(content);
            initializeCollapsibleFields();
            initializeGoogleMapListeners();
            populateSiteImages(content);

            const nicheHelm = document.getElementById('niche_helm');
            const niche = document.getElementById('niche');
            if (nicheHelm && niche && nicheHelm.value) {
                niche.value = nicheHelm.value;
            }
            document.getElementById('wordpress-content').style.display = 'block';
            updateTotalWordCount();

        } catch (error) {
            document.getElementById('wordpress-content').style.display = 'none';
            console.error('Error loading homepage sections:', error);
        }
    } else {
        document.getElementById('wordpress-content').style.display = 'none';
    }
}

function displayHomepageSections(sections) {
    const container = document.getElementById('homepage-sections');
    container.innerHTML = '';

    sections.forEach((section, index) => {
        const sectionDiv = document.createElement('div');
        let sectionType = section.type;

        // Correct known spelling errors
        if (sectionType.includes("Thrid")) {
            sectionType = sectionType.replace("Thrid", "Third");
        }

        sectionDiv.className = 'mb-4 p-4 border rounded';
        sectionDiv.innerHTML = `
            <h3 class="text-lg font-bold mb-2">Section ${index + 1}: ${sectionType}</h3>
            ${getSectionInputs(section, index)}
        `;
        container.appendChild(sectionDiv);

        const libraryButtonElementId = `library-button-${index}`;
        const libraryButtonElement = document.getElementById(libraryButtonElementId);
        if (libraryButtonElement) {
            const vueApp = createApp({
                components: {
                    LibraryButton
                }
            });

            // Each component instance is mounted in isolation
            vueApp.mount(`#${libraryButtonElementId}`);
        }

        // Handle nested boxes if they exist (like Image Accordion, Icon Box, etc.)
        if (section.content && section.content.nested_boxes && section.content.nested_boxes.length > 0) {
            section.content.nested_boxes.forEach((box, i) => {
                const nestedLibraryButtonElementId = `library-button-${index}-${i}`;
                const nestedLibraryButtonElement = document.getElementById(nestedLibraryButtonElementId);
                if (nestedLibraryButtonElement) {
                    const nestedVueApp = createApp({
                        components: {
                            LibraryButton
                        }
                    });

                    // Mount Vue component for nested LibraryButton
                    nestedVueApp.mount(`#${nestedLibraryButtonElementId}`);
                }
            });
        }
    });

    sections.forEach((section, index) => {
        initializeQuillEditor(`#editor-content-${index}`);
        initializeQuillEditor(`#editor-content-${index}-left`);
        initializeQuillEditor(`#editor-content-${index}-right`);
        initializeQuillEditor(`#editor-content-${index}-first`);
        initializeQuillEditor(`#editor-content-${index}-second`);
        initializeQuillEditor(`#editor-content-${index}-third`);

        if (section.content.image_boxes && section.content.image_boxes.length) {
            for (let i = 0; i < section.content.image_boxes.length; i++) {
                initializeQuillEditor(`#editor-image-box-${index}-${i}`);
            }
        }

        if (section.content.icon_boxes && section.content.icon_boxes.length) {
            for (let i = 0; i < section.content.icon_boxes.length; i++) {
                initializeQuillEditor(`#editor-icon-box-${index}-${i}`);
            }
        }

        // Handle other nested boxes if necessary
        if (section.content.nested_boxes && section.content.nested_boxes.length) {
            for (let i = 0; i < section.content.nested_boxes.length; i++) {
                initializeQuillEditor(`#editor-icon-box-${index}-${i}`);
            }
        }
    });

    document.querySelectorAll('.generate-ai-content').forEach(button => {
        button.addEventListener('click', function () {
            const sectionIndex = this.getAttribute('data-index');
            const aiInputContainer = document.getElementById(`ai-input-container-${sectionIndex}`);
            const generatedContentContainer = document.getElementById(`generated-content-${sectionIndex}`);

            aiInputContainer.classList.toggle('hidden');
            generatedContentContainer.classList.add('hidden');
        });
    });

    document.querySelectorAll('.submit-ai-content').forEach(button => {
        button.addEventListener('click', function () {
            const sectionIndex = this.getAttribute('data-index');
            const sectionType = this.getAttribute('data-type');  // New attribute
            let spinnerId, contentId, additionalPromptId, keywordsId, minId, maxId;

            if (sectionType === 'icon-box') {
                spinnerId = `icon-box-spinner-${sectionIndex}`;
                contentId = `editor-icon-box-${sectionIndex}`;
                additionalPromptId = `additional-prompt-${sectionIndex}`;
                keywordsId = `keywords-${sectionIndex}`;
                minId = `min-${sectionIndex}`;
                maxId = `max-${sectionIndex}`;
            } else if (sectionType === 'image-box') {
                spinnerId = `image-box-spinner-${sectionIndex}`;
                contentId = `editor-image-box-${sectionIndex}`;
                additionalPromptId = `additional-prompt-${sectionIndex}`;
                keywordsId = `keywords-${sectionIndex}`;
                minId = `min-${sectionIndex}`;
                maxId = `max-${sectionIndex}`;
            } else {
                spinnerId = `spinner-${sectionIndex}`;
                contentId = `editor-content-${sectionIndex}`;
                additionalPromptId = `additional-prompt-${sectionIndex}`;
                keywordsId = `keywords-${sectionIndex}`;
                minId = `min-${sectionIndex}`;
                maxId = `max-${sectionIndex}`;
            }

            const spinner = document.getElementById(spinnerId);
            if (spinner) {
                spinner.classList.remove('hidden');
            }

            const contentElement = document.getElementById(contentId);
            let contentSnippet = '';
            if (contentElement) {
                contentSnippet = Quill.find(contentElement).root.innerText;
                //console.log('Content Snippet:', contentSnippet);
            }

            const additionalPrompt = document.getElementById(additionalPromptId).value;
            const keywords = document.getElementById(keywordsId).value;
            const minWordCount = document.getElementById(minId).value;
            const maxWordCount = document.getElementById(maxId).value;

            generateAIContent(sectionIndex, additionalPrompt, keywords, contentSnippet, minWordCount, maxWordCount);
        });
    });

    async function generateAIContent(sectionIndex, additionalPrompt = '', keywords = '', contentSnippet = '', minWords = null, maxWords = null) {
        try {

            const niche = document.getElementById('niche_helm').value;
            const tone = document.getElementById('tone').value;

            let spinnerId, generatedContentContainerId, generatedTextId;

            if (sectionIndex.includes('-left') || sectionIndex.includes('-right') || sectionIndex.includes('-first') || sectionIndex.includes('-second') || sectionIndex.includes('-third')) {
                spinnerId = `spinner-${sectionIndex}`;
                generatedContentContainerId = `generated-content-${sectionIndex}`;
                generatedTextId = `generated-text-${sectionIndex}`;
            } else if (sectionIndex.includes('icon-box') || sectionIndex.includes('image-box')) {
                const parts = sectionIndex.split('-');
                const [mainIndex, boxIndex] = parts.slice(1, 3); // Get the main and sub-index
                spinnerId = `${parts[0]}-spinner-${mainIndex}-${boxIndex}`;
                generatedContentContainerId = `${parts[0]}-generated-content-${mainIndex}-${boxIndex}`;
                generatedTextId = `${parts[0]}-generated-text-${mainIndex}-${boxIndex}`;
            } else {
                spinnerId = `spinner-${sectionIndex}`;
                generatedContentContainerId = `generated-content-${sectionIndex}`;
                generatedTextId = `generated-text-${sectionIndex}`;
            }

            const spinner = document.getElementById(spinnerId);

            const response = await fetch(`${window.generateContent}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    additionalPrompt: additionalPrompt,
                    keywords: keywords,
                    content: contentSnippet,
                    niche: niche,
                    tone: tone,
                    minWords: minWords,
                    maxWords, maxWords
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            const generatedContentContainer = document.getElementById(generatedContentContainerId);
            const generatedText = document.getElementById(generatedTextId);

            if (generatedText) {
                // console.log(data.original.content);
                generatedText.innerHTML = data.original.content;
                generatedContentContainer.classList.remove('hidden');
            }

            if (spinner) {
                spinner.classList.add('hidden');
            }

        } catch (error) {
            console.error('Error generating AI content:', error);
            alert('Error generating AI content: ' + error.message);
        } finally {
            if (spinner) {
                spinner.classList.add('hidden');
            }
        }
    }

    document.querySelectorAll('.use-generated-content').forEach(button => {
        button.addEventListener('click', function () {
            const sectionIndex = this.getAttribute('data-index');
            const generatedText = document.getElementById(`generated-text-${sectionIndex}`).innerHTML;
            const editorSelector = `#editor-${sectionIndex}, #editor-content-${sectionIndex}, #editor-icon-box-${sectionIndex}`;

            const editor = Quill.find(document.querySelector(editorSelector));
            if (editor) {
                editor.clipboard.dangerouslyPasteHTML(generatedText, 'api');
            }
            document.getElementById(`generated-content-${sectionIndex}`).classList.add('hidden');
        });
    });
}

function updateTextarea() {
    document.querySelectorAll('[id^="editor-content-"], [id^="editor-icon-box-"], [id^="editor-image-box-"]').forEach((editor) => {
        const quill = Quill.find(editor);
        const content = quill ? quill.root.innerHTML : null;

        let textareaId = '';

        // Determine the correct textarea ID based on the editor ID pattern
        if (editor.id.startsWith('editor-content-')) {
            textareaId = editor.id.replace('editor-content', 'input-text');
        } else if (editor.id.startsWith('editor-icon-box-')) {
            const matches = editor.id.match(/^editor-icon-box-(\d+)-(\d+)$/);
            if (matches) {
                const [_, index, subIndex] = matches;
                textareaId = `input-icon-box-${index}-${subIndex}`;
            }
        } else if (editor.id.startsWith('editor-image-box-')) {
            const matches = editor.id.match(/^editor-image-box-(\d+)-(\d+)$/);
            if (matches) {
                const [_, index, subIndex] = matches;
                textareaId = `input-image-box-${index}-${subIndex}`;
            }
        }
        const textarea = document.getElementById(textareaId);

        if (textarea) {
            textarea.value = content;
        } else {
            console.error(`Textarea not found for ID: ${textareaId}`);
        }
    });
}

function validatePhoneNumber(phoneNumberField) {
    const phoneNumber = phoneNumberField.value;
    const phonePattern = /^\d{3}-\d{3}-\d{4}$/;

    if (phoneNumber && !phonePattern.test(phoneNumber)) {
        alert('Please enter the phone number in the format: 000-000-0000');
        phoneNumberField.focus();
        return;
    }
}

function normalizeImage(imageTag) {
    if (!imageTag) return '';
    const srcMatch = imageTag.match(/src="([^"]+)"/);
    return srcMatch ? srcMatch[1].trim() : ''; // Return only the image URL (src)
}



function processSection(sectionContainer, index) {
    const sectionHeadingElement = sectionContainer.querySelector('h3');
    if (!sectionHeadingElement) {
        console.error(`No header element found for section ${index + 1}`);
        return;
    }

    // Extract the section type from the h3 text and trim any extra spaces
    const sectionType = sectionHeadingElement.innerText.split(': ')[1]?.trim() || 'Unknown Section';
    console.log('Determined section type:', sectionType);

    const section = {
        type: sectionType,
        content: {}
    };

    switch (sectionType) {
        case 'Top Banner With Content & Button':
            section.content.heading = sectionContainer.querySelector(`input[name="sections[${index}][heading]"]`)?.value || '';
            section.content.subheading = sectionContainer.querySelector(`input[name="sections[${index}][subheading]"]`)?.value || '';

            const bannerImageId = document.getElementById(`new-image-id-section-${index}`).value;
            if (bannerImageId) {
                section.content.image_id = bannerImageId;
            }
            break;

        case 'Two Blurb Section With Address & Form':
        case 'CTA With Background Image & Button':
            section.content.section_heading = sectionContainer.querySelector(`input[name="sections[${index}][heading]"]`)?.value || '';
            section.content.section_content = sectionContainer.querySelector(`textarea[name="sections[${index}][text]"]`)?.value || '';
            break;

        case 'Content Block':
            case 'Content Block Full Width':
                // Get the original content
                let contentBlockFullWidth = sectionContainer.querySelector(`textarea[name="sections[${index}][text]"]`)?.value || '';
                
                // Extract and remove any existing images from the original content
                let cleanedContent = contentBlockFullWidth.replace(/<img[^>]*>/g, '');
                
                let appendedImages = ''; // This will store all images to be appended
            
                // Collect and decode the extracted images
                let imgIndex = 0;
                while (sectionContainer.querySelector(`input[name="sections[${index}][extracted_image_${imgIndex}]"]`)) {
                    const extractedImage = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image_${imgIndex}]"]`)?.value || '';
                    if (extractedImage) {
                        const decodedImage = atob(extractedImage); // Decode the base64 encoded image tag
                        appendedImages += decodedImage; // Append decoded image to the images string
                    }
                    imgIndex++;
                }
            
                // Combine the cleaned content and the appended images
                let updatedContent = cleanedContent + appendedImages;
            
                // Update the section content with the cleaned content and new images
                section.content.content = updatedContent;
            
                break;
                
        case 'Right Image Left Text With Button & Heading':
        case 'Left Image Right Text With Button & Heading':
        case 'Text Block With Background Image & Heading':
        case 'Left Image & Right Text With Accordion':
            section.content.heading = sectionContainer.querySelector(`input[name="sections[${index}][heading]"]`)?.value || '';
            section.content.content = sectionContainer.querySelector(`textarea[name="sections[${index}][text]"]`)?.value || '';

            const sectionImageId = document.getElementById(`new-image-id-section-${index}`).value;
            if (sectionImageId) {
                section.content.image_id = sectionImageId;
            }

            break;

        case 'Two Third Text on Left and One Third Form on Right':
            section.content.content = sectionContainer.querySelector(`textarea[name="sections[${index}][text]"]`)?.value || '';
            break;

            case 'Two Side By Side Text Blurb Columns':
                let sectionContentLeft = sectionContainer.querySelector(`textarea[name="sections[${index}][content]"]`)?.value || '';
                let sectionContentRight = sectionContainer.querySelector(`textarea[name="sections[${index}][content_1]"]`)?.value || '';
            
                // Remove existing images from both content areas
                let cleanedContentLeft = sectionContentLeft.replace(/<img[^>]*>/g, '');
                let cleanedContentRight = sectionContentRight.replace(/<img[^>]*>/g, '');
            
                let appendedImagesLeft = '';
                let appendedImagesRight = '';
            
                // Collect and decode the left extracted image
                const extractedImageLeft = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image]"]`)?.value || '';
                if (extractedImageLeft) {
                    const decodedImageLeft = atob(extractedImageLeft);
                    appendedImagesLeft += decodedImageLeft;
                }
            
                // Collect and decode the right extracted image
                const extractedImageRight = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image_1]"]`)?.value || '';
                if (extractedImageRight) {
                    const decodedImageRight = atob(extractedImageRight);
                    appendedImagesRight += decodedImageRight;
                }
            
                // Combine cleaned content and images
                section.content.content_left = appendedImagesLeft + cleanedContentLeft;
                section.content.content_right = appendedImagesRight + cleanedContentRight;
            
                break;
            
            case 'Three Side By Side Text Blurb Columns':
                section.content.section_heading = sectionContainer.querySelector(`input[name="sections[${index}][heading]"]`)?.value || '';

                let sectionContent1 = sectionContainer.querySelector(`textarea[name="sections[${index}][section_content]"]`)?.value || '';
                let sectionContent2 = sectionContainer.querySelector(`textarea[name="sections[${index}][section_content_1]"]`)?.value || '';
                let sectionContent3 = sectionContainer.querySelector(`textarea[name="sections[${index}][section_content_2]"]`)?.value || '';
            
                // Remove existing images from all content areas
                let cleanedContent1 = sectionContent1.replace(/<img[^>]*>/g, '');
                let cleanedContent2 = sectionContent2.replace(/<img[^>]*>/g, '');
                let cleanedContent3 = sectionContent3.replace(/<img[^>]*>/g, '');
            
                let appendedImages1 = '';
                let appendedImages2 = '';
                let appendedImages3 = '';
            
                // Collect and decode the images
                const extractedImage1 = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image]"]`)?.value || '';
                if (extractedImage1) {
                    const decodedImage1 = atob(extractedImage1);
                    appendedImages1 += decodedImage1;
                }
            
                const extractedImage2 = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image_1]"]`)?.value || '';
                if (extractedImage2) {
                    const decodedImage2 = atob(extractedImage2);
                    appendedImages2 += decodedImage2;
                }
            
                const extractedImage3 = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image_2]"]`)?.value || '';
                if (extractedImage3) {
                    const decodedImage3 = atob(extractedImage3);
                    appendedImages3 += decodedImage3;
                }
            
                // Combine cleaned content and images
                section.content.section_content = appendedImages1 + cleanedContent1;
                section.content.section_content_1 = appendedImages2 + cleanedContent2;
                section.content.section_content_2 = appendedImages3 + cleanedContent3;
            
                break;
        case 'Image Box Four Columns With Background':
            section.content.section_heading = sectionContainer.querySelector(`input[name="sections[${index}][section_heading]"]`)?.value || '';
            section.content.image_boxes = [];
            for (let i = 0; i < sectionContainer.querySelectorAll('.editor-image-box').length; i++) {
                section.content.image_boxes.push({
                    section_image: sectionContainer.querySelector(`input[name="sections[${index}][image_boxes][${i}][section_image]"]`)?.value || '',
                    section_heading: sectionContainer.querySelector(`input[name="sections[${index}][image_boxes][${i}][section_heading]"]`)?.value || '',
                    section_content: sectionContainer.querySelector(`textarea[name="sections[${index}][image_boxes][${i}][section_content]"]`)?.value || ''
                });
            }
            break;
        case 'Image Accordion without background image':
        case 'Icon Box Section With Background':
        case 'Icon Box Section Without Background':
        case 'Icon Box Four Columns With Background':
            let iconSectionContent = sectionContainer.querySelector(`textarea[name="sections[${index}][section_content]"]`)?.value || '';

            section.content.section_heading = sectionContainer.querySelector(`input[name="sections[${index}][section_heading]"]`)?.value || '';
            section.content.section_content = sectionContainer.querySelector(`textarea[name="sections[${index}][section_text]"]`)?.value || '';
           
             // Remove existing images
            let cleanedIconSectionContent = iconSectionContent.replace(/<img[^>]*>/g, '');
            let appendedIconImages = '';

            const extractedIconImage = sectionContainer.querySelector(`input[name="sections[${index}][extracted_image]"]`)?.value || '';
            if (extractedIconImage) {
                const decodedIconImage = atob(extractedIconImage);
                appendedIconImages += decodedIconImage;
            }
           
            section.content.section_content = iconSectionContent;

            section.content.icon_boxes = [];
            const iconBoxes = sectionContainer.querySelectorAll('.editor-icon-box');

            for (let i = 0; i < iconBoxes.length; i++) {
                const imageId = document.getElementById(`new-image-id-${index}-${i}`)?.value || ''; // Ensuring correct image ID is fetched

                section.content.icon_boxes.push({
                    section_heading: sectionContainer.querySelector(`input[name="sections[${index}][icon_boxes][${i}][section_heading]"]`)?.value || '',
                    section_content: sectionContainer.querySelector(`textarea[name="sections[${index}][icon_boxes][${i}][section_content]"]`)?.value || '',
                    image_id: imageId
                });
            }

            break;

        default:
            console.error('Unknown section type:', sectionType);
            return;
    }


    return section;
}

document.getElementById("niche_helm").addEventListener('change', loadSites);

document.getElementById("site").addEventListener('change', loadHomepageSections);

document.getElementById('homepage-post-form').onsubmit = async function (event) {

    event.preventDefault();

    const phoneNumberField = document.getElementById('phone_number');

    if (phoneNumberField) {
        validatePhoneNumber(phoneNumberField);
    }

    $('#loading').show();

    async function uploadImages(selectedImages) {
        const uploadedImageCache = {}; // Cache to store already uploaded images
        const uploadedImageIds = {};

        for (const sectionId in selectedImages) {
            const selectedImageData = selectedImages[sectionId];

            console.log(`Uploading image for section: ${sectionId}`, selectedImageData);

            if (Array.isArray(selectedImageData)) {
                // Handle nested images
                for (let i = 0; i < selectedImageData.length; i++) {
                    const nestedImageData = selectedImageData[i];

                    // Check if this image has already been uploaded
                    if (uploadedImageCache[nestedImageData.url]) {
                        console.log(`Image already uploaded for ${sectionId}-${i}, using cached ID.`);
                        uploadedImageIds[`${sectionId}-${i}`] = uploadedImageCache[nestedImageData.url];
                        continue;
                    }

                    let formData = new FormData();
                    formData.append('file', nestedImageData.url);
                    formData.append('title', nestedImageData.title);
                    formData.append('alt', nestedImageData.altText);
                    formData.append('siteId', document.getElementById('site').value);

                    console.log(`Starting upload for nested image:`, nestedImageData);

                    try {
                        const response = await fetch(`${window.uploadFileToWordpress}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.csrfToken
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            console.log(`Upload successful for ${sectionId}-${i}:`, result);
                            const uploadedId = result.data.id;
                            uploadedImageIds[`${sectionId}-${i}`] = uploadedId;

                            uploadedImageCache[nestedImageData.url] = uploadedId;
                        } else {
                            console.error(`Upload failed for ${sectionId}-${i}:`, result.message);
                            return null;
                        }
                    } catch (error) {
                        console.error(`Error uploading image for ${sectionId}-${i}:`, error);
                        return null;
                    }
                }
            } else {
                // Handle top-level images
                // console.log(`Processing top-level image for section: ${sectionId}`, selectedImageData);

                if (uploadedImageCache[selectedImageData.url]) {
                    console.log(`Image already uploaded for ${sectionId}, using cached ID.`);
                    uploadedImageIds[sectionId] = uploadedImageCache[selectedImageData.url];
                    continue;
                }

                let formData = new FormData();
                formData.append('file', selectedImageData.url);
                formData.append('title', selectedImageData.title);
                formData.append('alt', selectedImageData.altText);
                formData.append('siteId', document.getElementById('site').value);

                console.log(`Starting upload for image:`, selectedImageData);

                try {
                    const response = await fetch(`${window.uploadFileToWordpress}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        console.log(`Upload successful for ${sectionId}:`, result);
                        const uploadedId = result.data.id;
                        uploadedImageIds[sectionId] = uploadedId;

                        // Cache the uploaded image URL with its ID
                        uploadedImageCache[selectedImageData.url] = uploadedId;
                    } else {
                        console.error(`Upload failed for ${sectionId}:`, result.message);
                        return null;
                    }
                } catch (error) {
                    console.error(`Error uploading image for ${sectionId}:`, error);
                    return null;
                }
            }
        }

        console.log('Final uploadedImageIds:', uploadedImageIds);
        return uploadedImageIds;
    }


    async function handleLogoAndIconUploads() {
        const uploadedImageCache = {}; // Cache to store already uploaded images
        const uploadedImageIds = {};

        // Process the company logo if selected
        if (selectedImages['company-logo']) {
            const { url, altText, title } = selectedImages['company-logo'];

            // console.log(`Processing company logo`, selectedImages['company-logo']);

            // Check if this image has already been uploaded
            if (uploadedImageCache[url]) {
                // console.log(`Company logo already uploaded, using cached ID.`);
                uploadedImageIds['company-logo'] = uploadedImageCache[url];
            } else {
                // Prepare form data for uploading
                let formData = new FormData();
                formData.append('file', url);
                formData.append('title', title); // Use the title from the selected image
                formData.append('alt', altText); // Use the altText from the selected image
                formData.append('siteId', document.getElementById('site').value);

                // Start the upload
                try {
                    const response = await fetch(`${window.uploadFileToWordpress}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        // console.log(`Upload successful for company logo:`, result);
                        const uploadedId = result.data.id;
                        uploadedImageIds['company-logo'] = uploadedId;
                        uploadedImageCache[url] = uploadedId; // Cache the uploaded image ID
                    } else {
                        console.error(`Upload failed for company logo:`, result.message);
                        return null;
                    }
                } catch (error) {
                    console.error(`Error uploading company logo:`, error);
                    return null;
                }
            }
        }

        if (selectedImages['favicon-logo']) {
            const { url, altText, title } = selectedImages['favicon-logo'];

            // console.log(`Processing favicon logo`, selectedImages['favicon-logo']);

            // Check if this image has already been uploaded
            if (uploadedImageCache[url]) {
                // console.log(`Favicon logo already uploaded, using cached ID.`);
                uploadedImageIds['favicon-logo'] = uploadedImageCache[url];
            } else {
                // Prepare form data for uploading
                let formData = new FormData();
                formData.append('file', url);
                formData.append('title', title); // Use the title from the selected image
                formData.append('alt', altText); // Use the altText from the selected image
                formData.append('siteId', document.getElementById('site').value);

                // Start the upload
                try {
                    const response = await fetch(`${window.uploadFileToWordpress}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        // console.log(`Upload successful for favicon logo:`, result);
                        const uploadedId = result.data.id;
                        uploadedImageIds['favicon-logo'] = uploadedId;
                        uploadedImageCache[url] = uploadedId; // Cache the uploaded image ID
                    } else {
                        console.error(`Upload failed for favicon logo:`, result.message);
                        return null;
                    }
                } catch (error) {
                    console.error(`Error uploading favicon logo:`, error);
                    return null;
                }
            }
        }

        if (selectedImages['footer_logo']) {
            const { url, altText, title } = selectedImages['footer_logo'];

            // console.log(`Processing footer logo`, selectedImages['footer_logo']);

            // Check if this image has already been uploaded
            if (uploadedImageCache[url]) {
                // console.log(`footer logo already uploaded, using cached ID.`);
                uploadedImageIds['footer_logo'] = uploadedImageCache[url];
            } else {
                // Prepare form data for uploading
                let formData = new FormData();
                formData.append('file', url);
                formData.append('title', title); // Use the title from the selected image
                formData.append('alt', altText); // Use the altText from the selected image
                formData.append('siteId', document.getElementById('site').value);

                // Start the upload
                try {
                    const response = await fetch(`${window.uploadFileToWordpress}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        // console.log(`Upload successful for footer logo:`, result);
                        const uploadedId = '/' + result.data.media_details.file;
                        uploadedImageIds['footer_logo'] = uploadedId;
                        uploadedImageCache[url] = uploadedId; // Cache the uploaded image ID
                    } else {
                        console.error(`Upload failed for footer logo:`, result.message);
                        return null;
                    }
                } catch (error) {
                    console.error(`Error uploading footer logo:`, error);
                    return null;
                }
            }
        }

        console.log('Final uploadedImageIds for logos:', uploadedImageIds);
        return uploadedImageIds;
    }

    const logoUploadedImageIds = await handleLogoAndIconUploads();
    // console.log(logoUploadedImageIds);
    if (!logoUploadedImageIds) {
        console.error('Image uploads failed.');
        return false;
    }

    // Update form fields for logos and icons with uploaded IDs
    if (logoUploadedImageIds['company-logo']) {
        document.getElementById('new-image-id-company-logo').value = logoUploadedImageIds['company-logo'];
    }

    if (logoUploadedImageIds['favicon-logo']) {
        document.getElementById('new-image-id-favicon-logo').value = logoUploadedImageIds['favicon-logo'];
    }

    if (logoUploadedImageIds['footer_logo']) {
        document.getElementById('theme_options[footer_logo]').value = logoUploadedImageIds['footer_logo'];
    }

    if (selectedImages.length != 0) {
        uploadedImageIds = await uploadImages(selectedImages);
        console.log(uploadedImageIds);

        for (const sectionId in uploadedImageIds) {
            const imageId = uploadedImageIds[sectionId];
            if (imageId) {
                const imageIdField = document.getElementById(`new-image-id-${sectionId}`);
                if (imageIdField) {
                    imageIdField.value = imageId;
                } else {
                    console.error(`Field for sectionId not found: new-image-id-${sectionId}`);
                }
            }
        }
    }

    const sections = [];

    updateTextarea();

    document.querySelectorAll('div.mb-4.p-4.border.rounded').forEach((sectionContainer, index) => {

        const section = processSection(sectionContainer, index);
        sections.push(section);
    });

    // Set the value of the hidden input
    document.getElementById('sections').value = JSON.stringify(sections);

    const formData = new FormData();

    // Add site and postId to FormData
    formData.append('site', document.getElementById('site').value);
    formData.append('postId', document.getElementById('postId').value);
    formData.append('rankMathKeywords', document.getElementById('rank_math_focus_keyword').value);
    formData.append('rankMathDescription', document.getElementById('rank_math_description').value);
    formData.append('sections', JSON.stringify(sections));
    formData.append('company_logo_image_id', document.getElementById('new-image-id-company-logo').value);
    formData.append('favicon_logo_image_id', document.getElementById('new-image-id-favicon-logo').value);

    // Add theme options to FormData
    const themeOptionsFields = document.querySelectorAll('[name^="theme_options["]');
    const themeOptions = {};

    themeOptionsFields.forEach(field => {
        const key = field.name.match(/\[(.*?)\]/)[1];
        themeOptions[key] = field.value;
    });

    // Special handling for the 'data' field
    if (themeOptions.data) {
        try {
            themeOptions.data = JSON.parse(themeOptions.data);
        } catch (e) {
            console.warn('Failed to parse data field as JSON. Keeping as string.');
        }
    }

    formData.append('theme_options', JSON.stringify(themeOptions));

    // Submit the form using fetch
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                showAlert('post-success-alert', data.message);
            } else if (data.error) {
                showAlert('post-error-alert', data.error);
            }
            scrollToTop();
            $('#loading').hide();
        })
        .catch((error) => {
            console.error('Error:', error);
            showAlert('post-error-alert', 'An unexpected error occurred. Please try again.');
            scrollToTop();
            $('#loading').hide();
        })
    return false;
};