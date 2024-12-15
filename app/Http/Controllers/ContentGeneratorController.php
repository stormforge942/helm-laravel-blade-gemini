<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmCategory;
use Illuminate\Validation\Rule;
use App\Services\ContentGenerationService;

class ContentGeneratorController extends Controller
{
    protected $contentGenerationService;

    public function __construct(ContentGenerationService $contentGenerationService)
    {
        $this->contentGenerationService = $contentGenerationService;
    }

    public function generateContent($data)
    {
        // Check if the keywords or core focus contain "fencing"
        if (stripos($data['keywords'], 'fencing') !== false || stripos($data['existing_prompt'], 'fencing') !== false) {
            // Modify the content to emphasize fencing as building a physical fence
            $data['keywords'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['keywords']);
            $data['existing_prompt'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['existing_prompt']);
        }

        // Word count adjustments based on min_words_count
        if ($data['min_words_count'] >= 100 && $data['min_words_count'] <= 500) {
            $data['min_words_count'] += 300;
            $data['max_words_count'] += 300;
        } elseif ($data['min_words_count'] >= 500 && $data['min_words_count'] <= 1000) {
            $data['min_words_count'] += 500;
            $data['max_words_count'] += 500;
        } elseif ($data['min_words_count'] >= 1000 && $data['min_words_count'] <= 2000) {
            $data['min_words_count'] += 1000;
            $data['max_words_count'] += 1000;
        } elseif ($data['min_words_count'] >= 2000 && $data['min_words_count'] <= 3000) {
            $data['min_words_count'] += 1500;
            $data['max_words_count'] += 1500;
        }

        // Generate the prompt based on the content type (blog or others)
        if ($data['type'] === 'blog') {
            $prompt = "
            Generate a unique version of the provided commercial services content that is highly informative, reliable, and useful for people in the specified locality. The content must be between {$data['min_words_count']} and {$data['max_words_count']} per paragraph, with a total of {$data['number_of_paragraphs']} paragraphs. Adhere to the required tone, emphasize the given keywords, and follow these guidelines:

            - Include the target keywords in the first paragraph.
            - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
            - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
            - Do not include links to other websites or languages other than English.";

            // HTML-specific prompt details
            if (isset($data['use_html'])) {
                $prompt .= "
                - Organize the content using HTML for headings, subheadings, and tables where applicable. Use <h1> for the main title, <h2> for section headings, and <p> for paragraphs. Avoid Markdown symbols, HTML wrapper tags, or CSS styles.
                - Do not use bullet points or asterisks for lists. Use proper HTML tags for content organization.";
            } else {
                $prompt .= " - Output format: plain text.";
            }

            $prompt .= "
                    - Use Only English.
                    - Avoid links to other websites.
                    - Ensure all paragraphs contain at least 5 sentences, expanding on each point with detailed descriptions, examples, and elaborations to meet the word count requirements.
                    - Use storytelling elements, customer testimonials, and expert quotes to add depth and length to the content.
                    - Provide historical context, benefits, and detailed steps involved in the service, expanding on each aspect thoroughly.
                    - Incorporate several subheadings within each section to break down information into smaller, digestible sections.
                    - Add additional sections such as case studies, industry trends, comparisons with competitors, and future predictions to increase word count.
                    - Ensure each paragraph explores different aspects of the service in-depth, such as planning, material selection, contractor hiring, and benefits, with multiple examples and detailed explanations.
                    - Add detail and increase word count, but ensure they are formatted with proper HTML tags.

                    Original Content: {$data['article_content']}
                    Locality: {$data['locality']}
                    Tone: {$data['tone']}
                    Keywords: {$data['keywords']}
                    Custom Prompt: {$data['existing_prompt']}

                    Guidelines to adhere:
                    - No plagiarism
                    - Short sentences and concise writing
                    - County and state included once";

            $result_array = [];

            for ($i = 0; $i < $data['num_copies']; $i++) {
                try {
                    $result = $this->contentGenerationService->generateContent($prompt);
                    if (empty($result['error'])) {
                        // Post-processing step to remove unnecessary newlines
                        $cleanResponse = $this->removeUnnecessaryNewlines($result['content']);
                        array_push($result_array, $cleanResponse);
                    } else {
                        array_push($result_array, "Error in generating content: " . $result['error']);
                    }
                } catch (Exception $e) {
                    error_log('Error generating content: ' . $e->getMessage());
                    array_push($result_array, "Error in generating content: " . $e->getMessage());
                }
            }
            // dd($result_array);
            return $result_array;
        } else {
            $prompt = "Generate a unique version of the provided commercial services content that is highly informative, reliable, and useful for people in the specified locality. The content must be between {$data['min_words_count']} and {$data['max_words_count']} words. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:
                - Generate content with {$data['min_words_count']} to {$data['max_words_count']} words.
                - Include the target keywords in the first paragraph.
                - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
                - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
                - You MUST use every keyword at least once and mention the locality at least once in the content.
                - Don't include links to other websites or languages other than English
                - Ensure the content is rich in detail and comprehensive, covering all aspects of the topic to meet the word count requirement.";

            if (isset($data['use_html'])) {
                $prompt .= "
                        - Organize the content using HTML for headings, subheadings, bullet points, numbering, and tables where applicable. Specifically:
                            - Use <h1> for the main title.
                            - Use <h2> for section headings.
                            - Use <p> for paragraphs.
                            - Avoid any Markdown symbols or HTML wrapper tags.
                            - Avoid Markdown symbols, HTML wrapper, and any css styles.
                            - Do not include any CSS styles.
                            - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.
                            - Avoid adding <br> tags within specific tags such as <ul>, <ol>, <li>.
                            - Output format: Strictly HTML (with appropriate use of tags as mentioned above).
                            - Organize the content using both bullet points (<ul> and <li> tags) and numbering (<ol> and <li> tags) where applicable. Use <ol> for ordered lists. Use <ul> for unordered lists and <li> for list items.";
            } else {
                $prompt .= " - Output format: plain text.";
            }

            // Append additional instructions and keywords
            $prompt .= "
                    - Use Only English.
                    - Avoid links to other websites.
                    - Ensure all paragraphs contain at least 5 sentences, expanding on each point with detailed descriptions, examples, and elaborations to meet the word count requirements.
                    - Use storytelling elements, customer testimonials, and expert quotes to add depth and length to the content.
                    - Provide historical context, benefits, and detailed steps involved in the service, expanding on each aspect thoroughly.
                    - Incorporate several subheadings within each section to break down information into smaller, digestible sections.
                    - Add additional sections such as case studies, industry trends, comparisons with competitors, and future predictions to increase word count.
                    - Ensure each paragraph explores different aspects of the service in-depth, such as planning, material selection, contractor hiring, and benefits, with multiple examples and detailed explanations.
                    - Add detail and increase word count, but ensure they are formatted with proper HTML tags.
                    
                    Original Content: {$data['article_content']}
                    Locality: {$data['locality']}
                    Tone: {$data['tone']}
                    Keywords: {$data['keywords']}
                    Custom Prompt: {$data['existing_prompt']}

                    Guidelines to adhere:
                    - No plagiarism
                    - Short sentences and concise writing
                    - County and state included once";

            // Process the content generation
            return $this->processContentGeneration($prompt, $data['num_copies']);
        }
    }

    private function processContentGeneration($prompt, $numCopies)
    {
        $result_array = [];

        for ($i = 0; $i < $numCopies; $i++) {
            try {
                $result = $this->contentGenerationService->generateContent($prompt);
                if (empty($result['error'])) {
                    // Remove unnecessary newlines and format content
                    $cleanResponse = $this->removeUnnecessaryNewlines($result['content']);
                    array_push($result_array, $cleanResponse);
                } else {
                    array_push($result_array, "Error in generating content: " . $result['error']);
                }
            } catch (\Exception $e) {
                Log::error('Error generating content: ' . $e->getMessage());
                array_push($result_array, "Error in generating content: " . $e->getMessage());
            }
        }

        return $result_array;
    }


    private function removeUnnecessaryNewlines($content) {
        // Replace multiple newlines with a single newline
        $content = preg_replace('/\n+/', '<br>', $content);

        // Avoid adding <br> tags within specific tags such as <ul>, <ol>, <li>
        $content = preg_replace_callback('/(<ul>.*?<\/ul>|<ol>.*?<\/ol>|<li>.*?<\/li>)/s', function($matches) {
            // Remove all <br> tags and newlines within these tags
            return str_replace(["\n", "<br>"], '', $matches[0]);
        }, $content);

        // Ensure no more than one <br> tag in a row
        $content = preg_replace('/(<br>\s*)+/', '<br>', $content);

        // Optionally, remove leading and trailing whitespace
        $content = trim($content);

        return $content;
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'article_content' => 'required|string',
            'keywords' => 'nullable|string',
            'locality' => 'nullable|string',
            'tone' => 'required|string',
            'existing_prompt' => 'nullable|string',
            'num_copies' => 'required|int',
            'use_html' => 'string',
            'min_words_count' => 'required|int|min:1',
            'max_words_count' => 'nullable|int',
            'type' => 'nullable|string',
            'number_of_paragraphs' => [
                'nullable',
                'integer',
                Rule::requiredIf($request->input('type') === 'blog'),
            ],
        ]);

        try {
            $result = $this->generateContent($data);
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error in generate method: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating content: ' . $e->getMessage()], 500);
        }
    }

    public function showForm()
    {

        $breadcrumbs = [
            ['name' => 'Creation', 'url' => route('creation.index')],
            ['name' => 'Generate Content', 'url' => null] // Current page
        ];


        return view('creation.generate-content.index', [
            'breadcrumbs' => $breadcrumbs,
            'article_content' => '',
            'keywords' => '',
            'locality' => '',
            'tone' => 'professional', // Default to 'professional'
            'existing_prompt' => '',
            'num_copies' =>  1, // Default to 1,
            'min_words_count' => 1,
            'max_words_count' => '',
            'number_of_paragraphs' => 1,
        ]);
    }

    public function reviewForm()
    {
        return view('creation.generate-content.review', [
            'input_review' => '',
            'keywords' => '',
            'num_reviews' => 1,
            'custom_prompt' => ''
        ]);
    }

    public function generateReviews(Request $request)
    {
        try {
            $tone = 'positive';

            $validated = $request->validate([
                'num_reviews' => 'required|integer|min:1|max:20',
                'input_review' => 'required|string',
                'keywords' => 'nullable|string',
                'custom_prompt' => 'nullable|string',
            ]);

            $numberOfReviews = (int) $validated['num_reviews'];
            $inputReview = $validated['input_review'];
            $keywords = $validated['keywords'];
            $customPrompt = $validated['custom_prompt'];
            $result_array = [];


            // Ensure keywords are separated by commas
            if (!empty($keywords)) {
                $keywordsArray = array_map('trim', explode(',', $keywords));
                $formattedKeywords = implode(', ', $keywordsArray);
            } else {
                $formattedKeywords = '';
            }

            for ($i = 0; $i < $numberOfReviews; $i++) {
                $prompt = "Generate a unique review in a {$tone} tone based on the following input review: \"{$inputReview}\".";

                if (!empty($formattedKeywords)) {
                    $prompt .= " Use the following keywords: {$formattedKeywords}.";
                }

                if (!empty($customPrompt)) {
                    $prompt .= " Additional instructions: {$customPrompt}.";
                }

                $prompt .= " Use Only English.";

                // Old Implementation
                $response = Gemini::geminiPro()
                    ->generateContent($prompt);
                // $response = Gemini::generateText($prompt);
                array_push($result_array, $response->text());

                // New Implementation
                // $result = $this->contentGenerationService->generateContent($prompt);
                // if (empty($result['error'])) {
                //     array_push($result_array, $result['content']);
                // } else {
                //     return response()->json(['error' => "Error in generating reviews: " . $result['error']], 500);
                // }
            }

            return response()->json($result_array);
        } catch (\Exception $e) {
            // Log the exception with more details
            Log::error('Error generating reviews:', ['exception' => $e, 'request' => $request->all()]);

            return response()->json(['error' => 'An error occurred while generating reviews.'], 500);
        }
    }

    public function generateNeighborhoods(Request $request)
    {
        try {

            $validated = $request->validate([
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:100',
                'num_neighborhoods' => 'required|integer|min:1|max:20',
            ]);

            $numberOfNeighborhoods = (int) $validated['num_neighborhoods'];
            $city = $validated['city'];
            $state = $validated['state'];
            $result_array = [];

            $prompt = "Using only English. List the names of {$numberOfNeighborhoods} unique neighborhoods in {$city}, {$state}.";

            try {
                // Old Implementation
                $response = Gemini::geminiPro()
                            ->generateContent($prompt);
                // $response = Gemini::generateText($prompt);
                $neighborhoods = explode("\n", $response->text());
                foreach ($neighborhoods as $neighborhood) {
                    if (trim($neighborhood) !== "") {
                        $result_array[] = $neighborhood;
                    }
                }

                // New Implementation

                // $result = $this->contentGenerationService->generateContent($prompt);
                // if (empty($result['error'])) {
                //     $neighborhoods = explode("\n", $result['content']);
                //     foreach ($neighborhoods as $neighborhood) {
                //         if (trim($neighborhood) !== "") {
                //             $result_array[] = $neighborhood;
                //         }
                //     }
                // } else {
                //     array_push($result_array, "Error in generating content: " . $result['error']);
                // }
            } catch (Exception $e) {
                error_log('Error generating content: ' . $e->getMessage());
                array_push($result_array, "Error in generating content: " . $e->getMessage());
            }

            return response()->json($result_array);
        } catch (\Exception $e) {
            // Log the exception with more details
            Log::error('Error generating neigborhoods:', ['exception' => $e, 'request' => $request->all()]);

            return response()->json(['error' => 'An error occurred while generating neigborhoods.'], 500);
        }
    }

    public function generateNeighborhoodsContent(Request $request)
    {
        try {
            $validated = $request->validate([
                'neighborhoods_list' => 'required|string|max:255',
                'niche' => 'required|string|max:255',
                'num_paragraphs' => 'required|integer|min:1|max:20',
                'keywords' => 'nullable|string'
            ]);

            $numberOfParagraphs = (int) $validated['num_paragraphs'];
            $neighborhoodsList = json_decode($validated['neighborhoods_list'], true);
            $niche = $validated['niche'];
            $keywords = $validated['keywords'];
            $result_array = [];

            foreach ($neighborhoodsList as $neighborhood) {
                $prompt = "Using only English, write {$numberOfParagraphs} full-length paragraph(s) about the neighborhood {$neighborhood} for city {$request->city} and state {$request->state} including the topic {$niche}.";
                if (!empty($keywords)) {
                    $keywordsArray = array_map('trim', explode(',', $keywords));
                    $formattedKeywords = implode(', ', $keywordsArray);
                    $prompt .= " Use the following keywords: {$formattedKeywords}.";
                }

                try {
                    // Old Implementation

                    // $response = Gemini::generateText($prompt);
                    $response = Gemini::geminiPro()
                        ->generateContent($prompt);

                    $result_array[] = [
                        'neighborhood' => $neighborhood,
                        'content' => $response->text()
                    ];

                    // New Implementation
                    // $result = $this->contentGenerationService->generateContent($prompt);
                    // if (empty($result['error'])) {
                    //     $result_array[] = [
                    //         'neighborhood' => $neighborhood,
                    //         'content' => $result['content']
                    //     ];
                    // } else {
                    //     array_push($result_array, "Error in generating content: " . $result['error']);
                    // }
                } catch (Exception $e) {
                    error_log('Error generating content: ' . $e->getMessage());
                    $result_array[] = [
                        'neighborhood' => $neighborhood,
                        'content' => "Error in generating content: " . $e->getMessage()
                    ];
                }
            }

            return response()->json($result_array);
        } catch (\Exception $e) {
            Log::error('Error generating neighborhoods:', ['exception' => $e, 'request' => $request->all()]);
            return response()->json(['error' => 'An error occurred while generating neighborhoods.'], 500);
        }
    }


    public function generatePlaces(Request $request)
    {
        try {

            $validated = $request->validate([
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:100',
            ]);

            $numberOfPlaces = 10;
            $city = $validated['city'];
            $state = $validated['state'];
            $result_array = [];

            $prompt = "Using only English. List only the names of {$numberOfPlaces} unique places to go to in {$city}, {$state}. ";
            $prompt .= "Do not include any markdown or styling.";

            try {
                // Old Implementation
                // $response = Gemini::generateText($prompt);
                $response = Gemini::geminiPro()
                        ->generateContent($prompt);

                \Log::info($response->text());
                $places = explode("\n", $response->text());
                foreach ($places as $place) {
                    if (trim($place) !== "") {
                        $result_array[] = $place;
                    }
                }

                // New implementation

                // $result = $this->contentGenerationService->generateContent($prompt);
                // if (empty($result['error'])) {
                //     \Log::info($response->text());
                //     $places = explode("\n", $response->text());
                //     foreach ($places as $place) {
                //         if (trim($place) !== "") {
                //             $result_array[] = $place;
                //         }
                //     }
                // } else {
                //     array_push($result_array, "Error in generating content: " . $result['error']);
                // }
            } catch (Exception $e) {
                error_log('Error generating places: ' . $e->getMessage());
                array_push($result_array, "Error in generating places: " . $e->getMessage());
            }

            return response()->json($result_array);
        } catch (\Exception $e) {
            // Log the exception with more details
            Log::error('Error generating places:', ['exception' => $e, 'request' => $request->all()]);

            return response()->json(['error' => 'An error occurred while generating places.'], 500);
        }
    }

    public function generateIframe(Request $request)
    {

        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:100',
        ]);

        $origin = $request->input('address') ?? $validated['city'] . ', ' . $validated['state'];
        $prompt = "Get a valid Google embedded map iFrame link for $origin that looks like 'https://www.google.com/maps/embed'. Ensure the link is correctly formatted. Output format: Link Only.";

        $response = Gemini::geminiPro()
            ->generateContent($prompt);
        // Check if the response has the expected structure
        if (isset($response->candidates[0]->content->parts[0]->text)) {
            $embeded_link = trim($response->candidates[0]->content->parts[0]->text);
        } else {
            $embeded_link = '';
        }
        $result_array = [];
        $result_array[] = '<iframe src="' . $embeded_link . '" style="width:100%;height:500px"></iframe>';
        return response()->json($result_array);
    }
}
