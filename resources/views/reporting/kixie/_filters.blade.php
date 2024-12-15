<!-- Dropdown menu [CallerName]-->
<div id="CallerName" class="z-10 hidden bg-white rounded-lg shadow dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <select  name ="caller_name" id="caller_name" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 js-example-basic-single">
                <option value="">-Select-</option>
                @foreach($rawData as $value)
                    @if($value['name'])
                        <option value="{{$value['name']}}" {{($value['name'] == $filters['callerName'] ?"selected":"")}} >{{ empty($value['name']) ? "Unanswered" : $value['name'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="caller_name" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [inboundCalls]-->
<div id="inboundCalls" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="inbound_calls" id="inbound_calls" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Calls In" value="{{ $filters['inboundCalls'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="inbound_calls" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>


<!-- Input menu [outboundCalls]-->
<div id="outboundCalls" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="outbound_calls" id="outbound_calls" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Calls Out" value="{{ $filters['outboundCalls'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="outbound_calls" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [totalCalls]-->
<div id="totalCalls" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="total_calls" id="total_calls" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Total Calls" value="{{ $filters['totalCalls'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="total_calls" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [smsIn]-->
<div id="smsIn" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="sms_in" id="sms_in" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="SMS In" value="{{ $filters['smsIn'] ??'' }}">                    
        </div>
        <div class="flex mt-6 items-center justify-center">
            <button type="button" id="reset" data-attr="sms_in" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [smsOut]-->
<div id="smsOut" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="sms_out" id="sms_out" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="SMS out" value="{{ $filters['smsOut'] ??'' }}">        
        </div>
        <div class="flex mt-6 items-center justify-center">
            <button type="button" id="reset" data-attr="sms_out" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [totalSms]-->
<div id="totalSms" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="total_sms" id="total_sms" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="SMS Total" value="{{ $filters['totalSms'] ??'' }}">
        </div>
        <div class="flex mt-6 items-center justify-center">
            <button type="button" id="reset" data-attr="total_sms" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [contractOut]-->
<div id="contractOut" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="contract_out" id="contract_out" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Io Out" value="{{ $filters['contractOut'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="contract_out" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [contractIn]-->
<div id="contractIn" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="contract_in" id="contract_in" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Sales" value="{{ $filters['contractIn'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="contract_in" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [conversionRate]-->
<div id="conversionRate" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="conversion_rate" id="conversion_rate" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Conversion Rate" value="{{ $filters['conversionRate'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="conversion_rate" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>

<!-- Input menu [totalSalesAmount]-->
<div id="totalSalesAmount" class="z-10 hidden bg-white rounded-lg shadow w-40 dark:bg-gray-700">
    <div class="p-3">
        <div class="relative">
            <input type="text" name="total_sales_amount" id=    "total_sales_amount" class="block w-full rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Amount" value="{{ $filters['totalSalesAmount'] ??'' }}">
        </div>
        <div class="flex mt-4 items-center justify-center">
            <button type="button" id="reset" data-attr="total_sales_amount" class="reset-filter rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-2">Clear</button>
            <input type="submit" id="filter-button" class="rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" value="Filter" />
        </div>
    </div>
</div>



