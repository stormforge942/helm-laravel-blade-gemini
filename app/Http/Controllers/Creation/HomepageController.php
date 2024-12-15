<?php

namespace App\Http\Controllers\Creation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
// use GeminiAPI\Laravel\Facades\Gemini;
use Gemini\Laravel\Facades\Gemini;

use Corcel\Model\Term;
use Corcel\Model\Taxonomy;
use Corcel\Model\Option;
use Corcel\Model\Attachment;
use Corcel\Model\Meta\PostMeta;

use Illuminate\Support\Str;
use Faker\Factory as Faker;

use App\Http\Controllers\Controller;
use App\Services\WordPressService;
use App\Models\NicheContent;
use App\Models\Site;
use App\Models\WpApiDetail;
use Corcel\Model\Post;
use Illuminate\Support\Facades\Http;

class HomepageController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    public function showForm()
    {
        // Retrieve unique niches from the wordpress_sites table
        $niches = Site::select('niche')
            ->whereNotNull('niche')
            ->where('niche', '!=', '')
            ->orderBy('niche', 'asc')
            ->groupBy('niche')
            ->get();
        // Add an option for empty/null niches
        $niches->prepend((object)['niche' => 'Unspecified']);
        // Return the view with the niches
        return view('creation.generate-content.homepage', compact('niches'));
    }


    public function getHomepageSections(Request $request, $siteId)
    {
        $connectionName = $this->wordPressService->connectToWp($siteId);

        $post = $this->getHomePage($connectionName);

        if (!$post) {
            return response()->json(['error' => 'Homepage not found'], 404);
        }

        $sections = $this->getSections($post, $connectionName);

        return response()->json([
            'postId' => $post->ID,
            'rank_math_focus_keyword' => $post->rank_math_focus_keyword,
            'rank_math_description' => $post->rank_math_description,
            'theme_options' => $post->theme_options,
            'sections' => $sections,
            'site_icon' => $post->logos['site_icon'],
            'site_logo' => $post->logos['site_logo']
        ]);
    }

    private function getHomePage($connectionName)
    {
        $postMeta = new PostMeta();
        $postMeta->setConnection($connectionName);

        // Get the front page settings
        $frontPageMeta = $postMeta->where('meta_key', 'show_on_front')
            ->where('meta_value', 'page')
            ->first();

        $homepage = null;

        if ($frontPageMeta) {
            $pageForFrontMeta = $postMeta->where('meta_key', 'page_on_front')->first();

            if ($pageForFrontMeta) {
                $homepage = Post::on($connectionName)->find($pageForFrontMeta->meta_value);
            }
        }

        if (!$homepage) {
            $homepage = Post::on($connectionName)
                ->where('post_type', 'page')
                ->where(function ($query) {
                    $query->where('post_title', 'Home')
                        ->orWhere('post_title', 'Homepage');
                })
                ->status('publish')
                ->first();
        }

        if ($homepage) {
            $rankMathSeoKeyword = $this->wordPressService->getPostMetaValue($postMeta, $homepage->ID, 'rank_math_focus_keyword');
            $description = $this->wordPressService->getPostMetaValue($postMeta, $homepage->ID, 'rank_math_description');

            // Retrieve theme options
            $themeOptions = $this->wordPressService->getThemeOptions($connectionName);
            $themeOptionsArray = $themeOptions ? @unserialize($themeOptions) : [];
            //  \Log::info("theme options arr: ", $themeOptionsArray);

            $logos = $this->getSiteLogos($connectionName);

            // Attach values to the homepage object
            $homepage->rank_math_seo_keyword = $rankMathSeoKeyword;
            $homepage->rank_math_seo_description = $description;
            $homepage->theme_options = $themeOptionsArray;
            $homepage->logos = $logos;

            return $homepage;
        }

        return null;
    }

    private function getSections(Post $post, $connectionName)
    {
        $sections = [];
        $postMeta = new PostMeta();
        $postMeta->setConnection($connectionName);
        $allMeta = $postMeta->where('post_id', $post->ID)->get()->sortByDesc('meta_id');
        // \Log::info($allMeta);
        $sectionCount = $this->getSectionCount($allMeta);

        for ($i = 0; $i < $sectionCount; $i++) {
            $sectionType = $this->getLatestMetaValue($allMeta, "all_sections_{$i}_section");
            if ($sectionType) {
                $sections[$i] = [
                    'type' => $sectionType,
                    'content' => $this->getSectionContent($allMeta, $i, $sectionType, $connectionName)
                ];
            }
        }

        return $sections;
    }

    private function getSectionCount($allMeta)
    {
        $count = 0;
        while ($this->getLatestMetaValue($allMeta, "all_sections_{$count}_section")) {
            $count++;
        }
        return $count;
    }

    private function getSectionContent($allMeta, $sectionNumber, $sectionType, $connectionName)
    {
        $content = [];

        switch ($sectionType) {
            case 'Top Banner With Content & Button':
                $content['heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_heading");
                $content['subheading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_top_banner_with_content_button_sub_heading");
                $content['button_text'] = $this->unserializeButtonText($this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_button"));
                // $content['image_id'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_image");
                $imageId = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_image");
                $content['image'] = $this->wordPressService->getImageDetails($imageId, $connectionName);  // Fetch image details

                break;
            case 'Content Block':
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_content_block");
                break;
            case 'Content Block Full Width':
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_content_block_full_width");
                break;
            case 'Right Image Left Text With Button & Heading':
                $content['heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_heading");
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_content");
                $content['button_text'] = $this->unserializeButtonText($this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_button"));
                $content['image_id'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_image");
                $content['image_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading");

                $content['image'] = $this->wordPressService->getImageDetails($content['image_id'], $connectionName);
                break;
            case 'Left Image Right Text With Button & Heading':
                $content['heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_heading");
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_content");
                $content['button_text'] = $this->unserializeButtonText($this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_button"));
                $content['image_id'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_image");
                $content['image_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading");
                $content['image'] = $this->wordPressService->getImageDetails($content['image_id'], $connectionName);

                break;
            case 'Text Block With Background Image & Heading':
                $content['heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_heading");
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_content");
                $content['button_text'] = $this->unserializeButtonText($this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_button"));
                $content['button_text_1'] = $this->unserializeButtonText($this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_button"));
                $content['image_id'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_image");
                $content['image_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button");
                $content['image'] = $this->wordPressService->getImageDetails($content['image_id'], $connectionName);

                break;
            case 'Left Image & Right Text With Accordion':
                $content['heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_heading");
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_content");
                $content['image_id'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_image");
                $content['image'] = $this->wordPressService->getImageDetails($content['image_id'], $connectionName);

                break;
            case 'Icon Box Section Without Background':
                $sectionSelector = "all_sections_{$sectionNumber}_icon_box_section_without_backgorund";
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_content");

                $content['nested_boxes'] = $this->getNestedBoxes($allMeta, $sectionSelector, $type = "icon", $connectionName);
                break;
            case 'Image Box Four Columns With Background':
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_section_content");
                $content['image_boxes'] = $this->getImageBoxes($allMeta, $sectionNumber);
                break;
            case 'Image Accordion without background image':
                $sectionSelector = "all_sections_{$sectionNumber}_image_accordion_without_background_image";

                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_content");
                $content['nested_boxes'] = $this->getNestedBoxes($allMeta, $sectionSelector, $type = "accordion", $connectionName);
                break;
            case 'Icon Box Section With Background':
                $sectionSelector = "all_sections_{$sectionNumber}_icon_box_section_with_backgorund";
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_content");
                $content['section_subheading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_sub_heading");

                $content['nested_boxes'] = $this->getNestedBoxes($allMeta, $sectionSelector, $type = "icon", $connectionName);
                break;
            case 'Icon Box Four Columns With Background':
                $sectionSelector = "all_sections_{$sectionNumber}_icon_box_four_columns_with_background";

                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_content");
                $content['section_subheading'] = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_sub_heading");

                $content['nested_boxes'] = $this->getNestedBoxes($allMeta, $sectionSelector, $type = "icon2", $connectionName);
                break;
            case 'Two Blurb Section With Address & Form':
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_two_blurb_section_with_address_form_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_two_blurb_section_with_address_form_section_content");
                break;
            case 'CTA With Background Image & Button':
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_content");
                break;
            case 'Two Third Text on Left and One Thrid Form on Right':
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_two_third_text_on_left_and_one_thrid_form_on_right");
                break;
            case 'Two Side By Side Text Blurb Columns':
                $content['content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_two_side_by_side_text_blurb_columns_section_content");
                $content['content_1'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_two_side_by_side_text_blurb_columns_section_content_1");
                break;
            case 'Three Side By Side Text Blurb Columns':
                $content['section_heading'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_heading");
                $content['section_content'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content");
                $content['section_content_1'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content_1");
                $content['section_content_2'] = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content_2");
                break;
        }

        return $content;
    }

    private function unserializeButtonText($buttonText)
    {
        return @unserialize($buttonText) ?: $buttonText;
    }

    private function extractAltText($htmlContent)
    {
        preg_match('/<img[^>]+alt="([^"]*)"/', $htmlContent, $matches);
        return $matches[1] ?? '';
    }

    private function extractImageSrc($htmlContent)
    {
        preg_match('/<img[^>]+src="([^"]*)"/', $htmlContent, $matches);
        return $matches[1] ?? '';
    }


    private function getImageBoxes($allMeta, $sectionNumber)
    {
        $imageBoxes = [];
        $i = 0;
        while (true) {
            $section_image = $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_image_box_{$i}_section_image");
            if ($section_image === null) {
                break;
            }
            $imageBoxes[] = [
                'section_image' => $section_image,
                'heading_tag' => $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_image_box_{$i}_heading_tag"),
                'section_heading' => $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_image_box_{$i}_section_heading"),
                'section_content' => $this->getLatestMetaValue($allMeta, "all_sections_{$sectionNumber}_image_box_four_columns_with_background_image_box_{$i}_section_content"),
            ];
            $i++;
        }
        return $imageBoxes;
    }

    private function getNestedBoxes($allMeta, $sectionSelector, $type, $connectionName)
    {
        $boxes = [];
        $i = 0;
        while (true) {
            if ($type == "accordion") {
                $section_content = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_accordions_{$i}_section_content");
                $section_heading = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_accordions_{$i}_section_heading");
                $image_id = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_accordions_{$i}_section_image");
            } else if ($type == "icon") {
                $section_content = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_icon_box_{$i}_section_content");
                $section_heading = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_icon_box_{$i}_section_heading");
                $image_id = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_section_icon_box_{$i}_section_image");
            } else if ($type == "icon2") {
                $section_content = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_icon_box_{$i}_section_content");
                $section_heading = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_icon_box_{$i}_section_heading");
                $image_id = $this->getLatestMetaValue($allMeta, "{$sectionSelector}_icon_box_{$i}_section_icon");
            }

            if ($section_content === null) {
                break;
            }

            $image = null;
            if ($image_id) {
                $image = $this->wordPressService->getImageDetails($image_id, $connectionName);
            }

            $boxes[] = [
                'section_content' => $section_content,
                'section_heading' => $section_heading,
                'image' => $image
            ];
            $i++;
        }
        return $boxes;
    }

    private function getLatestMetaValue($allMeta, $metaKey)
    {
        return $allMeta->where('meta_key', $metaKey)->sortByDesc('meta_id')->first()->meta_value ?? null;
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'niche' => 'required|string',
            'tone' => 'required|string|max:255',
            'additionalPrompt' => 'nullable|string',
            'keywords' => 'nullable|string',
            'contentSnippet' => 'nullable|string',
            'minWords' => 'nullable|int',
            'maxWords' => 'nullable|int'
        ]);

        try {
            $results = $this->generateContent($data);
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error in generate method: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating content: ' . $e->getMessage()], 500);
        }
    }


    public function generateContent($data)
    {
        // Check and update 'fencing' keyword to the correct context
        if (!empty($data['keywords']) && stripos($data['keywords'], 'fencing') !== false) {
            $data['keywords'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['keywords']);
        }

        if (!empty($data['contentSnippet']) && stripos($data['contentSnippet'], 'fencing') !== false) {
            $data['contentSnippet'] = str_ireplace('fencing', 'fencing (building a physical fence for homes)', $data['contentSnippet']);
        }

        // Process the keywords and contentSnippet further
        !empty($data['keywords']) ? $formattedKeywords = $this->wordPressService->formatKeywords($data['keywords']) : "";

        $niche = $data['niche'];
        $tone = $data['tone'] ?? 'Professional';
        $additionalPrompt = !empty($data['additionalPrompt']) ? "Additional Instructions: {$data['additionalPrompt']}\n\n" : '';
        $keywords = !empty($formattedKeywords) ? "Keywords to Include: {$formattedKeywords}\n\n" : '';
        $context = !empty($data['contentSnippet']) ? "Content Template: \"{$data['contentSnippet']}\"\n\n" : '';

        $minWords = $data['minWords'];
        $maxWords = $data['maxWords'];

        // Word count requirements
        $wordCountRequirement = '';
        if ($minWords !== null && $maxWords !== null) {
            $wordCountRequirement = "Word Count Requirement: Generate between {$minWords} and {$maxWords} words.\n\n";
        } elseif ($minWords !== null) {
            $wordCountRequirement = "Word Count Requirement: Generate at least {$minWords} words.\n\n";
        } elseif ($maxWords !== null) {
            $wordCountRequirement = "Word Count Requirement: Generate no more than {$maxWords} words.\n\n";
        }

        // Construct the final prompt
        $prompt = <<<EOT
Task: Generate content for a {$niche} WordPress website homepage section.

{$context}{$additionalPrompt}{$keywords}{$wordCountRequirement}Tone: {$tone}

Content Guidelines:
1. Use the provided Content Template as a basis for generating the content. Maintain its structure, flow, and approximate length while adapting it to fit the specific niche and requirements.
2. Aim to keep the generated content within 10% of the Template Word Count (either above or below).
3. Demonstrate expertise, authority, and trustworthiness in the {$niche} field.
4. Write at a 5th-grade reading level while maintaining professionalism.
5. Prioritize clarity and conciseness. Use short sentences and paragraphs.
6. Incorporate passive voice where appropriate, but maintain a balanced, engaging style.
7. Avoid exaggeration and ensure factual accuracy.
8. Never use the word 'nestled' or similar clichés.
9. Naturally integrate the provided keywords without forcing them.

Structural Guidelines:
1. When applicable, organize content using appropriate HTML tags:
    - Main heading: <h2>
    - Subheadings: <h3>, <h4> (as needed)
    - Paragraphs: <p>
    - Unordered lists: <ul> with <li> for each item
    - Ordered lists: <ol> with <li> for each item
    - Tables: <table>, <tr>, <td> (if applicable)
2. Use semantic HTML to enhance structure and accessibility.
3. Do not include any CSS styles or class attributes.

Technical Requirements:
1. Output in clean HTML format without any surrounding markup or Markdown symbols.
2. Use only English language.
3. Do not include any external links or references to other websites.
4. Ensure the content is completely original and free from plagiarism.

Final Check:
1. Verify that all guidelines have been followed.
2. Ensure the content aligns with the provided tone and niche.
3. Confirm that all specified keywords are naturally incorporated.
4. Verify that the generated content follows the structure and style of the provided Content Template while being unique and tailored to the specific requirements.
5. Check that the word count of the generated content is within 10% of the Template Word Count.
EOT;

        try {
            $response = Gemini::geminiPro()
                ->generateContent($prompt);

            return response()->json([
                'success' => true,
                'content' => $response->text()
            ]);
        } catch (Exception $e) {
            Log::error('Error generating content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => "Error in generating content: " . $e->getMessage()
            ], 500);
        }
    }

    public function updateHomepage(Request $request)
    {
        // \Log::info('Request Data:', $request->all());

        DB::enableQueryLog();

        DB::listen(function ($query) {
            \Log::info('SQL Query', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        });

        $sections = $request->input('sections');

        // Check if sections is a JSON string, if so, decode it
        if (is_string($sections)) {
            $sections = json_decode($sections, true);
        }

        $themeOptions = $request->input('theme_options');

        if (is_string($themeOptions)) {
            $themeOptions = json_decode($themeOptions, true);
            // \Log::info('Decoded Theme Options:', ['theme_options' => $themeOptions]);

            $niche = $themeOptions['niche'] ?? null;
            $this->updateNiche($request->input('site'), $niche);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'site' => 'required|integer',
            'sections' => 'required|json',
            'postId' => 'required|string',
            'rankMathKeywords' => 'nullable|string',
            'rankMathDescription' => 'nullable|string',
            'theme_options' => 'nullable|json',
            'company_logo_image_id' => 'nullable|integer',
            'favicon_logo_image_id' => 'nullable|integer',
        ]);
        // \Log::info($validatedData);

        $siteId = $request->input('site');

        $this->wordPressService->setDb($siteId);

        $post = Post::findOrFail($request->input('postId'));

        $metaDescription = $validatedData['rankMathDescription'] ?? "";
        $metaTags = $validatedData['rankMathKeywords'] ?? "";
        $themeOptions = $validatedData['theme_options'];
        $companyLogoImageId = $request->input('company_logo_image_id');
        $faviconLogoImageId = $request->input('favicon_logo_image_id');

        if (!empty($themeOptions)) {
            $result = $this->wordPressService->updateThemeOptions($themeOptions);
            // \Log::info('updateThemeOptions method called', ['result' => $result]);
        }


        if ($companyLogoImageId) {
            Option::updateOrCreate(
                ['option_name' => 'site_logo'],
                ['option_value' => $companyLogoImageId]
            );
        }

        if ($faviconLogoImageId) {
            Option::updateOrCreate(
                ['option_name' => 'site_icon'],
                ['option_value' =>  $faviconLogoImageId]
            );
        }

        try {

            $postData = [
                'post_status' => 'draft',
                'post_type' => 'page',
                'post_author' => '2'
            ];

            DB::beginTransaction();

            // Loop through each section and update the post accordingly
            foreach ($sections as $index => $section) {
                $sectionNumber = $index;
                // \Log::info('Processing section:', ['number' => $sectionNumber, 'type' => $section['type']]);

                switch ($section['type']) {
                    case 'Top Banner With Content & Button':
                        $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_heading", $section['content']['heading']);
                        $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_top_banner_with_content_button_sub_heading", $section['content']['subheading']);

                        if (!empty($section['content']['image_id'])) {
                            $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_image", $section['content']['image_id']);
                        }

                        break;

                    case 'Content Block':
                        $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_content_block", $section['content']['content']);
                        break;

                    case 'Content Block Full Width':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_content_block_full_width",
                            $section['content']['content']
                        );
                        break;

                    case 'Right Image Left Text With Button & Heading':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_heading",
                            $section['content']['heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_content",
                            $section['content']['content']
                        );

                        if (!empty($section['content']['image_id'])) {

                            $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_image", $section['content']['image_id']);
                        }
                        break;

                    case 'Left Image Right Text With Button & Heading':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_heading",
                            $section['content']['heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_content",
                            $section['content']['content']
                        );
                        if (!empty($section['content']['image_id'])) {

                            $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_left_image_right_text_with_button_heading_section_image", $section['content']['image_id']);
                        }
                        break;

                    case 'Text Block With Background Image & Heading':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_heading",
                            $section['content']['heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_content",
                            $section['content']['content']
                        );
                        if (!empty($section['content']['image_id'])) {

                            $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_image", $section['content']['image_id']);
                        }
                        break;
                    case 'Left Image & Right Text With Accordion':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_heading",
                            $section['content']['heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_content",
                            $section['content']['content']
                        );
                        if (!empty($section['content']['image_id'])) {

                            $this->wordPressService->updateOrCreateMeta($post->ID, "all_sections_{$sectionNumber}_left_image_right_text_with_accordion_section_image", $section['content']['image_id']);
                        }
                        break;
                    case 'Icon Box Section Without Background':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_section_without_backgorund_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_section_without_backgorund_section_content",
                            $section['content']['section_content']
                        );

                        foreach ($section['content']['icon_boxes'] as $i => $iconBox) {
                            $this->wordPressService->updateOrCreateMeta(
                                $post->ID,
                                "all_sections_{$sectionNumber}_icon_box_section_without_backgorund_section_icon_box_{$i}_section_content",
                                $iconBox['section_content'] ?? $currentContent['icon_boxes'][$i]['section_content'] ?? null
                            );

                            if (!empty($iconBox['image_id'])) {
                                $this->wordPressService->updateOrCreateMeta(
                                    $post->ID,
                                    "all_sections_{$sectionNumber}_icon_box_section_without_backgorund_section_icon_box_{$i}_section_image",
                                    $iconBox['image_id']
                                );
                            }
                        }
                        break;
                    case 'Icon Box Section With Background':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_section_with_backgorund_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_section_with_backgorund_section_content",
                            $section['content']['section_content']
                        );

                        foreach ($section['content']['icon_boxes'] as $i => $iconBox) {
                            $this->wordPressService->updateOrCreateMeta(
                                $post->ID,
                                "all_sections_{$sectionNumber}_icon_box_section_with_backgorund_section_icon_box_{$i}_section_content",
                                $iconBox['section_content'] ?? $currentContent['icon_boxes'][$i]['section_content'] ?? null
                            );

                            if (!empty($iconBox['image_id'])) {
                                $this->wordPressService->updateOrCreateMeta(
                                    $post->ID,
                                    "all_sections_{$sectionNumber}_icon_box_section_with_backgorund_section_icon_box_{$i}_section_image",
                                    $iconBox['image_id']
                                );
                            }
                        }
                        break;
                    case 'Image Accordion without background image':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_image_accordion_without_background_image_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_image_accordion_without_background_image_section_content",
                            $section['content']['section_content']
                        );

                        foreach ($section['content']['icon_boxes'] as $i => $iconBox) {
                            $this->wordPressService->updateOrCreateMeta(
                                $post->ID,
                                "all_sections_{$sectionNumber}_image_accordion_without_background_image_section_accordions_{$i}_section_content",
                                $iconBox['section_content'] ?? $currentContent['icon_boxes'][$i]['section_content'] ?? null
                            );

                            if (!empty($iconBox['image_id'])) {
                                $this->wordPressService->updateOrCreateMeta(
                                    $post->ID,
                                    "all_sections_{$sectionNumber}_image_accordion_without_background_image_section_accordions_{$i}_section_image",
                                    $iconBox['image_id']
                                );
                            }
                        }
                        break;
                    case 'Image Box Four Columns With Background':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_image_box_four_columns_with_background_section_heading",
                            $section['content']['section_heading']
                        );

                        foreach ($section['content']['image_boxes'] as $i => $imageBox) {
                            $this->wordPressService->updateOrCreateMeta(
                                $post->ID,
                                "all_sections_{$sectionNumber}_image_box_four_columns_with_background_image_box_{$i}_section_content",
                                $imageBox['section_content'] ?? $currentContent['image_boxes'][$i]['section_content'] ?? null
                            );
                        }
                        break;
                    case 'Icon Box Four Columns With Background':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_four_columns_with_background_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_icon_box_four_columns_with_background_section_content",
                            $section['content']['section_content']
                        );

                        foreach ($section['content']['icon_boxes'] as $i => $iconBox) {

                            if (isset($iconBox['section_content'])) {
                                $this->wordPressService->updateOrCreateMeta(
                                    $post->ID,
                                    "all_sections_{$sectionNumber}_icon_box_four_columns_with_background_icon_box_{$i}_section_content",
                                    $iconBox['section_content'] ?? $currentContent['icon_boxes'][$i]['section_content'] ?? null
                                );
                            }

                            if (!empty($iconBox['image_id'])) {

                                $this->wordPressService->updateOrCreateMeta(
                                    $post->ID,
                                    "all_sections_{$sectionNumber}_icon_box_four_columns_with_background_icon_box_{$i}_section_icon",
                                    $iconBox['image_id']
                                );
                            }
                        }
                        break;

                    case 'Two Blurb Section With Address & Form':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_two_blurb_section_with_address_form_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_two_blurb_section_with_address_form_section_content",
                            $section['content']['section_content']
                        );
                        break;
                    case 'CTA With Background Image & Button':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_heading",
                            $section['content']['section_heading']
                        );
                        break;
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_text_block_with_background_image_button_section_content",
                            $section['content']['section_content']
                        );
                        break;
                    case 'Two Third Text on Left and One Third Form on Right': // Changed mispelling of third to match with corrected spelling in frontend
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_two_third_text_on_left_and_one_thrid_form_on_right",
                            $section['content']['content']
                        );
                        break;
                    case 'Two Side By Side Text Blurb Columns':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_two_side_by_side_text_blurb_columns_section_content",
                            $section['content']['content_left']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_two_side_by_side_text_blurb_columns_section_content_1",
                            $section['content']['content_right']
                        );
                        break;
                    case 'Three Side By Side Text Blurb Columns':
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_heading",
                            $section['content']['section_heading']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content",
                            $section['content']['section_content']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content_1",
                            $section['content']['section_content_1']
                        );
                        $this->wordPressService->updateOrCreateMeta(
                            $post->ID,
                            "all_sections_{$sectionNumber}_three_side_by_side_text_blurb_columns_section_content_2",
                            $section['content']['section_content_2']
                        );
                        break;
                    default:
                        throw new \Exception("Unknown section type: " . $section['type']);
                }
                // \Log::info('Current queries:', DB::getQueryLog());
            }

            // \Log::info('Committing Transaction');
            DB::commit();

            $this->wordPressService->updateRankMathMetaDescription($post->ID, $metaDescription);
            $this->wordPressService->updateRankMathMetaTags($post->ID, $metaTags);


            return response()->json(['message' => 'Homepage updated successfully'], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Error in updateHomepage:', [
                'error' => $e->getMessage(),
                'section_number' => $sectionNumber ?? 'N/A',
                'section_type' => $section['type'] ?? 'N/A',
                'stack_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function updateNiche($siteId, $niche)
    {
        $site = Site::find($siteId);

        if ($site) {
            if ($niche === null || $niche === '' || strtolower($niche) === 'n/a') {
                $site->niche = null;
            } else {
                $site->niche = $niche;
            }
            $site->save();
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'siteId' => 'required|exists:wordpress_sites,id',
            'title' => 'nullable|string|max:255',
            'alt' => 'nullable|string',
            'section' => 'required|string', // Section type
            'sectionNumber' => 'required|integer', // Section number
        ]);

        $siteId = $request->input('siteId');
        $file = $request->file('file');
        $title = $request->input('title');
        $alt = $request->input('alt');
        $sectionType = $request->input('section');
        $sectionNumber = $request->input('sectionNumber');

        $wpSite = Site::findOrFail($siteId);
        $wpDetails = WpApiDetail::where('site_url', $wpSite->site_url)
            ->where('server', $wpSite->server)
            ->first();

        $username = decrypt($wpDetails->username);
        $password = decrypt($wpDetails->application_password);

        // Upload file to WordPress using REST API
        $url = $wpDetails->site_url . '/wp-json/wp/v2/media';
        $fileName = $file->getClientOriginalName();
        $filePath = $file->getPathname();
        $fileType = $file->getMimeType();

        $response = Http::withBasicAuth($username, $password)
            ->attach('file', file_get_contents($filePath), $fileName)
            ->post($url, [
                'title' => $title,
                'alt_text' => $alt,
            ]);

        if ($response->successful()) {
            $mediaDetails = $response->json();
            $mediaId = $mediaDetails['id'];
            $mediaUrl = $mediaDetails['source_url'];

            // Retrieve meta key based on section type and number
            $metaKey = $this->getMetaKeyForSection($sectionType, $sectionNumber);

            // Compose the content to insert
            $htmlContent = "<img class=\"alignnone wp-image-{$mediaId} size-full\" src=\"{$mediaUrl}\" alt=\"{$alt}\" />";

            // Store the image details in WordPress as postmeta
            $postmetaData = [
                'meta_key' => $metaKey,
                'meta_value' => $htmlContent
            ];

            $metaResponse = Http::withBasicAuth($username, $password)
                ->post($wpDetails->site_url . '/wp-json/wp/v2/posts/' . $post_id . '/meta', $postmetaData);

            if ($metaResponse->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $metaResponse->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store image metadata in WordPress'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file to WordPress'
            ]);
        }
    }

    private function getMetaKeyForSection($sectionType, $sectionNumber)
    {
        switch ($sectionType) {
            case 'Top Banner With Content & Button':
                return "all_sections_{$sectionNumber}_top_banner_with_content_button_banner_image";
            case 'Right Image Left Text With Button & Heading':
                return "all_sections_{$sectionNumber}_right_image_left_text_with_button_heading_section_image";
                // Add cases for other section types based on your mapping
            default:
                throw new \Exception("Unknown section type: {$sectionType}");
        }
    }


    private function getSiteLogos($connectionName)
    {
        $siteIconId = Option::on($connectionName)->where('option_name', 'site_icon')->value('option_value');
        $siteLogoId = Option::on($connectionName)->where('option_name', 'site_logo')->value('option_value');

        // Fetch the image details for both site_icon and site_logo
        $siteIconDetails = $this->wordPressService->getImageDetails($siteIconId, $connectionName);
        $siteLogoDetails = $this->wordPressService->getImageDetails($siteLogoId, $connectionName);

        return [
            'site_icon' => $siteIconDetails,
            'site_logo' => $siteLogoDetails,
        ];
    }

    public function getAllForms()
    {
        $forms = DB::table('forms')->select('id', 'name')->get();
        return response()->json($forms);
    }

    public function getFormData($id)
    {
        $form = DB::table('forms')->where('id', $id)->first(['header_code', 'body_js', 'form_code']);
        return response()->json($form);
    }
}
