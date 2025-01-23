<?php

namespace App\Http\Controllers;

use App;
use Flash;
use Exception;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\BedAssign;
use App\Models\Dietitian;
use Illuminate\View\View;
use App\Models\Medication;
use App\Models\Appointment;
use App\Models\BirthReport;
use App\Models\DeathReport;
use App\Models\NursingForm;
use App\Models\PatientCase;
use App\Models\Vaccination;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Exports\PatientExport;
use Illuminate\Support\Carbon;
use App\Models\AdvancedPayment;
use App\Models\OperationReport;
use App\Models\PatientAdmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use App\Models\InvestigationReport;
use App\Models\IpdPatientDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\DietitianRequest;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Repositories\MedicineRepository;
use App\Models\MedicattionAdministration;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Repositories\PatientCaseRepository;
use App\Repositories\AdvancedPaymentRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientController extends AppBaseController
{
    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(PatientRepository $patientRepo)
    {
        $this->patientRepository = $patientRepo;
    }


    /**
     * Display a listing of the Patient.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index()
    {
        $data['statusArr'] = Patient::STATUS_ARR;

        return view('patients.index', $data);
    }

    public function dietitanFormList()
    {
        $data['statusArr'] = Patient::STATUS_ARR;

        return view('patients.dietitan.index', $data);
    }
    /**
     * Show the form for creating a new Patient.
     *
     * @return Factory|View
     */
    public function create()
    {
        $bloodGroup = getBloodGroups();

        return view('patients.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created Patient in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePatientRequest $request)
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['email'] =  null;
        $userID = $this->patientRepository->store($input);
        $this->patientRepository->createNotification($input);
        $data = [
            "case_id" => mb_strtoupper(PatientCase::generateUniqueCaseId()),
            "currency_symbol" => "pkr",
            "patient_id" => $userID,
            "date" => now(),
            "phone" => null,
            "emergencyPhone" => null,
            "prefix_code" => "92",
            "status" => "1",
            "description" => null
        ];

        //---------------------

        // $patientCase = PatientCase::create($data);

        //-------------------


        Flash::success(__('messages.advanced_payment.patient') . ' ' . __('messages.common.saved_successfully'));

        return redirect(route('patients.index'));
    }


    public function storePatientCase($data)
    {
        $input = $data;

        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $caseDate = Carbon::parse($input['date'])->toDateString();
        // if (! empty($birthDate) && $caseDate < $birthDate) {
        //     Flash::error('Case date should not be smaller than patient birth date.');

        //     return redirect()->back()->withInput($input);
        // }


        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['phone'] = preparePhoneNumber($input, 'phone');

        $this->patientCaseRepository->store($input);
        $this->patientCaseRepository->createNotification($input);

        // Flash::success(__('messages.case.case').' '.__('messages.common.saved_successfully'));

        // return redirect(route('patient-cases.index'));
    }

    /**
     * @param  int  $patientId
     * @return Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function show($patientId)
    {
        $data = $this->patientRepository->getPatientAssociatedData($patientId);
        if (!$data) {
            return view('errors.404');
        }
        if (getLoggedinPatient() && checkRecordAccess($data->id)) {
            return view('errors.404');
        } else {
            $advancedPaymentRepo = App::make(AdvancedPaymentRepository::class);
            $patients = $advancedPaymentRepo->getPatients();
            $user = Auth::user();
            if ($user->hasRole('Doctor')) {
                $vaccinationPatients = getPatientsList($user->owner_id);
            } else {
                $vaccinationPatients = Patient::getActivePatientNames();
            }
            $vaccinations = Vaccination::toBase()->pluck('name', 'id')->toArray();
            natcasesort($vaccinations);

            $forms = DB::table('form_type')->where('fileName', '!=', 'preTestForm')->get();
            $currentForm = DB::table('form_patient')->where('formName', '!=', 'Pre-Test Form')->where(['patientID' => $patientId])->get();

            $dietdata = DB::table('dietitianAssessment')->where(['patient_id' => $patientId])->first();

            $patientMR = Patient::where('id', $patientId)->pluck('MR')->first();


            $nursingData = NursingForm::where('patient_mr_number', $patientMR)->with(['patient.user', 'Allergies', 'Medication'])->first();

            return view('patients.show', ['ignore_minify' => true], compact('data', 'patients', 'vaccinations', 'vaccinationPatients', 'forms', 'currentForm', 'dietdata', 'nursingData'));
        }
    }

    public function dietitanShow($patientId)
    {
        $nutrition = DB::table('form_type')->where('fileName', '=', 'Nutritional Assessment Form
        ')->get();
        $forNutritions = DB::table('form_patient')->where('formName','Nutritional Assessment Form')->where('patientID' , $patientId)->orderBy('id', 'desc')->first();
        $data = $this->patientRepository->getPatientAssociatedData($patientId);
        if (!$data) {
            return view('errors.404');
        }
        if (getLoggedinPatient() && checkRecordAccess($data->id)) {
            return view('errors.404');
        } else {
            $advancedPaymentRepo = App::make(AdvancedPaymentRepository::class);
            $patients = $advancedPaymentRepo->getPatients();
            $user = Auth::user();
            if ($user->hasRole('Doctor')) {
                $vaccinationPatients = getPatientsList($user->owner_id);
            } else {
                $vaccinationPatients = Patient::getActivePatientNames();
            }
            $vaccinations = Vaccination::toBase()->pluck('name', 'id')->toArray();
            natcasesort($vaccinations);

            $forms = DB::table('form_type')->get();
            $currentForm = DB::table('form_patient')->where(['patientID' => $patientId])->get();


            $dietdata = DB::table('dietitianAssessment')->where(['patient_id' => $patientId])->first();

            $patientMR = Patient::where('id', $patientId)->pluck('MR')->first();
            $nursingData = NursingForm::where('patient_mr_number', $patientMR)->with(['patient.user', 'Allergies', 'Medication'])->first();

            return view('patients.dietitan.show', compact('data','patientMR', 'patients', 'vaccinations', 'vaccinationPatients', 'forms', 'currentForm', 'dietdata', 'nursingData','forNutritions'));
        }
    }

    public function formSubmit(Request $request)
    {
        $path = '';
        if ($request->hasFile('nutritionalInterventionFile')) {
            $file = $request->file('nutritionalInterventionFile');
            $fileName = 'NutritionalInterventionPlan' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

           $file->move(public_path('storage/Attachments'), $fileName);
           $path =  $fileName;
           $patientIds = DB::table('dietitianAssessment')->pluck('patient_id')->toArray();
           if (in_array($request->patient_id, $patientIds)) {
               // Patient ID already exists, so return a response indicating that it's a duplicate.
               // return response()->json(['message' => 'Patient ID already exists'], 400); // You can use a 400 status code for a bad request or choose an appropriate status code.
               // dd($request);
               $insertedId = DB::table('dietitianAssessment')->where('patient_id', $request->patient_id)->update([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age ?? 0,
                   'weight' => $request->weight ?? 0,
                   'height' => $request->height ?? 0,
                   'bmi' => $request->bmi ?? 0,
                   'ibw' => $request->ibw ?? 0,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory ?? 0,
                   'pastDietaryPattern' => $request->pastDietaryPattern ?? 0,
                   'pastFluidIntake' => $request->pastFluidIntake ?? 0,
                   'foodAllergy' => $request->foodAllergy ?? 0,
                   'activityFactor' => $request->activityFactor ?? 0,
                   'appetite' => $request->appetite ?? 0,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension ?? 0,
                   'Stroke' => $request->Stroke ?? 0,
                   'Cancer' => $request->Cancer ?? 0,
                   'arthritis' => $request->Arthritis ?? 0,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease ?? 0,
                   'copd' => $request->copd ?? 0,
                   'Thyroid' => $request->Thyroid ?? 0,
                   'Asthma' => $request->Asthma ?? 0,
                   'Alzheimer' => $request->Alzheimer ?? 0,
                   'cysticFibrosis' => $request->cysticFibrosis ?? 0,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease ?? 0,
                   'osteoporosis' => $request->osteoporosis ?? 0,
                   'mentalIllness' => $request->mentalIllness ?? 0,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome ?? 0,
                   'Depression' => $request->Depression ?? 0,
                   'multipleSclerosis' => $request->multipleSclerosis ?? 0,
                   'inputEmail3' => $request->inputEmail3 ?? 0,
                   'Breakfast' => $request->Breakfast ?? 0,
                   'Midmorning' => $request->Midmorning ?? 0,
                   'Lunch' => $request->Lunch ?? 0,
                   'Dinner' => $request->Dinner ?? 0,
                   'Regimen' => $request->Regimen ?? 0,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost ?? 0,
                   'Midmorningpost' => $request->Midmorningpost ?? 0,
                   'Lunchpost' => $request->Lunchpost ?? 0,
                   'Dinnerpost' => $request->Dinnerpost ?? 0,
                   'Regimenpost' => $request->Regimenpost ?? 0,
                   'nutritionalInterventionFile' => $path ?? 0,
                   'Protein' => $request->Protein ?? 0,
                   'Carbohydrates' => $request->Carbohydrates ?? 0,
                   'Fat' => $request->Fat ?? 0,
                   'Fluid' => $request->Fluid ?? 0,
                   'Restriction' => $request->Restriction ?? 0,
                   'Proteincalories' => $request->Proteincalories ?? 0,
                   'Carbohydratescalories' => $request->Carbohydratescalories ?? 0,
                   'Fatcalories' => $request->Fatcalories ?? 0,
                   'ProteinNutrients' => $request->ProteinNutrients ?? 0,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients ?? 0,
                   'FatNutrients' => $request->FatNutrients ?? 0,
                   'BasalEnergy' => $request->BasalEnergy ?? 0,
                   'TotalCalories' => $request->TotalCalories ?? 0,
                   'date1' => $request->date1 ?? 0,
                   'time1' => $request->time1 ?? 0,
                   'week1' => $request->week1 ?? 0,
                   'date2' => $request->date2 ?? 0,
                   'time2' => $request->time2 ?? 0,
                   'week2' => $request->week2 ?? 0,
                   'date3' => $request->date3 ?? 0,
                   'time3' => $request->time3 ?? 0,
                   'week3' => $request->week3 ?? 0,
                   'date4' => $request->date4 ?? 0,
                   'time4' => $request->time4 ?? 0,
                   'week4' => $request->week4 ?? 0,
                   'date21' => $request->date21 ?? 0,
                   'time21' => $request->time21 ?? 0,
                   'week21' => $request->week21 ?? 0,
                   'date22' => $request->date22 ?? 0,
                   'time22' => $request->time22 ?? 0,
                   'week22' => $request->week22 ?? 0,
                   'date33' => $request->date33 ?? 0,
                   'time33' => $request->time33 ?? 0,
                   'week33' => $request->week33 ?? 0,
                   'date31' => $request->date31 ?? 0,
                   'time31' => $request->time31 ?? 0,
                   'week31' => $request->week31 ?? 0,
                   'date88' => $request->date88 ?? 0,
                   'time88' => $request->time88 ?? 0,
                   'week88' => $request->week88 ?? 0,
                   'date42' => $request->date42 ?? 0,
                   'time42' => $request->time42 ?? 0,
                   'week42' => $request->week42 ?? 0,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,
               ]);

           } else {
               $insertedId = DB::table('dietitianAssessment')->insertGetId([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age,
                   'weight' => $request->weight,
                   'height' => $request->height,
                   'bmi' => $request->bmi,
                   'ibw' => $request->ibw,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory,
                   'pastDietaryPattern' => $request->pastDietaryPattern,
                   'pastFluidIntake' => $request->pastFluidIntake,
                   'foodAllergy' => $request->foodAllergy,
                   'activityFactor' => $request->activityFactor,
                   'appetite' => $request->appetite,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension,
                   'Stroke' => $request->Stroke,
                   'Cancer' => $request->Cancer,
                   'arthritis' => $request->Arthritis,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease,
                   'copd' => $request->copd,
                   'Thyroid' => $request->Thyroid,
                   'Asthma' => $request->Asthma,
                   'Alzheimer' => $request->Alzheimer,
                   'cysticFibrosis' => $request->cysticFibrosis,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease,
                   'osteoporosis' => $request->osteoporosis,
                   'mentalIllness' => $request->mentalIllness,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome,
                   'Depression' => $request->Depression,
                   'multipleSclerosis' => $request->multipleSclerosis,
                   'inputEmail3' => $request->inputEmail3,
                   'Breakfast' => $request->Breakfast,
                   'Midmorning' => $request->Midmorning,
                   'Lunch' => $request->Lunch,
                   'Dinner' => $request->Dinner,
                   'Regimen' => $request->Regimen,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost,
                   'Midmorningpost' => $request->Midmorningpost,
                   'Lunchpost' => $request->Lunchpost,
                   'Dinnerpost' => $request->Dinnerpost,
                   'Regimenpost' => $request->Regimenpost,
                   'nutritionalInterventionFile' => $path,
                   'Protein' => $request->Protein,
                   'Carbohydrates' => $request->Carbohydrates,
                   'Fat' => $request->Fat,
                   'Fluid' => $request->Fluid,
                   'Restriction' => $request->Restriction,
                   'Proteincalories' => $request->Proteincalories,
                   'Carbohydratescalories' => $request->Carbohydratescalories,
                   'Fatcalories' => $request->Fatcalories,
                   'ProteinNutrients' => $request->ProteinNutrients,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients,
                   'FatNutrients' => $request->FatNutrients,
                   'BasalEnergy' => $request->BasalEnergy,
                   'TotalCalories' => $request->TotalCalories,
                   'date1' => $request->date1,
                   'time1' => $request->time1,
                   'week1' => $request->week1,
                   'date2' => $request->date2,
                   'time2' => $request->time2,
                   'week2' => $request->week2,
                   'date3' => $request->date3,
                   'time3' => $request->time3,
                   'week3' => $request->week3,
                   'date4' => $request->date4,
                   'time4' => $request->time4,
                   'week4' => $request->week4,
                   'date21' => $request->date21,
                   'time21' => $request->time21,
                   'week21' => $request->week21,
                   'date22' => $request->date22,
                   'time22' => $request->time22,
                   'week22' => $request->week22,
                   'date33' => $request->date33,
                   'time33' => $request->time33,
                   'week33' => $request->week33,
                   'date31' => $request->date31,
                   'time31' => $request->time31,
                   'week31' => $request->week31,
                   'date88' => $request->date88,
                   'time88' => $request->time88,
                   'week88' => $request->week88,
                   'date42' => $request->date42,
                   'time42' => $request->time42,
                   'week42' => $request->week42,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,
               ]);

               return response()->json(['message' => 'Success'], 200);

           }
        } else{
            $patientIds = DB::table('dietitianAssessment')->pluck('patient_id')->toArray();
           if (in_array($request->patient_id, $patientIds)) {
               // Patient ID already exists, so return a response indicating that it's a duplicate.
               // return response()->json(['message' => 'Patient ID already exists'], 400); // You can use a 400 status code for a bad request or choose an appropriate status code.
               // dd($request);
               $insertedId = DB::table('dietitianAssessment')->where('patient_id', $request->patient_id)->update([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age ?? 0,
                   'weight' => $request->weight ?? 0,
                   'height' => $request->height ?? 0,
                   'bmi' => $request->bmi ?? 0,
                   'ibw' => $request->ibw ?? 0,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory ?? 0,
                   'pastDietaryPattern' => $request->pastDietaryPattern ?? 0,
                   'pastFluidIntake' => $request->pastFluidIntake ?? 0,
                   'foodAllergy' => $request->foodAllergy ?? 0,
                   'activityFactor' => $request->activityFactor ?? 0,
                   'appetite' => $request->appetite ?? 0,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension ?? 0,
                   'Stroke' => $request->Stroke ?? 0,
                   'Cancer' => $request->Cancer ?? 0,
                   'arthritis' => $request->Arthritis ?? 0,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease ?? 0,
                   'copd' => $request->copd ?? 0,
                   'Thyroid' => $request->Thyroid ?? 0,
                   'Asthma' => $request->Asthma ?? 0,
                   'Alzheimer' => $request->Alzheimer ?? 0,
                   'cysticFibrosis' => $request->cysticFibrosis ?? 0,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease ?? 0,
                   'osteoporosis' => $request->osteoporosis ?? 0,
                   'mentalIllness' => $request->mentalIllness ?? 0,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome ?? 0,
                   'Depression' => $request->Depression ?? 0,
                   'multipleSclerosis' => $request->multipleSclerosis ?? 0,
                   'inputEmail3' => $request->inputEmail3 ?? 0,
                   'Breakfast' => $request->Breakfast ?? 0,
                   'Midmorning' => $request->Midmorning ?? 0,
                   'Lunch' => $request->Lunch ?? 0,
                   'Dinner' => $request->Dinner ?? 0,
                   'Regimen' => $request->Regimen ?? 0,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost ?? 0,
                   'Midmorningpost' => $request->Midmorningpost ?? 0,
                   'Lunchpost' => $request->Lunchpost ?? 0,
                   'Dinnerpost' => $request->Dinnerpost ?? 0,
                   'Regimenpost' => $request->Regimenpost ?? 0,
                   'Protein' => $request->Protein ?? 0,
                   'Carbohydrates' => $request->Carbohydrates ?? 0,
                   'Fat' => $request->Fat ?? 0,
                   'Fluid' => $request->Fluid ?? 0,
                   'Restriction' => $request->Restriction ?? 0,
                   'Proteincalories' => $request->Proteincalories ?? 0,
                   'Carbohydratescalories' => $request->Carbohydratescalories ?? 0,
                   'Fatcalories' => $request->Fatcalories ?? 0,
                   'ProteinNutrients' => $request->ProteinNutrients ?? 0,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients ?? 0,
                   'FatNutrients' => $request->FatNutrients ?? 0,
                   'BasalEnergy' => $request->BasalEnergy ?? 0,
                   'TotalCalories' => $request->TotalCalories ?? 0,
                   'date1' => $request->date1 ?? 0,
                   'time1' => $request->time1 ?? 0,
                   'week1' => $request->week1 ?? 0,
                   'date2' => $request->date2 ?? 0,
                   'time2' => $request->time2 ?? 0,
                   'week2' => $request->week2 ?? 0,
                   'date3' => $request->date3 ?? 0,
                   'time3' => $request->time3 ?? 0,
                   'week3' => $request->week3 ?? 0,
                   'date4' => $request->date4 ?? 0,
                   'time4' => $request->time4 ?? 0,
                   'week4' => $request->week4 ?? 0,
                   'date21' => $request->date21 ?? 0,
                   'time21' => $request->time21 ?? 0,
                   'week21' => $request->week21 ?? 0,
                   'date22' => $request->date22 ?? 0,
                   'time22' => $request->time22 ?? 0,
                   'week22' => $request->week22 ?? 0,
                   'date33' => $request->date33 ?? 0,
                   'time33' => $request->time33 ?? 0,
                   'week33' => $request->week33 ?? 0,
                   'date31' => $request->date31 ?? 0,
                   'time31' => $request->time31 ?? 0,
                   'week31' => $request->week31 ?? 0,
                   'date88' => $request->date88 ?? 0,
                   'time88' => $request->time88 ?? 0,
                   'week88' => $request->week88 ?? 0,
                   'date42' => $request->date42 ?? 0,
                   'time42' => $request->time42 ?? 0,
                   'week42' => $request->week42 ?? 0,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,

               ]);
           } else {
               $insertedId = DB::table('dietitianAssessment')->insertGetId([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age,
                   'weight' => $request->weight,
                   'height' => $request->height,
                   'bmi' => $request->bmi,
                   'ibw' => $request->ibw,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory,
                   'pastDietaryPattern' => $request->pastDietaryPattern,
                   'pastFluidIntake' => $request->pastFluidIntake,
                   'foodAllergy' => $request->foodAllergy,
                   'activityFactor' => $request->activityFactor,
                   'appetite' => $request->appetite,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension,
                   'Stroke' => $request->Stroke,
                   'Cancer' => $request->Cancer,
                   'arthritis' => $request->Arthritis,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease,
                   'copd' => $request->copd,
                   'Thyroid' => $request->Thyroid,
                   'Asthma' => $request->Asthma,
                   'Alzheimer' => $request->Alzheimer,
                   'cysticFibrosis' => $request->cysticFibrosis,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease,
                   'osteoporosis' => $request->osteoporosis,
                   'mentalIllness' => $request->mentalIllness,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome,
                   'Depression' => $request->Depression,
                   'multipleSclerosis' => $request->multipleSclerosis,
                   'inputEmail3' => $request->inputEmail3,
                   'Breakfast' => $request->Breakfast,
                   'Midmorning' => $request->Midmorning,
                   'Lunch' => $request->Lunch,
                   'Dinner' => $request->Dinner,
                   'Regimen' => $request->Regimen,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost,
                   'Midmorningpost' => $request->Midmorningpost,
                   'Lunchpost' => $request->Lunchpost,
                   'Dinnerpost' => $request->Dinnerpost,
                   'Regimenpost' => $request->Regimenpost,
                   'Protein' => $request->Protein,
                   'Carbohydrates' => $request->Carbohydrates,
                   'Fat' => $request->Fat,
                   'Fluid' => $request->Fluid,
                   'Restriction' => $request->Restriction,
                   'Proteincalories' => $request->Proteincalories,
                   'Carbohydratescalories' => $request->Carbohydratescalories,
                   'Fatcalories' => $request->Fatcalories,
                   'ProteinNutrients' => $request->ProteinNutrients,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients,
                   'FatNutrients' => $request->FatNutrients,
                   'BasalEnergy' => $request->BasalEnergy,
                   'TotalCalories' => $request->TotalCalories,
                   'date1' => $request->date1,
                   'time1' => $request->time1,
                   'week1' => $request->week1,
                   'date2' => $request->date2,
                   'time2' => $request->time2,
                   'week2' => $request->week2,
                   'date3' => $request->date3,
                   'time3' => $request->time3,
                   'week3' => $request->week3,
                   'date4' => $request->date4,
                   'time4' => $request->time4,
                   'week4' => $request->week4,
                   'date21' => $request->date21,
                   'time21' => $request->time21,
                   'week21' => $request->week21,
                   'date22' => $request->date22,
                   'time22' => $request->time22,
                   'week22' => $request->week22,
                   'date33' => $request->date33,
                   'time33' => $request->time33,
                   'week33' => $request->week33,
                   'date31' => $request->date31,
                   'time31' => $request->time31,
                   'week31' => $request->week31,
                   'date88' => $request->date88,
                   'time88' => $request->time88,
                   'week88' => $request->week88,
                   'date42' => $request->date42,
                   'time42' => $request->time42,
                   'week42' => $request->week42,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,
               ]);

               return response()->json(['message' => 'Success'], 200);
           }
        }
        $path2 = '';
        if ($request->hasFile('patientFollowUpFile')) {
            $file = $request->file('patientFollowUpFile');
            $fileName = 'PatientFollowUp' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

           $file->move(public_path('storage/Attachments'), $fileName);
           $path2 =  $fileName;
           $patientIds = DB::table('dietitianAssessment')->pluck('patient_id')->toArray();

           if (in_array($request->patient_id, $patientIds)) {
               // Patient ID already exists, so return a response indicating that it's a duplicate.
               // return response()->json(['message' => 'Patient ID already exists'], 400); // You can use a 400 status code for a bad request or choose an appropriate status code.
               // dd($request);
               $insertedId = DB::table('dietitianAssessment')->where('patient_id', $request->patient_id)->update([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age ?? 0,
                   'weight' => $request->weight ?? 0,
                   'height' => $request->height ?? 0,
                   'bmi' => $request->bmi ?? 0,
                   'ibw' => $request->ibw ?? 0,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory ?? 0,
                   'pastDietaryPattern' => $request->pastDietaryPattern ?? 0,
                   'pastFluidIntake' => $request->pastFluidIntake ?? 0,
                   'foodAllergy' => $request->foodAllergy ?? 0,
                   'activityFactor' => $request->activityFactor ?? 0,
                   'appetite' => $request->appetite ?? 0,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension ?? 0,
                   'Stroke' => $request->Stroke ?? 0,
                   'Cancer' => $request->Cancer ?? 0,
                   'arthritis' => $request->Arthritis ?? 0,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease ?? 0,
                   'copd' => $request->copd ?? 0,
                   'Thyroid' => $request->Thyroid ?? 0,
                   'Asthma' => $request->Asthma ?? 0,
                   'Alzheimer' => $request->Alzheimer ?? 0,
                   'cysticFibrosis' => $request->cysticFibrosis ?? 0,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease ?? 0,
                   'osteoporosis' => $request->osteoporosis ?? 0,
                   'mentalIllness' => $request->mentalIllness ?? 0,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome ?? 0,
                   'Depression' => $request->Depression ?? 0,
                   'multipleSclerosis' => $request->multipleSclerosis ?? 0,
                   'inputEmail3' => $request->inputEmail3 ?? 0,
                   'Breakfast' => $request->Breakfast ?? 0,
                   'Midmorning' => $request->Midmorning ?? 0,
                   'Lunch' => $request->Lunch ?? 0,
                   'Dinner' => $request->Dinner ?? 0,
                   'Regimen' => $request->Regimen ?? 0,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost ?? 0,
                   'Midmorningpost' => $request->Midmorningpost ?? 0,
                   'Lunchpost' => $request->Lunchpost ?? 0,
                   'Dinnerpost' => $request->Dinnerpost ?? 0,
                   'Regimenpost' => $request->Regimenpost ?? 0,
                   'Protein' => $request->Protein ?? 0,
                   'Carbohydrates' => $request->Carbohydrates ?? 0,
                   'Fat' => $request->Fat ?? 0,
                   'Fluid' => $request->Fluid ?? 0,
                   'Restriction' => $request->Restriction ?? 0,
                   'Proteincalories' => $request->Proteincalories ?? 0,
                   'Carbohydratescalories' => $request->Carbohydratescalories ?? 0,
                   'Fatcalories' => $request->Fatcalories ?? 0,
                   'ProteinNutrients' => $request->ProteinNutrients ?? 0,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients ?? 0,
                   'FatNutrients' => $request->FatNutrients ?? 0,
                   'BasalEnergy' => $request->BasalEnergy ?? 0,
                   'TotalCalories' => $request->TotalCalories ?? 0,
                   'date1' => $request->date1 ?? 0,
                   'time1' => $request->time1 ?? 0,
                   'week1' => $request->week1 ?? 0,
                   'date2' => $request->date2 ?? 0,
                   'time2' => $request->time2 ?? 0,
                   'week2' => $request->week2 ?? 0,
                   'date3' => $request->date3 ?? 0,
                   'time3' => $request->time3 ?? 0,
                   'week3' => $request->week3 ?? 0,
                   'date4' => $request->date4 ?? 0,
                   'time4' => $request->time4 ?? 0,
                   'week4' => $request->week4 ?? 0,
                   'date21' => $request->date21 ?? 0,
                   'time21' => $request->time21 ?? 0,
                   'week21' => $request->week21 ?? 0,
                   'date22' => $request->date22 ?? 0,
                   'time22' => $request->time22 ?? 0,
                   'week22' => $request->week22 ?? 0,
                   'date33' => $request->date33 ?? 0,
                   'time33' => $request->time33 ?? 0,
                   'week33' => $request->week33 ?? 0,
                   'date31' => $request->date31 ?? 0,
                   'time31' => $request->time31 ?? 0,
                   'week31' => $request->week31 ?? 0,
                   'date88' => $request->date88 ?? 0,
                   'time88' => $request->time88 ?? 0,
                   'week88' => $request->week88 ?? 0,
                   'date42' => $request->date42 ?? 0,
                   'time42' => $request->time42 ?? 0,
                   'week42' => $request->week42 ?? 0,
                   'patientFollowUpFile' => $path2 ?? 0,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,

               ]);

           } else {
               $insertedId = DB::table('dietitianAssessment')->insertGetId([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age,
                   'weight' => $request->weight,
                   'height' => $request->height,
                   'bmi' => $request->bmi,
                   'ibw' => $request->ibw,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory,
                   'pastDietaryPattern' => $request->pastDietaryPattern,
                   'pastFluidIntake' => $request->pastFluidIntake,
                   'foodAllergy' => $request->foodAllergy,
                   'activityFactor' => $request->activityFactor,
                   'appetite' => $request->appetite,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension,
                   'Stroke' => $request->Stroke,
                   'Cancer' => $request->Cancer,
                   'arthritis' => $request->Arthritis,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease,
                   'copd' => $request->copd,
                   'Thyroid' => $request->Thyroid,
                   'Asthma' => $request->Asthma,
                   'Alzheimer' => $request->Alzheimer,
                   'cysticFibrosis' => $request->cysticFibrosis,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease,
                   'osteoporosis' => $request->osteoporosis,
                   'mentalIllness' => $request->mentalIllness,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome,
                   'Depression' => $request->Depression,
                   'multipleSclerosis' => $request->multipleSclerosis,
                   'inputEmail3' => $request->inputEmail3,
                   'Breakfast' => $request->Breakfast,
                   'Midmorning' => $request->Midmorning,
                   'Lunch' => $request->Lunch,
                   'Dinner' => $request->Dinner,
                   'Regimen' => $request->Regimen,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost,
                   'Midmorningpost' => $request->Midmorningpost,
                   'Lunchpost' => $request->Lunchpost,
                   'Dinnerpost' => $request->Dinnerpost,
                   'Regimenpost' => $request->Regimenpost,
                   'Protein' => $request->Protein,
                   'Carbohydrates' => $request->Carbohydrates,
                   'Fat' => $request->Fat,
                   'Fluid' => $request->Fluid,
                   'Restriction' => $request->Restriction,
                   'Proteincalories' => $request->Proteincalories,
                   'Carbohydratescalories' => $request->Carbohydratescalories,
                   'Fatcalories' => $request->Fatcalories,
                   'ProteinNutrients' => $request->ProteinNutrients,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients,
                   'FatNutrients' => $request->FatNutrients,
                   'BasalEnergy' => $request->BasalEnergy,
                   'TotalCalories' => $request->TotalCalories,
                   'date1' => $request->date1,
                   'time1' => $request->time1,
                   'week1' => $request->week1,
                   'date2' => $request->date2,
                   'time2' => $request->time2,
                   'week2' => $request->week2,
                   'date3' => $request->date3,
                   'time3' => $request->time3,
                   'week3' => $request->week3,
                   'date4' => $request->date4,
                   'time4' => $request->time4,
                   'week4' => $request->week4,
                   'date21' => $request->date21,
                   'time21' => $request->time21,
                   'week21' => $request->week21,
                   'date22' => $request->date22,
                   'time22' => $request->time22,
                   'week22' => $request->week22,
                   'date33' => $request->date33,
                   'time33' => $request->time33,
                   'week33' => $request->week33,
                   'date31' => $request->date31,
                   'time31' => $request->time31,
                   'week31' => $request->week31,
                   'date88' => $request->date88,
                   'time88' => $request->time88,
                   'week88' => $request->week88,
                   'date42' => $request->date42,
                   'time42' => $request->time42,
                   'week42' => $request->week42,
                   'patientFollowUpFile' => $path2,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,
               ]);
               return response()->json(['message' => 'Success'], 200);
           }

        }else{
            $patientIds = DB::table('dietitianAssessment')->pluck('patient_id')->toArray();
           if (in_array($request->patient_id, $patientIds)) {
               // Patient ID already exists, so return a response indicating that it's a duplicate.
               // return response()->json(['message' => 'Patient ID already exists'], 400); // You can use a 400 status code for a bad request or choose an appropriate status code.
               // dd($request);
               $insertedId = DB::table('dietitianAssessment')->where('patient_id', $request->patient_id)->update([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age ?? 0,
                   'weight' => $request->weight ?? 0,
                   'height' => $request->height ?? 0,
                   'bmi' => $request->bmi ?? 0,
                   'ibw' => $request->ibw ?? 0,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory ?? 0,
                   'pastDietaryPattern' => $request->pastDietaryPattern ?? 0,
                   'pastFluidIntake' => $request->pastFluidIntake ?? 0,
                   'foodAllergy' => $request->foodAllergy ?? 0,
                   'activityFactor' => $request->activityFactor ?? 0,
                   'appetite' => $request->appetite ?? 0,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension ?? 0,
                   'Stroke' => $request->Stroke ?? 0,
                   'Cancer' => $request->Cancer ?? 0,
                   'arthritis' => $request->Arthritis ?? 0,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease ?? 0,
                   'copd' => $request->copd ?? 0,
                   'Thyroid' => $request->Thyroid ?? 0,
                   'Asthma' => $request->Asthma ?? 0,
                   'Alzheimer' => $request->Alzheimer ?? 0,
                   'cysticFibrosis' => $request->cysticFibrosis ?? 0,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease ?? 0,
                   'osteoporosis' => $request->osteoporosis ?? 0,
                   'mentalIllness' => $request->mentalIllness ?? 0,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome ?? 0,
                   'Depression' => $request->Depression ?? 0,
                   'multipleSclerosis' => $request->multipleSclerosis ?? 0,
                   'inputEmail3' => $request->inputEmail3 ?? 0,
                   'Breakfast' => $request->Breakfast ?? 0,
                   'Midmorning' => $request->Midmorning ?? 0,
                   'Lunch' => $request->Lunch ?? 0,
                   'Dinner' => $request->Dinner ?? 0,
                   'Regimen' => $request->Regimen ?? 0,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost ?? 0,
                   'Midmorningpost' => $request->Midmorningpost ?? 0,
                   'Lunchpost' => $request->Lunchpost ?? 0,
                   'Dinnerpost' => $request->Dinnerpost ?? 0,
                   'Regimenpost' => $request->Regimenpost ?? 0,
                   'Protein' => $request->Protein ?? 0,
                   'Carbohydrates' => $request->Carbohydrates ?? 0,
                   'Fat' => $request->Fat ?? 0,
                   'Fluid' => $request->Fluid ?? 0,
                   'Restriction' => $request->Restriction ?? 0,
                   'Proteincalories' => $request->Proteincalories ?? 0,
                   'Carbohydratescalories' => $request->Carbohydratescalories ?? 0,
                   'Fatcalories' => $request->Fatcalories ?? 0,
                   'ProteinNutrients' => $request->ProteinNutrients ?? 0,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients ?? 0,
                   'FatNutrients' => $request->FatNutrients ?? 0,
                   'BasalEnergy' => $request->BasalEnergy ?? 0,
                   'TotalCalories' => $request->TotalCalories ?? 0,
                   'date1' => $request->date1 ?? 0,
                   'time1' => $request->time1 ?? 0,
                   'week1' => $request->week1 ?? 0,
                   'date2' => $request->date2 ?? 0,
                   'time2' => $request->time2 ?? 0,
                   'week2' => $request->week2 ?? 0,
                   'date3' => $request->date3 ?? 0,
                   'time3' => $request->time3 ?? 0,
                   'week3' => $request->week3 ?? 0,
                   'date4' => $request->date4 ?? 0,
                   'time4' => $request->time4 ?? 0,
                   'week4' => $request->week4 ?? 0,
                   'date21' => $request->date21 ?? 0,
                   'time21' => $request->time21 ?? 0,
                   'week21' => $request->week21 ?? 0,
                   'date22' => $request->date22 ?? 0,
                   'time22' => $request->time22 ?? 0,
                   'week22' => $request->week22 ?? 0,
                   'date33' => $request->date33 ?? 0,
                   'time33' => $request->time33 ?? 0,
                   'week33' => $request->week33 ?? 0,
                   'date31' => $request->date31 ?? 0,
                   'time31' => $request->time31 ?? 0,
                   'week31' => $request->week31 ?? 0,
                   'date88' => $request->date88 ?? 0,
                   'time88' => $request->time88 ?? 0,
                   'week88' => $request->week88 ?? 0,
                   'date42' => $request->date42 ?? 0,
                   'time42' => $request->time42 ?? 0,
                   'week42' => $request->week42 ?? 0,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,

               ]);

           } else {
               $insertedId = DB::table('dietitianAssessment')->insertGetId([
                   'patient_id' => $request->patient_id,
                   'age' => $request->age,
                   'weight' => $request->weight,
                   'height' => $request->height,
                   'bmi' => $request->bmi,
                   'ibw' => $request->ibw,
                   'nutritionalStatusCategory' => $request->nutritionalStatusCategory,
                   'pastDietaryPattern' => $request->pastDietaryPattern,
                   'pastFluidIntake' => $request->pastFluidIntake,
                   'foodAllergy' => $request->foodAllergy,
                   'activityFactor' => $request->activityFactor,
                   'appetite' => $request->appetite,
                   'Diabetes' => $request->Diabetes,
                   'Hypertension' => $request->Hypertension,
                   'Stroke' => $request->Stroke,
                   'Cancer' => $request->Cancer,
                   'arthritis' => $request->Arthritis,
                   'chronicKidneyDisease' => $request->chronicKidneyDisease,
                   'copd' => $request->copd,
                   'Thyroid' => $request->Thyroid,
                   'Asthma' => $request->Asthma,
                   'Alzheimer' => $request->Alzheimer,
                   'cysticFibrosis' => $request->cysticFibrosis,
                   'inflammatoryBowelDisease' => $request->inflammatoryBowelDisease,
                   'osteoporosis' => $request->osteoporosis,
                   'mentalIllness' => $request->mentalIllness,
                   'polycysticOvarySyndrome' => $request->polycysticOvarySyndrome,
                   'Depression' => $request->Depression,
                   'multipleSclerosis' => $request->multipleSclerosis,
                   'inputEmail3' => $request->inputEmail3,
                   'Breakfast' => $request->Breakfast,
                   'Midmorning' => $request->Midmorning,
                   'Lunch' => $request->Lunch,
                   'Dinner' => $request->Dinner,
                   'Regimen' => $request->Regimen,
                   'history_description' => $request->history_description ?? '',
                   'bed_time' => $request->bed_time ?? '',
                   'Breakfastpost' => $request->Breakfastpost,
                   'Midmorningpost' => $request->Midmorningpost,
                   'Lunchpost' => $request->Lunchpost,
                   'Dinnerpost' => $request->Dinnerpost,
                   'Regimenpost' => $request->Regimenpost,
                   'Protein' => $request->Protein,
                   'Carbohydrates' => $request->Carbohydrates,
                   'Fat' => $request->Fat,
                   'Fluid' => $request->Fluid,
                   'Restriction' => $request->Restriction,
                   'Proteincalories' => $request->Proteincalories,
                   'Carbohydratescalories' => $request->Carbohydratescalories,
                   'Fatcalories' => $request->Fatcalories,
                   'ProteinNutrients' => $request->ProteinNutrients,
                   'CarbohydratesNutrients' => $request->CarbohydratesNutrients,
                   'FatNutrients' => $request->FatNutrients,
                   'BasalEnergy' => $request->BasalEnergy,
                   'TotalCalories' => $request->TotalCalories,
                   'date1' => $request->date1,
                   'time1' => $request->time1,
                   'week1' => $request->week1,
                   'date2' => $request->date2,
                   'time2' => $request->time2,
                   'week2' => $request->week2,
                   'date3' => $request->date3,
                   'time3' => $request->time3,
                   'week3' => $request->week3,
                   'date4' => $request->date4,
                   'time4' => $request->time4,
                   'week4' => $request->week4,
                   'date21' => $request->date21,
                   'time21' => $request->time21,
                   'week21' => $request->week21,
                   'date22' => $request->date22,
                   'time22' => $request->time22,
                   'week22' => $request->week22,
                   'date33' => $request->date33,
                   'time33' => $request->time33,
                   'week33' => $request->week33,
                   'date31' => $request->date31,
                   'time31' => $request->time31,
                   'week31' => $request->week31,
                   'date88' => $request->date88,
                   'time88' => $request->time88,
                   'week88' => $request->week88,
                   'date42' => $request->date42,
                   'time42' => $request->time42,
                   'week42' => $request->week42,
                   'Fever' => $request->Fever ?? 0,
                   'Fatigue' => $request->Fatigue ?? 0,
                   'WeightLoss' => $request->WeightLoss ?? 0,
                   'ShortnessofBreath' => $request->ShortnessofBreath ?? 0,
                   'Cough' => $request->Cough ?? 0,
                   'Edema' => $request->Edema ?? 0,
                   'NauseaVomiting' => $request->NauseaVomiting ?? 0,
                   'Diarrhea' => $request->Diarrhea ?? 0,
                   'Abdominalpain' => $request->Abdominalpain ?? 0,
                   'MuscleStiffness' => $request->MuscleStiffness ?? 0,
                   'LesionsWounds' => $request->LesionsWounds ?? 0,
                   'ExcessiveThirst' => $request->ExcessiveThirst ?? 0,
                   'FrequentUrination' => $request->FrequentUrination ?? 0,
                   'BleedingGums' => $request->BleedingGums ?? 0,
                   'FoodCravings' => $request->FoodCravings ?? 0,
                   'Irritability' => $request->Irritability ?? 0,
                   'Confusion' => $request->Confusion ?? 0,
                   'DrySkin' => $request->DrySkin ?? 0,
                   'HungerPangs' => $request->HungerPangs ?? 0,
                   'Constipation' => $request->Constipation ?? 0,
                   'MuscleCramps' => $request->MuscleCramps ?? 0,
                   'Bloating' => $request->Bloating ?? 0,
                   'Paleness' => $request->Paleness ?? 0,
                   'HairLoss' => $request->HairLoss ?? 0,
                   'Tingling' => $request->Tingling ?? 0,
               ]);

               return response()->json(['message' => 'Success'], 200);
           }
        }


    }

    /**
     * Show the form for editing the specified Patient.
     *
     * @return Factory|View
     */
    public function edit(Patient $patient)
    {
        //        $user = $patient->patientUser;
        $bloodGroup = getBloodGroups();

        return view('patients.edit', compact('patient', 'bloodGroup'));
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function update(Patient $patient, UpdatePatientRequest $request)
    {
        if ($patient->is_default == 1) {
            Flash::error('This action is not allowed for default record.');

            return redirect(route('patients.index'));
        }

        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $this->patientRepository->update($input, $patient);

        Flash::success(__('messages.advanced_payment.patient') . ' ' . __('messages.common.updated_successfully'));

        return redirect(route('patients.index'));
    }

    /**
     * Remove the specified Patient from storage.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Patient $patient)
    {
        if ($patient->is_default == 1) {
            return $this->sendError('This action is not allowed for default record.');
        }

        $patientModels = [
            BirthReport::class, DeathReport::class, InvestigationReport::class, OperationReport::class,
            Appointment::class, BedAssign::class, PatientAdmission::class, PatientCase::class, Bill::class,
            Invoice::class, AdvancedPayment::class, Prescription::class, IpdPatientDepartment::class,
        ];
        $result = canDelete($patientModels, 'patient_id', $patient->id);
        if ($result) {
            return $this->sendError(__('messages.advanced_payment.patient') . ' ' . __('messages.common.cant_be_deleted'));
        }
        $patient->patientUser()->delete();
        $patient->address()->delete();
        $patient->delete();

        return $this->sendSuccess(__('messages.advanced_payment.patient') . ' ' . __('messages.common.deleted_successfully'));
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function activeDeactiveStatus($id)
    {
        $patient = Patient::findOrFail($id);
        $status = !$patient->patientUser->status;
        $patient->patientUser()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function showForm(Request $request, $patient)
    {
        $patientID =  Patient::where('id', $patient)->pluck('MR')->first();
        $patientData =  Patient::where('id', $patient)->with('user')->first();
        $medicines = Medicine::with('category')->get();
        $doctors = Doctor::with('user')->get();
        $ageDifference = Carbon::parse($patientData->user->dob)->diff(Carbon::now());
        $age = ($ageDifference->y > 0) ? ($ageDifference->y . ' Years') : (
            ($ageDifference->m > 0) ? ($ageDifference->m . ' Months') : ($ageDifference->d . ' Days')
        );
        $nursingData = NursingForm::where('patient_mr_number', $patientID)->with(['patient.user', 'Allergies', 'Medication'])->first();
        $medication = MedicattionAdministration::where('mr_number', $patientID)->with('Medication')->first();

        $form_patientId = DB::Table('form_patient')->where(['id' => $request->formPatientID])->first();
        $DietData = DB::Table('dietitianAssessment')->where(['patient_id' => $patient])->first();
        if ($form_patientId) {
            $formFile = DB::Table('form_type')->where('fileName', '!=', 'preTestForm')->where(['id' => $form_patientId->formID])->first();
            $fileName = $formFile->fileName;
            $formData = DB::Table('form_data')->where(['formID' => $request->formPatientID])->get();

            return view('patients.' . $fileName, compact('formData', 'nursingData', 'patientData', 'DietData', 'age','doctors','medication','medicines'),['ignore_minify' => true],);
        }
        return "fdsfasdf";
    }

    public function submitForm(Request $request)
    {
        if ($request->BloodPressure != null) {
            Patient::where('id', $request->patient_id)->update([
                'blood_pressure' => $request->BloodPressure,
            ]);
        }
        if ($request->HeartRate != null) {
            Patient::where('id', $request->patient_id)->update([
                'heart_rate' => $request->HeartRate,
            ]);
        }
        if ($request->Temperature != null) {
            Patient::where('id', $request->patient_id)->update([
                'temperature' => $request->Temperature,
            ]);
        }
        if ($request->RespiratoryRate != null) {
            Patient::where('id', $request->patient_id)->update([
                'respiratory_rate' => $request->RespiratoryRate,
            ]);
        }
        if ($request->height != null) {
            Patient::where('id', $request->patient_id)->update([
                'height' => $request->height,
            ]);
        }
        if ($request->weight != null) {
            Patient::where('id', $request->patient_id)->update([
                'weight' => $request->weight,
            ]);
        }
        if ($request->bmi != null) {
            Patient::where('id', $request->patient_id)->update([
                'bmi' => $request->bmi,
            ]);
        }
        if ($request->ibw != null) {
            Patient::where('id', $request->patient_id)->update([
                'bmi' => $request->ibw,
            ]);
        }

        $reqArray = $request->all();
        $patientID =  $request->patient_id;
        $prescriptionId = Prescription::where('patient_id', $patientID)->pluck('id')->first();
        if ($prescriptionId) {
            if($request->medication_id != null){
            // Iterate over each medication in the request
            foreach ($request->medication_id as $key => $medicineID) {
                // Get the category ID for the medicine
                $medicine = Medicine::find($medicineID);

                // Create a new entry in the prescription_medicines table
                DB::table('prescriptions_medicines')->insert([
                    'prescription_id' => $prescriptionId,
                    'category_id' => $medicine->category_id,   // Get category ID from Medicine table
                    'medicine_id' => $medicineID,              // Medicine ID from request
                    'dosage' => $request->dosage[$key] ?? null, // Dosage from request
                    'day' => null,                             // Assuming 'day' is not provided in request, set to null
                    'time' => null,                            // Assuming 'time' is not provided in request, set to null
                    'comment' => null,                         // Assuming 'comment' is not provided in request, set to null
                    'route' => $request->route[$key] ?? null,  // Route from request
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            }

        }

        foreach ($reqArray as $fieldName => $fieldValue) {
            if ($fieldValue != null) {
                DB::table('form_data')
                ->where('fieldName', $fieldName)->where('formID', $request->formPatientID) // Specify the condition for the update
                ->update([
                    'fieldValue' => $fieldValue, // Update the fieldValue column with the new value
                ]);
            } else {
                DB::table('form_data')
                    ->where('fieldName', $fieldName)->where('formID', $request->formPatientID) // Specify the condition for the update
                    ->update([
                        'fieldValue' => '', // Update the fieldValue column with the new value
                    ]);
            }

        }

        // Handle updating the image data
        if ($request->hasFile('soapFormAttachment')) {
            $file = $request->file('soapFormAttachment');
            // $fileName = url('/') . '/storage/SoapForm/' . $file->getClientOriginalName();
            $fileName = 'SoapForm'. date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);


            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'soapFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        // Handle updating the image data
        if ($request->hasFile('referralFormAttachment')) {
            $file = $request->file('referralFormAttachment');
            $fileName = 'ReferralForm' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);

            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'referralFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        // Handle updating the image data
        if ($request->hasFile('progressFormAttachment')) {
            $file = $request->file('progressFormAttachment');
            $fileName = 'ProgressForm' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);

            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'progressFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        // Handle updating the image data
        if ($request->hasFile('nutritionalFormAttachment')) {
            $file = $request->file('nutritionalFormAttachment');
            $fileName = 'NutritionalForm' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);

            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'nutritionalFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        // Handle updating the image data
        if ($request->hasFile('fastFormAttachment')) {
            $file = $request->file('fastFormAttachment');
            $fileName = 'FastForm' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);

            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'fastFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        // Handle updating the image data
        if ($request->hasFile('dentalFormAttachment')) {
            $file = $request->file('dentalFormAttachment');
            $fileName = 'DentalForm' . date('Y-m-d') . '.'. $file->getClientOriginalExtension();

            // Move and store the new file
            $file->move(public_path('storage/Attachments'), $fileName);

            // Update the database with the new image file name
            DB::table('form_data')
                ->where('fieldName', 'dentalFormAttachment')
                ->where('formID', $request->formPatientID)
                ->update(['fieldValue' => $fileName]);
        }

        return view('patients.blankView');
    }

    /**
     * @return BinaryFileResponse
     */
    public function patientExport()
    {
        return Excel::download(new PatientExport, 'patients-' . time() . '.xlsx');
    }

    /**
     * @return Patient|Builder|Model|object|null
     */
    public function getBirthDate($id)
    {
        return Patient::whereId($id)->with('user')->first();
    }

    public function formCreat(Request $req)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');

        $formName = DB::table('form_type')->where(['id' => $req->formName])->value('formName');
        //DB::insert("INSERT INTO `formPatient` (formID, formName, patientID, formDate) VALUES (?, ?, ?, ?)", [(int)$req->formName, $formName, (int)$req->patientID, $formattedDate]);
        $insertedId = DB::table('form_patient')->insertGetId([
            'formID' => (int) $req->formName,
            'formName' => $formName,
            'patientID' => (int) $req->patientID,
            'formDate' => $formattedDate,
        ]);

        Flash::success(__('messages.advanced_payment.patient') . ' Form has been added successfully');

        if ($formName == 'SOAP Form') {
            $this->insertSOAPForm($insertedId, (int) $req->patientID);
        } elseif ($formName == 'Pre-Test Form') {
            $this->insertPreTestData($insertedId, (int) $req->patientID);
        } elseif ($formName == 'Nutritional Assessment Form') {
            $this->insertNutritientData($insertedId, (int) $req->patientID);
        } elseif ($formName == 'FAST FORM') {
            $this->insertFASTData($insertedId, (int) $req->patientID);
        } elseif ($formName == 'Referral Form') {
            $this->insertReferralData($insertedId, (int) $req->patientID);
        } elseif ($formName == 'Dental Form') {
            $this->insertDentalData($insertedId, (int) $req->patientID);
        } elseif ($formName == 'Progress Form') {
            $this->insertProgressForm($insertedId, (int) $req->patientID);
        }elseif ($formName == 'Medical Certificate') {
            $this->insertMedicalCertificate($insertedId, (int) $req->patientID);
        }

        return redirect(route('patients.show', ['patient' => $req->patientID]));

        return 'working';
    }
   public function  insertMedicalCertificate($formID, $patientID){
    $data = [
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'drName', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'patient_name', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'age', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'gender', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'suffering_from', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'advise', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'until', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'remarks', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'date', 'fieldValue' => ''],
        ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'signature', 'fieldValue' => ''],

    ];

    DB::table('form_data')->insert($data);
   }
    public function insertNutritientData($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleInputFname', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'datepicker', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'datepicker2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck12', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck13', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck14', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck15', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck16', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck17', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck18', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck19', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck20', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck21', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck23', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck24', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck25', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck26', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck27', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck28', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck29', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck30', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck31', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck32', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck34', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck35', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck36', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck37', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck38', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck39', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck40', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck41', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck42', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck43', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck45', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck46', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck47', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck48', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck49', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck499', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck50', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck51', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck52', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck53', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck54', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck56', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck57', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck58', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck59', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'father_condition_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mother_condition_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'siblings_condition_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'father_condition_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mother_condition_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'siblings_condition_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'father_condition_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mother_condition_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'siblings_condition_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'father_condition_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mother_condition_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'siblings_condition_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'father_condition_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mother_condition_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'siblings_condition_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication_10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage_10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'start_date_10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason_10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck60', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck61', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck62', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck63', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck64', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck65', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck67', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck68', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck69', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck70', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck71', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck72', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck73', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck74', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck75', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck76', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck77', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck78', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck79', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck80', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck81', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck82', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck83', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck84', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck85', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck86', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck87', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck88', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck89', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck90', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck91', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck92', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck93', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck94', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck95', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck96', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck97', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck98', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck99', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck100', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck101', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Noneone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dailyone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weeklyone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthlyone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Nonetwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dailytwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weeklytwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthlytwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Nonethree', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dailythree', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weeklythree', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthlythree', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yesone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Noone', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yestwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Notwo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Nonefour', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dailyfour', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weeklyfour', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthlyfour', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Noneqwe', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dailydf', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weeklysdvf', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthlysd', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None77', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily77', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly77', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly77', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None88', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily88', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly88', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly88', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None888', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily888', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly888', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly888', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None123', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily123', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly123', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly123', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None1234', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily1234', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly1234', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly1234', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None124', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily124', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly124', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly124', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None126', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily126', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly126', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly126', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None127', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily127', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly127', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly127', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None788', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily788', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly788', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly788', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck101', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck102', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck103', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck104', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck105', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck106', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck107', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck108', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck109', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck110', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck111', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck112', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck113', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck114', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck115', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck116', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck117', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck118', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck119', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck120', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck121', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck122', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck123', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck124', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck125', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck126', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck127', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck128', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck129', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck130', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck131', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck132', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck133', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck134', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'exampleCheck135', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'none1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weekly1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'monthly1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'none2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weekly2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'monthly2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'none3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weekly3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'monthly3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'none4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weekly4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'monthly4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'none5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'daily5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weekly5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'monthly5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example12', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example13', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example14', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example15', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example16', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example17', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example18', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example19', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example20', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example21', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example23', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example24', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example25', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example26', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example27', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example28', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example29', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example30', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example31', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example32', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example33', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example34', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example35', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example36', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example37', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example38', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example39', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example40', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example41', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example42', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example43', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example44', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example45', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example46', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example47', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example48', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example49', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example50', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example51', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example52', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example53', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example54', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example55', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example56', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example57', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example58', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example59', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example60', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example61', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example62', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example63', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example64', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example65', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example66', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example67', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example68', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example69', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example70', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example71', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'example72', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7019', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7019', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7019', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7019', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7020', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7020', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7020', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7020', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7021', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7021', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7021', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7021', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7022', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7022', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7022', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7022', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes22770', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No22770', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes9078', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No9078', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7025', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7025', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7025', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7025', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7026', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7026', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7026', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7026', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7027', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7027', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7027', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7027', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7028', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7028', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7028', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7028', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes90645', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No90645', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7029', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7029', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7029', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7029', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7030', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7030', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7030', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7030', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes906875', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No906875', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7031', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7031', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7031', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7031', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7032', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7032', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7032', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7032', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7033', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7033', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7033', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7033', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes906098', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No906098', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7034', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7034', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7034', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7034', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes8799715', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No8799715', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7035', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7035', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7035', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7035', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7036', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7036', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7036', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7036', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7037', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7037', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7037', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7037', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes726265', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No726265', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes989871', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No989871', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes9087456', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No9087456', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes908979', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No908979', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Yes459845', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'No459845', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7039', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7039', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7039', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7039', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7050', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7050', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7050', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7050', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7041', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7041', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7041', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7041', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7051', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7051', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7051', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7051', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7053', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7053', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7053', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7053', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7054', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7054', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7054', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7054', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7055', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7055', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7055', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7055', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7056', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7056', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7056', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7056', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7057', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7057', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7057', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7057', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7058', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7058', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7058', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7058', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7059', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7059', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7059', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7059', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7060', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7060', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7060', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7060', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7061', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7061', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7061', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7061', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7062', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7062', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7062', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7062', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7063', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7063', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7063', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7063', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7064', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7064', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7064', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7064', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7065', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7065', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7065', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7065', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'None7066', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Daily7066', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Weekly7066', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Monthly7066', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'nutritionalFormAttachment', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }

    public function insertPreTestData($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Mr', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fullname', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ResponsiblePerson', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fatigue', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'energy', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'sleep', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'hairloss', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'stress', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weightgain', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'constipation', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'diarrhea', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'abdominalpain', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'skinsryness', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'brainfog', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Digestion', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'IntestinalPermeability', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'GutMicrobione', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ImuneSystem', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'NervousSystem', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Hypertension', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diabetes', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Depression', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Anxiety', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Alziemer', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Chronic', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Eczema', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weightloss', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }

    public function insertSOAPForm($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Chiefcomplaint', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HistoryofPresentIllness', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PastMedicalHistory', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Medications', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Reofviewsysemts', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'BloodPressure', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HeartRate', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'RespiratoryRate', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Temperature', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'LaboratoryandDiagnosticTests', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'LaboratoryValues', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diagnosis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DifferentialDiagnosis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ProblemList', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Prognosis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HealthStatus', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Medications2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'TestsandConsultations', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientEducation', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Follow-Up', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'OtherConsiderations', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'FamilyMedicalHistory', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SocialHistory', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PhysicalExamination', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'editor', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'soapFormAttachment', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'medication', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dosage', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'frequency', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'route', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }
    public function insertProgressForm($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'date', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'time', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'location', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weight', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'height', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'foc', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'temp', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'bp', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'pulse', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'res', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'drsName', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'painScore', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'allergy', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'sign', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'desciption', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'progressFormAttachment', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }


    public function insertFASTData($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mrNum', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'fullName', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'height', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'weight', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'bmi', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ibw', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'breakfast', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'lunch', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'teaTime', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dinner', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'watterIntake', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'reason', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'sedentaryLifestyle', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'lightActivity', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'RegularExercise', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'yoga', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'meditation', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'others', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'others2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'others3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'visit1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'visit2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'visit3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'visit4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'hypertension', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'diabetesMiletus', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ischemicHeartDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'depressionOrAnxiety', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'alzheimersDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'chronicUrticaria', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'eczema', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'others5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Energy1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Energy2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Energy3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Energy4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fatigue1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fatigue2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fatigue3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fatigue4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sleep1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sleep2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sleep3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sleep4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HairLoss1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HairLoss2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HairLoss3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HairLoss4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Stress1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Stress2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Stress3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Stress4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Concentration1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Concentration2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Concentration3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Concentration4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ChangeinWeight1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ChangeinWeight2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ChangeinWeight3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ChangeinWeight4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SkinChanges1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SkinChanges2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SkinChanges3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SkinChanges4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diarrhea1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diarrhea2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diarrhea3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diarrhea4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Constipation1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Constipation2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Constipation3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Constipation4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AbdominalPain1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AbdominalPain2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AbdominalPain3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AbdominalPain4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Bloating1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Bloating2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Bloating3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Bloating4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'fastFormAttachment', 'fieldValue' => ''],


        ];

        DB::table('form_data')->insert($data);
    }
    public function insertReferralData($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'FullName', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mr_no', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'age', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'gendercheck', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PhoneNumber', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'date_of_referral', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'NameAndDesignationOfRequestingPhysician', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientIsReferredTo', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ConsultantDr', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ServiceSpeciality', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ReasonForReferral', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientsMedicalHistory', 'fieldValue' => ''], remove by dr fariha
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'drSignature', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'referralFormAttachment', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'signature', 'fieldValue' => ''],

            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'CurrentMedicalConditions', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PastMedicalHistory2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Allergies', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Medications3', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => '', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SurgicalHistory', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'FamilyMedicalHistory', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SocialHistory', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'RelevantPhysicalExaminationFindings', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Result', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Date', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HealthcareFacility', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DescribeAnyTreatmentsorManagementStrategies', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'TypeofSpecialist', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PreferredSpecialist', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AnySpecificInstructions/ConcernsRelatedToTheReferral', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Attachanyrelevantmedicalrecords,testreports,orimagingstudiesthatsupportthereferral.', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientName', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DateofBirth2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Gender', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Address3', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'City', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'State', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ZipCode', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PhoneNumber3', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'EmailAddress', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ReferringProviderName', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ClinicName', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Address4', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'City2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'State2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PhoneNumber4', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'EmailAddress2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DescribeTheReasonForReferral/AnySpecificConcerns', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientsMedicalHistory', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'CurrentMedications', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AreThereAnyKnownAllergies', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'LaboratoryandDiagnosticTests2', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PatientsPreviousTreatment', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AnyAdditionalInformation', 'fieldValue' => ''],
            // ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'signature', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }
    public function insertDentalData($formID, $patientID)
    {
        $data = [
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dates', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dentist_name', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'name', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'age', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'male', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'female', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'mr_no', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Pain', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Swelling', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Malocclusion', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'missingTeeth', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Mobility', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'FoodImpaction', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Aesthetics', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'LimitedMouthOpening', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'BleedingGums', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sensitivity', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Fracture', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Prostheris', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Caries', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Staining', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Trauma', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Anyother', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Duration', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Continous', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Sharp', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Stimulus', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Intermittent', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Dull', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Spontaneous', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Localized', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Radiating', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'AgrravatingFactors', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'RelievingFactors', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HistoryofPresentingComplaints', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Chalia', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Smoking', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Naswar', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Paan', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Tobacco', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Alcohol', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Braxium', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Pregnancy', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Asthama', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Epilepsy', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HypertensionProfile', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Tuberculosis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'CerebrovascularAccident', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DiabetesMellittus', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PepticUlcerDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'IschemicHeartDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'RenalDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ValvularHeartDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Arthritis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'BleedingDisorder', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'SkinDisease', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Hepatitis', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'ThyroidDisorder', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PreviousHospitalization', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Donor', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Recipient', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DrugHistory', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Allergies', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Diabetes', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Ihd', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'HypertensionHistory', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'BleedingDisorder2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Anemia', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Swelling', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Jaundice', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'FacialAsymmetry', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'note', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PAXray', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'PAXray_feild', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'OPG', 'fieldValue'  => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'OPG_feild', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'Duration', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'DentistSignature', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'dentalFormAttachment', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child12', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child13', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child14', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child15', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child16', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child17', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child18', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child19', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'child20', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult1', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult2', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult3', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult4', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult5', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult6', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult7', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult8', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult9', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult10', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult11', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult12', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult13', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult14', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult15', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult16', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult17', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult18', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult19', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult20', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult21', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult22', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult23', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult24', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult25', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult26', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult27', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult28', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult29', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult30', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult31', 'fieldValue' => ''],
            ['formID' => $formID, 'patientID' => $patientID, 'fieldName' => 'adult32', 'fieldValue' => ''],
        ];

        DB::table('form_data')->insert($data);
    }
}
