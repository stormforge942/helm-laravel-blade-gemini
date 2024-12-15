<x-app-layout>
    <style>
        .custom-color {
            color: rgb(74 222 128 / var(--tw-text-opacity)) !important;
        }
    </style>
    <div class="py-12">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
                    {{ __('Namecheap DNS configuration') }}
                </h2>

            </div>

        </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(!empty($failed))
            <x-input-error :messages="$failed" class="mt-2" />
            @endif
            @if(!empty($success))
            <x-input-error :messages="$success" class="mt-2 custom-color" />
            @endif
            <div class="font-semibold text-md dark:text-white text-gray-800 leading-tight mb-4">
                * When entering your domains, please ensure that each domain is on a separate line. And format should only follow these examples:
                <br><br>
                <p>Example.com</p>
                <p>MYWEBSITE.org</p>
                <p>anotherdomain.NET</p>
                <p>domain.com</p>
            </div>
            <form action="{{ route('update-dns') }}" method="POST">
                @csrf
                <div>
                    <x-input-label for="domains" :value="__('Domains')" />
                    <textarea name="domains" id="domains" class="mt-2" style="width:100%;height: 300px;"></textarea>
                    <x-input-error :messages="$errors->get('domains')" class="mt-2 mb-2 color" />
                </div>
                <x-primary-button class="">
                    {{ __('Update DNS') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>