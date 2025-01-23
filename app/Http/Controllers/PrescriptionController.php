<?php

namespace App\Http\Controllers;

use Flash;
use Exception;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Http\RedirectResponse;
use App\Repositories\DoctorRepository;
use Illuminate\Contracts\View\Factory;
use App\Repositories\MedicineRepository;
use App\Models\PrescriptionMedicineModal;
use App\Http\Requests\CreateMedicineRequest;
use App\Repositories\PrescriptionRepository;
use App\Http\Requests\CreatePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use Illuminate\Contracts\Foundation\Application;

class PrescriptionController extends AppBaseController
{
    /** @var  PrescriptionRepository
     * @var DoctorRepository
     */
    private $prescriptionRepository;

    private $doctorRepository;

    private $medicineRepository;

    public function __construct(
        PrescriptionRepository $prescriptionRepo,
        DoctorRepository $doctorRepository,
        MedicineRepository $medicineRepository

    ) {
        $this->prescriptionRepository = $prescriptionRepo;
        $this->doctorRepository = $doctorRepository;
        $this->medicineRepository = $medicineRepository;
    }

    /**
     * Display a listing of the Prescription.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index()
    {
        $data['statusArr'] = Prescription::STATUS_ARR;

        return view('prescriptions.index', $data);
    }

    /**
     * Show the form for creating a new Prescription.
     *
     */
    public function getFormData($patient_id)
    {
        // Fetch rows where fieldName is either 'TestsandConsultations' or 'PatientEducation'
        $data = DB::table('form_data')
                  ->where('patientID', $patient_id)
                  ->whereIn('fieldName', ['TestsandConsultations', 'PatientEducation'])
                  ->select('fieldName', 'fieldValue')
                  ->orderByDesc('id')->get();


        return response()->json(['success' => true, 'data' => $data], 200);
    }





    public function create()
    {
        $patients2 = $this->prescriptionRepository->getPatients();
        $patients;
        // foreach ($patients2 as $key => $value) {
        //     $patients[$key] = $key . ' - '. $value;
        // }

        foreach ($patients2 as $key => $value) {
            $patients[$value->id] = $value->MR. " - ".$value->patientUser->full_name;
        }

        $medicines = $this->prescriptionRepository->getMedicines();
        $doctors = $this->doctorRepository->getDoctors();
        $data = $this->medicineRepository->getSyncList();
        $medicineList = $this->medicineRepository->getMedicineList($medicines['medicines']);
        $mealList = $this->medicineRepository->getMealList();
        // dd($medicines);
        $medicines = $medicines['medicines'];
        // return $medicines;
        return view('prescriptions.create',
            compact('patients', 'doctors', 'medicines', 'mealList'))->with($data);
    }

    /**
     * Store a newly created Prescription in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePrescriptionRequest $request)
    {
        // dd($request->all());
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['TestsandConsultations'] = $request->input('TestsandConsultations') ?? '';
        $input['PatientEducation'] = $request->input('PatientEducation') ?? '';


        $prescription = $this->prescriptionRepository->create($input);
        $this->prescriptionRepository->createPrescription($input, $prescription);
        $this->prescriptionRepository->createNotification($input);
        Flash::success(__('messages.prescription.prescription').' '.__('messages.common.saved_successfully'));

        return redirect(route('prescriptions.index'));
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(Prescription $prescription)
    {


        $prescription = $this->prescriptionRepository->find($prescription->id);
        if (empty($prescription)) {
            Flash::error('Prescription not found');

            return redirect(route('prescriptions.index'));
        }

        return view('prescriptions.show')->with('prescription', $prescription);
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function edit(Prescription $prescription)
    {
        if (checkRecordAccess($prescription->doctor_id)) {
            return view('errors.404');
        } else {
            $prescription->getMedicine;
            $PrescriptionMedicine = PrescriptionMedicineModal::where('prescription_id',$prescription->id)->with('medicine')->get();
            $patients = $this->prescriptionRepository->getPatients();
            // dd($patients);
            $doctors = $this->doctorRepository->getDoctors();
            $medicines = $this->prescriptionRepository->getMedicines();
            $data = $this->medicineRepository->getSyncList();
            $medicineList = $this->medicineRepository->getMedicineList($medicines['medicines']);
            $mealList = $this->medicineRepository->getMealList();


            return view('prescriptions.edit',
                compact('patients', 'prescription', 'doctors', 'medicines', 'medicineList', 'mealList','PrescriptionMedicine'))->with($data);
        }
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function update(Prescription $prescription, UpdatePrescriptionRequest $request)
    {
        $prescription = $this->prescriptionRepository->find($prescription->id);
        if (empty($prescription)) {
            Flash::error('Prescription not found');

            return redirect(route('prescriptions.index'));
        }
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $this->prescriptionRepository->updatePrescription($prescription, $request->all());

        Flash::success(__('messages.prescription.prescription').' '.__('messages.common.updated_successfully'));

        return redirect(route('prescriptions.index'));
    }

    /**
     * @return JsonResponse|RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function destroy(Prescription $prescription)
    {
        if (checkRecordAccess($prescription->doctor_id)) {
            $this->sendError(__('messages.prescription.prescription').' '.__('messages.common.not_found'));
        } else {
            $prescription = $this->prescriptionRepository->find($prescription->id);
            if (empty($prescription)) {
                Flash::error('Prescription not found');

                return redirect(route('prescriptions.index'));
            }
            $prescription->delete();

            return $this->sendSuccess(__('messages.prescription.prescription').' '.__('messages.common.deleted_successfully'));
        }
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function activeDeactiveStatus($id)
    {
        $prescription = Prescription::findOrFail($id);
        if (checkRecordAccess($prescription->doctor_id)) {
            return $this->sendError(__('messages.prescription.prescription').' '.__('messages.common.not_found'));
        } else {
            $status = ! $prescription->status;
            $prescription->update(['status' => $status]);

            return $this->sendSuccess(__('messages.common.status_updated_successfully'));
        }
    }
    //
    //    /**
    //     * @param $id
    //     *
    //     * @return JsonResponse
    //     */
    //    public function showModal($id)
    //    {
    //        $prescription = $this->prescriptionRepository->find($id);
    //        $prescription->load(['patient.user', 'doctor.user']);
    //        if (empty($prescription)) {
    //            return $this->sendError('Prescription Not Found');
    //        }
    //
    //        return $this->sendResponse($prescription, 'Prescription Retrieved Successfully');
    //    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function prescriptionsView($id)
    {
        // $data = $this->prescriptionRepository->getSettingList();

        // $prescription = $this->prescriptionRepository->getData($id);
        // if (checkRecordAccess($prescription['prescription']->doctor_id)) {
        //     return view('errors.404');
        // } else {
        //     $medicines = $this->prescriptionRepository->getMedicineData($id);
        // dd($medicines);

        // return view('prescriptions.view', compact('prescription', 'medicines', 'data'));
        return view('prescriptions.view', [
            'prescriptions' => Prescription::where('id', $id)->with(['getMedicine.medicine', 'doctor.user', 'patient.user'])->get(),
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function prescreptionMedicineStore(CreateMedicineRequest $request)
    {
        $input = $request->all();

        $this->medicineRepository->create($input);

        return $this->sendSuccess('Medicine saved successfully.');
    }

    public function convertToPDF($id)
    {
        // $data = $this->prescriptionRepository->getSettingList();

        // $prescription = $this->prescriptionRepository->getData($id);

        // $medicines = $this->prescriptionRepository->getMedicineData($id);

        //        App::setLocale(getCurrentLoginUserLanguageName());

        $prescription = Prescription::where('id', $id)->with('getMedicine.medicine', 'doctor.doctorUser', 'patient.patientUser')->first();

        // set_time_limit(120);
        // dd($prescriptions);

        // $pdf = PDF::loadView('prescriptions.prescription_pdf', compact('prescription'));

        // return $pdf->stream($prescription->patient->patientUser->full_name.'-'.$prescription->id);
        return view('prescriptions.prescription_pdf', [
            'prescription' => $prescription,
        ]);
    }
}
