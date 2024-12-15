<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\KixieCallLog;
use App\Models\SalesForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\KixieSmsLogCsv;

class KixieReprotController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $filters = [
            'callerName' => $request->query('caller_name'),
            'inboundCalls'=> $request->query('inbound_calls'),
            'outboundCalls'=> $request->query('outbound_calls'),
            'totalCalls'=> $request->query('total_calls'),
            'smsIn'=>$request->query('sms_in'),
            'smsOut'=>$request->query('sms_out'),
            'totalSms'=>$request->query('total_sms'),
            'contractOut'=> $request->query('contract_out'),
            'contractIn'=> $request->query('contract_in'),
            'conversionRate'=> $request->query('conversion_rate'),
            'totalSalesAmount'=> $request->query('total_sales_amount'),
        ];

        // Sortable Columns Array
        $sortableColumns = ['name','inbound_calls' , 'outbound_calls','total_calls','sms_in','sms_out','total_sms','contract_in','contract_out','total_sms','conversion_rate', 'total_sales_amount'];

        // Get the sorting criteria from the request or default to 'first_name'
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Validate the sorting criteria
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'name';
        }

        // Set Sort Direction
        if (!in_array($sortDirection , ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $callQuery = KixieCallLog::select(
            DB::raw("CONCAT(fname,' ',lname) as fullname"),
            DB::raw('COUNT(*) as total_calls'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw("SUM(case when calltype = 'outgoing' then 1 else 0 end) as outbound_calls"),
            DB::raw("SUM(case when calltype = 'incoming' then 1 else 0 end) as inbound_calls")
        )->groupBy('fullname');

        if ($startDate && \DateTime::createFromFormat('m/d/Y', $startDate)) {
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
        } else {
            $startDate = null;
        }

        if ($endDate && \DateTime::createFromFormat('m/d/Y', $endDate)) {
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();
        } else {
            $endDate = null;
        }


        if ($startDate && $endDate) {
            $callQuery->whereBetween('calldate', [$startDate, $endDate]);
        } elseif ($startDate) {
            $callQuery->where('calldate', '>=', $startDate);
        } elseif ($endDate) {
            $callQuery->where('calldate', '<=', $endDate);
        }

        $callStats = $callQuery->get()->keyBy('fullname');


        // Get sales count, website_rented count, total sales price, contract_in, and contract_out from SalesForm table
        $salesQuery = SalesForm::select(
            'sales_representative',
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('COUNT(website_rented) as total_website_rented'),
            DB::raw("SUM(CASE WHEN status = 'converted' THEN price ELSE 0 END) as total_sales_amount"),
            DB::raw("SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as contract_in"),
            DB::raw("SUM(CASE WHEN status = 'converted' OR status = 'cancelled' OR status IS NULL THEN 1 ELSE 0 END) as contract_out")
        )->groupBy('sales_representative');


        if ($startDate && $endDate) {
            $salesQuery->whereBetween('select_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $salesQuery->where('select_date', '>=', $startDate);
        } elseif ($endDate) {
            $salesQuery->where('select_date', '<=', $endDate);
        }

        $salesStats = $salesQuery->get()->keyBy('sales_representative');


        //Get SMS stats 
        $smsQuery = KixieSmsLogCsv::select(
            DB::raw("CONCAT(first_name,' ',last_name) as fullname"),
            DB::raw('COUNT(*) as total_sms'),
            DB::raw("SUM(CASE WHEN type = 'incoming' THEN 1 ELSE 0 END) AS sms_in"),
            DB::raw("SUM(CASE WHEN type = 'outgoing' THEN 1 ELSE 0 END) AS sms_out")
        )
        ->whereNotNull('first_name')
        ->whereNotNull('last_name')
        ->whereRaw("TRIM(first_name) != ''")
        ->whereRaw("TRIM(last_name) != ''")
        ->groupBy('fullname');


        if ($startDate && $endDate) {
            $smsQuery->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $smsQuery->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $smsQuery->where('date', '<=', $endDate);
        }

        $smsStats = $smsQuery->get()->keyBy('fullname');
        

        // Prepare an empty collection for future data sources
        $combinedStats = collect();
        foreach ($callStats as $name => $call) {
            $totalCalls = $call->total_calls;
            $contractIn = isset($salesStats[$name]) ? $salesStats[$name]->contract_in : 0;

            $combinedStats[$name] = [
                'name' => $name,
                'total_calls' => $totalCalls,
                'total_amount' => $call->total_amount,
                'total_sales' => isset($salesStats[$name]) ? $salesStats[$name]->total_sales : 0,
                'total_website_rented' => isset($salesStats[$name]) ? $salesStats[$name]->total_website_rented : 0,
                'total_sales_amount' => isset($salesStats[$name]) ? $salesStats[$name]->total_sales_amount : 0,
                'outbound_calls' => $call->outbound_calls,
                'inbound_calls' => $call->inbound_calls,
                'total_sms' => isset($smsStats[$name]) ? $smsStats[$name]->total_sms : 0,
                'sms_in' => isset($smsStats[$name]) ? $smsStats[$name]->sms_in : 0,
                'sms_out' => isset($smsStats[$name]) ? $smsStats[$name]->sms_out : 0,
                'contract_in' => $contractIn,
                'contract_out' => isset($salesStats[$name]) ? $salesStats[$name]->contract_out : 0,
                'conversion_rate' => ($totalCalls > 0) ? ($contractIn / $totalCalls) * 100 : 0,
            ];
        }

        // Add sales representatives who made sales but no calls
        foreach ($salesStats as $name => $sales) {
            $totalSMS =isset($smsStats[$name]) ? $smsStats[$name]->total_sms : 0;
            $contractIn = $sales->contract_in ?? 0;

            if (!isset($combinedStats[$name])) {
                $combinedStats[$name] = [
                    'name' => $name,
                    'total_calls' => 0,
                    'total_amount' => 0,
                    'total_sales' => $sales->total_sales ?? 0,
                    'total_website_rented' => $sales->total_website_rented ?? 0,
                    'total_sales_amount' => $sales->total_sales_amount ?? 0,
                    'outbound_calls' => 0,
                    'inbound_calls' => 0,
                    'total_sms' =>$totalSMS,
                    'sms_in' => isset($smsStats[$name]) ? $smsStats[$name]->sms_in : 0,
                    'sms_out' => isset($smsStats[$name]) ? $smsStats[$name]->sms_out : 0,
                    'contract_in' => $contractIn,
                    'contract_out' => $sales->contract_out ?? 0,
                    'conversion_rate' => ($totalSMS > 0) ? ($contractIn / $totalSMS) * 100 : 0,
                ];
            }
        }
        
        foreach ($smsStats as $name => $sms) {
            // Check if the name exists in combinedStats
            if (!$combinedStats->has($name)) {
                // If the name does not exist, add it to combinedStats
                $combinedStats->put($name, [
                    'name' => $name,
                    'total_calls' => 0,
                    'total_amount' => 0,
                    'total_sales' => 0,
                    'total_website_rented' => 0,
                    'total_sales_amount' => 0,
                    'outbound_calls' => 0,
                    'inbound_calls' => 0,
                    'total_sms' => $sms->total_sms,
                    'sms_in' => $sms->sms_in,
                    'sms_out' => $sms->sms_out,
                    'contract_in' => 0,
                    'contract_out' => 0,
                    'conversion_rate' => 0,
                ]);
            }
        }

        $rawData = $combinedStats;
        $combinedStats = $combinedStats->filter(function ($item) use ($filters) {

            $passes = true;
            if (isset($filters['callerName'])) {
                $passes = $passes && stripos($item['name'], $filters['callerName']) !== false;
            }
            if (isset($filters['inboundCalls'])) {
                $passes = $this->applyFilter($passes, $item['inbound_calls'], $filters['inboundCalls']);
            }
            if (isset($filters['outboundCalls'])) {
                $passes = $this->applyFilter($passes, $item['outbound_calls'], $filters['outboundCalls']);
            }
            if (isset($filters['totalCalls'])) {
                $passes = $this->applyFilter($passes, $item['total_calls'], $filters['totalCalls']);
            }
            if (isset($filters['smsIn'])) {
                $passes = $this->applyFilter($passes, $item['sms_in'], $filters['sms_in']);
            }
            if (isset($filters['smsOut'])) {
                $passes = $this->applyFilter($passes, $item['sms_out'], $filters['sms_out']);
            }
            if (isset($filters['totalSms'])) {
                $passes = $this->applyFilter($passes, $item['total_sms'], $filters['total_sms']);
            }
            if (isset($filters['contractOut'])) {
                $passes = $this->applyFilter($passes, $item['contract_out'], $filters['contractOut']);
            }
            if (isset($filters['contractIn'])) {
                $passes = $this->applyFilter($passes, $item['contract_in'], $filters['contractIn']);
            }
            if (isset($filters['conversionRate'])) {
                $passes = $this->applyFilter($passes, $item['conversion_rate'], $filters['conversionRate']);
            }
            if (isset($filters['totalSalesAmount'])) {
                $passes = $this->applyFilter($passes, $item['total_sales_amount'], $filters['totalSalesAmount']);
            }
            return $passes;
        });

        $combinedStats = ($sortDirection == 'asc') ? $combinedStats->sortBy($sortBy) : $combinedStats->sortByDesc($sortBy);
        return view('reporting.kixie.index', compact('combinedStats', 'startDate', 'endDate', 'filters', 'sortBy', 'sortDirection','rawData'));

    }

    public function applyFilter($passes, $itemValue, $filterValue) {
        $filterValue = trim($filterValue);
        $operator = substr($filterValue, 0, 1);

        // Check if the operator is a valid sign, otherwise assume '='
        if (!in_array($operator, ['>', '<', '='])) {
            $operator = '=';
            $value = (float)$filterValue; // Parse the entire string as the value
        } else {
            $value = (float)trim(substr($filterValue, 1));
        }

        switch ($operator) {
            case '>':
                return $passes && $itemValue > $value;
            case '<':
                return $passes && $itemValue < $value;
            case '=':
                return $passes && $itemValue == $value;
            default:
                return $passes; // or handle invalid operator scenario
        }
    }

}
