    <style>
    .p-l-0 {
        padding-left: 0 !important;
    }
    </style>

    <div class="row gx-10 mb-5">
        <div class="form-group col-md-3 mb-5">
            {{ Form::label('patient_id', __('MR / Patient').(':'), ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::select('patient_id',$patients, null, ['class' => 'form-select','required','id' => 'prescriptionPatientId','placeholder'=>'Select Patient', 'onchange' => 'yourJavaScriptFunction(this.value)']) }}
        </div>
        <div class="form-group col-md-3 mb-5">
            <label class="form-lable mb-2">OPD</label>
            <span class="required"></span>


            <select class="form-select select2-hidden-accessible" required="" id="prescriptionDoctorId" name="patient_opd" data-select2-id="select2-data-prescriptionDoctorId" tabindex="-1" aria-hidden="true">
            <option>Select OPD Number</option>
            </select>
        </div>

        <div class="col-md-3">
            <div class="form-group mb-5">
                <label class="form-lable mb-2">Doctor Name</label>
                <input id="doctornamee" class="form-control" value="" disabled/>
                <input type="hidden" id="doctoride" class="form-control" value="" name="doctor_id"/>
            </div>
        </div>
        {{-- @if(Auth::user()->hasRole('Doctor'))
            <input type="hidden" name="doctor_id" value="{{ Auth::user()->owner_id }}">
        @else
            <div class="form-group col-md-3 mb-5">
                {{ Form::label('doctor_name', __('messages.case.doctor').(':'), ['class' => 'form-label']) }}
                <span class="required"></span>
                {{ Form::select('doctor_id',$doctors, null, ['class' => 'form-select','required','id' => 'prescriptionDoctorId','placeholder'=>'Select Doctor']) }}
            </div>
        @endif --}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('food_allergies', __('messages.prescription.food_allergies').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('food_allergies', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('tendency_bleed', __('messages.prescription.tendency_bleed').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('tendency_bleed', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('heart_disease', __('messages.prescription.heart_disease').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('heart_disease', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('high_blood_pressure', __('messages.prescription.high_blood_pressure').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('high_blood_pressure', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('diabetic', __('messages.prescription.diabetic').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('diabetic', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('surgery', __('messages.prescription.surgery').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('surgery', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('accident', __('messages.prescription.accident').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('accident', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('others', __('messages.prescription.others').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('others', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('medical_history', __('messages.prescription.medical_history').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('medical_history', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('current_medication', __('messages.prescription.current_medication').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('current_medication', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('female_pregnancy', __('messages.prescription.female_pregnancy').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('female_pregnancy', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="col-md-3">--}}
    {{--        <div class="form-group mb-5">--}}
    {{--            {{ Form::label('breast_feeding', __('messages.prescription.breast_feeding').(':'), ['class' => 'form-label']) }}--}}
    {{--            {{ Form::text('breast_feeding', null, ['class' => 'form-control']) }}--}}
    {{--        </div>--}}
    {{--    </div>--}}
        <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('health_insurance', __('messages.prescription.health_insurance').(':'), ['class' => 'form-label']) }}
                {{ Form::text('health_insurance', null, ['class' => 'form-control']) }}
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('low_income', __('messages.prescription.low_income').(':'), ['class' => 'form-label']) }}
                {{ Form::text('low_income', null, ['class' => 'form-control']) }}
            </div>
        </div> --}}
        <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('reference', __('messages.prescription.reference').(':'), ['class' => 'form-label']) }}
                {{ Form::text('reference', null, ['class' => 'form-control']) }}
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('reference', __('messages.prescription.reference').(':'), ['class' => 'form-label']) }}
                {{ Form::text('reference', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('reference', __('messages.prescription.reference').(':'), ['class' => 'form-label']) }}
                {{ Form::text('reference', null, ['class' => 'form-control']) }}
            </div>
        </div> --}}
        <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('status', __('messages.common.status').(':'), ['class' => 'form-label']) }}
                <br>
                <div class="form-check form-switch">
                    <input name="status" class="form-check-input  is-active" value="1" type="checkbox" checked>
                    <label class="form-check-label" for="allowmarketing"></label>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-5 p-l-0">
                {{ Form::label('TestsandConsultations', __('Tests and Consultations').(':'), ['class' => 'form-label']) }}
                <br>
                <div class="form-check form-switch p-l-0">
                    <input name="TestsandConsultations" class="form-control p-l-0" value="" id="TestsandConsultations" type="text">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-5">
                {{ Form::label('PatientEducation', __('Patient Education').(':'), ['class' => 'form-label']) }}
                <br>
                <div class="form-check p-l-0">
                    <input name="PatientEducation" class="form-control" value="" id="PatientEducation" type="text">
                </div>
            </div>
        </div>
    </div>

    {{--<div class="d-flex justify-content-end">--}}
    {{--    {!! Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2 btnPrescriptionSave','id' => 'prescriptionSave']) !!}--}}
    {{--    <a href="{!! route('prescriptions.index') !!}"--}}
    {{--       class="btn btn-secondary">{!! __('messages.common.cancel') !!}</a>--}}
    {{--</div>--}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap">

    <script>

        function yourJavaScriptFunction(selectedValue) {
            $.ajax({
                type: "GET",
                url: "/get_opd_data_by_pataint_id/" + selectedValue,
                success: function (response) {
                    let data = response;
                    console.log(data);
                    let prescriptionDoctorIdSelect = document.getElementById("prescriptionDoctorId");
                    prescriptionDoctorIdSelect.innerHTML = "";
                    let initialOption = document.createElement("option");
                    initialOption.text = "Select OPD Number";
                    initialOption.value = "";
                    prescriptionDoctorIdSelect.appendChild(initialOption);
                    for (let i = 0; i < data.length; i++) {
                        let opdNumber = data[i].opd_number;
                        let option = document.createElement("option");
                        option.text = opdNumber;
                        option.value = opdNumber;
                        prescriptionDoctorIdSelect.appendChild(option);
                    }
                    $(prescriptionDoctorIdSelect).trigger('change.select2');
                },
                failure: function (response) {
                    console.log(response.responseText);
                },
                error: function (response) {
                    console.log(response.responseText);
                }
            });
        }


    $("#prescriptionDoctorId").on("change", function () {
        let selectedOpdNumber = $(this).val();
        $.ajax({
            type: "GET",
            url: "/get_opd_doc_by_opd/" + selectedOpdNumber,
            success: function (response) {
                let name = response;
                let doc = name.doctor_user.full_name;
                let docid = name.id;
                let doctorid = document.getElementById("doctoride");
                doctorid.value = docid;
                let docname = document.getElementById("doctornamee");
                docname.value = doc;
            },
            failure: function (response) {
                console.log(response.responseText);
            },
            error: function (response) {
                console.log(response.responseText);
            }
        });
    });
    $("#prescriptionPatientId").on("change", function () {
        let selectedPatientId = $(this).val();
        $.ajax({
            type: "GET",
            url: "/get-prescription-formData/" + selectedPatientId,
            success: function (response) {
                console.log("response", response);
                // response.data.forEach(function (data) {
                //     if(data.fieldName == "PatientEducation"){
                //         console.log("PatientEducation", data.fieldValue);
                //         document.getElementById("PatientEducation").value = data.fieldValue;
                //     }
                //     if(data.fieldName == "TestsandConsultations"){
                //         console.log("TestsandConsultations", data.fieldValue);
                //         document.getElementById("TestsandConsultations").value = data.fieldValue;
                //     }
                // });

                // Iterate over the data and break when the first match is found
                for (let i = 0; i < response.data.length; i++) {
                    let data = response.data[i];
                    console.log("data.fieldName", data.fieldName);
                    if(data.fieldName == "TestsandConsultations"){
                        console.log("TestsandConsultations", data.fieldValue);
                        document.getElementById("TestsandConsultations").value = data.fieldValue;
                        break; // Stop after finding the first match
                    }
                }

                for (let i = 0; i < response.data.length; i++) {
                    let data = response.data[i];

                    // Check if the fieldName is PatientEducation
                    if (data.fieldName == "PatientEducation") {
                        // Set the value in the input field
                        document.getElementById("PatientEducation").value = data.fieldValue;
                        break; // Stop after finding and setting PatientEducation
                    }
                }
            },
            failure: function (response) {
                console.log(response.responseText);
            },
            error: function (response) {
                console.log(response.responseText);
            }
        });
    });

    </script>
