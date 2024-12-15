<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\GenerationConfig;
use GeminiAPI\Requests\GenerateContentRequest;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Enums\ModelName;

class ExtendedGemini
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function generateTextWithConfig(string $prompt, GenerationConfig $config): string
    {
        // Prepare the text part
        $textPart = new TextPart($prompt);

        // Prepare the generate content request with config
        $generateContentRequest = new GenerateContentRequest(
            ModelName::GeminiPro,
            [$textPart],
            $config->jsonSerialize()
        );

        // Generate content
        $response = $this->client->generateContent($generateContentRequest);

        return $response->text();
    }
}
