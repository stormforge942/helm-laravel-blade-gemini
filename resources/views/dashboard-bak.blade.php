<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- {{ __('Dashboard') }} --}}
            Welcome, {{ Auth::user()->name }}!
        </h2>
    </x-slot>

    <div class="max-w-8xl mx-auto p-6 lg:p-8">

        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                {{-- Dashboard --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin']))
                    <a href="{{ url('/admin/dashboard') }}"
                        class="scale-100 p-6 bg-sky-950 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center">
                                <svg class="w-[44px] h-[44px] text-orange-200 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-100 dark:text-white">Admin Dashboard</h2>
                        </div>
                    </a>
                @else
                    <a href="{{ url('/dashboard') }}"
                        class="scale-100 p-6 bg-sky-950 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center">
                                <svg class="w-[44px] h-[44px] text-orange-200 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-100 dark:text-white">Dashboard</h2>
                        </div>
                    </a>
                @endif

                {{-- Creation --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin']) ||
                        Auth::user()->hasAnyPermission([
                            'purchase_domain',
                            'create_neighborhoods_maps',
                            'create_rel_zipcode_list',
                            'get_phone_number_link_to_ringba',
                            'create_services_pages',
                            'add_to_lead_gen_portal',
                            'create_logo',
                            'request_gbp',
                            'create_blog_pages',
                            'create_geotag_images',
                            'send_messages_to_affiliates',
                        ]))
                    <a href="{{ url('/creation') }}"
                        class="scale-100 p-6 bg-sky-950 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center">
                                <svg class="w-[44px] h-[44px] text-orange-200 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-100 dark:text-white">Forge <br>Creation &
                                Maintenance</h2>

                        </div>
                    </a>
                @endif

                {{-- Maintenance --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin']) ||
                        Auth::user()->hasAnyPermission([
                            'swap_phone_numbers',
                            'add_neighborhoods_maps_directions',
                            'order_reviews',
                            'order_backlinks',
                            'add_service_pages',
                            'update_content',
                            'respond_to_reviews',
                            'login_to_gbp',
                            'login_to_site_email',
                        ]))
                    <a href="{{ url('/maintenance') }}"
                        class="scale-100 p-6 bg-sky-950 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center">
                                <svg class="w-[36px] h-[36px] text-orange-200 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-100 dark:text-white">Mission Control <br>
                                Maintenance</h2>

                        </div>
                    </a>
                @endif


                {{-- Reporting --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin','reporting','sales_manager']) 
                       )
                    <a href="{{ url('/reporting') }}"
                        class="scale-100 p-6 bg-sky-950 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="h-16 w-16 flex items-center justify-center">
                                <svg class="w-[44px] h-[44px] text-orange-200 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
                                </svg>

                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-100 dark:text-white">ClearVue <br>Reporting
                                & Analysis
                            </h2>

                        </div>
                    </a>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
