<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('ClearVue: Reporting & Analysis')}}
        </h2>
    </x-slot>

    <x-permission-list :permissions="$permissions"></x-permission-list>
</x-app-layout>
