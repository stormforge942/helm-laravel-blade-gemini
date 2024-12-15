<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\SalesForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesFormController extends Controller
{
    public function index(){
        // Combine and remove duplicates
        $salesRepresentatives = $this->getSalesRepresentative();
        return view('reporting.salesForm', compact('salesRepresentatives'));
    }

    public function store(Request $request){
        $validatedData=$request -> validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email'=> 'required|email|max:255',
                'phone'=> 'required|digits:10|numeric',
                'website_rented'=> 'required|string|max:255',
                'price'=>'required|numeric|min:0',
                'sales_representative'=>'required|string|max:255',
                'originating_lead'=>'required|in:inbound call,text message,outbound call,email blast,other',
                'status' => 'nullable|in:cancelled,converted',
                'select_date' => 'required|date'
            ]);

            $validatedData['phone'] = '+1' . $validatedData['phone'];
        try {
            DB::transaction(function () use ($validatedData) {
                SalesForm::create($validatedData);
            });

            return redirect()->route('sales.form.index')->with('success', 'Sales information added successfully');
            // SalesForm::create($request->all());

        }catch(\Exception $e) {
            return redirect()->route('sales.form.index')->with('error', 'There was an error processing your request.');
        }
    }

    public function edit($id)
    {
        $record = SalesForm::findOrFail($id);
        return response()->json($record);
    }

    public function update(Request $request)
    {
        $record = SalesForm::findOrFail($request->id);
        $record->update($request->all());

        return redirect()->route('pipeline.reporting')->with('success', 'Record updated successfully');
    }

    public function pipelineReporting(Request $request) {

        $salesRepresentatives = $this->getSalesRepresentative();

        // sortable columns
        $sortableColumns = ['first_name', 'last_name', 'email', 'phone', 'website_rented', 'price', 'sales_representative', 'originating_lead', 'status', 'select_date'];

        // Get the sorting criteria from the request or default to 'first_name'
        $sortBy = $request->get('sort_by', 'first_name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Validate the sorting criteria
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'first_name';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Fetch records where status is empty
        $pipelineReports = SalesForm::whereNull('status')->orderBy($sortBy, $sortDirection)->get();

        // Return the view with the data
        return view('reporting.pipelineReporting', compact('pipelineReports', 'sortBy', 'sortDirection', 'salesRepresentatives'));
    }

    public function getSalesRepresentative() {
        $callLogNames = DB::table('kixie_call_logs')
            ->select(DB::raw('DISTINCT CONCAT(fname, " ", lname) AS fullname'))
            ->whereNotNull('fname')
            ->whereNotNull('lname')
            ->pluck('fullname');

        // Fetch unique names from kixie_sms_logs table
        $smsLogNames = DB::table('kixie_sms_logs')
            ->select(DB::raw('DISTINCT CONCAT(contact_firstName, " ", contact_lastName) AS fullname'))
            ->whereNotNull('contact_firstName')
            ->whereNotNull('contact_lastName')
            ->pluck('fullname');

        $salesRepresentatives = $callLogNames->merge($smsLogNames)->unique();
        return $salesRepresentatives;
    }
}
