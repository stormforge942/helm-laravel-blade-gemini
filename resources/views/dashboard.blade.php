<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Welcome, {{ Auth::user()->name }}!
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto p-6 lg:p-8 flex justify-center">
        <div class="mt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 sm:grid-cols-2 gap-6 lg:gap-8">

                {{-- Creation --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin', 'creation']))
                <div class="top-btn-cont button-container relative transition-transform duration-250 hover:scale-105">
                    <img src="{{ asset('images/top_puzzle.png') }}" alt="Top Puzzle Piece" class="w-full h-auto">
                    <a href="{{ route('niche') }}" class="top-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">Forge</h2>
                    </a>
                </div>
                @else
                <div class="top-btn-cont button-container relative">
                    <img src="{{ asset('images/top_puzzle.png') }}" alt="Top Puzzle Piece" class="w-full h-auto no-pointer">
                    <div data-popover-target="forge-popover-default" class="top-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">Forge</h2>
                    </div>
                </div>

                <div data-popover id="forge-popover-default" role="tooltip" class="relative z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" data-popper-placement="top">
                    <div class="px-3 py-2">
                        <p>Ask to upgrade your account for access to Forge - our content creation tool!</p>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                @endif

                {{-- Admin --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin']))
                <div class="middle-btn-cont button-container relative transition-transform duration-250 hover:scale-105">
                    <img src="{{ asset('images/middle_puzzle.png') }}" alt="Middle Puzzle Piece" class="w-full h-auto">
                    <a href="{{ url('/admin/dashboard') }}" class="middle-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl sm:text-3xl font-semibold text-slate-200 dark:text-white">Admin</h2>
                    </a>
                </div>
                @else
                <div class="middle-btn-cont button-container relative">
                    <img src="{{ asset('images/middle_puzzle.png') }}" alt="Middle Puzzle Piece" class="w-full h-auto">
                    <div data-popover-target="admin-popover-default" class="middle-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl sm:text-3xl font-semibold text-slate-200 dark:text-white">Admin</h2>
                    </div>
                </div>

                <div data-popover id="admin-popover-default" role="tooltip" class="relative z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" data-popper-placement="top">
                    <div class="px-3 py-2">
                        <p>Ask to upgrade your account for access to admin tools!</p>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                @endif

                {{-- Maintenance --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin', 'maintenance']))
                <div class="left-btn-cont button-container relative transition-transform duration-250 hover:scale-105">
                    <img src="{{ asset('images/left_puzzle.png') }}" alt="Left Puzzle Piece" class="w-full h-auto">
                    <a href="{{ url('/maintenance') }}" class="left-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">Mission<br>Control</h2>
                    </a>
                </div>
                @else
                <div class="left-btn-cont button-container relative ">
                    <img src="{{ asset('images/left_puzzle.png') }}" alt="Left Puzzle Piece" class="w-full h-auto no-pointer">
                    <div data-popover-target="mission-control-popover-default" class="left-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">Mission<br>Control</h2>
                    </div>
                </div>

                <div data-popover id="mission-control-popover-default" role="tooltip" class="relative z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" data-popper-placement="top">
                    <div class="px-3 py-2">
                        <p>Ask to upgrade your account for access to Mission Control - our maintenance tools!</p>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                @endif

                {{-- Reporting --}}
                @if (Auth::user()->hasRole(['administrator', 'super_admin', 'reporting', 'sales_manager', 'sales_person', 'leads_manager','partner']))
                <div class="right-btn-cont button-container relative transition-transform duration-250 hover:scale-105">
                    <img src="{{ asset('images/right_puzzle.png') }}" alt="Right Puzzle Piece" class="w-full h-auto">
                    <a href="{{ url('/reporting') }}" class="right-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">ClearVue</h2>
                    </a>
                </div>
                @else
                <div class="right-btn-cont button-container relative">
                    <img src="{{ asset('images/right_puzzle.png') }}" alt="Right Puzzle Piece" class="w-full h-auto no-pointer">
                    <div data-popover-target="clearvue-popover-default" class="right-btn-pos absolute inset-0 flex items-center justify-center">
                        <h2 class="text-2xl lg:text-4xl md:text-4xl font-semibold text-slate-200 dark:text-white">ClearVue</h2>
                    </div>
                </div>

                <div data-popover id="clearvue-popover-default" role="tooltip" class="relative z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800" data-popper-placement="top">
                    <div class="px-3 py-2">
                        <p>Ask to upgrade your account for access to ClearVue - our reporting and sales tools!</p>
                    </div>
                    <div data-popper-arrow></div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-6 lg:p-8">
        <!-- Announcements Section -->
        @if($announcements->isNotEmpty())
            @foreach($announcements as $announcement)
                <div class="mb-8 p-4 bg-blue-100 border border-blue-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800">Announcement</h3>
                    <p class="mt-2 text-gray-700">{{ strip_tags($announcement->content)  }}</p>
                </div>
            @endforeach
        @endif
</x-app-layout>

<style>
    .button-container {
        position: relative;
        display: inline-block;
        width: 75%;
        height: auto;
    }

    .button-container img {
        display: block;
        width: 100%;
        height: auto;
        transition: transform 0.25s ease;
    }

    .button-container:hover img {
        transform: scale(0.9);
    }

    .button-container img.no-hover {
        display: block;
        width: 100%;
        height: auto;
        transition: none;
    }

    .button-container:hover img.no-hover {
        transform: none;
    }

    .button-container .no-pointer {
        pointer-events: none;
    }

    .button-container a {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none;
    }

    .button-container h2 {
        z-index: 10;
        pointer-events: none;
    }

    .top-btn-cont {
        left: 50%;
    }

    .left-btn-cont {
        top: -32%;
        left: 10%;
    }

    .right-btn-cont {
        top: -34%;
        left: -15%;
        scale: 0.96;
    }

    .middle-btn-cont {
        top: 55%;
        left: -55%;
        scale: 1.03;
    }

    .top-btn-pos {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -90%);
    }

    .middle-btn-pos {
        top: 50%;
        left: 50%;
        transform: translate(-56%, -77%);
    }

    .left-btn-pos {
        top: 50%;
        left: 50%;
        transform: translate(-55%, -10%);
    }

    .right-btn-pos {
        top: 60%;
        left: 38%;
    }

    a:hover {
        color: lightgrey;
        text-shadow: -1px -1px 0 gray, 1px -1px 0 gray, -1px 1px 0 gray, 1px 1px 0 gray;
    }

    @media (max-width: 640px) {
        .button-container {
            position: relative;
            display: inline-block;
            width: 50%;
            height: auto;
        }

        .button-container h2 {
            z-index: 10;
            pointer-events: none;
        }

        .top-btn-cont {
            transform: translate(-50%, 0%);
        }

        .top-btn-pos {
            transform: translate(-50%, -67%);
        }

        .left-btn-cont {
            transform: translate(-24%, -120%);
        }

        .left-btn-pos {
            transform: translate(-7px, 7px);
        }

        .right-btn-cont {
            transform: translate(141%, -231%);
        }

        .middle-btn-cont {
            transform: translate(157%, -85%);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popovers = document.querySelectorAll('[data-popover]');
        const triggers = document.querySelectorAll('[data-popover-target]');

        triggers.forEach(trigger => {
            const popoverId = trigger.getAttribute('data-popover-target');
            const popover = document.getElementById(popoverId);

            trigger.addEventListener('mouseenter', () => {
                popover.classList.remove('invisible', 'opacity-0');
                const rect = trigger.getBoundingClientRect();
                popover.style.top = `${rect.top + window.scrollY + rect.height}px`;
                popover.style.left = `${rect.left + window.scrollX}px`;
            });

            trigger.addEventListener('mouseleave', () => {
                popover.classList.add('invisible', 'opacity-0');
            });
        });
    });
</script>