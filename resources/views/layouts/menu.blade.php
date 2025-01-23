@php($modules = App\Models\Module::cacheFor(now()->addDays())->toBase()->get())
{{-- For CSR Role --}}
@if (auth()->user()->hasRole('CSR'))
@module('Appointments', $modules)
        <li class="nav-item {{ Request::is('appointment*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('appointments.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-calendar-check"></i></span>
                <span class="aside-menu-title">{{ __('messages.appointments') }}</span>
            </a>
        </li>
    @endmodule

{{-- Billing --}}
<?php
    $billingMGT = getMenuLinks(\App\Models\User::MAIN_BILLING_MGT);
    ?>
    @if ($billingMGT)
        <li
            class="nav-item  {{ Request::is('accounts*', 'employee-payrolls*', 'invoices*', 'payments*', 'payment-reports*', 'advanced-payments*', 'bills*', 'pos*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $billingMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="aside-menu-title">{{ __('messages.billing') }}</span>
                <span class="d-none">{{ __('messages.employee_payrolls') }}</span>
                <span class="d-none">{{ __('messages.invoices') }}</span>
                <span class="d-none">{{ __('messages.payments') }}</span>
                <span class="d-none">{{ __('messages.payment_reports') }}</span>
                <span class="d-none">{{ __('messages.advanced_payments') }}</span>
                <span class="d-none">{{ __('messages.bills') }}</span>
                <span class="d-none">{{ __('messages.pos') }}</span>
            </a>
        </li>
    @endif
    {{-- Doctors --}}
    @module('Doctors', $modules)
        <li class="nav-item  {{ Request::is('doctors*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('doctors.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
            </a>
        </li>
    @endmodule
    {{-- Front office --}}
    <?php
    $frontReceptionistOfficeMGT = getMenuLinks(\App\Models\User::MAIN_FRONT_OFFICE);
    ?>
    @if ($frontReceptionistOfficeMGT)
        <li class="nav-item {{ Request::is('call-logs*', 'visitor*', 'receives*', 'dispatches*') ? 'active' : '' }}">
            <a href="{{ $frontReceptionistOfficeMGT }}" class="py-3 nav-link d-flex align-items-center">
                <span class="aside-menu-icon pe-3"><i class="fa fa-dungeon"></i></span>
                <span class="aside-menu-title">{{ __('messages.front_office') }}</span>
                <span class="d-none">{{ __('messages.call_logs') }}</span>
                <span class="d-none">{{ __('messages.visitors') }}</span>
                <span class="d-none">{{ __('messages.postal_receive') }}</span>
                <span class="d-none">{{ __('messages.postal_dispatch') }}</span>
            </a>
        </li>
    @endif

    {{-- Hospital Charges --}}
    <?php
    $hospitalCharge = getMenuLinks(\App\Models\User::MAIN_HOSPITAL_CHARGE);
    ?>
    @if ($hospitalCharge)
        <li
            class="nav-item  {{ Request::is('charge-categories*', 'charges*', 'doctor-opd-charges*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $hospitalCharge }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-coins"></i></span>
                <span class="aside-menu-title">Charges List</span>
                <span class="d-none">{{ __('messages.charge_categories') }}</span>
                <span class="d-none">{{ __('messages.charges') }}</span>
                <span class="d-none">{{ __('messages.doctor_opd_charges') }}</span>
            </a>
        </li>
    @endif

    {{-- Cases Mgt --}}
    <?php
    $patientCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
    ?>
    @if ($patientCaseMgt)
        <li
            class="nav-item  {{ Request::is('patients*', 'patient-cases*', 'case-handlers*', 'patient-admissions*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientCaseMgt }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-user-injured"></i>
                </span>
                <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                <span class="d-none">{{ __('messages.cases') }}</span>
                <span class="d-none">{{ __('messages.case_handlers') }}</span>
                <span class="d-none">{{ __('messages.patient_admissions') }}</span>
            </a>
        </li>
    @endif

    @module('Patient Admissions', $modules)
        <li class="nav-item  {{ Request::is('employee/patient-admissions*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/patient-admissions') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-history"></i></span>
                <span class="aside-menu-title">{{ __('messages.patient_admissions') }}</span>
            </a>
        </li>
    @endmodule


    {{-- ipds/opds --}}
    <?php
    $ipdOPD = getMenuLinks(\App\Models\User::MAIN_IPD_OPD);
    ?>
    @if ($ipdOPD)
        <li class="nav-item  {{ Request::is('ipds*', 'opds*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('opd.patient.index') }}"
                title="{{ __('messages.ipd_opd') }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-notes-medical"></i>
                </span>
                <span class="aside-menu-title">OPD</span>
                <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                <span class="d-none">{{ __('messages.opd_patients') }}</span>
            </a>
        </li>
    @endif

    <li class="nav-item  {{ Request::is('dentalOpds*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dentalopd.patient.index') }}"
            title="{{ __('messages.ipd_opd') }}">
            <span class="aside-menu-icon pe-3">
                <i class="fas fa-notes-medical"></i>
            </span>
            <span class="aside-menu-title">Dental OPD</span>
            <span class="d-none">{{ __('messages.ipd_patients') }}</span>
            <span class="d-none">{{ __('messages.opd_patients') }}</span>
        </a>
    </li>


    {{-- Services dropdown --}}
    <?php
    $receptionistServiceMgt = getMenuLinks(\App\Models\User::MAIN_SERVICE);
    ?>
    @if ($receptionistServiceMgt)
        <li
            class="nav-item {{ Request::is('insurances*', 'packages*', 'services*', 'ambulances*', 'ambulance-calls*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistServiceMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-box"></i></span>
                <span class="aside-menu-title">{{ __('messages.services') }}</span>
                <span class="d-none">{{ __('messages.insurances') }}</span>
                <span class="d-none">{{ __('messages.packages') }}</span>
                <span class="d-none">{{ __('messages.services') }}</span>
                <span class="d-none">{{ __('messages.ambulances') }}</span>
                <span class="d-none">{{ __('messages.ambulance_calls') }}</span>
            </a>
        </li>
    @endif

@endif
{{-- <div class="mx-3 mt-2 mb-5 position-relative sidebar-search-box"> --}}
{{--    <span class="svg-icon svg-icon-1 svg-icon-primary position-absolute top-50 translate-middle ms-9"> --}}
{{--                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" --}}
{{--                                                                 height="24" viewBox="0 0 24 24" fill="none"> --}}
{{--                                                                <rect opacity="0.5" x="17.0365" y="15.1223" --}}
{{--                                                                      width="8.15546" height="2" rx="1" --}}
{{--                                                                      transform="rotate(45 17.0365 15.1223)" --}}
{{--                                                                      fill="black"></rect> --}}
{{--                                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" --}}
{{--                                                                      fill="black"></path> --}}
{{--                                                            </svg> --}}
{{--                                                        </span> --}}
{{--    <input type="text" class="form-control form-control-lg ps-15" id="menuSearch" name="search" --}}
{{--           value="" placeholder="Search" style="background-color: #2A2B3A;border: none;color: #FFFFFF" --}}
{{--           autocomplete="off"> --}}
{{-- </li> --}}
{{-- <div class="text-center text-white no-record d-none">No matching records found</li> --}}
@role('Admin')
    {{-- Dashboard --}}
    <li class="nav-item {{ Request::is('dashboard*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dashboard') }}">
            <span class="aside-menu-icon pe-3">
                <i class="fas fa-chart-pie"></i>
            </span>
            <span class="aside-menu-title">{{ __('messages.dashboard.dashboard') }}</span>
        </a>
    </li>

    {{-- Admin --}}
    @module('Admin', $modules)
        <li class="nav-item  {{ Request::is('admins*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('admins.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-user"></i></span>
                <span class="aside-menu-title">{{ __('messages.admin') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Accountantants --}}
    @module('Accountants', $modules)
        <li class="nav-item  {{ Request::is('accountants*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('accountants.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice"></i></span>
                <span class="aside-menu-title">{{ __('messages.accountants') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Appointments --}}
    @module('Appointments', $modules)
        <li class="nav-item {{ Request::is('appointment*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('appointments.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-calendar-check"></i></span>
                <span class="aside-menu-title">{{ __('messages.appointments') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Billing --}}
    <?php
    $billingMGT = getMenuLinks(\App\Models\User::MAIN_BILLING_MGT);
    ?>
    @if ($billingMGT)
        <li
            class="nav-item  {{ Request::is('accounts*', 'employee-payrolls*', 'invoices*', 'payments*', 'payment-reports*', 'advanced-payments*', 'bills*', 'pos*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $billingMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="aside-menu-title">{{ __('messages.billing') }}</span>
                <span class="d-none">{{ __('messages.employee_payrolls') }}</span>
                <span class="d-none">{{ __('messages.invoices') }}</span>
                <span class="d-none">{{ __('messages.payments') }}</span>
                <span class="d-none">{{ __('messages.payment_reports') }}</span>
                <span class="d-none">{{ __('messages.advanced_payments') }}</span>
                <span class="d-none">{{ __('messages.bills') }}</span>
                <span class="d-none">{{ __('messages.pos') }}</span>
            </a>
        </li>
    @endif

    <?php
    $bedMGT = getMenuLinks(\App\Models\User::MAIN_BED_MGT);
    ?>
    @if ($bedMGT)
        {{-- Bed Management  --}}
        <li
            class="nav-item  {{ Request::is('bed-types*', 'beds*', 'bed-assigns*', 'bulk-beds', 'bed-status') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $bedMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-bed"></i></span>
                <span class="aside-menu-title">{{ __('messages.bed_management') }}</span>
                <span class="d-none">{{ __('messages.bed_types') }}</span>
                <span class="d-none">{{ __('messages.beds') }}</span>
                <span class="d-none">{{ __('messages.bed_assigns') }}</span>
            </a>
        </li>
    @endif

    {{-- Blood Bank dropdown --}}
    <?php
    $bloodbankMGT = getMenuLinks(\App\Models\User::MAIN_BLOOD_BANK_MGT);
    ?>
    @if ($bloodbankMGT)
        <li
            class="nav-item  {{ Request::is('blood-banks*', 'blood-donors*', 'blood-donations*', 'blood-issues*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $bloodbankMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
                <span class="aside-menu-title">{{ __('messages.blood_bank') }}</span>
                <span class="d-none">{{ __('messages.blood_donors') }}</span>
                <span class="d-none">{{ __('messages.blood_donations') }}</span>
                <span class="d-none">{{ __('messages.blood_issues') }}</span>
            </a>
        </li>
    @endif

    {{-- dietitian-form --}}


    <li class="nav-item  {{ Request::is('dietitian-form*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dietitan.index') }}">
            <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
            <span class="aside-menu-title">Dietitan Assessment</span>
        </a>
    </li>
    <li class="nav-item  {{ Request::is('discount*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('discount.index') }}">
            <span class="aside-menu-icon pe-3"><i class="fas fa-cash-register"></i></span>
            <span class="aside-menu-title">Discounts</span>
        </a>
    </li>




    {{-- Documents Mgt --}}
    <?php
    $documentMGT = getMenuLinks(\App\Models\User::MAIN_DOCUMENT);
    ?>
    @if ($documentMGT)
        <li class="nav-item {{ Request::is('documents*', 'document-types*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $documentMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file"></i></span>
                <span class="aside-menu-title">{{ __('messages.documents') }}</span>
                <span class="d-none">{{ __('messages.document_types') }}</span>
            </a>
        </li>
    @endif

    {{-- Doctors dropdown --}}
    <?php
    $doctorMGT = getMenuLinks(\App\Models\User::MAIN_DOCTOR);
    ?>
    @if ($doctorMGT)
        <li
            class="nav-item  {{ Request::is('doctors*', 'doctor-departments*', 'schedules*', 'prescriptions*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $doctorMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
                <span class="d-none">{{ __('messages.doctor_departments') }}</span>
                <span class="d-none">{{ __('messages.schedules') }}</span>
                <span class="d-none">{{ __('messages.prescriptions') }}</span>
            </a>
        </li>
    @endif

    {{-- Diagnosis Test --}}
    <?php
    $diagnosisMGT = getMenuLinks(\App\Models\User::MAIN_DIAGNOSIS);
    ?>
    @if ($diagnosisMGT)
        <li class="nav-item  {{ Request::is('diagnosis-categories*', 'patient-diagnosis-test*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $diagnosisMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-diagnoses"></i></span>
                <span class="aside-menu-title">{{ __('messages.patient_diagnosis_test.diagnosis') }}</span>
                <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_category') }}</span>
                <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_test') }}</span>
            </a>
        </li>
    @endif

    {{-- Enquiries --}}
    @module('Enquires', $modules)
        <li class="nav-item  {{ Request::is('enquiries*') || Request::is('enquiry*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('enquiries') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-question-circle"></i></span>
                <span class="aside-menu-title">{{ __('messages.enquiries') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Finance --}}
    <?php
    $financeMGT = getMenuLinks(\App\Models\User::MAIN_FINANCE);
    ?>
    @if ($financeMGT)
        <li class="nav-item  {{ Request::is('incomes*', 'expenses*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $financeMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-money-bill"></i></span>
                <span class="aside-menu-title">{{ __('messages.finance') }}</span>
                <span class="d-none">{{ __('messages.incomes.incomes') }}</span>
                <span class="d-none">{{ __('messages.expenses') }}</span>
                <span class="d-none">POS Report</span>
                <span class="d-none">POS Return Report</span>
                <span class="d-none">POS Daily Report</span>
            </a>
        </li>
    @endif

    {{-- Front office --}}
    <?php
    $frontOfficeMGT = getMenuLinks(\App\Models\User::MAIN_FRONT_OFFICE);
    ?>
    @if ($frontOfficeMGT)
        <li class="nav-item  {{ Request::is('call-logs*', 'visitor*', 'receives*', 'dispatches*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $frontOfficeMGT }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-dungeon"></i></span>
                <span class="aside-menu-title">Visitors</span>
                <span class="d-none">{{ __('messages.call_logs') }}</span>
                <span class="d-none">{{ __('messages.visitors') }}</span>
                <span class="d-none">{{ __('messages.postal_receive') }}</span>
                <span class="d-none">{{ __('messages.postal_dispatch') }}</span>
            </a>
        </li>
    @endif

    {{-- Front settings
<li class="nav-item {{ Request::is('front-settings*','notice-boards*','testimonials*', 'front-cms-services*') ? 'active' : '' }}">
    <a class="py-3 nav-link d-flex align-items-center"
       href="{{ route('front.settings.index') }}">
                                                                        <span class="aside-menu-icon pe-3"><i
                                                                                    class="fas fa fa-cog"></i></span>
        <span class="aside-menu-title">{{ __('messages.front_cms') }}</span>
        <span class="d-none">{{ __('messages.notice_boards') }}</span>
        <span class="d-none">{{ __('messages.testimonials') }}</span>
        <span class="d-none">{{ __('messages.cms') }}</span>
        <span class="d-none">{{ __('messages.front_cms_services') }}</span>
    </a>
</li> --}}

    {{-- Hospital Charges --}}
    <?php
    $ReceptionisthospitalCharge = getMenuLinks(\App\Models\User::MAIN_HOSPITAL_CHARGE);
    ?>
    @if ($ReceptionisthospitalCharge)
        <li class="nav-item  {{ Request::is('charge-categories*', 'charges*', 'doctor-opd-charges*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $ReceptionisthospitalCharge }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-coins"></i></span>
                <span class="aside-menu-title">{{ __('messages.hospital_charges') }}</span>
                <span class="d-none">{{ __('messages.charge_categories') }}</span>
                <span class="d-none">{{ __('messages.charges') }}</span>
                <span class="d-none">{{ __('messages.doctor_opd_charges') }}</span>
            </a>
        </li>
    @endif

    {{-- ipds/opds --}}
    <?php
    $ipdOPD = getMenuLinks(\App\Models\User::MAIN_IPD_OPD);
    ?>
    @if ($ipdOPD)
        <li class="nav-item  {{ Request::is('ipds*', 'opds*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('opd.patient.index') }}"
                title="{{ __('messages.ipd_opd') }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-notes-medical"></i>
                </span>
                <span class="aside-menu-title">OPD</span>
                <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                <span class="d-none">{{ __('messages.opd_patients') }}</span>
            </a>
        </li>
    @endif

    <li class="nav-item  {{ Request::is('dentalOpds*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dentalopd.patient.index') }}"
            title="{{ __('messages.ipd_opd') }}">
            <span class="aside-menu-icon pe-3">
                <i class="fas fa-notes-medical"></i>
            </span>
            <span class="aside-menu-title">Dental OPD</span>
            <span class="d-none">{{ __('messages.ipd_patients') }}</span>
            <span class="d-none">{{ __('messages.opd_patients') }}</span>
        </a>
    </li>
    {{-- Inventory Management  --}}
    <?php
    $inventoryMgt = getMenuLinks(\App\Models\User::MAIN_INVENTORY);
    ?>
    @if ($inventoryMgt)
        <li
            class="nav-item {{ Request::is('item-categories*', 'items*', 'item-stocks*', 'issued-items*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $inventoryMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-luggage-cart"></i></span>
                <span class="aside-menu-title">{{ __('messages.inventory') }}</span>
                <span class="d-none">{{ __('messages.items_categories') }}</span>
                <span class="d-none">{{ __('messages.items') }}</span>
                <span class="d-none">{{ __('messages.items_stocks') }}</span>
                <span class="d-none">{{ __('messages.issued_items') }}</span>
            </a>
        </li>
    @endif

    {{-- Lab Technician --}}

    @module('Lab Technicians', $modules)
        <li class="nav-item  {{ Request::is('lab-technicians*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('lab-technicians.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-microscope"></i></span>
                <span class="aside-menu-title">{{ __('messages.lab_technicians') }}</span>
            </a>
        </li>
    @endmodule



    {{-- Live Consultation --}}
    <?php
    $liveConsultation = getMenuLinks(\App\Models\User::MAIN_LIVE_CONSULATION);
    ?>
    @if ($liveConsultation)
        <li class="nav-item  {{ Request::is('live-consultation*', 'live-meeting*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $liveConsultation }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-video"></i></span>
                <span class="aside-menu-title">{{ __('messages.live_consultations') }}</span>
                <span class="d-none">{{ __('messages.live_meetings') }}</span>
            </a>
        </li>
    @endif

    {{-- Medicines dropdown --}}
    <?php
    $medicineMgt = getMenuLinks(\App\Models\User::MAIN_MEDICINES);
    ?>
    @if ($medicineMgt)
        <li class="nav-item  {{ Request::is('categories*', 'brands*', 'medicines*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $medicineMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-capsules"></i></span>
                <span class="aside-menu-title">{{ __('messages.medicines') }}</span>
                <span class="d-none">{{ __('messages.medicine_categories') }}</span>
                <span class="d-none">{{ __('messages.medicine_brands') }}</span>
                <span class="d-none">{{ __('messages.medicines') }}</span>
            </a>
        </li>
    @endif

    <li class="nav-item  {{ Request::is('nursing-form*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nursing.index') }}">
            <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
            <span class="aside-menu-title">Nursing Form </span>
        </a>
    </li>
    <li class="nav-item  {{ Request::is('medication-administration*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('medication.index') }}">
            <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
            <span class="aside-menu-title">Medication Administration</span>
        </a>
    </li>

    @role('Admin|Pharmacist|Receptionist|Nurse|Doctor|Dietitian')
        <li class="nav-item  {{ Request::is('fast-medical-record*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('fast-medical-record.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-cog"></i></span>
                <span class="aside-menu-title">Fast Medical Record</span>
            </a>
        </li>
    @endrole
    @role('Admin|Pharmacist|Receptionist|Nurse|Doctor|Dietitian')
        <li class="nav-item  {{ Request::is('functional-medicine*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('functional-medicine.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-medkit"></i></span>
                <span class="aside-menu-title">Functional Medicine</span>
            </a>
        </li>
    @endrole

    {{-- Nursers --}}
    @module('Nurses', $modules)
        <li class="nav-item  {{ Request::is('nurses*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nurses.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">{{ __('messages.nurses') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Cases Mgt --}}
    <?php
    $patientCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
    ?>
    @if ($patientCaseMgt)
        <li
            class="nav-item  {{ Request::is('patients*', 'patient-cases*', 'case-handlers*', 'patient-admissions*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientCaseMgt }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-user-injured"></i>
                </span>
                <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                <span class="d-none">{{ __('messages.cases') }}</span>
                <span class="d-none">{{ __('messages.case_handlers') }}</span>
                <span class="d-none">{{ __('messages.patient_admissions') }}</span>
            </a>
        </li>
    @endif

    {{-- Pharmacsist --}}
    @module('Pharmacists', $modules)
        <li class="nav-item {{ Request::is('pharmacists*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('pharmacists.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-prescription"></i></span>
                <span class="aside-menu-title">{{ __('messages.pharmacists') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Pathology --}}
    <?php
    $pathologyMgt = getMenuLinks(\App\Models\User::MAIN_PATHOLOGY);
    ?>
    @if ($pathologyMgt)
        <li class="nav-item  {{ Request::is('pathology-categories*', 'pathology-tests*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $pathologyMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-flask"></i></span>
                <span class="aside-menu-title">{{ __('messages.pathologies') }}</span>
                <span class="d-none">{{ __('messages.pathology_categories') }}</span>
                <span class="d-none">{{ __('messages.pathology_tests') }}</span>
            </a>
        </li>
    @endif

    {{-- Receptinist --}}
    @module('Receptionists', $modules)
        <li class="nav-item  {{ Request::is('receptionists*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('receptionists.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-tie"></i></span>
                <span class="aside-menu-title">{{ __('messages.receptionists') }}</span>
            </a>
        </li>
    @endmodule

    {{-- Hospital Activities dropdown --}}
    <?php
    $reportMgt = getMenuLinks(\App\Models\User::MAIN_REPORT);
    ?>
    @if ($reportMgt)
        <li
            class="nav-item  {{ Request::is('birth-reports*', 'death-reports*', 'investigation-reports*', 'operation-reports*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $reportMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-medical"></i></span>
                <span class="aside-menu-title">{{ __('messages.reports') }}</span>
                <span class="d-none">{{ __('messages.birth_reports') }}</span>
                <span class="d-none">{{ __('messages.death_reports') }}</span>
                <span class="d-none">{{ __('messages.investigation_reports') }}</span>
                <span class="d-none">{{ __('messages.operation_reports') }}</span>
            </a>
        </li>
    @endif

    {{-- Radiology --}}
    <?php
    $radiology = getMenuLinks(\App\Models\User::MAIN_RADIOLOGY);
    ?>
    @if ($radiology)
        <li class="nav-item {{ Request::is('radiology-categories*', 'radiology-tests*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $radiology }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-x-ray"></i></span>
                <span class="aside-menu-title">{{ __('messages.radiologies') }}</span>
                <span class="d-none">{{ __('messages.radiology_categories') }}</span>
                <span class="d-none">{{ __('messages.radiology_tests') }}</span>
            </a>
        </li>
    @endif

    {{-- Services dropdown --}}
    <?php
    $serviceMgt = getMenuLinks(\App\Models\User::MAIN_SERVICE);
    ?>
    @if ($serviceMgt)
        <li
            class="nav-item {{ Request::is('insurances*', 'packages*', 'services*', 'ambulances*', 'ambulance-calls*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $serviceMgt }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-box"></i></span>
                <span class="aside-menu-title">{{ __('messages.services') }}</span>
                <span class="d-none">{{ __('messages.insurances') }}</span>
                <span class="d-none">{{ __('messages.packages') }}</span>
                <span class="d-none">{{ __('messages.services') }}</span>
                <span class="d-none">{{ __('messages.ambulances') }}</span>
                <span class="d-none">{{ __('messages.ambulance_calls') }}</span>
            </a>
        </li>
    @endif

    {{-- sms/mail --}}
    <?php
    $smsMailMgt = getMenuLinks(\App\Models\User::MAIN_SMS_MAIL);
    ?>
    @if ($smsMailMgt)
        <li class="nav-item  {{ Request::is('sms*', 'mail*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $smsMailMgt }}"
                title="{{ __('SMS/Mail') }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-bell"></i>
                </span>
                <span class="aside-menu-title">{{ __('messages.sms.sms') }}/{{ __('messages.mail') }}</span>
                <span class="d-none">{{ __('messages.sms.sms') }}</span>
                <span class="d-none">{{ __('messages.mail') }}</span>
            </a>
        </li>
    @endif

    {{-- Settings --}}
    <li class="nav-item  {{ Request::is('settings*', 'hospital-schedules*', 'currency-settings*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('settings.edit') }}">
            <span class="aside-menu-icon pe-3"><i class="fa fa-cogs"></i></span>
            <span class="aside-menu-title">{{ __('messages.settings') }}</span>
            <span class="d-none">{{ __('messages.general') }}</span>
            <span class="d-none">{{ __('messages.sidebar_setting') }}</span>
        </a>
    </li>


    {{-- Users --}}
    <li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
        <a class="py-3 nav-link d-flex align-items-center" href="{{ route('users.index') }}">
            <span class="aside-menu-icon pe-3">
                <i class="fas fa-user-friends"></i>
            </span>
            <span class="aside-menu-title">{{ __('messages.users') }}</span>
        </a>
    </li>

    {{-- Vaccination --}}
    <?php
    $vaccinationsPatient = getMenuLinks(\App\Models\User::MAIN_VACCINATION_MGT);
    ?>
    @if ($vaccinationsPatient)
        <li class="nav-item  {{ Request::is('vaccinated-patients*', 'vaccinations*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ $vaccinationsPatient }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-syringe"></i>
                </span>
                <span class="aside-menu-title">{{ __('messages.vaccinations') }}</span>
                <span class="d-none">{{ __('messages.vaccinated_patients') }}</span>
            </a>
        </li>
    @endif
@endrole
@if (Auth::user()->email_verified_at != null)
    @role('Doctor')
        @module('Appointments', $modules)
            <li class="nav-item  {{ Request::is('appointments*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('appointments.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="nav-icon fas fa-calendar-check"></i></span>
                    <span class="aside-menu-title">{{ __('messages.appointments') }}</span>
                </a>
            </li>
        @endmodule

        {{-- dietitian-form --}}


        <li class="nav-item  {{ Request::is('dietitian-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dietitan.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
                <span class="aside-menu-title">Dietitan Assessment</span>
            </a>
        </li>



        @module('Doctors', $modules)
            <li
                class="nav-item  {{ Request::is('employee/doctor*', 'prescriptions*', 'schedules*', 'doctors*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/doctor') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                    <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
                    <span class="d-none">{{ __('messages.schedules') }}</span>
                    <span class="d-none">{{ __('messages.prescriptions') }}</span>
                </a>
            </li>
        @endmodule

        @module('Documents', $modules)
            <li class="nav-item  {{ Request::is('documents*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('documents.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file"></i></span>
                    <span class="aside-menu-title">{{ __('messages.documents') }}</span>
                </a>
            </li>
        @endmodule


        {{-- ipds/opds --}}
        <?php
        $ipdOPD = getMenuLinks(\App\Models\User::MAIN_IPD_OPD);
        ?>
        @if ($ipdOPD)
            <li class="nav-item  {{ Request::is('ipds*', 'opds*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $ipdOPD }}"
                    title="{{ __('messages.ipd_opd') }}">
                    <span class="aside-menu-icon pe-3">
                        <i class="fas fa-notes-medical"></i>
                    </span>
                    <span class="aside-menu-title">OPD</span>
                    <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                    <span class="d-none">{{ __('messages.opd_patients') }}</span>
                </a>
            </li>
        @endif
        {{-- Dental OPDS --}}
        <li class="nav-item  {{ Request::is('dentalOpds*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dentalopd.patient.index') }}"
                title="{{ __('messages.ipd_opd') }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-notes-medical"></i>
                </span>
                <span class="aside-menu-title">Dental OPD</span>
                <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                <span class="d-none">{{ __('messages.opd_patients') }}</span>
            </a>
        </li>

        {{-- Nursers --}}
        @module('Nurses', $modules)
            <li class="nav-item  {{ Request::is('nurses*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nurses.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                    <span class="aside-menu-title">{{ __('messages.nurses') }}</span>
                </a>
            </li>
        @endmodule

        <li class="nav-item  {{ Request::is('nursing-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nursing.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Nursing Form </span>
            </a>
        </li>
        <li class="nav-item  {{ Request::is('medication-administration*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('medication.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Medication Administration</span>
            </a>
        </li>

        {{-- Patients --}}
        <?php
        $patientDoctorCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
        ?>
        @if ($patientDoctorCaseMgt)
            <li
                class="nav-item  {{ Request::is('patients*', 'patient-admissions*', 'patient-cases*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientDoctorCaseMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-user-injured"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                    <span class="d-none">{{ __('messages.patient_admissions') }}</span>
                </a>
            </li>
        @endif
        {{-- Reports --}}
        <?php
        $reportDoctorMgt = getMenuLinks(\App\Models\User::MAIN_REPORT);
        ?>
        @if ($reportDoctorMgt)
            <li
                class="nav-item  {{ Request::is('birth-reports*', 'death-reports*', 'investigation-reports*', 'operation-reports*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $reportDoctorMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file-medical"></i></span>
                    <span class="aside-menu-title">{{ __('messages.reports') }}</span>
                    <span class="d-none">{{ __('messages.birth_reports') }}</span>
                    <span class="d-none">{{ __('messages.death_reports') }}</span>
                    <span class="d-none">{{ __('messages.investigation_reports') }}</span>
                    <span class="d-none">{{ __('messages.operation_reports') }}</span>
                </a>
            </li>
        @endif

        {{-- SMS --}}
        @module('SMS', $modules)
            <li class="nav-item {{ Request::is('sms*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('sms.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-sms"></i></span>
                    <span class="aside-menu-title">{{ __('messages.sms.sms') }}</span>
                </a>
            </li>
        @endmodule
    @endrole

    @role('Case Manager')
        @module('Doctors', $modules)
            <li class="nav-item  {{ Request::is('employee/doctor*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/doctor') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                    <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Notice Boards --}}
        @module('Notice Boards', $modules)
            <li class="nav-item  {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>

            {{-- Live Meeting --}}
            @module('Live Meetings', $modules)
                <li class="nav-item  {{ Request::is('live-meeting*') ? 'active' : '' }}">
                    <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.meeting.index') }}">
                        <span class="aside-menu-icon pe-3"><i class="fa fa-file-video"></i></span>
                        <span class="aside-menu-title">{{ __('messages.live_meetings') }}</span>
                    </a>
                </li>
            @endmodule

            @module('My Payrolls', $modules)
                <li class="nav-item  {{ Request::is('employee/payroll*', 'employee-payrolls*') ? 'active' : '' }}">
                    <a class="py-3 nav-link d-flex align-items-center" href="{{ route('payroll') }}">
                        <span class="aside-menu-icon pe-3"><i class="fas fa-chart-pie"></i></span>
                        <span class="aside-menu-title">{{ __('messages.my_payrolls') }}</span>
                    </a>
                </li>
            @endmodule

            {{-- Patient admissions and Cases --}}
            <?php
            $patientCaseMangerCaseMgt = getMenuLinks(\App\Models\User::MAIN_CASE_MANGER_PATIENT_CASE);
            ?>
            @if ($patientCaseMangerCaseMgt)
                <li class="nav-item  {{ Request::is('patient-admissions*', 'patient-cases*') ? 'active' : '' }}">
                    <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientCaseMangerCaseMgt }}"
                        title="{{ __('Patients') }}">
                        <span class="aside-menu-icon pe-3"><i class="fas fa-user-injured"></i></span>
                        <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                        <span class="d-none">{{ __('messages.case_handlers') }}</span>
                        <span class="d-none">{{ __('messages.patient_admissions') }}</span>
                    </a>
                </li>
            @endif

            {{-- Ambulances and Ambulance Calls --}}
            <?php
            $serviceCaseMangerCaseMgt = getMenuLinks(\App\Models\User::MAIN_CASE_MANGER_SERVICE);
            ?>
            @if ($serviceCaseMangerCaseMgt)
                <li class="nav-item  {{ Request::is('ambulances*', 'ambulance-calls*') ? 'active' : '' }}">
                    <a class="py-3 nav-link d-flex align-items-center" href="{{ $serviceCaseMangerCaseMgt }}"
                        title="{{ __('Services') }}">
                        <span class="aside-menu-icon pe-3"><i class="fas fa-box"></i></span>
                        <span class="aside-menu-title">{{ __('messages.services') }}</span>
                        <span class="d-none">{{ __('messages.ambulances') }}</span>
                        <span class="d-none">{{ __('messages.ambulance_calls') }}</span>
                    </a>
                </li>
            @endif

            {{-- Mail and SMS --}}
            <?php
            $smsMailCaseManagerMgt = getMenuLinks(\App\Models\User::MAIN_SMS_MAIL);
            ?>
            @if ($smsMailCaseManagerMgt)
                <li class="nav-item  {{ Request::is('sms*', 'mail*') ? 'active' : '' }}">
                    <a class="py-3 nav-link d-flex align-items-center" href="{{ route('sms.index') }}"
                        title="{{ __('SMS/Mail') }}">
                        <span class="aside-menu-icon pe-3">
                            <i class="fas fa-bell"></i>
                        </span>
                        <span class="aside-menu-title">{{ __('messages.sms.sms') }}/{{ __('messages.mail') }}</span>
                    </a>
                </li>
            @endif
        @endmodule
    @endrole

    @role('Receptionist')
        {{-- Appointments --}}
        @module('Appointments', $modules)
            <li class="nav-item  {{ Request::is('appointments*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('appointments.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-calendar-check"></i></span>
                    <span class="aside-menu-title">{{ __('messages.appointments') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Doctors --}}
        @module('Doctors', $modules)
            <li class="nav-item  {{ Request::is('doctors*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('doctors.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                    <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Diagnosis Test --}}
        <?php
        $diagnosisReceptionistMGT = getMenuLinks(\App\Models\User::MAIN_DIAGNOSIS);
        ?>
        @if ($diagnosisReceptionistMGT)
            <li class="nav-item  {{ Request::is('diagnosis-categories*', 'patient-diagnosis-test*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $diagnosisReceptionistMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-diagnoses"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patient_diagnosis_test.diagnosis') }}</span>
                    <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_category') }}</span>
                    <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_test') }}</span>
                </a>
            </li>
        @endif

        {{-- Enquires --}}
        @module('Enquires', $modules)
            <li class="nav-item  {{ Request::is('enquiries*') || Request::is('enquiry*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('enquiries') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-question-circle"></i></span>
                    <span class="aside-menu-title">{{ __('messages.enquiries') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Front office --}}
        <?php
        $frontReceptionistOfficeMGT = getMenuLinks(\App\Models\User::MAIN_FRONT_OFFICE);
        ?>
        @if ($frontReceptionistOfficeMGT)
            <li class="nav-item {{ Request::is('call-logs*', 'visitor*', 'receives*', 'dispatches*') ? 'active' : '' }}">
                <a href="{{ $frontReceptionistOfficeMGT }}" class="py-3 nav-link d-flex align-items-center">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-dungeon"></i></span>
                    <span class="aside-menu-title">{{ __('messages.front_office') }}</span>
                    <span class="d-none">{{ __('messages.call_logs') }}</span>
                    <span class="d-none">{{ __('messages.visitors') }}</span>
                    <span class="d-none">{{ __('messages.postal_receive') }}</span>
                    <span class="d-none">{{ __('messages.postal_dispatch') }}</span>
                </a>
            </li>
        @endif

        @module('Notice Boards', $modules)
            <li class="nav-item {{ Request::is('employee/notice-board', 'testimonials*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Hospital Charges --}}
        <?php
        $ReceptionisthospitalCharge = getMenuLinks(\App\Models\User::MAIN_HOSPITAL_CHARGE);
        ?>
        @if ($ReceptionisthospitalCharge)
            <li
                class="nav-item  {{ Request::is('charge-categories*', 'charges*', 'doctor-opd-charges*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $ReceptionisthospitalCharge }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-coins"></i></span>
                    <span class="aside-menu-title">{{ __('messages.hospital_charges') }}</span>
                    <span class="d-none">{{ __('messages.charge_categories') }}</span>
                    <span class="d-none">{{ __('messages.charges') }}</span>
                    <span class="d-none">{{ __('messages.doctor_opd_charges') }}</span>
                </a>
            </li>
        @endif

        {{-- ipds/opds --}}
        <?php
        $receptionistIpdOPD = getMenuLinks(\App\Models\User::MAIN_IPD_OPD);
        ?>
        @if ($receptionistIpdOPD)
            <li class="nav-item  {{ Request::is('ipds*', 'opds*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistIpdOPD }}"
                    title="{{ __('messages.ipd_opd') }}">
                    <span class="aside-menu-icon pe-3">
                        <i class="fas fa-notes-medical"></i>
                    </span>
                    <span class="aside-menu-title">{{ __('messages.ipd_opd') }}</span>
                    <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                    <span class="d-none">{{ __('messages.opd_patients') }}</span>
                </a>
            </li>
        @endif

        {{-- Live Meeting --}}
        @module('Live Meetings', $modules)
            <li class="nav-item  {{ Request::is('live-meeting*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.meeting.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-file-video"></i></span>
                    <span class="aside-menu-title">{{ __('messages.live_meetings') }}</span>
                    <span class="d-none">{{ __('messages.live_meetings') }}</span>
                </a>
            </li>
        @endmodule

        @module('My Payrolls', $modules)
            <li class="nav-item {{ Request::is('employee/payroll*', 'employee-payrolls*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('payroll') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-chart-pie"></i></span>
                    <span class="aside-menu-title">{{ __('messages.my_payrolls') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Cases Mgt --}}
        <?php
        $receptionistPatientCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
        ?>
        @if ($receptionistPatientCaseMgt)
            <li
                class="nav-item {{ Request::is('patients*', 'patient-cases*', 'case-handlers*', 'patient-admissions*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistPatientCaseMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-user-injured"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                    <span class="d-none">{{ __('messages.cases') }}</span>
                    <span class="d-none">{{ __('messages.case_handlers') }}</span>
                    <span class="d-none">{{ __('messages.patient_admissions') }}</span>
                </a>
            </li>
        @endif

        {{-- Pathology --}}
        <?php
        $receptionistPathologyMgt = getMenuLinks(\App\Models\User::MAIN_PATHOLOGY);
        ?>
        @if ($receptionistPathologyMgt)
            <li class="nav-item  {{ Request::is('pathology-categories*', 'pathology-tests*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistPathologyMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-flask"></i></span>
                    <span class="aside-menu-title">{{ __('messages.pathologies') }}</span>
                    <span class="d-none">{{ __('messages.pathology_categories') }}</span>
                    <span class="d-none">{{ __('messages.pathology_tests') }}</span>
                </a>
            </li>
        @endif

        {{-- Radiology --}}
        <?php
        $receptionistRadiology = getMenuLinks(\App\Models\User::MAIN_RADIOLOGY);
        ?>
        @if ($receptionistRadiology)
            <li class="nav-item {{ Request::is('radiology-categories*', 'radiology-tests*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistRadiology }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-x-ray"></i></span>
                    <span class="aside-menu-title">{{ __('messages.radiologies') }}</span>
                    <span class="d-none">{{ __('messages.radiology_categories') }}</span>
                    <span class="d-none">{{ __('messages.radiology_tests') }}</span>
                </a>
            </li>
        @endif

        {{-- Services dropdown --}}
        <?php
        $receptionistServiceMgt = getMenuLinks(\App\Models\User::MAIN_SERVICE);
        ?>
        @if ($receptionistServiceMgt)
            <li
                class="nav-item {{ Request::is('insurances*', 'packages*', 'services*', 'ambulances*', 'ambulance-calls*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistServiceMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-box"></i></span>
                    <span class="aside-menu-title">{{ __('messages.services') }}</span>
                    <span class="d-none">{{ __('messages.insurances') }}</span>
                    <span class="d-none">{{ __('messages.packages') }}</span>
                    <span class="d-none">{{ __('messages.services') }}</span>
                    <span class="d-none">{{ __('messages.ambulances') }}</span>
                    <span class="d-none">{{ __('messages.ambulance_calls') }}</span>
                </a>
            </li>
        @endif

        {{-- Mail and SMS --}}
        <?php
        $receptionistSmsMailMgt = getMenuLinks(\App\Models\User::MAIN_SMS_MAIL);
        ?>
        @if ($receptionistSmsMailMgt)
            <li class="nav-item  {{ Request::is('sms*', 'mail*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $receptionistSmsMailMgt }}"
                    title="{{ __('SMS/Mail') }}">
                    <span class="aside-menu-icon pe-3">
                        <i class="fas fa-bell"></i>
                    </span>
                    <span class="aside-menu-title">{{ __('messages.sms.sms') }}/{{ __('messages.mail') }}</span>
                    <span class="d-none">{{ __('messages.sms.sms') }}</span>
                    <span class="d-none">{{ __('messages.mail') }}</span>
                </a>
            </li>
        @endif

        {{-- @module('Testimonial',$modules) --}}
        {{-- <li class="nav-item"> --}}
        {{--    <a class="nav-link  d-flex align-items-center py-3 ps-0 {{ Request::is('testimonials*') ? 'active' : '' }}" --}}
        {{--       href="{{ route('testimonials.index') }}"> --}}
        {{--        <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span> --}}
        {{--               <span class="aside-menu-title">{{ __('messages.front_settings') }}</span>
            <span class="d-none">{{ __('messages.notice_boards') }}</span> --}}
        {{--    </a> --}}
        {{-- </li> --}}
        {{-- @endmodule --}}
    @endrole

    @role('Pharmacist|PharmacistAdmin')
        {{-- Billing --}}
        <?php
        $billingMGT = getMenuLinks(\App\Models\User::MAIN_BILLING_MGT);
        ?>
        @if ($billingMGT)
            <li
                class="nav-item  {{ Request::is('accounts*', 'employee-payrolls*', 'invoices*', 'payments*', 'payment-reports*', 'advanced-payments*', 'bills*', 'pos*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="/pos">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice-dollar"></i></span>
                    <span class="aside-menu-title">{{ __('messages.billing') }}</span>
                    <span class="d-none">{{ __('messages.employee_payrolls') }}</span>
                    <span class="d-none">{{ __('messages.invoices') }}</span>
                    <span class="d-none">{{ __('messages.payments') }}</span>
                    <span class="d-none">{{ __('messages.payment_reports') }}</span>
                    <span class="d-none">{{ __('messages.advanced_payments') }}</span>
                    <span class="d-none">{{ __('messages.bills') }}</span>
                    <span class="d-none">{{ __('messages.pos') }}</span>
                </a>
            </li>
        @endif

        {{-- Finance --}}
        <?php
        $financeMGT = getMenuLinks(\App\Models\User::MAIN_FINANCE);
        ?>
        @if ($financeMGT)
            <li class="nav-item  {{ Request::is('incomes*', 'expenses*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="/returnposreport/daily-report">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-money-bill"></i></span>
                    <span class="aside-menu-title">{{ __('messages.finance') }}</span>
                    <span class="d-none">{{ __('messages.incomes.incomes') }}</span>
                    <span class="d-none">{{ __('messages.expenses') }}</span>
                    <span class="d-none">POS Report</span>
                    <span class="d-none">POS Return Report</span>
                    <span class="d-none">POS Daily Report</span>
                </a>
            </li>
        @endif


        {{-- Inventory Management  --}}
        <?php
        $inventoryMgt = getMenuLinks(\App\Models\User::MAIN_INVENTORY);
        ?>
        @if ($inventoryMgt)
            <li
                class="nav-item {{ Request::is('item-categories*', 'items*', 'item-stocks*', 'issued-items*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $inventoryMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-luggage-cart"></i></span>
                    <span class="aside-menu-title">{{ __('messages.inventory') }}</span>
                    <span class="d-none">{{ __('messages.items_categories') }}</span>
                    <span class="d-none">{{ __('messages.items') }}</span>
                    <span class="d-none">{{ __('messages.items_stocks') }}</span>
                    <span class="d-none">{{ __('messages.issued_items') }}</span>
                </a>
            </li>
        @endif


        @module('Notice Boards', $modules)
            <li class="nav-item  {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule



        {{-- Medicines --}}
        <?php
        $medicinePharmacistMgt = getMenuLinks(\App\Models\User::MAIN_MEDICINES);
        ?>
        @if ($medicinePharmacistMgt)
            <li class="nav-item {{ Request::is('categories*', 'brands*', 'medicines*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $medicinePharmacistMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-capsules"></i></span>
                    <span class="aside-menu-title">{{ __('messages.medicines') }}</span>
                    <span class="d-none">{{ __('messages.medicine_categories') }}</span>
                    <span class="d-none">{{ __('messages.medicine_brands') }}</span>
                    <span class="d-none">{{ __('messages.medicines') }}</span>
                </a>
            </li>
        @endif


        {{-- SMS --}}
        @module('SMS', $modules)
            <li class="nav-item {{ Request::is('sms*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('sms.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-sms"></i></span>
                    <span class="aside-menu-title">{{ __('messages.sms.sms') }}</span>
                </a>
            </li>
        @endmodule
    @endrole

    @role('Nurse')
        {{-- Bed Manager --}}
        <?php $bedNurseMGT = getMenuLinks(\App\Models\User::MAIN_BED_MGT);
        ?>
        @if ($bedNurseMGT)
            <li class="nav-item  {{ Request::is('bed-types*', 'beds*', 'bed-assigns*', 'bulk-beds') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $bedNurseMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="nav-icon fas fa-bed"></i></span>
                    <span class="aside-menu-title">{{ __('messages.bed_management') }}</span>
                    <span class="d-none">{{ __('messages.bed_types') }}</span>
                    <span class="d-none">{{ __('messages.beds') }}</span>
                    <span class="d-none">{{ __('messages.bed_assigns') }}</span>
                </a>
            </li>
        @endif


        {{-- dietitian-form --}}


        <li class="nav-item  {{ Request::is('dietitian-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dietitan.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
                <span class="aside-menu-title">Dietitan Assessment</span>
            </a>
        </li>

        @module('Notice Boards', $modules)
            <li class="nav-item  {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Live Meeting --}}
        @module('Live Meetings', $modules)
            <li class="nav-item {{ Request::is('live-meeting*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.meeting.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-file-video"></i></span>
                    <span class="aside-menu-title">{{ __('messages.live_meetings') }}</span>
                </a>
            </li>
        @endmodule

        {{-- My Payrolls --}}
        @module('My Payrolls', $modules)
            <li class="nav-item  {{ Request::is('employee/payroll*', 'employee-payroll*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('payroll') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-chart-pie"></i></span>
                    <span class="aside-menu-title">{{ __('messages.my_payrolls') }}</span>
                </a>
            </li>
        @endmodule

        <li class="nav-item  {{ Request::is('nursing-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nursing.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Nursing Form </span>
            </a>
        </li>
        <li class="nav-item  {{ Request::is('medication-administration*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('medication.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Medication Administration</span>
            </a>
        </li>
    @endrole

    @role('Lab Technician')
        {{-- Blood Bank dropdown --}}
        <?php
        $bloodbankLabMGT = getMenuLinks(\App\Models\User::MAIN_BLOOD_BANK_MGT);
        ?>
        @if ($bloodbankLabMGT)
            <li
                class="nav-item {{ Request::is('blood-banks*', 'blood-donors*', 'blood-donations*', 'blood-issues*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $bloodbankLabMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
                    <span class="aside-menu-title">{{ __('messages.blood_bank') }}</span>
                    <span class="d-none">{{ __('messages.blood_donors') }}</span>
                    <span class="d-none">{{ __('messages.blood_donations') }}</span>
                    <span class="d-none">{{ __('messages.blood_issues') }}</span>
                </a>
            </li>
        @endif

        @module('Doctors|CSR', $modules)
            <li class="nav-item {{ Request::is('employee/doctor*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/doctor') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-md"></i></span>
                    <span class="aside-menu-title">{{ __('messages.doctors') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Diagnosis Test --}}
        <?php
        $diagnosiLabMGT = getMenuLinks(\App\Models\User::MAIN_DIAGNOSIS);
        ?>
        @if ($diagnosiLabMGT)
            <li class="nav-item {{ Request::is('diagnosis-categories*', 'patient-diagnosis-test*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $diagnosiLabMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-diagnoses"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patient_diagnosis_test.diagnosis') }}</span>
                    <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_category') }}</span>
                    <span class="d-none">{{ __('messages.patient_diagnosis_test.diagnosis_test') }}</span>
                </a>
            </li>
        @endif

        {{-- Front Settings --}}
        @module('Notice Boards', $modules)
            <li class="nav-item  {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Live Meeting --}}
        @module('Live Meetings', $modules)
            <li class="nav-item  {{ Request::is('live-meeting*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.meeting.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-file-video"></i></span>
                    <span class="aside-menu-title">{{ __('messages.live_meetings') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Medicines --}}
        <?php
        $medicinelabMgt = getMenuLinks(\App\Models\User::MAIN_MEDICINES);
        ?>
        @if ($medicinelabMgt)
            <li class="nav-item {{ Request::is('categories*', 'brands*', 'medicines*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $medicinelabMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-capsules"></i></span>
                    <span class="aside-menu-title">{{ __('messages.medicines') }}</span>
                    <span class="d-none">{{ __('messages.medicine_categories') }}</span>
                    <span class="d-none">{{ __('messages.medicine_brands') }}</span>
                    <span class="d-none">{{ __('messages.medicines') }}</span>
                </a>
            </li>
        @endif

        {{-- My Payrolls --}}
        @module('My Payrolls', $modules)
            <li class="nav-item {{ Request::is('employee/payroll*', 'employee-payrolls*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('payroll') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-chart-pie"></i></span>
                    <span class="aside-menu-title">{{ __('messages.my_payrolls') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Pathologies --}}
        @module('Pathology Tests', $modules)
            <li class="nav-item  {{ Request::is('pathology-tests*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('pathology.test.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-flask"></i></span>
                    <span class="aside-menu-title">{{ __('messages.pathologies') }}</span>
                    <span class="d-none">{{ __('messages.pathology_tests') }}</span>
                </a>
            </li>
        @endmodule

        @module('Radiology Tests', $modules)
            <li class="nav-item  {{ Request::is('radiology-tests*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('radiology.test.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-x-ray"></i></span>
                    <span class="aside-menu-title">{{ __('messages.radiologies') }}</span>
                    <span class="d-none">{{ __('messages.radiology_tests') }}</span>
                </a>
            </li>
        @endmodule
    @endrole

    @role('Accountant')
        {{-- Account Manager dropdown --}}
        <?php
        $billingAccountMGT = getMenuLinks(\App\Models\User::MAIN_ACCOUNT_MANAGER_MGT);
        ?>
        @if ($billingAccountMGT)
            <li
                class="nav-item {{ Request::is('accounts*', 'employee-payrolls*', 'invoices*', 'payments*', 'bills*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $billingAccountMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="fab fa-adn"></i></span>
                    <span class="aside-menu-title">{{ __('messages.account_manager') }}</span>
                    <span class="d-none">{{ __('messages.accounts') }}</span>
                    <span class="d-none">{{ __('messages.employee_payrolls') }}</span>
                    <span class="d-none">{{ __('messages.invoices') }}</span>
                    <span class="d-none">{{ __('messages.payments') }}</span>
                    <span class="d-none">{{ __('messages.bills') }}</span>
                    <span class="d-none">{{ __('messages.pos') }}</span>
                </a>
            </li>
        @endif

        {{-- Finance --}}
        <?php
        $financeAccountantMGT = getMenuLinks(\App\Models\User::MAIN_FINANCE);
        ?>
        @if ($financeAccountantMGT)
            <li class="nav-item {{ Request::is('incomes*', 'expenses*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $financeAccountantMGT }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-money-bill"></i></span>
                    <span class="aside-menu-title">{{ __('messages.finance') }}</span>
                    <span class="d-none">{{ __('messages.incomes.incomes') }}</span>
                    <span class="d-none">{{ __('messages.expenses') }}</span>
                    <span class="d-none">POS Report</span>
                    <span class="d-none">POS Return Report</span>
                    <span class="d-none">POS Daily Report</span>
                </a>
            </li>
        @endif

        {{-- Notice Boards --}}
        @module('Notice Boards', $modules)
            <li class="nav-item {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Live Meeting --}}
        @module('Live Meetings', $modules)
            <li class="nav-item  {{ Request::is('live-meeting*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.meeting.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-file-video"></i></span>
                    <span class="aside-menu-title">{{ __('messages.live_meetings') }}</span>
                </a>
            </li>
        @endmodule

        {{-- My Payrolls --}}
        @module('My Payrolls', $modules)
            <li class="nav-item  {{ Request::is('employee/payroll*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('payroll') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-chart-pie"></i></span>
                    <span>{{ __('messages.my_payrolls') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Services --}}
        @module('Services', $modules)
            <li class="nav-item {{ Request::is('services*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('services.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-box"></i></span>
                    <span class="aside-menu-title">{{ __('messages.services') }}</span>
                </a>
            </li>
        @endmodule

        {{-- SMS --}}
        @module('SMS', $modules)
            <li class="nav-item  {{ Request::is('sms*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('sms.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-sms"></i></span>
                    <span class="aside-menu-title">{{ __('messages.sms.sms') }}</span>
                </a>
            </li>
        @endmodule
    @endrole

    @role('Patient')
        @module('Appointments', $modules)
            <li class="nav-item  {{ Request::is('appointments*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('appointments.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-calendar-check"></i></span>
                    <span class="aside-menu-title">{{ __('messages.appointments') }}</span>
                </a>
            </li>
        @endmodule

        @module('Bills', $modules)
            <li class="nav-item  {{ Request::is('employee/bills*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/bills') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-rupee-sign"></i></span>
                    <span class="aside-menu-title">{{ __('messages.bills') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Diagnosis test Report --}}
        @module('Diagnosis Tests', $modules)
            <li class="nav-item  {{ Request::is('employee/patient-diagnosis-test*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/patient-diagnosis-test') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file-medical"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patient_diagnosis_test.diagnosis_test') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Documents --}}
        @module('Documents', $modules)
            <li class="nav-item  {{ Request::is('documents*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('documents.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file"></i></span>
                    <span class="aside-menu-title">{{ __('messages.documents') }}</span>
                </a>
            </li>
        @endmodule

        @module('Notice Boards', $modules)
            <li class="nav-item  {{ Request::is('employee/notice-board*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/notice-board') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa fa-cog"></i></span>
                    <span class="aside-menu-title">{{ __('messages.notice_boards') }}</span>
                    <span class="d-none">{{ __('messages.notice_boards') }}</span>
                </a>
            </li>
        @endmodule

        {{-- ipds/opds --}}
        <li class="nav-item  {{ Request::is('patient/my-ipds*', 'opds*', 'patient/my-opds*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('patient.ipd') }}"
                title="{{ __('messages.ipd_opd') }}">
                <span class="aside-menu-icon pe-3">
                    <i class="fas fa-notes-medical"></i>
                </span>
                <span class="aside-menu-title">{{ __('messages.ipd_opd') }}</span>
                <span class="d-none">{{ __('messages.ipd_patients') }}</span>
                <span class="d-none">{{ __('messages.opd_patients') }}</span>
            </a>
        </li>

        @module('Invoices', $modules)
            <li class="nav-item  {{ Request::is('employee/invoices*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/invoices') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice"></i></span>
                    <span class="aside-menu-title">{{ __('messages.invoices') }}</span>
                </a>
            </li>
        @endmodule

        {{-- Live Consultation --}}
        @module('Live Consultations', $modules)
            <li class="nav-item  {{ Request::is('live-consultation*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('live.consultation.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-video"></i></span>
                    <span class="aside-menu-title">{{ __('messages.live_consultations') }}</span>
                </a>
            </li>
        @endmodule

        @module('Patient Cases', $modules)
            <li class="nav-item  {{ Request::is('patient/my-cases*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('patient/my-cases') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-briefcase-medical"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patients_cases') }}</span>
                </a>
            </li>
        @endmodule

        @module('Patient Admissions', $modules)
            <li class="nav-item  {{ Request::is('employee/patient-admissions*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ url('employee/patient-admissions') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-history"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patient_admissions') }}</span>
                </a>
            </li>
        @endmodule

        @module('Prescriptions', $modules)
            <li class="nav-item {{ Request::is('patient/my-prescriptions*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('prescriptions.list') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-prescription"></i></span>
                    <span class="aside-menu-title">{{ __('messages.prescriptions') }}</span>
                </a>
            </li>
        @endmodule

        @module('Vaccinated Patients', $modules)
            <li class="nav-item  {{ Request::is('patient/my-vaccinated*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('patient.vaccinated') }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-head-side-mask"></i></span>
                    <span class="aside-menu-title">{{ __('messages.vaccinated_patients') }}</span>
                </a>
            </li>
        @endmodule
    @endrole

    @role('Nurse')
        {{-- Patients --}}
        <?php
        $patientDoctorCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
        ?>
        @if ($patientDoctorCaseMgt)
            <li
                class="nav-item  {{ Request::is('patients*', 'patient-admissions*', 'patient-cases*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientDoctorCaseMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-user-injured"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                    <span class="d-none">{{ __('messages.patient_admissions') }}</span>
                </a>
            </li>
        @endif
    @endrole
    @role('Admin|SupplyChain|PharmacistAdmin')
        <div class="mb-5"></div>
        <li class="mt-5 nav-item">
            <a class="py-3 nav-link d-flex align-items-center reload-page"
                href="{{ route('inventory.products.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-head-side-mask"></i></span>
                <span class="aside-menu-title">{{ __('Inventory Management') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('shift/stock*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('shift.stock-in.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-calendar-check"></i></span>
                <span class="aside-menu-title">Inventory History Report</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="py-3 nav-link d-flex align-items-center reload-page"
                href="{{ route('purchase.requistions.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-head-side-mask"></i></span>
                <span class="aside-menu-title">{{ __('Purchase Management') }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="py-3 nav-link d-flex align-items-center reload-page" href="{{ route('shift.transfers.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-head-side-mask"></i></span>
                <span class="aside-menu-title">{{ __('Transfer Request') }}</span>
            </a>
        </li>
    @endrole

    @role('Admin|PharmacistAdmin|Accountant')
        <li class="nav-item">
            <a class="py-3 nav-link d-flex align-items-center reload-page" href="{{ route('FinanceReport') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-file-invoice"></i></span>
                <span class="aside-menu-title">Finance Report</span>
            </a>
        </li>
    @endrole

    @role('Admin')
        <li class="nav-item">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('logs.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-head-side-mask"></i></span>
                <span class="aside-menu-title">{{ __('Logs') }}</span>
            </a>
        </li>
    @endrole

    @role('Dietitian')
        {{-- dietitian-form --}}


        <li class="nav-item  {{ Request::is('dietitian-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('dietitan.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fas fa-tint"></i></span>
                <span class="aside-menu-title">Dietitan Assessment</span>
            </a>
        </li>



        {{-- Nursers --}}
        @module('Nurses', $modules)
            <li class="nav-item  {{ Request::is('nurses*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nurses.index') }}">
                    <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                    <span class="aside-menu-title">{{ __('messages.nurses') }}</span>
                </a>
            </li>
        @endmodule

        <li class="nav-item  {{ Request::is('nursing-form*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('nursing.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Nursing Form </span>
            </a>
        </li>
        <li class="nav-item  {{ Request::is('medication-administration*') ? 'active' : '' }}">
            <a class="py-3 nav-link d-flex align-items-center" href="{{ route('medication.index') }}">
                <span class="aside-menu-icon pe-3"><i class="fa fa-user-nurse"></i></span>
                <span class="aside-menu-title">Medication Administration</span>
            </a>
        </li>

        <?php
        $patientDoctorCaseMgt = getMenuLinks(\App\Models\User::MAIN_PATIENT_CASE);
        ?>
        @if ($patientDoctorCaseMgt)
            <li
                class="nav-item  {{ Request::is('patients*', 'patient-admissions*', 'patient-cases*') ? 'active' : '' }}">
                <a class="py-3 nav-link d-flex align-items-center" href="{{ $patientDoctorCaseMgt }}">
                    <span class="aside-menu-icon pe-3"><i class="fas fa-user-injured"></i></span>
                    <span class="aside-menu-title">{{ __('messages.patients') }}</span>
                    <span class="d-none">{{ __('messages.patient_admissions') }}</span>
                </a>
            </li>
        @endif
    @endrole
@endif
