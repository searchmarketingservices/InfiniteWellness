{{ Form::open(['id' => 'appointmentForm']) }}
<div class="row">
    <!-- Patient Name Field -->
    @if (Auth::user()->hasRole('Patient'))
        <input type="hidden" name="patient_id" value="{{ Auth::user()->owner_id }}">
    @else
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('patient_name', __('messages.case.patient') . ':', ['class' => 'form-label']) }}
            <span class="required"> </span>
            <button type="button" class="btn btn-primary  btn-sm" data-toggle="modal" data-target="#patientModal">
                Add Patient
            </button>
            {{-- {{ Form::select('patient_id', $patients, null, ['class' => 'form-select','required','id' => 'appointmentPatientId','placeholder'=>'Select Patient', 'data-control' => 'select2']) }} --}}
            <select name="patient_id" id="appointmentPatientId" placeholder='Select Patient' class="form-select"
                required>
                <option value="" selected disabled>Select Patient</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">({{ $patient->MR }}) {{ $patient->patientUser->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Department Name Field -->
    <div class="mb-5 form-group col-sm-6">
        {{ Form::label('department_name', __('messages.appointment.doctor_department') . ':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::select('doctor_department_id', $departments, null, ['class' => 'form-select', 'required', 'id' => 'appointmentDepartmentId', 'placeholder' => 'Select Department', 'data-control' => 'select2']) }}
    </div>
    <!-- Doctor Name Field -->
    <div class="mb-5 form-group col-sm-6">
        {{ Form::label('doctor_name', __('messages.case.doctor') . ':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::select('doctor_id', isset($doctors) ? $doctors : [], null, ['class' => 'form-select', 'required', 'id' => 'appointmentDoctorId', 'placeholder' => 'Select Doctor', 'data-control' => 'select2']) }}
    </div>

    @if (!Auth::user()->hasRole('Patient'))
        <!-- Date Field -->
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('opd_date', __('messages.appointment.date') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('opd_date', isset($appointment) ? $appointment->opd_date->format('Y-m-d') : null, ['id' => 'appointmentOpdDate', 'class' => getLoggedInUser()->thememode ? 'bg-light opdDate form-control' : 'bg-white opdDate form-control', 'required', 'autocomplete' => 'off']) }}
        </div>


        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('Service Description', __('Service Description') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            <select name="service_description" class="form-select" data-control="select2" id="service_description">
                <option value="" selected disabled>Select Service Description</option>
                @foreach ($service as $id => $serviceName)
                    <option value="{{ $serviceName }}">{{ $serviceName }}</option>
                @endforeach
            </select>
        </div>

        <!-- Notes Field -->
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('problem', __('messages.appointment.description') . ':', ['class' => 'form-label']) }}
            {{ Form::textarea('problem', null, ['class' => 'form-control', 'rows' => '4']) }}
        </div>
        <div class="mb-5 form-group col-sm-3">
            {{ Form::label('status', __('messages.common.status') . ':', ['class' => 'form-label']) }}
            <br>
            <div class="form-check form-switch">
                <input class="form-check-input w-35px h-20px" name="status" type="checkbox" value="1" checked>
            </div>
        </div>
        <div class="mb-5 form-group col-sm-3">
            {{ Form::label('follow_up', __('Follow Up') . ':', ['class' => 'form-label']) }}
            <br>
            <div class="form-check form-switch">
                <input class="form-check-input w-35px h-20px" name="follow_up" id="followUp" type="checkbox">
            </div>
        </div>

        <div class="mb-5 form-group col-sm-6 d-none" id="patientCaseDiv">
            {{ Form::label('Patient Cases', __('Patient Cases ') . ':', ['class' => 'form-label']) }}
            {{-- <span class="required"></span> --}}
            <select name="patient_case_id" class="form-select" data-control="select2" id="appointmentPatientCaseId">
                <option value="" selected disabled>Select Patient Case</option>
            </select>
            {{-- {{ Form::select('doctor_department_id',$departments, null, ['class' => 'form-select','required','id' => 'appointmentDepartmentId','placeholder'=>'Select Department', 'data-control' => 'select2']) }} --}}
        </div>

        <div class="mb-5 form-group col-sm-6">
            <div class="doctor-schedule" style="display: none">
                <i class="fas fa-calendar-alt"></i>
                <span class="day-name"></span>
                <span class="schedule-time"></span>
            </div>
            <strong class="error-message" style="display: none"></strong>
            <div class="slot-heading">
                <h3 class="available-slot-heading" style="display: none">
                    {{ __('messages.appointment.available_slot') . ':' }}</h3>
            </div>
            <div class="row">
                <div class="available-slot form-group col-sm-12">
                </div>
            </div>
            <div align="right" style="display: none">
                <span><i class="fa fa-circle color-information" aria-hidden="true"> </i>
                    {{ __('messages.appointment.no_available') }}</span>
            </div>
        </div>
    @endif

    @if (Auth::user()->hasRole('Patient'))
        <!-- Date Field -->
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('opd_date', __('messages.appointment.date') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('opd_date', null, ['id' => 'patientAppointmentOpdDate', 'class' => getLoggedInUser()->thememode ? 'bg-light opdDate form-control' : 'bg-white opdDate form-control', 'required', 'autocomplete' => 'off']) }}
        </div>

        <!-- Notes Field -->
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('problem', __('messages.appointment.description') . ':', ['class' => 'form-label']) }}
            {{ Form::textarea('problem', null, ['class' => 'form-control', 'rows' => '4']) }}
        </div>
        <div class="form-group col-sm-6 available-slot-div">
            <div class="doctor-schedule" style="display: none">
                <i class="fas fa-calendar-alt"></i>
                <span class="day-name"></span>
                <span class="schedule-time"></span>
            </div>
            <strong class="error-message" style="display: none"></strong>
            <div class="slot-heading">
                <strong class="available-slot-heading"
                    style="display: none">{{ __('messages.appointment.available_slot') . ':' }}</strong>
            </div>
            <div class="row">
                <div class="available-slot form-group col-sm-10">
                </div>
            </div>
            <div class="color-information" align="right" style="display: none">
                <span><i class="fa fa-circle fa-xs" aria-hidden="true"> </i>
                    {{ __('messages.appointment.no_available') }}</span>
            </div>
        </div>
    @endif
</div>

<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12 d-flex justify-content-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3', 'id' => 'saveAppointment']) }}
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">{{ __('messages.common.cancel') }}</a>
    </div>
</div>

{{ Form::close() }}

<!-- Add Patients Modal -->
<!-- Modal -->
{{-- <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="patientForm">
                    <div class="col-md-12">
                        <div class="form-group mb-5">
                            <label for="patientFirstName" class="form-label">First Name:</label>
                            <span class="required"></span>
                            <input type="text" name="first_name" class="form-control" required
                                id="patientFirstName" tabindex="1">
                        </div>
                        <div class="form-group mb-5">
                            <label for="patientLastName" class="form-label">Last Name:</label>
                            <span class="required"></span>
                            <input type="text" name="last_name" class="form-control" id="patientLastName"
                                required tabindex="2">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-5">
                            <label for="userCnic" class="form-label">CNIC:</label>
                            <input type="text" name="CNIC" class="form-control" tabindex="3"
                                id="userCnic">
                        </div>
                        <div class="form-group mb-5">
                            <label for="patientBirthDate" class="form-label">Date of Birth:</label>
                            <input type="date" name="dob" class="form-control" id="patientBirthDate"
                                autocomplete="off" tabindex="4">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mobile-overlapping mb-5">
                            <label for="patientPhoneNumber" class="form-label">Phone:</label>
                            <span class="required"></span><br>
                            <input type="tel" name="phone" class="form-control phoneNumber"
                                id="patientPhoneNumber" required
                                onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                tabindex="5">
                            <input type="hidden" name="prefix_code" id="prefixCode" class="prefix_code">
                            <span class="text-success valid-msg d-none fw-400 fs-small mt-2">✓ &nbsp; Valid</span>
                            <span class="text-danger error-msg d-none fw-400 fs-small mt-2"></span>
                        </div>

                        <div class="form-group mb-5">
                            <label class="form-label">Gender:</label>
                            <span class="required"></span> &nbsp;<br>
                            <span class="is-valid">
                                <label class="form-label">Male</label>&nbsp;&nbsp;
                                <input type="radio" name="gender" value="0" class="form-check-input"
                                    tabindex="6" id="patientMale" checked> &nbsp;
                                <label class="form-label">Female</label>
                                <input type="radio" name="gender" value="1" class="form-check-input"
                                    tabindex="7" id="patientFemale">
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-5">
                            <label for="patientBloodGroup" class="form-label">Blood Group:</label>
                            <select name="blood_group" class="form-select" id="patientBloodGroup"
                                data-control="select2" tabindex="9">
                                <option value="" disabled selected>Select Blood Group</option>
                                @foreach ($bloodGroup as $blood_group)
                                    <option value="{{ $blood_group }}">{{ $blood_group }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> --}}

<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Patient</h5>
                <button type="button" class="btn btn-secondary close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="patientTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="online-patient-tab" data-toggle="tab" href="#online-patient"
                            role="tab" aria-controls="online-patient" aria-selected="true">On Call Patient</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="walkin-patient-tab" data-toggle="tab" href="#walkin-patient"
                            role="tab" aria-controls="walkin-patient" aria-selected="false">Walk-in Patient</a>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="patientTabContent">
                    <!-- Online Patient Form -->
                    <div class="tab-pane fade show active" id="online-patient" role="tabpanel"
                        aria-labelledby="online-patient-tab">
                        <form action="" id="patientForm">
                            <div class="col-md-12">
                                <div class="form-group mb-5">
                                    <label for="patientFirstName" class="form-label">First Name:</label>
                                    <span class="required"></span>
                                    <input type="text" name="first_name" class="form-control" required
                                        id="patientFirstName" tabindex="1">
                                </div>
                                <div class="form-group mb-5">
                                    <label for="patientLastName" class="form-label">Last Name:</label>
                                    <span class="required"></span>
                                    <input type="text" name="last_name" class="form-control" id="patientLastName"
                                        required tabindex="2">
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group mb-5">
                                    <label for="userCnic" class="form-label">CNIC:</label>
                                    <input type="text" name="CNIC" class="form-control" tabindex="3"
                                        id="userCnic">
                                </div>
                                <div class="form-group mb-5">
                                    <label for="patientBirthDate" class="form-label">Date of Birth:</label>
                                    <input type="date" name="dob" class="form-control" id="patientBirthDate"
                                        autocomplete="off" tabindex="4">
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group mobile-overlapping mb-5">
                                    <label for="patientPhoneNumber" class="form-label">Phone:</label>
                                    <span class="required"></span><br>
                                    <input type="tel" name="phone" class="form-control phoneNumber"
                                        id="patientPhoneNumber" required
                                        onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                        tabindex="5">
                                    <input type="hidden" name="prefix_code" id="prefixCode" class="prefix_code">
                                    <span class="text-success valid-msg d-none fw-400 fs-small mt-2">✓ &nbsp;
                                        Valid</span>
                                    <span class="text-danger error-msg d-none fw-400 fs-small mt-2"></span>
                                </div>
                                <div class="form-group mb-5">
                                    <label class="form-label">Gender:</label>
                                    <span class="required"></span> &nbsp;<br>
                                    <span class="is-valid">
                                        <label class="form-label">Male</label>&nbsp;&nbsp;
                                        <input type="radio" name="gender" value="0" class="form-check-input"
                                            tabindex="6" id="patientMale2" checked> &nbsp;
                                        <label class="form-label">Female</label>
                                        <input type="radio" name="gender" value="1" class="form-check-input"
                                            tabindex="7" id="patientFemale2">
                                    </span>
                                </div>
                                {{-- <div class="form-group mb-5">
                                    <label class="form-label">Gender:</label>
                                    <span class="required"></span> &nbsp;<br>
                                    <span class="is-valid">
                                        <label class="form-label">Male</label>&nbsp;&nbsp;
                                        <input type="radio" name="gender" value="0" class="form-check-input"
                                            tabindex="6" id="patientMale" checked> &nbsp;
                                        <label class="form-label">Female</label>
                                        <input type="radio" name="gender" value="1" class="form-check-input"
                                            tabindex="7" id="patientFemale">
                                    </span>
                                </div> --}}
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group mb-5">
                                    <label for="patientBloodGroup" class="form-label">Blood Group:</label>
                                    <select name="blood_group" class="form-select" id="patientBloodGroup"
                                        data-control="select2" tabindex="9">
                                        <option value="" disabled selected>Select Blood Group</option>
                                        @foreach ($bloodGroup as $blood_group)
                                            <option value="{{ $blood_group }}">{{ $blood_group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                                <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                            </div>
                        </form>
                    </div>

                    <!-- Walk-in Patient Form -->
                    <div class="tab-pane fade" id="walkin-patient" role="tabpanel"
                        aria-labelledby="walkin-patient-tab">
                        <form action="" id="patientForm2">
                            <div class="col-md-12">
                                <div class="form-group mb-5">
                                    <label for="patientFirstName2" class="form-label">First Name:</label>
                                    <span class="required"></span>
                                    <input type="text" name="first_name" class="form-control" required
                                        id="patientFirstName2" tabindex="1">
                                </div>
                                <div class="form-group mb-5">
                                    <label for="patientLastName2" class="form-label">Last Name:</label>
                                    <span class="required"></span>
                                    <input type="text" name="last_name" class="form-control"
                                        id="patientLastName2" required tabindex="2">
                                </div>
                            </div>
                            <div class="col-md-12">
                                {{-- <div class="form-group mb-5">
                                    <label for="userCnic" class="form-label">CNIC:</label>
                                    <input type="text" name="CNIC" class="form-control" tabindex="3"
                                        id="userCnic">
                                </div> --}}
                                <div class="form-group mb-5">
                                    <label for="patientBirthDate2" class="form-label">
                                        Date of Birth:
                                        <span class="required"></span>
                                    </label>
                                    <input type="date" name="dob" required class="form-control"
                                        id="patientBirthDate2" autocomplete="off" tabindex="4">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mobile-overlapping mb-5">
                                    <label for="patientPhoneNumber2" class="form-label">Contact:</label>
                                    <span class="required"></span><br>
                                    <input type="tel" name="phone" class="form-control phoneNumber"
                                        id="patientPhoneNumber2" required
                                        onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                        tabindex="5">
                                    <input type="hidden" name="prefix_code" id="prefixCode2" class="prefix_code">
                                    <span class="text-success valid-msg d-none fw-400 fs-small mt-2">✓ &nbsp;
                                        Valid</span>
                                    <span class="text-danger error-msg d-none fw-400 fs-small mt-2"></span>
                                </div>
                                <div class="form-group mobile-overlapping mb-5">
                                    <label for="patientEmergencyPhoneNumber2" class="form-label">Emergency
                                        Contact:</label>
                                    <input type="tel" name="emergencyPhone" class="form-control phoneNumber"
                                        id="patientEmergencyPhoneNumber2"
                                        onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                        tabindex="5">
                                    <input type="hidden" name="prefix_code" id="prefixCode2" class="prefix_code">
                                    <span class="text-success valid-msg d-none fw-400 fs-small mt-2">✓ &nbsp;
                                        Valid</span>
                                    <span class="text-danger error-msg d-none fw-400 fs-small mt-2"></span>
                                </div>
                                <div class="form-group mb-5">
                                    <label class="form-label">Gender:</label>
                                    <span class="required"></span> &nbsp;<br>
                                    <span class="is-valid">
                                        <label class="form-label">Male</label>&nbsp;&nbsp;
                                        <input type="radio" name="gender" value="0" class="form-check-input"
                                            tabindex="6" id="patientMale2" checked> &nbsp;
                                        <label class="form-label">Female</label>
                                        <input type="radio" name="gender" value="1" class="form-check-input"
                                            tabindex="7" id="patientFemale2">
                                    </span>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group mb-5">
                                    <label for="patientBloodGroup2" class="form-label">Blood Group:</label>
                                    <select name="blood_group" class="form-select" id="patientBloodGroup2"
                                        data-control="select2" tabindex="9">
                                        <option value="" disabled selected>Select Blood Group</option>
                                        @foreach ($bloodGroup as $blood_group)
                                            <option value="{{ $blood_group }}">{{ $blood_group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                                <button type="submit" class="btn btn-primary" id="btnSave2">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {{-- <button type="submit" class="btn btn-primary" id="btnSave">Save</button> --}}
            </div>
        </div>
    </div>
</div>



<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // $(document).ready(function() {
    //     $("#followUp").change(function() {
    //         if ($(this).is(":checked")) {
    //             $("#patientCaseDiv").removeClass("d-none");
    //         } else {
    //             $("#patientCaseDiv").addClass("d-none");
    //             $("#followUpDate").val("");
    //         }
    //     });

    //     $("#appointmentPatientId").change(function() {
    //         var patientId = $(this).val();
    //         console.log(patientId);
    //         $.ajax({
    //             type: "POST",
    //             url: "{{ route('appointments.patient-case') }}",
    //             data: {
    //                 patient_id: patientId
    //             },
    //             success: function(response) {
    //                 console.log(response);
    //                 if (response.length > 0) {
    //                     $("#appointmentPatientCaseId").empty();
    //                     $("#appointmentPatientCaseId").append(
    //                         '<option value="" selected disabled>Select Case</option>');
    //                     for (var i = 0; i < response.length; i++) {
    //                         $("#appointmentPatientCaseId").append('<option value="' +
    //                             response[i].id + '">' + response[i].case_id +
    //                             '</option>');

    //                     }
    //                 } else {
    //                     $("#appointmentPatientCaseId").empty();
    //                     $("#appointmentPatientCaseId").append(
    //                         '<option value="" selected disabled>No Case Found</option>');
    //                 }
    //             },
    //             error: function(error) {
    //                 console.log(error);
    //             }
    //         })
    //     })
    //     $('#patientForm').on('submit', function(event) {
    //         event.preventDefault(); // Prevent the default form submission
    //         console.log("Form submitted");
    //         $.ajax({
    //             type: "POST",
    //             url: "{{ route('add-patient-appointment') }}",
    //             data: {
    //                 _token: "{{ csrf_token() }}",
    //                 first_name: $('#patientFirstName').val(),
    //                 last_name: $('#patientLastName').val(),
    //                 CNIC: $('#userCnic').val(),
    //                 dob: $('#patientBirthDate').val(),
    //                 phone: $('#patientPhoneNumber').val(),
    //                 emergencyPhone: $('#patientEmergencyPhoneNumber').val(),
    //                 gender: $('input[name="gender"]:checked').val(),
    //                 blood_group: $('#patientBloodGroup').val(),
    //                 prefix_code: $('#prefixCode').val() || 'default_prefix_code'
    //             },
    //             success: function(response) {
    //                 $("#appointmentPatientId").append(
    //                     '<option value="' + response.id + '">(' + response
    //                     .MR + ') ' + response.user.full_name + '</option>');
    //                 $("#appointmentPatientId").val(response.id);

    //                 $('#patientForm').trigger("reset");
    //                 $('#patientModal').modal('hide');
    //                 $('#btnClose').trigger("click");
    //             },
    //             error: function(xhr, status, error) {
    //                 console.log("error");
    //                 console.log(xhr.responseText); // Handle errors
    //             }
    //         });
    //     });
    //     $('#patientForm2').on('submit', function(event) {
    //         event.preventDefault(); // Prevent the default form submission
    //         console.log("Form submitted");
    //         $.ajax({
    //             type: "POST",
    //             url: "{{ route('add-patient-appointment') }}",
    //             data: {
    //                 _token: "{{ csrf_token() }}",
    //                 first_name: $('#patientFirstName2').val(),
    //                 last_name: $('#patientLastName2').val(),
    //                 CNIC: $('#userCnic2').val(),
    //                 dob: $('#patientBirthDate2').val(),
    //                 phone: $('#patientPhoneNumber2').val(),
    //                 emergencyPhone: $('#patientEmergencyPhoneNumber2').val(),
    //                 gender: $('input[name="gender"]:checked').val(),
    //                 blood_group: $('#patientBloodGroup2').val(),
    //                 prefix_code: $('#prefixCode2').val() || 'default_prefix_code'
    //             },
    //             success: function(response) {
    //                 $("#appointmentPatientId").append(
    //                     '<option value="' + response.id + '">(' + response
    //                     .MR + ') ' + response.user.full_name + '</option>');
    //                 $("#appointmentPatientId").val(response.id);

    //                 $('#patientForm').trigger("reset");
    //                 $('#patientModal').modal('hide');
    //                 $('#btnClose').trigger("click");
    //             },
    //             error: function(xhr, status, error) {
    //                 console.log("error");
    //                 console.log(xhr.responseText); // Handle errors
    //             }
    //         });
    //     });
    // })

    $(document).ready(function() {
        $("#followUp").change(function() {
            if ($(this).is(":checked")) {
                $("#patientCaseDiv").removeClass("d-none");
            } else {
                $("#patientCaseDiv").addClass("d-none");
                $("#followUpDate").val("");
            }
        });

        $("#appointmentPatientId").change(function() {
            var patientId = $(this).val();
            console.log(patientId);
            $.ajax({
                type: "POST",
                url: "{{ route('appointments.patient-case') }}",
                data: {
                    patient_id: patientId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);
                    if (response.length > 0) {
                        $("#appointmentPatientCaseId").empty();
                        $("#appointmentPatientCaseId").append(
                            '<option value="" selected disabled>Select Case</option>');
                        for (var i = 0; i < response.length; i++) {
                            $("#appointmentPatientCaseId").append('<option value="' +
                                response[i].id + '">' + response[i].case_id +
                                '</option>');
                        }
                    } else {
                        $("#appointmentPatientCaseId").empty();
                        $("#appointmentPatientCaseId").append(
                            '<option value="" selected disabled>No Case Found</option>');
                    }
                },
                error: function(error) {
                    toster.error(error)
                }
            });
        });

        $('#patientForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            console.log("Form submitted");

            // Clear previous error messages
            $('#createAppointmentErrorsBox').addClass('d-block').empty();

            $.ajax({
                type: "POST",
                url: "{{ route('add-patient-appointment') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    first_name: $('#patientFirstName').val(),
                    last_name: $('#patientLastName').val(),
                    CNIC: $('#userCnic').val(),
                    dob: $('#patientBirthDate').val(),
                    phone: $('#patientPhoneNumber').val(),
                    emergencyPhone: $('#patientEmergencyPhoneNumber').val(),
                    gender: $('input[name="gender"]:checked').val(),
                    blood_group: $('#patientBloodGroup').val(),
                    prefix_code: $('#prefixCode').val() || 'default_prefix_code'
                },
                success: function(response) {
                    $("#appointmentPatientId").append(
                        '<option value="' + response.id + '">(' + response.MR + ') ' +
                        response.user.full_name + '</option>'
                    );
                    $("#appointmentPatientId").val(response.id);

                    $('#patientForm').trigger("reset");
                    $('#patientModal').modal('hide');
                    $('#btnClose').trigger("click");
                },
                error: function(xhr) {
                    console.log("Error occurred while adding patient appointment.");
                    console.log(xhr.responseText);
                    // Handle errors and show them in the error box
                    if (xhr.status === 400) {
                        $('#createAppointmentErrorsBox').removeClass('d-none').html(xhr
                            .responseJSON.error);
                        toster.error(xhr.responseJSON.error);
                    } else if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        toster.error(errors);
                        $.each(errors, function(key, value) {
                            $('#createAppointmentErrorsBox').append('<p>' + value[
                                0] + '</p>');
                        });
                        $('#createAppointmentErrorsBox').removeClass('d-none');
                    } else {
                        $('#createAppointmentErrorsBox').html(
                            'An unexpected error occurred.').removeClass('d-none');
                        toster.error(xhr);
                    }
                }
            });
        });

        $('#patientForm2').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            console.log("Form submitted");

            // Clear previous error messages
            $('#createAppointmentErrorsBox').addClass('d-block').empty();

            $.ajax({
                type: "POST",
                url: "{{ route('add-patient-appointment') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    first_name: $('#patientFirstName2').val(),
                    last_name: $('#patientLastName2').val(),
                    CNIC: $('#userCnic2').val(),
                    dob: $('#patientBirthDate2').val(),
                    phone: $('#patientPhoneNumber2').val(),
                    emergencyPhone: $('#patientEmergencyPhoneNumber2').val(),
                    gender: $('input[name="gender"]:checked').val(),
                    blood_group: $('#patientBloodGroup2').val(),
                    prefix_code: $('#prefixCode2').val() || 'default_prefix_code'
                },
                success: function(response) {
                    $("#appointmentPatientId").append(
                        '<option value="' + response.id + '">(' + response.MR + ') ' +
                        response.user.full_name + '</option>'
                    );
                    $("#appointmentPatientId").val(response.id);

                    $('#patientForm').trigger("reset");
                    $('#patientModal').modal('hide');
                    $('#btnClose').trigger("click");
                },
                error: function(xhr) {
                    console.log("Error occurred while adding patient appointment.");
                    console.log(xhr.responseText);
                    // Handle errors and show them in the error box
                    if (xhr.status === 400) {
                        $('#createAppointmentErrorsBox').removeClass('d-none').html(xhr
                            .responseJSON.error);
                            toster.error(xhr.responseJSON.error);
                    } else if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        toster.error(errors);
                        $.each(errors, function(key, value) {
                            $('#createAppointmentErrorsBox').append('<p>' + value[
                                0] + '</p>');
                        });
                        $('#createAppointmentErrorsBox').removeClass('d-none');
                    } else {
                        $('#createAppointmentErrorsBox').html(
                            'An unexpected error occurred.').removeClass('d-none');
                            toster.error(xhr);
                    }
                }
            });
        });
    });

</script>
