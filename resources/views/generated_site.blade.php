{{-- <!DOCTYPE html>
<html>
<head>
    <title>Generated Articles</title>
</head>
<body>
    @foreach($websites as $website)
        <h1>Generated Articles based on {{ $website }}</h1>
        @if(isset($articles[$website]))
            @foreach($articles[$website] as $index => $article)
                <article>
                    <h2>Article {{ $index + 1 }}</h2>
                    {!! $article !!}
                </article>
            @endforeach
        @endif
        <p>Processed manual entry for website: {{ $website }}</p>
        <hr>
    @endforeach
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/favicon.jpg') }}">
    <title>Generated Articles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            font-size: 24px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #555;
            font-size: 20px;
            margin-top: 0;
        }
        article {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        p {
            color: #777;
            font-size: 14px;
        }
        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 40px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach($websites as $website)
            <h1>Generated Articles based on {{ $website }}</h1>
            @if(isset($articles[$website]))
                @foreach($articles[$website] as $index => $article)
                    <article>
                        <h2>Article {{ $index + 1 }}</h2>
                        {!! $article !!}
                    </article>
                @endforeach
            @endif
            @if ($type === 'csv')
                <p>Processed csv entry for website: {{ $website }}</p>
            @else
                <p>Processed manual entry for website: {{ $website }}</p>
            @endif
            <hr>
        @endforeach
    </div>
</body>
</html>
