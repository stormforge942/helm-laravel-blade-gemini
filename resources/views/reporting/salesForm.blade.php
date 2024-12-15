<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Sales Form</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
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
</head>
<body>
    <x-app-layout>
    <div class="container">
    <div class="form-container dark:bg-gray-600">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">Rental Sales</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
<form method="POST" action="{{ route('sales.form.store') }}" >
    @csrf
    <table>
        <tr>
            <td>First Name</td>
            <td><input type="text" name="first_name" value="{{ old('first_name') }}"></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><input type="text" name="last_name" value="{{ old('last_name') }}"></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input type="email" name="email" value="{{ old('email') }}"></td>
        </tr>
        <tr>
            <td>Phone</td>
            <td><input type="text" name="phone" value="{{ old('phone') }}"></td>
        </tr>
        <tr>
            <td>Price</td>
            <td><input type="number" name="price" value="{{ old('price') }}"></td>
        </tr>
        <tr>
            <td>Website Rented</td>
            <td><input type="text" name="website_rented" value="{{ old('website_rented') }}"></td>
        </tr>

        <tr>
                        <td>Sales Representative</td>
                        <td>
                            <select name="sales_representative">
                                <option value="" disabled selected>Select</option>
                                @foreach ($salesRepresentatives as $rep)
                                    <option value="{{ $rep }}" {{ old('sales_representative') == $rep ? 'selected' : '' }}>{{ $rep }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

        
        <tr>
            <td>Originating Lead</td>
            <td>
                <select name="originating_lead">
                    <option value="" disabled selected>Select</option>
                    <option value="inbound call" {{ old('originating_lead') == 'inbound call' ? 'selected' : '' }}>Inbound Call</option>
                    <option value="text message" {{ old('originating_lead') == 'text message' ? 'selected' : '' }}>Text Message</option>
                    <option value="outbound call" {{ old('originating_lead') == 'outbound call' ? 'selected' : '' }}>Outbound Call</option>
                    <option value="email blast" {{ old('originating_lead') == 'email blast' ? 'selected' : '' }}>Email Blast</option>
                    <option value="other" {{ old('originating_lead') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                <select name="status">
                    <option value="" selected>Select</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="converted" {{ old('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Select Date</td>
            <td><input type="date" name="select_date" value=""></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="submit"/>
            </td>
        </tr>
    </table>
</form>
    </div>
</div>
    </x-app-layout>
    </body>
