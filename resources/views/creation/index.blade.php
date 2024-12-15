<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('Forge: Creation & Maintenance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="font-semibold text-l dark:text-white text-gray-800 text-lg mb-4">Actions</p>
                    @if ($permissions->isEmpty() || $permissions->every(fn($permission) => empty($permission->link)))
                    <p>Coming soon!</p>
                    @else
                    <div class="flex gap-8"> <!-- Use Flexbox to create two columns -->
                        <ul class="w-1/2">
                            <p class="font-semibold text-l dark:text-white text-gray-800 text-base mb-4">Generate & Publish</p>
                            @if (Auth::user()->hasRole(['administrator', 'super_admin', 'creation']))
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/generate-content/homepage') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Homepages
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/pages/service') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Service Pages
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/posts/blog') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Individual Blogs
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/generate-content/bulk') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create Content for Bulk blogs
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/neighborhoods') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create Neighborhoods pages
                                </a>
                            </li>
                            {{-- <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                      style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                            <a href="{{ url('/creation/neighborhoods/google-poi') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                Google points of interests
                            </a>
                            </li> --}}
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/site-generator') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create Original Webpages Based Off Of Other Pages
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/generate-content') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Generate AI Content for Service pages or Blogs
                                </a>
                            </li>
                            @endif
                            <!-- Add more items here for the left column -->
                        </ul>

                        <!-- Right Column -->
                        <ul class="w-1/2">
                            <p class="font-semibold text-l dark:text-white text-gray-800 text-base mb-4">Update content</p>
                            @if (Auth::user()->hasRole(['administrator', 'super_admin', 'creation']))
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/generate-content/homepage') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Homepages
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/pages/service') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Service Pages
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/posts/blog') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create & Edit Individual Blogs
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/seo-bulk-update') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Meta Tag and Description Bulk Update
                                </a>
                            </li>
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/neighborhoods') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Create Neighborhoods pages
                                </a>
                            </li>
                            {{-- <li class="flex items-start mb-2 relative pl-8">
                                    <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                      style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                            <a href="{{ url('/creation/neighborhoods/google-poi') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                Google points of interests
                            </a>
                            </li> --}}
                            <li class="flex items-start mb-2 relative pl-8">
                                <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain"
                                    style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                <a href="{{ url('/creation/site-generator') }}" class="text-gray-600 hover:text-gray-900 text-lg dark:text-gray-300">
                                    Generate AI Content for Service pages or Blogs
                                </a>
                            </li>
                            @endif
                            <!-- Add more items here for the right column -->
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>