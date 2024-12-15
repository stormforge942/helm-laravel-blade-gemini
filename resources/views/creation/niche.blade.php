<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('Forge:') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('creation.index') }}" method="GET">
                <div class="row">
                    <div class="col-4 ">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

                            <div class="mb-4 mx-1">
                                <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight mb-2">
                                    {{ __('Welcome to Forge!') }}
                                </h2>

                                <h6 class="font-semibold dark:text-white text-gray-800 leading-tight">
                                    Create or manage your website today.
                                </h6>
                            </div>

                            <div class="mb-4 mx-1">
                                <h2 class="font-semibold mb-2 text-xl dark:text-white text-gray-800 leading-tight">
                                    Industry Selection
                                </h2>
                                <h6 class="font-semibold dark:text-white text-gray-800 leading-tight">
                                    Choose your industry:
                                </h6>
                            </div>

                            <label class="mx-1 dark:text-gray-300 text-gray-800">
                                <input type="radio" name="service" value="home_services" checked>
                                Home Services
                            </label>
                            <label class="mx-1 dark:text-gray-300 text-gray-800">
                                <input type="radio" disabled name="service" value="financial_services">
                                Financial Services (coming soon)
                            </label>
                            <label class="mx-1 dark:text-gray-300 text-gray-800">
                                <input type="radio" disabled name="service" value="real_estate">
                                Real Estate (coming soon)
                            </label>
                            <label class="mx-1 dark:text-gray-300 text-gray-800">
                                <input type="radio" disabled name="service" value="legal">
                                Legal (coming soon)
                            </label>
                        </div>
                        <div class="text-center">
                            <button type="submit"
                                class=" rounded-md bg-[color:var(--cdre-blue)]  px-3 py-2 border border-transparent font-medium text-white btn btn-primary btn-sm mt-4">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

</x-app-layout>
