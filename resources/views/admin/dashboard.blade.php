<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            Admin Dashboard
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
                        <ul>
                            @foreach ($permissions as $permission)
                                @if ($permission->link)
                                    <li class="flex items-start mb-2 relative pl-8">
                                        <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain" style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                        <a href="{{ $permission->link }}" class="text-gray-600 hover:text-gray-500 dark:text-gray-300 text-lg">
                                            {{ $permission->friendlyName }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                            @if (Auth::user()->hasRole(['administrator', 'super_admin']) || Auth::user()->hasAnyPermission(['news_generator']))
                                <li class="flex items-start mb-2 relative pl-8">
                                    <span class="absolute left-0 top-1 w-6 h-6 bg-no-repeat bg-contain" style="background-image: url('{{ asset('images/bullet-icon.png') }}'); background-size: contain;"></span>
                                    <a href="{{ url('/creation/news-generator') }}"
                                       class="text-gray-600 hover:text-gray-500 dark:text-gray-300 text-lg">
                                        Generate News Stories
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
