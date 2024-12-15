<div class="container">
    <div class="form-area">
        <form class="content-generation-form" id="content-generation-form" method="POST">
            @csrf
            <input type="hidden" value="blog" name="type">
            <div>
                <label for="article_content2">Article Content:</label>
                <div class="field mb-4" style="margin-bottom:15px;">
                    <textarea id="article_content2" name="article_content"style="display:none;"></textarea>
                    <div id="quill-content2"></div>
                </div>
            </div>
            <div class="field-container">
                <label for="keywords">Keywords: (put keywords in quotes)</label>
                <div class="field">
                    <input type="text" id="keywords" name="keywords" value="{{ $keywords }}">
                </div>
            </div>
            <div class="field-container">
                <label for="locality">Locality:</label>
                <div class="field">
                    <input type="text" id="locality" name="locality" value="{{ $locality }}">
                </div>
            </div>
            <div class="field-container">
                <label for="tone">Tone:</label>
                <div class="field">
                    <select name="tone">
                        <option value="professional" {{ $tone == 'professional' ? 'selected' : '' }}>
                            Professional
                        </option>
                        <option value="friendly" {{ $tone == 'friendly' ? 'selected' : '' }}>Friendly
                        </option>
                        <option value="authoritative" {{ $tone == 'authoritative' ? 'selected' : '' }}>
                            Authoritative
                        </option>
                        <option value="informative" {{ $tone == 'informative' ? 'selected' : '' }}>
                            Informative
                        </option>
                        <option value="persuasive" {{ $tone == 'persuasive' ? 'selected' : '' }}>Persuasive
                        </option>
                    </select>
                </div>
            </div>
            <div class="field-container">
                <label for="existing_prompt">Additional prompt instructions:</label>
                <div class="field">
                    <input type="text" id="existing_prompt" name="existing_prompt" value="{{ $existing_prompt }}">
                </div>
            </div>
            <div class="field-container">
                <label for="num_copies">Number of Copies:</label>
                <div class="field">
                    <input type="number" id="num_copies" name="num_copies" value="{{ $num_copies }}">
                </div>
            </div>
            <div class="field-container">
                <label for="min_words_count">Minimum words count:</label>
                <div class="field">
                    <input type="number" id="min_words_count" min="1" name="min_words_count"
                        value="{{ $min_words_count }}">
                </div>
            </div>
            <div class="field-container">
                <label for="max_words_count">Maximum words count:</label>
                <div class="field">
                    <input type="number" id="max_words_count" max="1" name="max_words_count"
                        value="{{ $max_words_count }}">
                </div>
            </div>
            <div class="field-container">
                <div></div>
                <div class="field">
                    <input type="checkbox" id="use-html" name="use_html" value="YES">
                    <label for="vehicle1"> Use HTML</label>
                </div>
            </div>
            <div class="flex items-center justify-between mb-4 button-container field-container" id="button-container">
                <x-primary-button class="ms-4 bg-blue" type="submit" id="generate-content-btn">
                    Generate Content
                </x-primary-button>
                <button id="download-button"
                    class="px-4 py-2 bg-gray-500 rounded-md font-semibold text-xs text-white cursor-auto uppercase tracking-widest"
                    disabled="disabled">Download Content</button>
            </div>
        </form>
    </div>
    <div class="content-area">
        <h2>Generated Content:</h2>
        <div id="generated-content2"></div>
    </div>
</div>
