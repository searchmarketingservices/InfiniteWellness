<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\Patient;
use App\Repositories\BillRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Response;
use App\Models\Discount;

class BillController extends AppBaseController
{
    /** @var BillRepository */
    private $billRepository;

    public function __construct(BillRepository $billRepo)
    {
        $this->billRepository = $billRepo;
    }

    /**
     * Display a listing of the Bill.
     *
     * @param  Request  $request
     * @return Factory|View|Response
     *
     * @throws Exception
     */
    public function index()
    {
        return view('bills.index');
    }

    /**
     * Show the form for creating a new Bill.
     *
     * @return Factory|View
     */
    public function create()
    {
        $discout = Discount::where('active', 1)->get();

        // $dd = DB::table('opd_patient_departments')->get();
        // $dd2 = DB::table('dental_opd_patient_departments')->get();

        $dd = DB::select('select * from opd_patient_departments inner join patients on patients.id = opd_patient_departments.patient_id inner join users on users.id = patients.user_id');
        $dd2 = DB::select('select * from dental_opd_patient_departments inner join patients on patients.id = dental_opd_patient_departments.patient_id inner join users on users.id = patients.user_id');

        $bills = Bill::PAYMENT_MODE;
        $db = [];

        foreach ($dd as $d) {
            $db[$d->patient_id] = $d->opd_number . ' - ' . $d->first_name . ' ' . $d->last_name . ' - ' . $d->phone;
        }
        foreach ($dd2 as $d) {
            $db[$d->patient_id] = $d->opd_number . ' - ' . $d->first_name . ' ' . $d->last_name . ' - ' . $d->phone;
        }

        $data = $this->billRepository->getSyncList(false);
        $data['opd'] = $db;
        $data['discount'] = $discout;

        return view('bills.opdCreate', compact('data', 'bills'));
    }


    public function opdCreate()
    {
        $dd = DB::table('opd_patient_departments')->get();
        $db = [];
        foreach ($dd as $d) {
            $db[$d->patient_id] = $d->opd_number;
        }
        //return $db;
        $data = $this->billRepository->getSyncList(false);
        $data['opd'] = $db;

        //return $data;
        return view('bills.opdCreate')->with($data);
    }

    public function opdGetPatient(Request $request)
    {
        $docID = DB::table('opd_patient_departments')->where(['opd_number' => $request->opdID])->get();
        if(count($docID) == 0){
            $docID = DB::table('dental_opd_patient_departments')->where(['opd_number' => $request->opdID])->get();
        }

        $patientData = DB::table('users')->where(['owner_id' => $docID[0]->patient_id])->where('owner_type', 'LIKE', '%Patient%')->first();

        $docData = DB::table('users')->where(['owner_id' => $docID[0]->doctor_id])->where('owner_type', 'LIKE', '%Doctor%')->first();
        $patientData->doctor = $docData;

        if($docID[0]->is_old_patient){
            if($docID[0]->standard_charge == 0){
                $patientData->followup_charge = $docID[0]->followup_charge;
                $patientData->advance_amount = $docID[0]->advance_amount;
            }
            else{
                $patientData->charges = $docID[0]->standard_charge;
                $patientData->advance_amount = $docID[0]->advance_amount;
            }
        }
        else{
            if($docID[0]->standard_charge == 0){
                $patientData->followup_charge = $docID[0]->followup_charge;
                $patientData->advance_amount = $docID[0]->advance_amount;
            }
            else{
                $patientData->charges = $docID[0]->standard_charge;
                $patientData->advance_amount = $docID[0]->advance_amount;
            }
        }

        if (count($docID) > 0 && isset($docID[0]->service_id)) {
            $patientData->service_id = $docID[0]->service_id;
        }

        return $patientData;
    }

    /**
     * Store a newly created Bill in storage.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(CreateBillRequest $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();
            $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
            $birthDate = $patientId->patientUser->dob;
            $billDate = Carbon::parse($input['bill_date'])->toDateString();

            if (! empty($birthDate) && $billDate < $birthDate) {
                return $this->sendError(__('messages.bed_assign.assign_date_should_not_be_smaller_than_patient_birth_date'));

            }

            $bill = $this->billRepository->saveBill($request->all());
            //return $this->sendError("testing");
            $this->billRepository->saveNotification($input);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.bill.bill').' '.__('messages.common.saved_successfully'));
    }

    /**
     * Display the specified Bill.
     *
     * @return Factory|View
     */
    public function show(Bill $bill)
    {
        $bill = Bill::with(['billItems.medicine', 'patient', 'patientAdmission'])->find($bill->id);

        if ($bill->patientAdmission) {
            $admissionDate = Carbon::parse($bill->patientAdmission->admission_date);
            $dischargeDate = Carbon::parse($bill->patientAdmission->discharge_date);
            $bill->totalDays = $admissionDate->diffInDays($dischargeDate) + 1;
        } else {

            $docID = DB::table('opd_patient_departments')->where(['opd_number' => $bill->patient_admission_id])->get();
            // dd($bill);

            if (isset($docID[0])) {
                $docData = DB::table('users')->where(['owner_id' => $docID[0]->doctor_id])->where('owner_type', 'LIKE', '%Doctor%')->first();
                $bill->doctor = $docData->first_name.' '.$docData->last_name;
            }else{
                $docData = null;
            }

            //$bill->doctor = $docData;

        }

        return view('bills.show')->with('bill', $bill);
    }

    /**
     * Show the form for editing the specified Bill.
     *
     * @return Factory|View
     */
    public function edit(Bill $bill)
    {
        $bill->billItems;
        $isEdit = true;
        $data = $this->billRepository->getSyncList($isEdit);
        $data['bill'] = $bill;

        return view('bills.edit')->with($data);
    }

    /**
     * Update the specified Bill in storage.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(Bill $bill, UpdateBillRequest $request)
    {
        $input = $request->all();
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $billDate = Carbon::parse($input['bill_date'])->toDateString();
        if (! empty($birthDate) && $billDate < $birthDate) {
            return $this->sendError(__('messages.bed_assign.assign_date_should_not_be_smaller_than_patient_birth_date'));
        }
        $bill = $this->billRepository->updateBill($bill->id, $request->all());

        return $this->sendResponse($bill, __('messages.bill.bill').' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified Bill from storage.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Bill $bill)
    {
        $this->billRepository->delete($bill->id);

        return $this->sendSuccess(__('messages.bill.bill').' '.__('messages.common.deleted_successfully'));
    }

    /**
     * @return JsonResponse
     */
    public function getPatientAdmissionDetails(Request $request)
    {
        $inputs = $request->all();
        $patientAdmissionDetails = $this->billRepository->patientAdmissionDetails($inputs);

        return $this->sendResponse($patientAdmissionDetails, 'Details retrieved successfully.');
    }

    /**
     * @return \Illuminate\Http\Response
     */

    public function convertToPdf(Bill $bill)
    {
        try {
            $bill->billItems;
            $data = $this->billRepository->getSyncListForCreate($bill->id);
            $data['bill'] = $bill;

            if ($bill->patientAdmission) {
                $admissionDate = Carbon::parse($bill->patientAdmission->admission_date);
                $dischargeDate = Carbon::parse($bill->patientAdmission->discharge_date);
                $bill->totalDays = $admissionDate->diffInDays($dischargeDate) + 1;
            } else {
                $docID = DB::table('opd_patient_departments')->where(['opd_number' => $bill->patient_admission_id])->get();

                if ($docID->isEmpty()) {
                    throw new \Exception("Doctor data not found");
                }

                $docData = DB::table('users')->where(['owner_id' => $docID[0]->doctor_id])->where('owner_type', 'LIKE', '%Doctor%')->first();

                if (!$docData) {
                    // throw new \Exception("Doctor data not found");
                    $docData = "Doctor Not Found!";
                }

                $bill->doctor = $docData->first_name.' '.$docData->last_name;
            }

            return view('bills.bill_pdf',$data);
            $pdf = PDF::loadView('bills.bill_pdf', $data);

            return $pdf->stream('bill.pdf');
        } catch (\Exception $e) {
            // Handle the exception here
            $docData = "Doctor Not Found!";
            return view('bills.bill_pdf',$data);

            // return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function print(Bill $bill)
    {
        dd($bill);
    }
}
