@extends('layouts.app3')

<style>
        #select2-patient_mr_number-container, #select2-opd_id-container {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-clip: padding-box;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: .313rem;
    color: #6c757d;
    display: block;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.5;
    padding: .688rem .938rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    width: 100%;

}

.select2-container--default .select2-selection--single{
    border:0 !important;
    margin-bottom:10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 10px !important;
    right: 10px !important;
}
</style>
@section('title')
    Nursing From List
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3>Nursing Assessment Form</h3>
                <a href="{{ route('nursing.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <form action="{{ route('nursing-form.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mt-5 mb-5 row">
                        <div class="col-md-6">
                            <label for="patient_mr_number" class="form-label">MR #<sup class="text-danger">*</sup></label>
                            <select class="form-select" name="patient_mr_number" id="patient_mr_number" required>
                                <option value="" selected disabled>select mr number</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->MR }}" data-blood_pressure="{{ $patient->blood_pressure }}"
                                        data-patient_id="{{ $patient->id }}" data-heart_rate="{{ $patient->heart_rate }}"
                                        data-respiratory_rate="{{ $patient->respiratory_rate }}"
                                        data-temperature="{{ $patient->temperature }}" data-height="{{ $patient->height }}"
                                        data-weight="{{ $patient->weight }}" data-bmi="{{ $patient->bmi }}"
                                        data-name="{{ $patient->user->full_name }}">{{ $patient->MR }}
                                        ~ {{ $patient->user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="opd_id" class="form-label">OPD<sup class="text-danger">*</sup></label>
                            <select class="form-control" name="opd_id" id="opd_id" required>
                                <option value="" selected disabled>select mr number first</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-5">
                            <label for="patient_name" class="form-label">Patient Name</label>
                            <input readonly type="text" name="patient_name" id="patient_name" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h4>VITAL SIGNS:</h4>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="blood_pressure" class="form-label">Blood Pressure (mmHg)</label>
                                <input type="text" name="blood_pressure" id="blood_pressure"
                                    class="form-control">
                                <input type="hidden" name="patient_id" id="patient_id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="heart_rate" class="form-label">Heart Rate (bpm)</label>
                                <input type="text" name="heart_rate" id="heart_rate" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="respiratory_rate" class="form-label">Respiratory Rate (breaths/min)</label>
                                <input type="text" name="respiratory_rate" id="respiratory_rate"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="temperature" class="form-label">Temperature (°C/°F)</label>
                                <input type="text" name="temperature" id="temperature" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="pbs" class="form-label">SpO2 (%)</label>
                                <input type="text" name="spo_2" id="spo_2" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="height" class="form-label">Height (cm/in)</label>
                                <input type="text" name="height" id="height" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="weight" class="form-label">Weight (kg/lb)</label>
                                <input type="text" name="weight" id="weight" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="foc" class="form-label">Fronto Occipital Circumference (FOC)</label>
                                <input type="text" name="foc" id="foc" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="ac" class="form-label">Abdominal circumference (AC)</label>
                                <input type="text" name="ac" id="ac" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-5">
                            <label for="bmi" class="form-label">BMI</label>
                            <input readonly type="text" name="bmi" id="bmi" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label for="pain_level" class="form-label">Pain Level (0-10)</label>
                                <input type="text" name="pain_level" id="pain_level" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label for="fbs" class="form-label">FBS (mg/dL)</label>
                                <input type="text" name="fbs" id="fbs" class="form-control">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label for="rbs" class="form-label">RBS (mg/dL)</label>
                                <input type="text" name="rbs" id="rbs" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>


                {{-- <div class="card-body">
                    <h4>Current Medications:</h4>
                    <table class="table table-bordered">
                        <thead>
                            <div class="row">
                                <div class="mt-5 mb-5 col-md-2">
                                    <button type="button" class="btn btn-primary" onclick="Addmore()"
                                        id="addmore-btn">Add More</button>
                                </div>
                            </div>
                            <tr>
                                <td>Medication Name</td>
                                <td>Dosage </td>
                                <td>Frequency </td>
                                <td>Prescribing Physician</td>
                                <td>Root</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody id="current_medications_table">
                            <tr>
                                <td><input type="text" name="medications[0][medication_name]" class="form-control">
                                </td>
                                <td><input type="text" class="form-control" name="medications[0][dosage]"></td>
                                <td><input type="text" class="form-control" name="medications[0][frequency]"></td>
                                <td><input type="text" class="form-control" name="medications[0][root]"></td>
                                <td><input type="text" class="form-control"
                                        name="medications[0][prescribing_physician]"></td>
                            </tr>
                        </tbody>
                    </table>
                </div> --}}

                <div class="card-body">
                    <h4>Allergies:</h4>
                    <table class="table table-bordered">
                        <thead>
                            <div class="row ">
                                <div class="mt-5 mb-5 col-md-2">
                                    <button type="button" class="btn btn-primary" onclick="addmoreallergy()"
                                        id="addmore-btn">Add More</button>
                                </div>
                            </div>
                            <tr>
                                <td>Allergen</td>
                                <td>Reaction</td>
                                <td>Severity</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody id="allergies_table">
                            <tr>
                                <td><input type="text" class="form-control" name="allergies[0][allergen]"></td>
                                <td><input type="text" class="form-control" name="allergies[0][reaction]"></td>
                                <td><input type="text" class="form-control" name="allergies[0][severity]"></td>
                            </tr>
                        </tbody>
                    </table>


                    <div class="row">
                        <div class="col-md-12 mt-5 mb-5">
                            <h2>Nursing Notes</h2>
                            {{-- <label for="details" class="form-label">Nursing Notes</label> --}}
                            <textarea name="details" id="details" class="form-control"></textarea>
                            @error('details')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-6">
                            <label for="assessment_date" class="form-label">Assessment Date</label>
                            <input type="date" name="assessment_date" id="assessment_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="nurse_name" class="form-label">Nurse's Name</label>
                            <input type="text" placeholder="Enter Nurse's Name" name="nurse_name" id="nurse_name"
                                class="form-control">
                        </div>
                    </div>

                    {{-- <div class="mt-5 mb-5 row">
                        <div class="col-md-12">
                            <label for="signature" class="form-label">Signature</label>
                            <input type="text" placeholder="Enter Signature" name="signature" id="signature"
                                class="form-control">
                        </div>
                    </div> --}}

                    <div class="mt-10 mb-5 text-center row">
                        <div class="col-md-12">
                            <button type="submit" id="save_Btn" class="btn btn-secondary">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>


        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Replace the textarea with CKEditor
                CKEDITOR.replace('details');
            });

            function deleteRowCMT() {
                var table = document.querySelector("#current_medications_table");
                var lastRow = table.lastElementChild;
                table.removeChild(lastRow);
            }

            function deleteRowAllergy() {
                var table = document.querySelector("#allergies_table");
                var lastRow = table.lastElementChild;
                table.removeChild(lastRow);
            }

            function addmoreallergy() {
                var tableRow = document.getElementById('allergies_table');
                var a = tableRow.rows.length;
                $('#allergies_table').append(`
            <tr id="allery-row${a}">
                <td><input type="text" class="form-control" name="allergies[${a}][allergen]"></td>
                                <td><input type="text" class="form-control" name="allergies[${a}][reaction]"></td>
                                <td><input type="text" class="form-control" name="allergies[${a}][severity]"></td>
                                <td><button onclick="deleteRowAllergy()"  class="btn-danger"><i class="fa fa-trash"></i></button></td>

                    </tr>
            `);
            }

            function Addmore() {
                var tableRow = document.getElementById('current_medications_table');
                var a = tableRow.rows.length;
                console.log('AAA ' + a);
                $('#current_medications_table').append(`
            <tr id="medicine-row${a}">
                                <td><input type="text" name="medications[${a}][medication_name]" class="form-control"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][dosage]"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][frequency]"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][prescribing_physician]"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][root]"></td>
                                <td><button onclick="deleteRowCMT()"  class="btn-danger"><i class="fa fa-trash"></i></button></td>

                    </tr>
            `);
            }


            $(document).ready(function() {
                $('#patient_mr_number').select2();
                $('#opd_id').select2();
                $('#patient_mr_number').change(function() {
                    var selectElement = document.getElementById('patient_mr_number');
                    var selectedOption = selectElement.options[selectElement.selectedIndex];

                    const PatientId = selectedOption.getAttribute('data-patient_id');
                    const BloodPressure = selectedOption.getAttribute('data-blood_pressure');
                    const HeartRate = selectedOption.getAttribute('data-heart_rate');
                    const RespiratoryRate = selectedOption.getAttribute('data-respiratory_rate');
                    const Temperature = selectedOption.getAttribute('data-temperature');
                    const Height = selectedOption.getAttribute('data-height');
                    const Weight = selectedOption.getAttribute('data-weight');
                    const BMI = selectedOption.getAttribute('data-bmi');
                    const PatientName = selectedOption.getAttribute('data-name');

                    // Check if the data values are null and set the input field values accordingly
                    $("#patient_id").val(((PatientId !== null) ? PatientId : ``));
                    $("#blood_pressure").val(((BloodPressure !== null) ? BloodPressure : ``));
                    $("#heart_rate").val(((HeartRate !== null) ? HeartRate : ``));
                    $("#respiratory_rate").val(((RespiratoryRate !== null) ? RespiratoryRate : ``));
                    $("#temperature").val(((Temperature !== null) ? Temperature : ``));
                    $("#height").val(((Height !== null) ? Height : ``));
                    $("#weight").val(((Weight !== null) ? Weight : ``));
                    $("#bmi").val(((BMI !== null) ? BMI : ``));
                    $("#patient_name").val(((PatientName !== null) ? PatientName : ``));



                    // $.ajax({
                    //     type: "get",
                    //     url: "/nursing-form/opd/list",
                    //     data: {
                    //         patient_mr_number: $(this).val()
                    //     },
                    //     dataType: "json",
                    //     success: function(response) {
                    //         $("#opd_id").empty();
                    //         let isOPDavailabel = false;
                    //         if (response.data.length !== 0) {
                    //             isOPDavailabel = true;
                    //             $('#opd_id').append(
                    //                 `<option selected disabled>Select OPD </option>`);
                    //             $.each(response.data, function(index, value) {
                    //                 $("#opd_id").append(
                    //                     `
            // <option value="${value.opd_number}" data-doctor="${value.doctor.user.full_name}"  data-patient="${value.patient.user.full_name}" >
            // ${value.opd_number}
            // </option>`
                    //                 );
                    //             });
                    //         }
                    //         if (response.data2.length !== 0) {
                    //             isOPDavailabel = true;
                    //             $.each(response.data2, function(index, value2) {
                    //                 // console.log(value2);
                    //                 $("#opd_id").append(
                    //                     `
            // <option value="${value2.opd_number}" data-patient="${value2.patient.user.full_name}" >
            // ${value2.opd_number}
            // </option>`
                    //                 );
                    //             });
                    //         }
                    //         if (!isOPDavailabel) {
                    //             $("#opd_id").html(
                    //                 `<option value="" class="text-danger" selected disabled>No any opd found!</option>`
                    //             );
                    //         }
                    //         // console.log(response.data2[0].patient.blood_pressure);
                    //         // if(response.data2[0]) {
                    //         //     $("#blood_pressure").val(response.data2[0].patient.blood_pressure);
                    //         //     $("#heart_rate").val(response.data2[0].patient.heart_rate);
                    //         //     $("#respiratory_rate").val(response.data2[0].patient.respiratory_rate);
                    //         //     $("#temperature").val(response.data2[0].patient.temperature);
                    //         //     $("#height").val(response.data2[0].patient.height);
                    //         //     $("#weight").val(response.data2[0].patient.weight);
                    //         //     $("#bmi").val(response.data2[0].patient.bmi);
                    //         // }


                    //     }
                    // });

                    $.ajax({
                        type: "get",
                        url: "/nursing-form/opd/list",
                        data: {
                            patient_mr_number: $(this).val()
                        },
                        dataType: "json",
                        success: function(response) {
                            $("#opd_id").empty();
                            let isOPDavailabel = false;

                            if (response.data) {
                                isOPDavailabel = true;
                                $('#opd_id').append(
                                    `<option selected disabled>Select OPD </option>`);
                                $("#opd_id").append(
                                    `<option value="${response.data.opd_number}" data-doctor="${response.data.doctor.user.full_name}" data-patient="${response.data.patient.user.full_name}">
                    ${response.data.opd_number}
                </option>`
                                );
                            }

                            if (response.data2) {
                                isOPDavailabel = true;
                                $("#opd_id").append(
                                    `<option value="${response.data2.opd_number}" data-patient="${response.data2.patient.user.full_name}">
                    ${response.data2.opd_number}
                </option>`
                                );
                            }

                            if (!isOPDavailabel) {
                                $("#opd_id").html(
                                    `<option value="" class="text-danger" selected disabled>No any opd found!</option>`
                                    );
                            }
                        },
                        error: function() {
                            $("#opd_id").html(
                                `<option value="" class="text-danger" selected disabled>Error retrieving OPD data</option>`
                                );
                        }
                    });

                });
            });
        </script>
        <script>
            const heightInput = document.getElementById("height"); //height k inoput ki id
            const weightInput = document.getElementById("weight"); //weigh k input ki id
            const bmiInput = document.getElementById("bmi"); // yhn p jonsi jagah p value show karani h whn ki id dedooo

            heightInput.addEventListener("input", calculateBMI);
            weightInput.addEventListener("input", calculateBMI);

            // Function to calculate BMI
            function calculateBMI() {
                const weightKg = parseFloat(weightInput.value);
                const heightCm = parseFloat(heightInput.value);

                // Convert height to meters (1 meter = 100 cm)
                const heightM = heightCm / 100;

                // Calculate BMI
                const bmi = (weightKg / (heightM * heightM)).toFixed(2);

                // Update the BMI input field
                bmiInput.value = isNaN(bmi) ? "" : bmi;

                // Determine BMI category based on the calculated BMI
                let bmiCategory = "";

                if (bmi < 18.5) {
                    bmiCategory = "Underweight";
                } else if (bmi >= 18.5 && bmi < 23) {
                    bmiCategory = "Normal";
                } else if (bmi >= 23 && bmi < 27) {
                    bmiCategory = "Overweight";
                } else if (bmi >= 27) {
                    bmiCategory = "Obese";
                }

                // Add the BMI category to the BMI input field
                if (bmiCategory) {
                    bmiInput.value += " (" + bmiCategory + ")";
                }
            }
        </script>
    @endsection
