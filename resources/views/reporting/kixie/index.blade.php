<x-app-layout>
    <style>
        .sorting:after {
            content: "▲\A▼";
            font-size: 0.5rem;
            white-space: pre;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            padding-left: 2px;
            line-height: 7px;
        }

        .sorting.asc:after {
            content: "▲";
        }

        .sorting.desc:after {
            content: "▼";
        }

        .total-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 8px;
            border-top: 2px solid #000;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            {{ __('ClearVue: Reporting & Analysis') }}
        </h2>
    </x-slot>

    <nav class="bg-white shadow dark:bg-slate-600">
         <div class="mx-auto max-w-8xl px-2 sm:px-4 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex px-2 lg:px-0">
                    <div class="hidden lg:flex lg:space-x-8">
                        <a href="rental-report" class="dark:text-gray-200 inline-flex items-center border-b-2 border-indigo-500 px-1 pt-1 text-sm font-medium text-gray-900 ">Report Summary</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-inner sm:rounded-lg">
                <div class="p-6 dark:text-gray-100">
                    <div class="px-4 sm:px-6 lg:px-8">
                    <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-400">Rental Report Summary</h1>
                            </div>
                        </div>
                        <form action="" method="get">
                            @include('reporting.kixie._filters')
                            <div class="flex flex-col gap-4 mt-4 md:flex-row">
                                <!-- Start Date and End Date fields -->
                                <div class="flex-1">
                                    <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400">Start Date</label>
                                    <div class="mt-2">
                                        <input type="text" name="start_date" id="start_date" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="MM/DD/YYYY" value="{{ $startDate ? $startDate->format('m/d/Y') : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="flex items-center md:mt-10 md:items-start">
                                    <label for="to" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400">To</label>
                                </div>
                                <div class="flex-1">
                                    <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400">End Date</label>
                                    <div class="mt-2">
                                        <input type="text" name="end_date" id="end_date" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="MM/DD/YYYY" value="{{ $endDate ? $endDate->format('m/d/Y') : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="flex mt-6 items-center justify-center">
                                    <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2 dark:bg-slate-600 dark:text-green-500">
                                    <button type="button" id="reset" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-slate-600 dark:text-red-500">Reset</button>
                                </div>
                            </div>
                        </form>

                        <!-- Toast UI Grid Container -->
                        <div id="grid" class="mt-8"></div>

                        <!-- Totals Table -->
                        <table class="total-table">
                            <tr>
                            <td style="width: 130px;">Totals:</td>
                            <td id="total-inbound-calls" style="width: 90px;"></td>
                            <td id="total-outbound-calls" style="width: 100px;"></td>
                            <td id="total-calls" style="width: 120px;"></td>
                            <td id="total-inbound-sms" style="width: 90px;"></td>
                            <td id="total-outbound-sms" style="width: 100px;"></td>
                            <td id="total-sms" style="width: 120px;"></td>
                            <td id="total-IO-out" style="width: 100px;"></td>
                            <td id="total-sales" style="width: 80px;"></td>
                            <td id="total-conversion-rate" style="width: 150px;"></td>
                            <td id="total-amount" style="width: 150px;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://uicdn.toast.com/grid/latest/tui-grid.css" />
    <script src="https://uicdn.toast.com/grid/latest/tui-grid.js"></script>

    <!-- Initialize the grid -->
    <script>
        let grid_data = [];

        document.addEventListener("DOMContentLoaded", function() {
            grid_data = Object.values({!! json_encode($combinedStats) !!});

            const rowHeight = 40; 
            const gridHeight = grid_data.length * rowHeight;

            const grid = new tui.Grid({
                el: document.getElementById('grid'),
                data: grid_data,
                columns: [
                    { header: 'Caller Name', name: 'name', filter: 'select', formatter: function({ value }) {
                                return value ? value : 'Unanswered';
                                }, sortingType: 'desc',sortable: true , width: 130 },
                    { header: 'Calls In', name: 'inbound_calls', filter: 'number',sortingType: 'desc',sortable: true,width: 100  },
                    { header: 'Calls Out', name: 'outbound_calls', filter: 'number',sortingType: 'desc',sortable: true,width: 100  },
                    { header: 'Total Calls', name: 'total_calls', filter: 'number',sortingType: 'desc',sortable: true,width: 120  },
                    { header: 'SMS In', name: 'sms_in', filter: 'number' ,sortingType: 'desc',sortable: true,width: 100 },
                    { header: 'SMS Out', name: 'sms_out', filter: 'number',sortingType: 'desc',sortable: true ,width: 100 },
                    { header: 'SMS Total', name: 'total_sms', filter: 'number',sortingType: 'desc',sortable: true ,width: 120 },
                    { header: 'Io Out', name: 'contract_out', filter: 'number' ,sortingType: 'desc',sortable: true,width: 100 },
                    { header: 'Sales', name: 'contract_in', filter: 'number',sortingType: 'desc',sortable: true ,width: 80 },
                    { header: 'Conversion Rate', name: 'conversion_rate', filter: 'number', formatter: function({ value }) {
                                return value ? parseFloat(value).toFixed(2) + ' %' : '0 %';
                                },sortingType: 'desc',sortable: true ,width: 150},
                    { header: 'Amount', name: 'total_sales_amount', filter: 'number',formatter: function({ value }) {
                                return '$' + value;
                                },sortingType: 'desc',sortable: true ,width: 150 }
                ],
                bodyHeight: gridHeight,
                scrollX: false,
                scrollY: false,
            });

            // Function to calculate totals
            function calculateTotals(data) {
                const totals = {
                    inbound_calls: 0,
                    outbound_calls: 0,
                    total_calls: 0,
                    sms_in: 0,
                    sms_out: 0,
                    total_sms: 0,
                    contract_out: 0,
                    contract_in: 0,
                    conversion_rate: 0,
                    total_sales_amount: 0
                };

                data.forEach(row => {
                    totals.inbound_calls += parseInt(row.inbound_calls) || 0;
                    totals.outbound_calls += parseInt(row.outbound_calls) || 0;
                    totals.total_calls += parseInt(row.total_calls) || 0;
                    totals.sms_in += parseInt(row.sms_in) || 0;
                    totals.sms_out += parseInt(row.sms_out) || 0;
                    totals.total_sms += parseInt(row.total_sms) || 0;
                    totals.contract_out += parseInt(row.contract_out) || 0;
                    totals.contract_in += parseInt(row.contract_in) || 0;
                    totals.total_sales_amount += parseFloat(row.total_sales_amount) || 0;
                    totals.conversion_rate += parseFloat(row.conversion_rate) || 0;
                });

                totals.conversion_rate = totals.conversion_rate / data.length || 0;

                return totals;
            }

            // Function to update totals in the UI
            function updateTotals(data) {
                const totals = calculateTotals(data);

                document.getElementById('total-calls').textContent = totals.total_calls;
                document.getElementById('total-inbound-calls').textContent = totals.inbound_calls;
                document.getElementById('total-outbound-calls').textContent = totals.outbound_calls;
                document.getElementById('total-sms').textContent = totals.total_sms;
                document.getElementById('total-inbound-sms').textContent = totals.sms_in;
                document.getElementById('total-outbound-sms').textContent = totals.sms_out;
                document.getElementById('total-IO-out').textContent = totals.contract_out;
                document.getElementById('total-sales').textContent = totals.contract_in;
                document.getElementById('total-conversion-rate').textContent = totals.conversion_rate.toFixed(2) + ' %';
                document.getElementById('total-amount').textContent = '$' + totals.total_sales_amount.toFixed(2);
            }

            // Call the function initially to update totals
            updateTotals(grid_data);
            grid.on('filter', function() {
                const filteredData = grid.getFilteredData();
                const hasActiveFilters = grid.getFilterState().length > 0;

                        if (hasActiveFilters) {
                    updateTotals(filteredData);  // Recalculate totals based on filtered data
                } else {
                    updateTotals(grid_data);  // Recalculate totals based on original unfiltered data
                }
        });

            // Update totals when the filter button is clicked
            document.getElementById('filter-button').addEventListener('click', function() {
                const filteredData = grid.getFilteredData(); 
                const hasActiveFilters = grid.getFilterState().length > 0;

                    if (hasActiveFilters) {
                        updateTotals(filteredData);
                    } else {
                        updateTotals(grid_data);
                    }
            });
        });

        // Datepicker initialization
        $(function() {
            $("#start_date").datepicker();
            $("#end_date").datepicker();
        });

        // Reset Filter Button Handler
        $(".reset-filter").click(function (e) {
            let currentUrl = window.location.href;
            let columnName = e.target.getAttribute('data-attr');
            window.location.href = removeQueryParameter(currentUrl, columnName);
        });

        // Function to Remove Query Parameter from URL
        function removeQueryParameter(url, param) {
            let urlObj = new URL(url);
            let params = urlObj.searchParams;
            if (param) {
                params.delete(param);
            } else {
                urlObj.search = '';
            }
            return urlObj.toString();
        }
    </script>
</x-app-layout>