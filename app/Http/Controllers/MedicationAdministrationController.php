<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MedicattionAdministration;
use App\Models\MedicationAdministrationDetails;

class MedicationAdministrationController extends Controller
{
    public function index()
    {
        $medication = MedicattionAdministration::orderBy('id', 'desc')->paginate(10);
        return view('medication_administration.index',compact('medication'));
    }
    public function create()
    {
        return view('medication_administration.create', [
            'patients' => Patient::with('user')->get(),
        ]);
    }
    public function store(Request $request)
    {
        Patient::where('id', $request->patient_id)->update([
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'temperature' => $request->temperature,
            'height' => $request->height,
            'weight' => $request->weight,
            'bmi' => $request->bmi,
        ]);

        $form = MedicattionAdministration::create([
            'mr_number' => $request->mr_number,
            'date' => $request->date,
            'time' => $request->time,
            'patient_name' => $request->patient_name,
            'bp' => $request->bp,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'spo_2' => $request->spo_2,
            'note' => $request->note,
        ]);

        if($request->medications){
            foreach ($request->medications as $medication) {
                    MedicationAdministrationDetails::create([
                    'medication_administration_id' => $form->id,
                    'drug_name' => $medication['drug_name'],
                    'dose' => $medication['dose'],
                    'route' => $medication['route'],
                    'staff_name' => $medication['staff_name']
                ]);
            }
        }

        return to_route('medication.index')->with('success', 'Nurse Form created!');
    }
    public function showForm(Request $request)
    {
        $medication = MedicattionAdministration::where('id', $request->formID)->first();

        return view('medication_administration.show', [
            'patients' => Patient::with('user')->where('MR', $medication->mr_number)->first(),
            'medication' => $medication,
            'medication_details' => MedicationAdministrationDetails::where('medication_administration_id', $request->formID)->get(),
        ]);
    }
}
