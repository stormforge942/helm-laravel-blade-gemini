<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('Change neighborhoods and maps') }}
        </h2>
    </x-slot>
    @csrf

    <!-- Server selection dropdown -->
    <select id="serverSelect" name="server">
        <option value="">Select Server</option>
        @foreach ($servers as $server)
            <option value="{{ $server }}">{{ $server }}</option>
        @endforeach
    </select>

    <!-- Dynamic dropdown for site list -->
    <select name="siteListDropdown" id="siteListDropdown">
        <option value="">Select Site</option>
    </select>

    <div id="siteDetails"></div>

    <script>
        document.getElementById('serverSelect').addEventListener('change', function() {
            var server = this.value;
            var siteListDropdown = document.getElementById('siteListDropdown');

            // Prepare the URL with query parameters
            var url = new URL('{{ route('creation.sites.byServer') }}');
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
                        option.textContent = site.base_uri;
                        siteListDropdown.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Event listener for site selection
        document.getElementById('siteListDropdown').addEventListener('change', function() {
            var siteId = this.value;
            if (siteId) {
                fetch(`/wordpress/authenticate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            siteId: siteId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Authentication successful');
                            console.log(data);
                            displaySiteDetails(data);
                        } else {
                            console.error('Authentication failed', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        })

        function displaySiteDetails(details) {
            console.log('Received details:', details); // Log to see what data is actually received
            const siteList = document.getElementById('siteDetails');
            siteList.innerHTML = ''; // Clear previous details

            // Check if 'details.data' exists and is an array before attempting to iterate
            if (details && details.data) {
                const pages = details.data;
                let content = '<h3>Site Details:</h3>';

                pages.forEach(page => {
                    content += `
                <div>
                    <h4>${page.title.rendered}</h4>
                    <p>${page.content.rendered}</p>
                    <a href="${page.link}" target="_blank">Read More</a>
                    <hr>
                </div>
            `;
                });

                siteList.innerHTML = content;
            } else {
                siteList.innerHTML = '<p>No data found or unable to parse data.</p>';
                console.error('Error: details.response is not an array or is undefined');
            }
        }
    </script>
</x-app-layout>
