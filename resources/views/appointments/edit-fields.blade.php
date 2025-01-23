<div class="row">
    <!-- Patient Name Field -->
    @if (Auth::user()->hasRole('Patient'))
        <input type="hidden" name="patient_id" value="{{ Auth::user()->owner_id }}">
    @else
        <div class="mb-5 form-group col-sm-6">
            {{ Form::label('patient_name', __('messages.case.patient') . ':', ['class' => 'form-label']) }}
            <span class="required"> </span>
            {{-- {{ Form::select('patient_id', $patients, null, ['class' => 'form-select','required','id' => 'appointmentPatientId','placeholder'=>'Select Patient', 'data-control' => 'select2']) }} --}}
            <select name="patient_id" id="appointmentPatientId" placeholder='Select Patient' class="form-select" required>
                <option value="" selected disabled>Select Patient</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}"
                        {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                        ({{ $patient->MR }})
                        {{ $patient->patientUser->full_name }}
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
    {{ Form::text('opd_date', $appointment->opd_date->format('Y-m-d'), ['id' => 'appointmentOpdDate', 'class' => getLoggedInUser()->thememode ? 'bg-light opdDate form-control' : 'bg-white opdDate form-control', 'required', 'autocomplete' => 'off']) }}
</div>

        {{-- <div class="mb-5 form-group col-sm-6">
            {{ Form::label('advance_amount', 'Advance Amount '.'(optional):', ['class' => 'form-label']) }} --}}
        {{-- <span class="required"></span> --}}
        {{-- {{ Form::text('advance_amount' ['id'=>'appointmentAdvanceAmount', 'class' => (getLoggedInUser()->thememode ? 'bg-light opdDate form-control' : 'bg-white opdDate form-control'), , 'autocomplete'=>'off']) }} --}}
        {{-- <input type="number" name="advance_amount" id="appointmentAdvanceAmount" class="form-control">
        </div> --}}

        {{-- <div class="mb-5 form-group col-sm-6">
            {{ Form::label('Payment Mode', __('Payment Mode ').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            <select name="payment_mode" class="form-select" data-control="select2" id="payment_mode">
                <option value="" selected disabled>Select Payment Method</option>
                <option value="1">Cash</option>
                <option value="2">Cheque</option>
                <option value="3">Card</option>
            </select>
        </div> --}}

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

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
         $('#appointmentOpdDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        
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
                    patient_id: patientId
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
                    console.log(error);
                }
            })
        })
    })
</script>
