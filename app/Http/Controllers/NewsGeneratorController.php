<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use League\Csv\Reader;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmCategory;
use Gemini\Enums\ModelType;
use App\Services\ContentGenerationService;

class NewsGeneratorController extends Controller
{
    protected $contentGenerationService;

    public function __construct(ContentGenerationService $contentGenerationService)
    {
        $this->contentGenerationService = $contentGenerationService;
    }

    public function index()
    {
        $user = auth()->user();
        $creationRole = Role::where('name', 'creation')->first();
        $permissions = $creationRole ? $creationRole->permissions : collect();
        return view('creation.generate-news.index', compact('user', 'permissions'));
    }

    public function generate(Request $request)
    {
        try {

            // Check if CSV file is uploaded
            $csvUploaded = $request->hasFile('csv_file');

            // Validate request data
            $validated = $request->validate([
                'csv_file' => 'nullable|file|mimes:csv,txt',
                'base_url' => $csvUploaded ? 'nullable' : 'required_without:csv_file|url',
                'keywords' => $csvUploaded ? 'nullable' : 'required_without:csv_file|string',
                'city' => 'nullable|string',
                'core_focus' => $csvUploaded ? 'nullable' : 'required_without:csv_file|string',
                'first_keyword_frequency' => 'nullable:csv_file|numeric|min:1',
                'second_keyword_frequency' => 'nullable:csv_file|numeric|min:1',
                'third_keyword_frequency' => 'nullable:csv_file|numeric|min:1',
                'additional_keyword_frequency' => 'nullable:csv_file|numeric|min:1',
                'additional_instructions' => 'nullable:csv_file|string',
                'number_of_copies' => 'required|numeric|min:1',
                'min_word_count' => 'required|numeric|min:1',
                'max_word_count' => 'required|numeric|min:1',
                'format_like_site' => 'required|url',
                'voice' => 'required|string'
            ]);

            // Clear previous session data
            $request->session()->forget('generated_articles');
            $request->session()->forget('websites');
            $request->session()->forget('type');

            $articlesByWebsite = [];
            $websites = [];

            if ($csvUploaded) {
                $csv = Reader::createFromPath($request->file('csv_file')->getRealPath(), 'r');
                $csv->setHeaderOffset(0);

                $headers = $csv->getHeader();
                if (!in_array('Website', $headers)) {
                    return response()->json(['error' => 'CSV file is missing the "Website" column. Download the template and try again.'], 400);
                }

                $records = $csv->getRecords();

                foreach ($records as $record) {
                    $website = $record['Website'];
                    $websites[] = $website;
                    $recordValidated = $this->validateCsvRecord($record);
                    $generatedArticles = $this->generateContent($recordValidated);
                    $articlesByWebsite[$website] = $generatedArticles;
                }
                if (!empty($websites)) {
                    $request->session()->put('generated_articles', $articlesByWebsite);
                    $request->session()->put('websites', $websites);
                    $request->session()->put('type', 'csv');
                } else {
                    return response()->json(['error' => 'No websites found in the CSV.'], 400);
                }
            } else {
                // Generate content based on validated data
                $articles = $this->generateContent($validated);
                $websites[] = $validated['base_url'];
                $articlesByWebsite[$validated['base_url']] = $articles;
                $request->session()->put('generated_articles', $articlesByWebsite);
                $request->session()->put('websites', $websites);
                $request->session()->put('type', 'manual');
        }

            // Return the URL to the generated news view
            return response()->json(['redirect_url' => route('generated.news.view')]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error in generate method: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating content: ' . $e->getMessage()], 500);
        }
    }

    private function generateContent($data)
    {
        if ($data['min_word_count'] >= 100 && $data['min_word_count'] <= 500) {
            $data['min_word_count'] += 300;
            $data['max_word_count'] += 300;
        }

        if ($data['min_word_count'] >= 500 && $data['min_word_count'] <= 1000) {
            $data['min_word_count'] += 500;
            $data['max_word_count'] += 500;
        }

        if ($data['min_word_count'] >= 1000 && $data['min_word_count'] <= 2000) {
            $data['min_word_count'] += 1000;
            $data['max_word_count'] += 1000;
        }

        if ($data['min_word_count'] >= 2000 && $data['min_word_count'] <= 3000) {
            $data['min_word_count'] += 1500;
            $data['max_word_count'] += 1500;
        }

        if (stripos($data['keywords'], 'fencing') !== false || stripos($data['core_focus'], 'fencing') !== false) {
            $data['keywords'] = str_replace('fencing', 'fencing (building a physical fence for homes)', $data['keywords']);
            $data['core_focus'] = str_replace('fencing', 'fencing (building a physical fence for homes)', $data['core_focus']);
        }
        // $prompt = "Generate a unique version of the provided commercial services content that is highly informative, reliable, and useful for people in the specified locality. Base this content on the content from the URL {$data['format_like_site']}. The content absolutely must be between {$data['min_word_count']} and {$data['max_word_count']}. Adhere to the required tone, emphasize the given keywords, and ensure the content follows these guidelines:
        //         - **The content must be between {$data['min_word_count']} and {$data['max_word_count']} words, excluding HTML elements. The word count requirement is strict and should be adhered to.**
        //         - Include the target keywords in the first paragraph.
        //         - **Follow the AP style guide.**
        //         - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
        //         - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
        //         - Do not include links to other websites or languages other than English.
        //         - Organize the content using HTML for headings, subheadings, bullet points, numbering, and tables where applicable. Specifically:
        //             - Use <h1> for the main title.
        //             - Use <h2> for section headings.
        //             - Use <p> for paragraphs.
        //             - Avoid any Markdown symbols or HTML wrapper tags.
        //             - Do not include any CSS styles.
        //             - {$data['additional_instructions']}
        //             - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.

        //         - Do not include any bullet points or numbering.  The content should follow AP guidelines and all paragraphs should contain at least 5 sentences.  Rewrite only the content contained on the sample site, do not expand on the content

        //         Output format: Strictly HTML (with appropriate use of tags as mentioned above).

        //         Use Only English. Avoid links to other websites.

        //         Base Site URL: {$data['base_url']}
        //         Format Like Site URL: {$data['format_like_site']}
        //         City: {$data['city']}
        //         Core Focus of the Site: {$data['core_focus']}
        //         Keywords: {$data['keywords']}
        //         First Keyword Frequency: {$data['first_keyword_frequency']}
        //         Second Keyword Frequency: {$data['second_keyword_frequency']}
        //         Third Keyword Frequency: {$data['third_keyword_frequency']}
        //         Additional Keyword Frequency: {$data['additional_keyword_frequency']}
        //         Additional Instructions: {$data['additional_instructions']}
        //         Content Voice: {$data['voice']}";

        // $articles = [];

        // for ($i = 0; $i < $data['number_of_copies']; $i++) {
        //     try {
        //         $result = $this->contentGenerationService->generateContent($prompt);
        //         if (strpos($result, 'Error:') === false) {
        //             $articles[] = $result;
        //         } else {
        //             $articles[] = "Error in generating content: " . $result;
        //         }
        //     } catch (Exception $e) {
        //         Log::error('Error generating content: ' . $e->getMessage());
        //         $articles[] = "Error in generating content: " . $e->getMessage();
        //     }
        // }
        // return $articles;
        $prompt = "Generate a unique version of the provided commercial services content that is highly informative, reliable, and useful for people in the specified locality. Base this content on the content from the URL {$data['format_like_site']}. The content absolutely must be between {$data['min_word_count']} and {$data['max_word_count']} words, excluding HTML elements. The word count requirement is strict and should be adhered to. Ensure the content follows these guidelines:

                    - **The content must be between {$data['min_word_count']} and {$data['max_word_count']} words, excluding HTML elements. The word count requirement is strict and should be adhered to.**
                    - Include the target keywords in the first paragraph.
                    - **Follow the AP style guide.**
                    - Content should be professional, easy to understand (5th grade level), and demonstrate experience, expertise, authority, and trust.
                    - Write using passive voice where applicable, avoid hyperbole, and ensure no grammatical errors.
                    - Do not include links to other websites or languages other than English.
                    - Organize the content using HTML for headings, subheadings, and tables where applicable. Specifically:
                        - Use <h1> for the main title.
                        - Use <h2> for section headings.
                        - Use <p> for paragraphs.
                        - Avoid any Markdown symbols or HTML wrapper tags.
                        - Do not include any CSS styles.
                        - {$data['additional_instructions']}
                        - Do not use asterisks (*) for bullet points or any other purposes. Use proper HTML tags for lists and content organization.

                    - Do not include any bullet points or numbering. The content should follow AP guidelines and all paragraphs should contain at least 5 sentences.  Rewrite only the content contained on the sample site, do not expand on the content

                    - Ensure all paragraphs contain at least 5 sentences, expanding on each point with detailed descriptions, examples, and elaborations to meet the word count requirements.
                    - Use storytelling elements, customer testimonials, and expert quotes to add depth and length to the content.
                    - Provide historical context, benefits, and detailed steps involved in the service, expanding on each aspect thoroughly.
                    - Incorporate several subheadings within each section to break down information into smaller, digestible sections.
                    - Add additional sections such as case studies, industry trends, comparisons with competitors, and future predictions to increase word count.
                    - Ensure each paragraph explores different aspects of the service in-depth, such as planning, material selection, contractor hiring, and benefits, with multiple examples and detailed explanations.
                    - Add detail and increase word count, but ensure they are formatted with proper HTML tags.

                    Output format: Strictly HTML (with appropriate use of tags as mentioned above).

                    Use Only English. Avoid links to other websites.

                    Base Site URL: {$data['base_url']}
                    Format Like Site URL: {$data['format_like_site']}
                    City: {$data['city']}
                    Core Focus of the Site: {$data['core_focus']}
                    Keywords: {$data['keywords']}
                    First Keyword Frequency: {$data['first_keyword_frequency']}
                    Second Keyword Frequency: {$data['second_keyword_frequency']}
                    Third Keyword Frequency: {$data['third_keyword_frequency']}
                    Additional Keyword Frequency: {$data['additional_keyword_frequency']}
                    Additional Instructions: {$data['additional_instructions']}
                    Content Voice: {$data['voice']}";

        $articles = [];

        for ($i = 0; $i < $data['number_of_copies']; $i++) {
            try {
                $result = $this->contentGenerationService->generateContent($prompt);
                if (empty($result['error'])) {
                    $articles[] = $result['content'];
                } else {
                    $articles[] = "Error in generating content: " . $result['error'];
                }
            } catch (Exception $e) {
                Log::error('Error generating content: ' . $e->getMessage());
                $articles[] = "Error in generating content: " . $e->getMessage();
            }
        }
        return $articles;
    }

    private function enforceWordCountAndFormat($text, $minWords, $maxWords, $prompt) {
        $wordCount = str_word_count(strip_tags($text));

        while ($wordCount < $minWords) {
            $additionalText = Gemini::generateText($prompt);
            $text .= " " . $additionalText;
            $wordCount = str_word_count(strip_tags($text));
        }

        if ($wordCount > $maxWords) {
            $words = explode(' ', strip_tags($text));
            $words = array_slice($words, 0, $maxWords);
            $text = implode(' ', $words);
        }

        // Ensure HTML formatting
        $text = nl2br($text); // Convert newlines to <br> tags for proper HTML formatting
        $text = "<p>" . str_replace("\n", "</p><p>", $text) . "</p>"; // Wrap content in <p> tags

        return $text;
    }

    public function viewGeneratedContent(Request $request)
    {
        // Retrieve the generated content from the session or temporary storage
        $articles = $request->session()->get('generated_articles', []);
        $websites = $request->session()->get('websites', 'N/A');
        $type = $request->session()->get('type' , 'N/A');

        // Return the view with the generated content
        return view('generated_site', [
            'articles' => $articles,
            'websites' => $websites,
            'type' => $type
        ]);
    }

    private function validateCsvRecord($record)
    {
        return [
            'base_url' => $record['Website'],
            'city' => $record['City'],
            'keywords' => $record['Keywords'],
            'first_keyword_frequency' => $record['Keyword Frequency One'],
            'second_keyword_frequency' => $record['Keyword Frequency Two'],
            'third_keyword_frequency' => $record['Keyword Frequency Three'],
            'additional_keyword_frequency' => $record['Keyword Frequency Additional'],
            'additional_instructions' => $record['Custom Prompt'],
            'core_focus' => $record['Core Focus'],
            'number_of_copies' => request()->input('number_of_copies'),
            'min_word_count' => request()->input('min_word_count'),
            'max_word_count' => request()->input('max_word_count'),
            'format_like_site' => request()->input('format_like_site'),
            'voice' => request()->input('voice'),
        ];
    }
}
