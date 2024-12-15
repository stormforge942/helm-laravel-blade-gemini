<!DOCTYPE html>
<html>
<head>
    <title>Form Creation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom CSS for extra styling */
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .form-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .form-control {
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .form-group {
        margin-bottom: 15px;
        }
        .btn-custom {
        width: 100%;
        padding: 10px;
        border-radius: 25px;
        background-color: #00003f  !important;
        color: white;
        border: none;
        font-weight: bold;
        text-align: center;
        transition: background-color 0.3s ease;
        }
        .btn-cancel {
        width: 100%;
        padding: 10px;
        border-radius: 25px;
        background-color: #8B0000 !important;
        color: white;
        border: none;
        font-weight: bold;
        text-align: center;
        transition: background-color 0.3s ease;
        }
        .btn-container {
        text-align: center;
        }
        .btn-container button {
        width: 120px;
        margin-top: 10px;
        }
        .btn-custom:hover {
            background-color:#0056b3;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <x-app-layout>
        <div class="form-container">
            <h2 class="form-title">Form Creation</h2>
                <!-- Show success message -->
                @if (session('success'))
                    <div class="alert alert-success"  id="success-message">{{ session('success') }}</div>
                @endif

                @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- New Form or Edit Form -->
                <form id="form-create-edit" method="POST" action="/maintenance/form-creation">
                    @csrf
                    <div class="form-group">
                        <label for="form-type">Select Option:</label>
                        <select id="form-type" name="form_type" class="form-control">
                            <option value="" selected disabled>Select</option> <!-- Default option -->
                            <option value="new">New Form</option>
                            <option value="edit">Edit Form</option>
                        </select>
                        <label for="form-niche">Select Niche:</label>
                        <select id="niche-id" name="niche" class="form-control">
                            <option value="" disabled selected>Select a Niche</option>
                            @foreach ($niches as $niche)
                                <option value="{{ $niche }}">{{ $niche }}</option>
                            @endforeach
                        </select>
                        
                    </div>

                    <!-- For new form -->
                    <div id="new-form-section" class="form-group">
                        <label for="form-name">Form Name:</label>
                        <input type="text" id="form-name" name="name" required value="{{ old('name') }}"class="form-control">

                        <!-- Error message container -->
                        <span id="name-error" class="text-danger" style="display: none;">This form name is already taken.</span>
                    </div>

                    <!-- For editing form -->
                    <div id="edit-form-section" class="form-group" style="display: none;">
                        <label for="form-select">Select Form:</label>
                        <select id="form-select" name="form_id" class="form-control">
                            <option value="" selected disabled>Select a Form</option>
                            
                        </select>
                    </div>
                    
                    <div class="btn-container">
                        <button type="button" id="next-btn"  class="btn-custom">Next</button>
                    </div>
                </form>

                <!-- Popup Form (Hidden by default) -->
                <div id="popup-form" style="display: none;">
                    <form id="popup-form-content" method="POST" action="{{ url('/maintenance/form-creation') }}">
                        @csrf
                        <input type="hidden" id="hidden-form-name" name="name">
                        <div class="form-group">
                            <label for="header-code">Header Code (Optional):</label>
                            <textarea id="header-code" name="header_code" class="form-control">{{ old('header_code') }}</textarea>
                        </div>    
                        <!--JavaScript (for body) field -->
                        <div class="form-group">
                            <label for="body-js">JavaScript (for body) (Optional):</label>
                            <textarea id="body-js" name="body_js" class="form-control">{{ old('body_js') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="form-code">Form Code (Required):</label>
                            <textarea id="form-code" name="form_code" required class="form-control">{{ old('form_code') }}</textarea>
                        </div>
                        <div class="btn-container">
                            <button type="submit" class="btn-custom">Submit</button>
                            <button type="button" id="cancel-btn" class="btn-cancel">Cancel</button>
                        </div>
                    </form>
                </div>
        </div>
    <script>
        $(document).ready(function() {
            
            setTimeout(function() {
                $('#success-message').fadeOut('slow');
                $('.alert-warning').fadeOut('slow');
            }, 5000);

            $('#cancel-btn').click(function() {
                window.location.href = '/maintenance/form-creation';
            });

            
            $('#form-type').change(function() {
                if ($(this).val() === 'new') {
                    $('#new-form-section').show();
                    $('#edit-form-section').hide();
                } else {
                    $('#new-form-section').hide();
                    $('#edit-form-section').show();
                }
            });
            $('form').on('submit', function() {
            console.log($('#body-js').val());  // Check what is in the body_js field
        });
           
            $('#next-btn').click(function(e) {
                e.preventDefault(); 
                const  formType = $('#form-type').val();
                const formName = $('#form-name').val();
                const selectedNiche = $('#niche-id').val();
                const selectedForm = $('#form-select').val();

                if (formType === 'new') {
                    if (!formName) {
                        alert('Please enter the form name');
                    } else if (!selectedNiche) {
                        alert('Please select a niche'); 
                    }else {
                        // Check for duplicate form name
                        $.ajax({
                            url: '{{ route("check.form.name") }}', // Endpoint for checking form name uniqueness
                            method: 'POST',
                            data: {
                                name: formName,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log(response); 
                                if (response.exists) {
                                    $('#name-error').text('This form name is already taken.').show();

                                    setTimeout(function() {
                                    $('#name-error').fadeOut('slow');
                                }, 5000);
                                } else {
                                    //if the name is unique
                                    $('#hidden-form-name').val(formName);
                                    $('#popup-form-content').attr('action', '/maintenance/form-creation');
                                    $('#next-btn').hide();
                                    $('#popup-form').show();

                                    $('#popup-form-content').append('<input type="hidden" name="niche" value="' + selectedNiche + '">');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('AJAX error: ', status, error); 
                                alert('Error checking form name. Please try again.');
                            }
                        });
                    }
                } else if (formType === 'edit') {
                    if (selectedForm === "") {
                        alert('Please select a form to edit');  
                    }else if (!selectedNiche) {
                        alert('Please select a niche'); 
                    }
                    else{
                    let formId = $('#form-select').val();
                    $.get('/maintenance/form-creation/edit/' + formId, function(data) {
                        $('#hidden-form-name').val(data.name)
                        $('#header-code').val(data.header_code);
                        $('#form-code').val(data.form_code);
                        $('#body-js').val(data.body_js);
                        $('#popup-form-content').attr('action', '/maintenance/form-creation/update/' + formId);
                        $('#next-btn').hide(); 
                        $('#popup-form').show();

                        $('#popup-form-content').append('<input type="hidden" name="niche" value="' + data.niche + '">');
                    });
                }
            }
            });
            $('#niche-id').change(function() {
                    const selectedNiche = $(this).val();

                    // If we are in "edit" mode and a niche is selected
                    if ($('#form-type').val() === 'edit' && selectedNiche) {
                        // Send AJAX request to get forms based on selected niche
                        $.ajax({
                            url: '{{ route("get.forms.by.niche") }}',
                            method: 'POST',
                            data: {
                                niche: selectedNiche,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(forms) {
                                // Clear the current form select options
                                $('#form-select').empty();
                                $('#form-select').append('<option value="" disabled selected>Select a Form</option>');

                                // Populate the form-select dropdown with the forms for the selected niche
                                $.each(forms, function(index, form) {
                                    $('#form-select').append('<option value="' + form.id + '">' + form.name + '</option>');
                                });
                            },
                            error: function(xhr, status, error) {
                                console.log('Error fetching forms by niche: ', error);
                            }
                        });
                    }
                });
        });
    </script>
    </x-app-layout>
</body>
</html>
