<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContentGenerationService
{
    public function generateContent($prompt)
    {
        $google_api_key = env('GOOGLE_API_KEY');
        $model = 'models/gemini-1.5-pro-latest';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/$model:generateContent?key=$google_api_key";

        $requestBody = [
            'contents' => [
                [
                    'role' => 'user',
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

        try {
            $response = Http::timeout(300)->post($endpoint, $requestBody);

            if ($response->failed()) {
                return [
                    'error' => 'Request failed with status ' . $response->status(),
                    'content' => ''
                ];
            }

            $candidates = $response['candidates'] ?? [];
            if (count($candidates) > 0) {
                $contentParts = $candidates[0]['content']['parts'] ?? [];
                $outputText = '';
                foreach ($contentParts as $part) {
                    $outputText .= $part['text'];
                }

                $tokensCount = $response['usageMetadata']['totalTokenCount'] ?? 0;

                return [
                    'error' => '',
                    'content' => $outputText,
                    'tokens_count:' => $tokensCount
                ];
            }

            return [
                'error' => 'No candidates found in the response',
                'content' => ''
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Error: ' . $e->getMessage(),
                'content' => ''
            ];
        }
    }
}
