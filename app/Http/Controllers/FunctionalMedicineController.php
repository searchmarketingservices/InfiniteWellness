<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FunctionalMedicine;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;

class FunctionalMedicineController extends Controller
{
    public function index()
    {
        $FunctionalMedicine = FunctionalMedicine::paginate(10);
        return view("FunctionalMedicine.index", compact("FunctionalMedicine"));
    }

    public function create()
    {
        $patients = Patient::all();
        return view("FunctionalMedicine.create", compact("patients"));
    }

    public function store(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            "patient_id" => "required|exists:patients,id",
            "help" => "nullable|string",
            "life_line" => "nullable|string",
            "food" => "nullable|string",
            "intellectual" => "nullable|string",
            "job_work" => "nullable|string",
            "leisure" => "nullable|string",
            "physical" => "nullable|string",
            "relationship" => "nullable|string",
            "social" => "nullable|string",
            "spritual" => "nullable|string",
            "interpretation" => "nullable|string",
            "examination" => "nullable|string",
            "investigation" => "nullable|string",
            "details" => "required|string",
            "nutrition" => "nullable|string",
            "aerobics" => "nullable|string",
            "balance" => "nullable|string",
            "strength" => "nullable|string",
            "schedule_sleep" => "nullable|string",
            "quality_sleep" => "nullable|string",
            "enivronment_sleep" => "nullable|string",
            "attitude" => "nullable|string",
            "stress" => "nullable|string",
            "social_connection" => "nullable|string",
            "seeking_help" => "nullable|string",
            "alcohol" => "nullable|string",
            "smoking" => "nullable|string",
            "abuse" => "nullable|string",
            "clean" => "nullable|string",
            "safety" => "nullable|string",
            "leisure_activities" => "nullable|string",
            "family" => "nullable|string",
            "social_time" => "nullable|string",
            "time_management" => "nullable|string",
            "intermittent" => "nullable|string",
            "essential_herbs" => "nullable|string",
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the new functional medicine record
        FunctionalMedicine::create($request->all());

        // Flash success message and redirect
        flash('Functional Medicine created successfully')->success();
        return redirect()->route('functional-medicine.index');
    }



    public function show(FunctionalMedicine $functionalMedicine)
    {
        return view('FunctionalMedicine.show', compact('functionalMedicine'));
    }

    public function edit(FunctionalMedicine $functionalMedicine)
    {
        $patients = Patient::all(); // Assuming you have a Patient model
        return view('FunctionalMedicine.edit', compact('functionalMedicine', 'patients'));
    }

    public function update(Request $request, FunctionalMedicine $functionalMedicine)
{
    // Validate the request inputs
    $validator = Validator::make($request->all(), [
        "patient_id" => "required|exists:patients,id",
        "help" => "nullable|string",
        "life_line" => "nullable|string",
        "food" => "nullable|string",
        "intellectual" => "nullable|string",
        "job_work" => "nullable|string",
        "leisure" => "nullable|string",
        "physical" => "nullable|string",
        "relationship" => "nullable|string",
        "social" => "nullable|string",
        "spritual" => "nullable|string",
        "interpretation" => "nullable|string",
        "examination" => "nullable|string",
        "investigation" => "nullable|string",
        "details" => "required|string",
        "nutrition" => "nullable|string",
        "aerobics" => "nullable|string",
        "balance" => "nullable|string",
        "strength" => "nullable|string",
        "schedule_sleep" => "nullable|string",
        "quality_sleep" => "nullable|string",
        "enivronment_sleep" => "nullable|string",
        "attitude" => "nullable|string",
        "stress" => "nullable|string",
        "social_connection" => "nullable|string",
        "seeking_help" => "nullable|string",
        "alcohol" => "nullable|string",
        "smoking" => "nullable|string",
        "abuse" => "nullable|string",
        "clean" => "nullable|string",
        "safety" => "nullable|string",
        "leisure_activities" => "nullable|string",
        "family" => "nullable|string",
        "social_time" => "nullable|string",
        "time_management" => "nullable|string",
        "intermittent" => "nullable|string",
        "essential_herbs" => "nullable|string",
    ]);

    // If validation fails, redirect back with errors
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Update the existing FunctionalMedicine record
    $functionalMedicine->update($request->all());

    // Redirect with success message
    return redirect()->route('functional-medicine.index')->with('success', 'Functional Medicine record updated successfully.');
}



    public function destroy($id)
    {
        $functionalMedicine = FunctionalMedicine::findOrFail($id);

        // Perform additional checks if necessary before deletion

        $functionalMedicine->delete();

        return redirect()->route('functional-medicine.index')
                         ->with('success', 'Functional medicine record deleted successfully.');
    }
}
