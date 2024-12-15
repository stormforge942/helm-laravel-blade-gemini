<?php

namespace App\Http\Controllers\BulkContent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
// use GeminiAPI\Laravel\Facades\Gemini;
use Gemini\Laravel\Facades\Gemini;

use Illuminate\Support\Str;
use Faker\Factory as Faker;
use GuzzleHttp\Client;

use App\Http\Controllers\Controller;
use App\Services\WordPressService;
use App\Models\NicheContent;
use App\Models\Site;

use App\Services\ContentGenerationService;
use Gemini\Data\GenerationConfig;

use HTMLPurifier;
use HTMLPurifier_Config;


class BulkContentController extends Controller
{
    protected $wordPressService;
    protected $contentGenerationService;

    public function __construct(WordpressService $wordPressService, ContentGenerationService $contentGenerationService)
    {
        $this->wordPressService = $wordPressService;
        $this->contentGenerationService = $contentGenerationService;
    }

    public function showForm()
    {
        // Retrieve unique niches from the wordpress_sites table
        $niches = Site::select('niche')
            ->distinct()
            ->whereNotNull('niche')
            ->where('niche', '!=', '')
            ->where('niche', '!=', 'N/A')
            ->orderBy('niche', 'asc')
            ->get();

        // Return the view with the niches
        return view('creation.generate-content.bulk.index', compact('niches'));
    }

    public function generateContent(array $data)
    {
        $google_api_key = env('GOOGLE_API_KEY');
        $model = 'models/gemini-1.5-flash-latest';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/$model:generateContent?key=$google_api_key";

        if (stripos($data['keywords'], 'fencing') !== false || stripos($data['template_content'], 'fencing') !== false) {
            // Modify keywords and template content to focus on physical fence building
            $data['keywords'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['keywords']);
            $data['template_content'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['template_content']);
        }

        // Build the prompt
        $prompt = "Generate a detailed and unique version of the provided commercial services content. Ensure the content is informative, professional, and optimized for reliability and usefulness, while closely resembling the structure of the original content. Follow these **strict** guidelines:

        - Retain the original outline of the content, including headings, subheadings, and sections. Ensure that the number of headings and the order of topics **match the original article exactly**.
        - **Do not add a 'Conclusion' section** unless the original content explicitly contains one. If there is no 'Conclusion' in the original content, the article should simply end at the final provided section.
        - **Do not use the words 'conclusion' or 'summary' anywhere in the content** unless they appear in the original text. Do not imply a summary or closing section.
        - Integrate the target keywords naturally within the first paragraph.
        - Ensure the writing is clear, easy to understand (suitable for a 5th-grade reading level), and conveys experience, expertise, authority, and trust.
        - Expand each section thoroughly. Provide additional examples, context, and explanation in each part. Be as detailed as possible and aim for **at least 2000 words**. The content should be between 2000 and 3000 words.
        - Output format: HTML (with appropriate use of tags <h2>, <h3>, <p>, <ul>, <li>, etc.).
        - Do not use markdown syntax in the output.
        - Create a unique, plain title for each generated piece of content. Ensure no title is repeated.
        - Use only English. Avoid markdown symbols or external links.
        - Original Content: {$data['article_content']}
        - Tone: {$data['tone']}
        - Keywords: {$data['keywords']}
        - Adhere to guidelines for uniqueness and a professional tone.";

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
                'temperature' => 0.7,
                'maxOutputTokens' => 19000,
                'topP' => 0.9,
                'topK' => 50,
                'stopSequences' => []
            ]
        ];

        $response = $this->postRequest($endpoint, $requestBody);

        if (isset($response['error'])) {
            Log::error('Error in Gemini API response', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorMessage = 'Error decoding JSON response: ' . json_last_error_msg();
            \Log::error($errorMessage, ['response' => $response]);
            return response()->json(['error' => $errorMessage], 500);
        }

        $candidates = $decodedResponse['candidates'] ?? [];
        if (count($candidates) > 0) {
            $contentParts = $candidates[0]['content']['parts'] ?? [];
            $outputText = '';
            foreach ($contentParts as $part) {
                $outputText .= $part['text'];
            }

            list($title, $content) = $this->splitTitleAndContent($outputText);

            $wordCount = str_word_count(strip_tags($content));


            // Structure the response to match what the frontend expects
            $structuredResponse = [
                [
                    'title' => $title,
                    'content' => $content,
                    'word_count' => $wordCount,
                ]
            ];


            return response()->json($structuredResponse);
        }

        // Process and return the response
        return $this->processResponse($response);
    }

    private function postRequest($url, $data, $accessToken = null)
    {
        $headers = ['Content-Type: application/json'];
        if ($accessToken) {
            $headers[] = "Authorization: Bearer $accessToken";
        }

        $attempts = 0;
        $maxAttempts = 3;
        $retryDelay = 2; // seconds

        do {
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
                Log::error("Curl error: $curlError - Retrying... ($attempts)");
                sleep($retryDelay);
            } elseif ($httpCode >= 400) {
                Log::error("HTTP error: $httpCode - Response: $response - Retrying... ($attempts)");
                sleep($retryDelay);
            } else {
                return $response;
            }

            $attempts++;
        } while ($attempts < $maxAttempts);

        return ['error' => 'Request failed after ' . $maxAttempts . ' attempts.'];
    }

    public function generateContentBatch(array $data)
    {
        $batch = Bus::catch([])->dispatch();

        foreach ($data['content_batches'] as $content_batch) {
            $batch->add(new GenerateContentJob($content_batch));
        }

        return $batch->id;
    }

    function sanitizeContent($content)
    {
        // Replace markdown-style bold and italics with proper HTML tags
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content); // Convert **bold** to <strong>bold</strong>
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content); // Convert *italic* to <em>italic</em>

        // Convert markdown-style headings (## Heading) to <h2>Heading</h2>
        $content = preg_replace('/^##\s*(.*?)$/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^###\s*(.*?)$/m', '<h3>$1</h3>', $content);

        // Convert bullet points
        $content = preg_replace('/^\*\s(.*)$/m', '<li>$1</li>', $content); // Convert * Bullet to <li>Bullet</li>

        // Wrap <li> items in <ul> tags
        $content = preg_replace('/(<li>[\s\S]*?<\/li>)/m', '<ul>$1</ul>', $content);

        // Handle paragraphs (double line breaks)
        $content = preg_replace('/\n\s*\n/', '</p><p>', $content);

        // Wrap in <p> tags to ensure valid HTML structure
        return '<p>' . $content . '</p>';
    }


    private function splitTitleAndContent($response)
    {
        // Extract title from <h1> tag
        if (preg_match('/<h1>\s*(.*?)\s*<\/h1>/s', $response, $matches)) {
            $title = trim($matches[1]);
            // Remove the <h1> tag and its content from the response
            $content = preg_replace('/<h1>.*?<\/h1>\s*/s', '', $response, 1);
        } else {
            // If no <h1> tag is found, use the first line as title
            $lines = explode("\n", $response);
            $title = trim(array_shift($lines));
            $content = trim(implode("\n", $lines));
        }

        // Remove any remaining HTML tags from the title
        $title = strip_tags($title);

        // Trim the content
        $content = trim($content);

        return [$title, $content];
    }

    private function removeNestled($text)
    {
        if (!is_string($text)) {
            return $text;
        }
        return preg_replace('/\s*\bnestled\b\s*/i', ' ', $text);
    }


    private function removeUnnecessaryNewlines($content)
    {
        // Replace multiple newlines with a single newline
        $content = preg_replace('/\n+/', '<br>', $content);

        // Avoid adding <br> tags within specific tags such as <ul>, <ol>, <li>
        $content = preg_replace_callback('/(<ul>.*?<\/ul>|<ol>.*?<\/ol>|<li>.*?<\/li>)/s', function ($matches) {
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

        // Validate the incoming request data
        $data = $request->validate([
            'keywords' => 'nullable|string',
            'num_copies' => 'required|int',
            'batch' => 'required|int',
            'topic' => 'nullable|string|max:255',
            'template_content' => 'required|string'
        ]);

        // Further processing
        $data['tone'] = htmlspecialchars($request->input('tone', 'Professional'), ENT_QUOTES, 'UTF-8');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'h2,h3,h4,h5,h6,p,b,i,strong,em,a[href],ul,ol,li,blockquote');
        $purifier = new HTMLPurifier($config);

        $data['article_content'] = $purifier->purify($request->input('template_content'));

        // Call content generation
        try {
            $results = $this->generateContent($data, $request->input('batch'), 5);
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error in generate method: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating content: ' . $e->getMessage()], 500);
        }
    }

    public function getSitesByNiche(Request $request)
    {
        $niche = $request->input('niche');
        $data = $this->getServersAndSites($niche);
        return response()->json($data['sites']);
    }

    public function fetchCategoriesByNiche(Request $request)
    {
        $niche = $request->input('niche');
        $site = Site::where('niche_id', $niche)->first();

        if (!$site) {
            return response()->json(['error' => 'No site found for the selected niche'], 404);
        }

        $this->configureDatabaseConnection($site);

        $categories = \DB::table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'category')
            ->select('wp_terms.name', 'wp_terms.term_id')
            ->get();

        return response()->json($categories);
    }

    private function getServersAndSites($niche = null)
    {
        $user = auth()->user();
        $restrictedSites = $user->restrictedSites;

        if ($restrictedSites->isEmpty()) {
            // User can access all sites
            $query = Site::query();
        } else {
            // User is restricted, so only retrieve allowed sites
            $query = Site::whereIn('id', $restrictedSites->pluck('id'));
        }

        // Apply niche filtering if provided
        if ($niche) {
            if (strcasecmp($niche, 'Unspecified') === 0) {
                $query->where(function ($q) {
                    $q->whereNull('niche')
                        ->orWhere('niche', '')
                        ->orWhereRaw('LOWER(TRIM(niche)) = ?', ['unspecified']);
                });
            } else {
                $query->whereRaw('LOWER(TRIM(niche)) = ?', [strtolower($niche)]);
            }
        }

        // Get servers and sites based on the filtered query
        $servers = $query->pluck('server')->unique();
        $sites = $query->orderBy('site_url', 'asc')->get(['id', 'site_url']);

        return compact('servers', 'sites');
    }

    public function generateHeadlines(Request $request)
    {
        $headlines = $request->input('num_headings');
        $niche = $request->input('keywords');
        $prompt = "
            Generate {$headlines} unique and one-line headings for the topic: {$niche}.
            - Use only English.
            - Ensure the headings are clear, concise, and professional.
            - Avoid any links to other websites.
            - Adhere to the specified number of headings exactly: {$headlines}.
            - Output format: plain text, without any formatting, markdown, or special characters.
            - No plagiarism.";

        $response = Gemini::geminiPro()
            ->generateContent($prompt);


        return json_encode($response->text());
    }

    public function generateTextBasedOnHeadlines(Request $request)
    {
        $index = $request->input('index');
        $topic = $request->input("heading_$index");
        $tone = $request->input("tone_$index");
        $keywords = $request->input('keywords');
        $prompt = "Generate a comprehensive and unique version of the provided commercial services content that is highly informative, reliable, and useful. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:
                    - Include the target keywords in the first paragraph.
                    - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
                    - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
                    - You MUST use every keyword in the content.
                    - You MUST include the topic in the content (if provided).
                    - NEVER include the word 'nestled'.
                    - Use this as a topic {$topic}/Main heading.
                    - Do not use subheadings like introduction etc.
                    - **Do not add a 'Conclusion' section** unless the original content explicitly contains one. If there is no 'Conclusion' in the original content, the article should simply end at the final provided section.
                    - Organize the content using HTML for headings, subheadings, bullet points, and tables where applicable.
                    - Output format: HTML (with appropriate use of tags <h2>, <h3>, <p>, <ul>, <li>, etc.).
                    - **When needs to create bullet points, use <ul> and <li> tags instead of '*' markdown.**
                    - Avoid Markdown symbols, HTML wrapper, and any CSS styles.
                    - Use only English.
                    - Avoid links to other websites.
                    - Tone: {$tone}
                    - Keywords: {$keywords}
                    - Topic: {$topic}
                    - Guidelines to adhere:
                    - No plagiarism
                    - Short sentences and concise writing.";
        $response = Gemini::geminiPro()
            ->generateContent($prompt);

        return json_encode($response->text());
    }


    public function postBlogContent(Request $request)
    {

        $data = $request->validate([
            'content' => 'required|array',
            'sites' => 'required|array',
            'num_posts_per_site' => 'required|array',
            'rank_math_focus_keyword' => 'nullable|string',
            'rank_math_description' => 'nullable|string|max:255',
            'post_category' => 'nullable|array',
            'headings' => 'required|array',
            'num_copies' => 'required|integer',
        ]);

        $postContent = $data['content'];
        $sites = $data['sites'];
        $numPostsPerSite = $data['num_posts_per_site'];
        $categories = $data['post_category'] ?? [];
        $headings = $data['headings'];
        $results = [];
        $currentIndex = 0;

        // Iterate through each site
        foreach ($sites as $siteId) {
            try {

                $connectionName = $this->wordPressService->connectToWp($siteId);
                $numPosts = $numPostsPerSite[$siteId]; // Number of posts to be published on this site

                for ($i = 0; $i < $numPosts; $i++) {
                    if ($currentIndex >= count($postContent)) {
                        // If not enough content available, log the error
                        $numContentAvailable = count($postContent);
                        $numContentNeeded = $numPosts - $numContentAvailable;

                        $results[] = [
                            'site_id' => $siteId,
                            'status' => 'error',
                            'message' => "Not enough content. {$numContentNeeded} more content items needed.",
                        ];
                        break;
                    }

                    $title = strip_tags($headings[$currentIndex]);
                    $title = preg_replace('/[^\w\s]/', '', $title);

                    $postData = [
                        'post_title' => $title,
                        'post_content' => str_replace(['<h1>', '</h1>'], ['<h2>', '</h2>'], $postContent[$currentIndex]), // Replace h1 with h2
                        'post_status' => 'publish',
                        'post_type' => 'post',
                        'post_author' => '2', // Default author (can be adjusted)
                    ];

                    try {

                        $postId = $this->wordPressService->createWpPost($postData, $connectionName);

                        // Attach categories if available and log each category
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $this->wordPressService->checkAndAttachCategory($postId, $category, $connectionName);
                            }
                        }

                        // Update Rank Math SEO meta tags and description if provided and log the updates
                        if ($request->filled('rank_math_focus_keyword')) {
                            $this->wordPressService->updateRankMathMetaTagsWithConn($postId, $data['rank_math_focus_keyword'], $connectionName);
                        }
                        if ($request->filled('rank_math_description')) {
                            $this->wordPressService->updateRankMathMetaDescriptionWithConn($postId, $data['rank_math_description'], $connectionName);
                        }

                        // Log the success and add to results
                        $results[] = [
                            'site_id' => $siteId,
                            'status' => 'success',
                            'message' => "Content posted successfully to $siteId",
                            'content' => $postContent[$currentIndex],
                        ];

                        // Move to the next piece of content and heading
                        $currentIndex++;
                    } catch (\Exception $e) {
                        // Log and return the error for this post
                        \Log::error('Error posting content', [
                            'site_id' => $siteId,
                            'message' => $e->getMessage(),
                        ]);
                        $results[] = [
                            'site_id' => $siteId,
                            'status' => 'error',
                            'message' => "Error posting content to $siteId: " . $e->getMessage(),
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log and return the error if connection fails
                \Log::error('Failed to connect to site', [
                    'site_id' => $siteId,
                    'message' => $e->getMessage(),
                ]);
                $results[] = [
                    'site_id' => $siteId,
                    'status' => 'error',
                    'message' => "Failed to connect to site {$siteId}: " . $e->getMessage(),
                ];
            }
        }

        return response()->json($results);
    }
}
