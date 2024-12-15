<!-- resources/views/pipelineReporting.blade.php -->

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        padding: 0;
        background-color: #f7f7f7;
    }

    .container {
        margin: 20px auto;
        max-width: 800px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        margin-bottom: 20px;
        padding: 20px;
        color: #051e40;
        text-align: center;
    }

    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .sorting:after {
            content: "▲\A▼";
            font-size: 0.5rem;
            white-space: pre;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            padding-left: 2px;
            line-height: 7px;
            padding-right: 5px;
    }
    .sorting.asc:after {
        content: "▲";
    }

    .sorting.desc:after {
        content: "▼";
    }

    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        text-align: center;
    }

    .form-container h1 {
        margin-bottom: 20px;    
        color: #051e40;
    }

    .form-container table {
        width: 100%;
    }

    .form-container table td {
        padding: 10px 0;
    }

    .form-container table td:first-child {
        text-align: left;
    }

    .form-container input[type="text"],
    .form-container input[type="email"],
    .form-container input[type="number"],
    .form-container input[type="date"],
    .form-container select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .form-container input[type="submit"] {
        background-color: #051e40;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .form-container input[type="submit"]:hover {
        background-color: #031a36;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<body>
    <x-app-layout>
        <div class="container dark:bg-gray-600">
            <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">Pipeline Reporting</h2>
            @if($pipelineReports->count() === 0)
                <p>No records found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 bg-white shadow-2xl rounded-lg text-sm">
                        <thead class="bg-blue-50">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'first_name', 'sort_direction' => $sortBy === 'first_name' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'first_name' ? $sortDirection : ''}}">
                                    First Name
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'last_name', 'sort_direction' => $sortBy === 'last_name' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'last_name' ? $sortDirection : ''}}">
                                    Last Name
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'email', 'sort_direction' => $sortBy === 'email' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'email' ? $sortDirection : ''}}">
                                    Email
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'phone', 'sort_direction' => $sortBy === 'phone' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'phone' ? $sortDirection : ''}}">
                                    Phone
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'website_rented', 'sort_direction' => $sortBy === 'website_rented' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'website_rented' ? $sortDirection : ''}}">
                                    Website Rented
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'price', 'sort_direction' => $sortBy === 'price' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'price' ? $sortDirection : ''}}">
                                    Price
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'sales_representative', 'sort_direction' => $sortBy === 'sales_representative' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'sales_representative' ? $sortDirection : ''}}">
                                    Sales Representative
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'originating_lead', 'sort_direction' => $sortBy === 'originating_lead' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'originating_lead' ? $sortDirection : ''}}">
                                    Originating Lead
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'status', 'sort_direction' => $sortBy === 'status' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'status' ? $sortDirection : ''}}">
                                    Status
                                </a>
                            </th>
                            <th scope="col" class="whitespace-nowrap px-2 py-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sortable relative">
                                <a href="{{ route('pipeline.reporting', ['sort_by' => 'select_date', 'sort_direction' => $sortBy === 'select_date' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="sorting inline-block align-middle cursor-pointer relative {{ $sortBy === 'select_date' ? $sortDirection : ''}}">
                                    Date
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach($pipelineReports as $report)
                            <tr class="{{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 dark:text-gray-500">
                                <td class="px-2 py-2">{{ $report->first_name }}</td>
                                <td class="px-2 py-2">{{ $report->last_name }}</td>
                                <td class="px-2 py-2">{{ $report->email }}</td>
                                <td class="px-2 py-2">{{ ltrim($report->phone, '+') }}</td>
                                <td class="px-2 py-2">{{ $report->website_rented }}</td>
                                <td class="px-2 py-2">${{ number_format($report->price, 0) }}</td>
                                <td class="px-2 py-2">{{ $report->sales_representative }}</td>
                                <td class="px-2 py-2">{{ $report->originating_lead }}</td>
                                <td class="px-2 py-2">{{ $report->status }}</td>
                                <td class="px-2 py-2">{{ $report->select_date }}</td>
                                <td class="px-2 py-2">
                                    <a href="javascript:void(0)" class="edit-button" data-id="{{ $report->id }}" data-toggle="modal" data-target="#editModal">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content form-container">
                    <form id="editForm" method="POST" action="{{ route('sales.form.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header flex justify-between items-center mb-3">
                            <h3 class="modal-title text-xl font-bold mx-auto" id="editModalLabel">Edit Record</h3>
                            <button type="button" class="close top-0 right-0" style="margin-top: -0.3rem;" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="recordId">
                            <!-- Include the same form fields as the insert form -->
                            <table>
                                <tr>
                                    <td>First Name</td>
                                    <td><input type="text" name="first_name" id="editFirstName"></td>
                                </tr>
                                <tr>
                                    <td>Last Name</td>
                                    <td><input type="text" name="last_name" id="editLastName"></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><input type="email" name="email" id="editEmail"></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td><input type="text" name="phone" id="editPhone"></td>
                                </tr>
                                <tr>
                                    <td>Price</td>
                                    <td><input type="number" name="price" id="editPrice"></td>
                                </tr>
                                <tr>
                                    <td>Website Rented</td>
                                    <td><input type="text" name="website_rented" id="editWebsiteRented"></td>
                                </tr>
                                <tr>
                                    <td>Sales Representative</td>
                                    <td>
                                        <select name="sales_representative" id="editSalesRepresentative">
                                            <option value="" disabled selected>Select</option>
                                            @foreach ($salesRepresentatives as $rep)
                                                <option value="{{ $rep }}">{{ $rep }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Originating Lead</td>
                                    <td>
                                        <select name="originating_lead" id="editOriginatingLead">
                                            <option value="" disabled selected>Select</option>
                                            <option value="inbound call">Inbound Call</option>
                                            <option value="text message">Text Message</option>
                                            <option value="outbound call">Outbound Call</option>
                                            <option value="email blast">Email Blast</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <select name="status" id="editStatus">
                                            <option value="" selected>Select</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="converted">Converted</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Select Date</td>
                                    <td><input type="date" name="select_date" id="editSelectDate"></td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="modal-footer flex justify-end mt-3">
                            <x-primary-button class="ms-4 bg-blue" type="submit" id="add-announcement-btn">
                                <span id="add-announcement-text">Save changes</span>
                            </x-primary-button>
                            <x-secondary-button class="ms-4 bg-gray" data-dismiss="modal" aria-label="Close" id="close-modal-button">
                                <span id="add-announcement-text">Close</span>
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
            $('#close-modal-button').on('click', function() {
                $('#editModal').hide();
            });
            document.addEventListener('DOMContentLoaded', function () {
                const editButtons = document.querySelectorAll('.edit-button');
                const modal = document.getElementById('editModal');
                const span = document.getElementsByClassName('close')[0];

                editButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');

                        // Fetch the record data
                        fetch(`/sales-form/${id}`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('recordId').value = data.id;
                                document.getElementById('editFirstName').value = data.first_name;
                                document.getElementById('editLastName').value = data.last_name;
                                document.getElementById('editEmail').value = data.email;
                                document.getElementById('editPhone').value = data.phone;
                                document.getElementById('editPrice').value = data.price;
                                document.getElementById('editWebsiteRented').value = data.website_rented;
                                document.getElementById('editSalesRepresentative').value = data.sales_representative;
                                document.getElementById('editOriginatingLead').value = data.originating_lead;
                                document.getElementById('editStatus').value = data.status;
                                document.getElementById('editSelectDate').value = data.select_date;

                                modal.style.display = 'block';
                            });
                    });
                });

                // Close the modal when the user clicks on <span> (x)
                span.onclick = function() {
                    modal.style.display = 'none';
                }

                // Close the modal when the user clicks anywhere outside of the modal
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                }
            });
        </script>
    </x-app-layout>
</body>
