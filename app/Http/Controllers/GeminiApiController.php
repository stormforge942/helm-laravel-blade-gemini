<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GeminiApiController extends Controller
{
    public function listModels() {
        // Google api key
        $google_api_key = env('GOOGLE_API_KEY');
        $client = new Client();

        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models';

        try {
            $response = $client->get($endpoint, [
                'query' => ['key' => $google_api_key],
            ]);

            $content = $response->getBody()->getContents();
            $decoded = json_decode($content, true);

            return response()->json($decoded);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $message = $response->getReasonPhrase();
                return response()->json(['error' => "HTTP $statusCode: $message"], $statusCode);
            } else {
                return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
            }
        }
    }

    public function verifyGoogle(Request $request) {
        $envID = env('GOOGLE_OAUTH_CLIENT_ID');
        $clientId = "{$envID}.apps.googleusercontent.com";
        // secret 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
        $redirectUri = 'https://alir.championdre.com/generate-content/callback';
        $scope = 'https://www.googleapis.com/auth/cloud-platform';
        $authUrl = "https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=$clientId&redirect_uri=$redirectUri&scope=$scope&access_type=offline";

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $envID = env('GOOGLE_OAUTH_CLIENT_ID');
        $code = $request->input('code');
        $clientId = "{$envID}.apps.googleusercontent.com";
        $clientSecret = env('GOOGLE_OAUTH_CLIENT_SECRET');
        $redirectUri = 'https://alir.championdre.com/generate-content/callback';

        $tokenUrl = "https://oauth2.googleapis.com/token";

        $response = $this->postRequest($tokenUrl, [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (isset($response['access_token'])) {
            $accessToken = $response['access_token'];
            $request->session()->put('access_token', $accessToken);
            return redirect('/generate-content/execute');
        } else {
            return response()->json(['error' => 'Failed to obtain access token.'], 500);
        }
    }

    public function generateContent(Request $request)
    {
        $min_words = $request->min_words;
        $max_words = $request->max_words;
        $google_api_key = env('GOOGLE_API_KEY');

        $model = 'models/gemini-1.5-pro-latest';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/$model:generateContent?key=$google_api_key";
        // $prompt = "
        //         Generate a unique version of the provided commercial services content that is highly informative, reliable,
        //         and useful for people in the specified locality. Base this content on the content from the
        //         URL https://www.remodelmybath.co/. The content absolutely must be between {$min_words} and {$max_words} words per paragraph and be 6 paragraphs total. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:

        //         - Include the target keywords in the first paragraph.
        //         - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
        //         - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
        //         - Organize the content using HTML for headings, subheadings, bullet points, numbering, and tables where applicable. Specifically:
        //             - Use <h1> for the main title.
        //             - Use <h2> for section headings.
        //             - Use <p> for paragraphs.
        //             - Avoid any Markdown symbols or HTML wrapper tags.
        //             - Do not include any CSS styles.
        //             - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.
                
        //         Output format: Strictly HTML (with appropriate use of tags as mentioned above).
                
        //         Use Only English. Avoid links to other websites.
                
        //         Base Site URL: remodelmybath.co/
        //         City: LA
        //         Core Focus of the Site: Remodel Bath
        //         Keywords: bath, remodel
        //         First Keyword Frequency: 3
        //         Second Keyword Frequency: 2
        //         Third Keyword Frequency: 
        //         Additional Keyword Frequency: 
        //         Additional Instructions: 
        //         ";

        // $prompt = "
        //         Generate a highly informative, reliable, and useful version of the provided commercial services content for people in LA, based on the content from the URL https://www.remodelmybath.co/. The content must be between {$min_words} and {$max_words} words per paragraph and have 6 paragraphs total. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:

        //         - Include the target keywords in the first paragraph.
        //         - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
        //         - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
        //         - Organize the content using HTML for headings, subheadings, bullet points, numbering, and tables where applicable. Specifically:
        //             - Use <h1> for the main title.
        //             - Use <h2> for section headings.
        //             - Use <p> for paragraphs.
        //             - Use <ul> and <li> for bullet points.
        //             - Avoid any Markdown symbols or HTML wrapper tags.
        //             - Do not include any CSS styles.
        //             - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.

        //         Additionally:
        //         - Expand each paragraph with detailed descriptions, explanations, and examples to increase the word count.
        //         - Include several subheadings within each paragraph to break down information into smaller, digestible sections.
        //         - Provide historical context, benefits, and detailed steps involved in bath remodeling.
        //         - Incorporate customer testimonials and expert quotes to add depth and length to the content.
        //         - Use storytelling elements to make the content engaging and comprehensive.
                                
        //         Output format: Strictly HTML (with appropriate use of tags as mentioned above).

        //         Use Only English. Avoid links to other websites.

        //         Base Site URL: remodelmybath.co/
        //         City: LA
        //         Core Focus of the Site: Remodel Bath
        //         Keywords: bath, remodel
        //         First Keyword Frequency: 3
        //         Second Keyword Frequency: 2
        //         ";

        // $prompt = "Write a detailed and intricate story, exceeding 4000 words, about Alex, a curious teenager who stumbles upon a worn leather backpack with frayed straps and a mysterious zipper.  As Alex unlocks the backpack's secrets, unveil intricate details of the fantastical worlds and historical periods he visits.  Throughout Alex's journeys, explore several diverse and thrilling adventures, emphasizing the challenges and consequences he faces in these unique environments.  Show how these experiences transform Alex's personality and perspective on the world.  Ultimately, convey a powerful message about the importance of curiosity, responsibility, and the lasting impact of our actions.";

        $prompt = "
                Generate a highly informative, reliable, and useful version of the provided commercial services content for people in LA, based on the content from the URL https://www.remodelmybath.co/. The content must be between {$min_words} and {$max_words} words per paragraph and have 6 paragraphs total. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:

                - Include the target keywords in the first paragraph.
                - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
                - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
                - Organize the content using HTML for headings, subheadings, bullet points, numbering, and tables where applicable. Specifically:
                    - Use <h1> for the main title.
                    - Use <h2> for section headings.
                    - Use <p> for paragraphs.
                    - Use <ul> and <li> for bullet points.
                    - Avoid any Markdown symbols or HTML wrapper tags.
                    - Do not include any CSS styles.
                    - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.

                Additionally:
                - Expand each paragraph with detailed descriptions, explanations, and examples to increase the word count.
                - Include several subheadings within each paragraph to break down information into smaller, digestible sections.
                - Provide historical context, benefits, and detailed steps involved in bath remodeling.
                - Incorporate customer testimonials and expert quotes to add depth and length to the content.
                - Use storytelling elements to make the content engaging and comprehensive.
                - Ensure each paragraph explores different aspects of bath remodeling in-depth, such as planning, material selection, contractor hiring, and benefits.
                - Use bullet points and lists where appropriate to add detail and increase word count.
                                
                Output format: Strictly HTML (with appropriate use of tags as mentioned above).

                Use Only English. Avoid links to other websites.

                Base Site URL: remodelmybath.co/
                City: LA
                Core Focus of the Site: Remodel Bath
                Keywords: bath, remodel
                First Keyword Frequency: 3
                Second Keyword Frequency: 2
            ";

        $requestBody = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_ONLY_HIGH'
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 8000,
                'temperature' => 0.7,
                'topP' => 0.9,
                'topK' => 50,
            ],
        ];

        $response = $this->postRequest($endpoint, $requestBody);

        if (isset($response['error'])) {
            return response('Error: ' . $response['error'], 500);
        }

        // $outputText = trim($response->candidates[0]->content->parts[0]->text);
        $candidates = $response['candidates'] ?? [];
        if (count($candidates) > 0) {
            $contentParts = $candidates[0]['content']['parts'] ?? [];
            $outputText = '';
            foreach ($contentParts as $part) {
                $outputText .= $part['text'];
            }

            $tokensCount = $response['usageMetadata']['totalTokenCount'] ?? 0;

            return response('Minimum words length: ' . $min_words . ' Maximum words length:' . $max_words . ' Output Text: ' . $outputText . ' Tokens Count: ' . $tokensCount);
        }

        return response('No candidates found in the response', 500);

        // $model = 'models/gemini-1.5-pro';
        // $endpoint = "https://generativelanguage.googleapis.com/v1beta/$model:generateContent?key=$google_api_key";
        // $prompt = "Write a story about a magic backpack in 2000-2200 words.";

        // $requestBody = [
        //     'contents' => [
        //         [
        //             'parts' => [
        //                 ['text' => $prompt]
        //             ]
        //         ]
        //     ],
        //     'safetySettings' => [
        //         [
        //             'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
        //             'threshold' => 'BLOCK_ONLY_HIGH'
        //         ]
        //     ],
        //     'generationConfig' => [
        //         'maxOutputTokens' => 4000,
        //         'temperature' => 0.7,
        //         'topP' => 0.9,
        //         'topK' => 50,
        //     ],
        // ];

        // $response = $this->postRequest($endpoint, $requestBody);

        // if (isset($response['error'])) {
        //     return response()->json(['error' => $response['error']], 500);
        // }

        // return response()->json($response);
    }

    private function postRequest($url, $data, $accessToken = null)
    {
        $headers = ['Content-Type: application/json'];
        if ($accessToken) {
            $headers[] = "Authorization: Bearer $accessToken";
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['error' => 'Curl error: ' . $curlError];
        }

        if ($httpCode >= 400) {
            return ['error' => 'HTTP error: ' . $httpCode . ' Response: ' . $response];
        }

        return json_decode($response, true);
    }
}
