<x-app-layout>


    <div class="my-5 max-w-lg mx-auto">
        <h1 class="text-3xl font-extrabold dark:text-white">
            Create Google Points of Interests
        </h1>
    </div>

        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 my-8">
        <!-- Server selection dropdown -->
        <div class="mb-4">
            <label for="server_option" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Server</label>
            <select id="serverSelect" name="server" class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
            <option value="">Select server</option>
            @foreach ($servers as $server)
                <option value="{{ $server }}">{{ $server }}</option>
            @endforeach
            </select>
            <p class="text-red-600 text-xs italic">Select the server where the site is located.
            </p>
        </div>

        <!-- Dynamic dropdown for site list -->
        <div>
            <label for="site_option" class="block text-gray-700 font-bold mb-2 dark:text-gray-300">Site</label>
             <select name="siteListDropdown" id="siteListDropdown"
             class="shadow appearance-none border rounded text-gray-700 focus:outline-none focus:shadow-outline mb-2">
            <option value="">Select site</option>
        </select>
        <p class="text-red-600 text-xs italic">Select site you want to modify.</p>

        </div>

        <div id="siteDetails" class="mb-4"></div>

        
    <form id="google-poi-form" class="" method="POST" action="{{ route('creation.poi.store') }}">
    @csrf
    <input type="hidden" name="siteId" id="siteIdInput" value="">

    <div class="mb-4">
            <label for="site_url" class="block font-bold text-gray-700 dark:text-gray-300">Site URL</label>
            <input type="text" id="site_url" 
            name="site_url" 
            value="" 
            placeholder="url"
            class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
        </div>

          <div class="mb-4">
            <label for="google_poi_heading" class="block font-bold text-gray-700 dark:text-gray-300">Heading</label>
            <input type="text" id="google_poi_heading" 
            name="google_poi_heading" 
            value="" 
            placeholder="Add a heading"
            class="shadow appearance-none border w-full rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

<!-- Map Repeater Fields -->
        <div class="google_poi_fields mb-4">
            <label for="google_poi_field" class="block text-gray-700 font-bold dark:text-gray-300">Locations</label>
            <div class="google_poi_field mb-2">
                <input type="text" name="google_pois[]" placeholder="Enter an address" 
                class="shadow appearance-none bordersite
           


        <div class="flex items-center justify-between mt-4">
            <x-primary-button class="ms-4" type="submit">
                {{ __('Update') }}
            </x-primary-button>
        </div>
    </form>
</div>
</x-app-layout>


<script>

$(document).ready(function() {

    $('#serverSelect').change(function() {
        const server = this.value;
        const url = new URL('{{ route('creation.neighborhoods.byServer') }}');
        url.searchParams.append('server', server);

        fetch(url, {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
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
        const siteDetails = $('#siteDetails');
        const googlePoiForm = $('#google-poi-form');
        const siteIdInput = $('#siteIdInput');

        if (!siteId) {
            siteDetails.text('Please select a site.');
            googlePoiForm.hide();
            return;
        } else {
            siteIdInput.val(siteId);
        }

        const url = '{{ route('fetch.poi.options') }}' + '?siteId=' + siteId;
        fetch(url, {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                siteDetails.text('Loaded existing POI options.');
                $('#site_url').val(data.siteUrl);
                $('#google_poi_heading').val(data.googlePoiHeading);
                console.log(data.googlePois)
                let pois = data.googlePois.split(':');
                console.log(pois)
                let poiFields = $('.google_poi_field');
                poiFields.empty(); // Clear existing fields

                pois.forEach(poi => {
                    poiFields.append(`<div class="google_poi_field"><input type="text" name="google_pois[]" value="${poi}" class="mt-1 block w-full opacity-75"></div>`);
                });
                googlePoiForm.show();
            } else {
                siteDetails.text('Failed to load POI options or data incomplete.');
                googlePoiForm.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            siteDetails.text('Error connecting to the site.');
        });
    });

    // Add more POI fields
    $('.add-google_poi_field').click(function() {
        $('.google_poi_fields').append(`
            <div class="google_poi_field">
                <input type="text" name="google_pois[]" placeholder="Enter another location" class="mt-1 block w-full opacity-75">
                <button type="button" class="remove-google_poi_field">Remove</button>
            </div>
        `);
    });

    // Remove POI field
    $('.google_poi_fields').on('click', '.remove-google_poi_field', function() {
        $(this).parent().remove();
    });
});
</script>

