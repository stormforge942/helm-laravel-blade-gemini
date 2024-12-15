<style>
    .column-3 {
        flex-basis: 33.33%;
        padding: 5px;
    }

    @media only screen and (max-width: 900px) {
        .column-3 {
            flex-basis: 100%;
            text-align: center !important;
            justify-content: center !important;
        }

        .mobile-center {
            justify-content: center !important;
        }

        .mobile-height {
            height: auto;
        }
    }

    .footer-right {
        margin-right: 60px; /* Adjust this value as needed */
    }

    .version-center {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
</style>
<!-- Footer -->
<footer class="bg-white dark:bg-gray-800 shadow mt-8">
    <div class="w-full mx-auto py-4 px-6">
        <!-- Follow Us Section -->
        <div class="flex justify-end mb-2 mobile-center footer-right">
            <div class="flex items-center">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mr-2 ">Follow Us</h3>
                <a href="https://www.linkedin.com/company/localsparksolutions" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-2" aria-label="LinkedIn">
                    <img src="{{ asset('images/linkedin.png') }}" alt="LinkedIn" class="h-5 w-5">
                </a>
                <a href="https://www.facebook.com/profile.php?id=61567146727204" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mr-2" aria-label="Facebook">
                    <img src="{{ asset('images/facebook.png') }}" alt="Facebook" class="h-5 w-5">
                </a>
            </div>
        </div>
        <!-- Main Footer Content -->
        <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm  flex-wrap justify-between">
            <!-- Left Section: Company Info and Links -->
            <div class="flex space-x-4 column-3">
                <span>Local Spark Solutions &trade;</span>
                <a href="https://www.localspark.ai/privacy-policy/" target="_blank" class="hover:text-gray-700 dark:hover:text-gray-300">Privacy Policy</a>
                <a href="https://www.localspark.ai/solutions/Helm/TermsAndConditions" target="_blank" class="hover:text-gray-700 dark:hover:text-gray-300">Terms and Conditions</a>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Credits</a>
            </div>
            <!-- Center Section: Version Info -->
            <div class="version-center">Version 1.1</div>
            <!-- Right Section: Helm Info -->
            <div class="footer-right flex justify-end">Helm by Local Spark {{ date('Y') }} &copy;</div>
        </div>
    </div>
</footer>
