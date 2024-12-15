<?php

namespace App\Http\Controllers;
use App\Models\Form; 

use Illuminate\Http\Request;
use App\Models\Site;

class FormController extends Controller
{
    // Show the form creation page
    public function index()
    {
        $forms = Form::all();
        $niches = $niches = Site::whereNotNull('niche')
                                ->where('niche', '!=', '')  
                                ->distinct()
                                ->orderBy('niche', 'asc')
                                ->pluck('niche');

        $niches->prepend('Unspecified');
        return view('maintenance.form.index', compact('forms','niches'));
    }

    // Store a new form
    public function store(Request $request)
    {

        //  dd($request->all());
        $request->validate([
            'name' => 'required|unique:forms',
            'form_code' => 'required',
            'header_code' => 'nullable',
            'body_js' => 'nullable', 
            'niche' => 'required', 
        ]);

        Form::create([
            'name' => $request->name,
            'header_code' => $request->header_code,
            'body_js' => $request->body_js,
            'form_code' => $request->form_code,
            'niche' => $request->niche, 
        ]);
        
        return redirect()->back()->with('success', 'Form created successfully');
       
    }

    // Show form data for editing
    public function edit($id)
    {
        $form = Form::findOrFail($id);
        return response()->json($form);
    }

    // Update the form
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:forms,name,' . $form->id,
            'form_code' => 'required',
            'header_code' => 'nullable',
            'body_js' => 'nullable',
            'niche' => 'required',
        ]);

        $form->update([
            'name' => $request->name,
            'header_code' => $request->header_code,
            'body_js' => $request->body_js,
            'form_code' => $request->form_code,
            'niche' => $request->niche, 
        ]);

        return redirect()->back()->with('success', 'Form updated successfully')
                                ->with('warning', 'You will need to republish this form for it to take effect');
    }


    // check if the form name already exists
    public function checkFormName(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

       
        $exists = Form::where('name', $request->name)->exists();

        if ($exists) {
            return response()->json(['exists' => true], 200);
        } else {
            return response()->json(['exists' => false], 200);
        }
    }

    public function getFormsByNiche(Request $request)
{
    // Validate that a niche is provided
    $request->validate([
        'niche' => 'required',
    ]);

    // Get the forms that match the selected niche
    $forms = Form::where('niche', $request->niche)->get();

    // Return the forms as a JSON response
    return response()->json($forms);
}

}
