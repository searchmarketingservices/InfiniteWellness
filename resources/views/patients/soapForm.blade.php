@extends('layouts.app2')
@section('title')
    {{ __('messages.patients') }}
@endsection

@section('content')
    <div class="container my-3">
        <form action="{{ request()->url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center">Subjective Objective Assessment Plan (SOAP)</h2>
                </div>
                <input type="hidden" name="patient_id" value="{{ $patientData->id }}">
                <br><br>
                <div class="col-12">
                    <h4 class="text-left">1. Subjective (S)</h4>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <li>
                        <label for="textAreaExample1">Chief complaint</label>
                        <textarea class="form-control" id="textAreaExample1" rows="4"
                            value="{{ $patientData === null ? '' : $patientData->user->full_name }}" placeholder="Chief complaint"
                            name="Chiefcomplaint">
                            @foreach ($formData as $item)
@if ($item->fieldName == 'Chiefcomplaint')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
                        </textarea>

                    </li>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <li>
                        <label for="textAreaExample2">History of Present Illness</label>
                        <textarea class="form-control" id="textAreaExample2" rows="4" placeholder="Detailed history of present illness"
                            name="HistoryofPresentIllness">
                            @foreach ($formData as $item)
@if ($item->fieldName == 'HistoryofPresentIllness')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </li>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label class="form-label" for="customFile">Past Medical / Surgical History</label>
                        <input type="text" class="form-control" id="customFile" name="PastMedicalHistory"
                            value="@foreach ($formData as $item)
                                @if ($item->fieldName == 'PastMedicalHistory')
                                    {{ trim($item->fieldValue) }}
                                    @break
                                @endif @endforeach" />
                    </li>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample3">Medications</label>
                        <textarea class="form-control" id="textAreaExample3" rows="4"
                            placeholder="Listing of current medications, including prescription, over-the-counter drugs, and supplements"
                            name="Medications">
                             @foreach ($formData as $item)
@if ($item->fieldName == 'Medications')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
                            </textarea>
                    </li>
                </div>

                <div class="row  mt-5 mb-5">
                    <div class="col-md-6">
                        <label for="textAreaExample20">Family Medical History</label>
                        <small>(if relevant)</small>
                        <!-- <input type="email" class="form-control " id="textAreaExample18" aria-describedby="emailHelp"
                                                                                                                placeholder=""> -->
                        <textarea class="form-control" id="textAreaExample20" rows="4" placeholder="Family medical history "
                            name="FamilyMedicalHistory">
                         @foreach ($formData as $item)
@if ($item->fieldName == 'FamilyMedicalHistory')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="textAreaExample21">Social History</label>
                        <small>(e.g., smoking, alcohol consumption)</small>
                        <!-- <input type="email" class="form-control " id="textAreaExample18" aria-describedby="emailHelp"
                                                                                                                placeholder=""> -->
                        <textarea class="form-control" id="textAreaExample21" rows="4" placeholder="(e.g., smoking, alcohol consumption) "
                            name="SocialHistory">
                        @foreach ($formData as $item)
@if ($item->fieldName == 'SocialHistory')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </div>
                </div>
                {{-- <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample4">Reofview  sysemts</label>
                        <textarea class="form-control" id="textAreaExample4" rows="4"
                            placeholder="An overview of the patient's major body systems, asking about symptoms related to each system." name="Reofviewsysemts">
                             @foreach ($formData as $item)
                             @if ($item->fieldName == 'Reofviewsysemts')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>
                </div> --}}

                <div class="col-12">
                    <br>
                    <h4 class="text-left">2. Objective (O)</h4>
                </div>
                <div class="col-12">
                    <br>
                    <h5 class="text-left">Vital Signs</h5>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput3">Blood Pressure</label>
                    <input type="text" class="form-control " id="exampleInput3"
                        value="{{ $patientData === null ? '' : $patientData->blood_pressure }}"
                        placeholder="patient blood pressure" name="BloodPressure"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'BloodPressure')

                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-md-1">
                    <div class="mt-5">
                        mmHg
                    </div>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput3">Heart Rate</label>
                    <input type="text" class="form-control " id="exampleInput3"
                        value="{{ $patientData === null ? '' : $patientData->heart_rate }}"
                        placeholder="patient heart rate" name="HeartRate"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'HeartRate')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-md-1">
                    <div class="mt-5">
                        bpm
                    </div>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput3">Respiratory Rate</label>
                    <input type="text" class="form-control " id="exampleInput3" placeholder="patient respiratory rate"
                        value="{{ $patientData === null ? '' : $patientData->respiratory_rate }}" name="RespiratoryRate"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'RespiratoryRate')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-md-1">
                    <div class="mt-5">
                        breaths/min
                    </div>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput3">Temperature</label>
                    <input type="text" class="form-control " id="exampleInput3" placeholder="patient temperature"
                        value="{{ $patientData === null ? '' : $patientData->temperature }}" name="Temperature"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'Temperature')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-md-1">
                    <div class="mt-5">
                        °C/°F
                    </div>
                </div>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <h5 class="text-left">Physical Examination</h5>

                    <textarea class="form-control" id="textAreaExample5" rows="4"
                        placeholder="Detailed examination findings, including general appearance, systems-specific examinations, and any abnormalities observed."
                        name="PhysicalExamination">
                         @foreach ($formData as $item)
@if ($item->fieldName == 'PhysicalExamination')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
                        </textarea>
                </div>


                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <h5 class="text-left">Laboratory and Diagnostic Tests</h5>

                    <textarea class="form-control" id="textAreaExample6" rows="4" name="LaboratoryandDiagnosticTests"
                        placeholder="Results of any tests performed, such as blood work, imaging studies, or other relevant investigations.">
                         @foreach ($formData as $item)
@if ($item->fieldName == 'LaboratoryandDiagnosticTests')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                </div>

                {{-- <div class="col-12">
                    <br>

                    <h5 class="text-left">Objective Measurements:</h5>
                </div>

                <div class="col-lx-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput4">Height</label>
                    <input type="text"  class="form-control " id="exampleInput4" value="{{($patientData === null) ? '' : $patientData->height }}" placeholder="patient height" name="Height"
                     @foreach ($formData as $item)
                                @if ($item->fieldName == 'Height')
                                    value="{{trim($item->fieldValue)}}"
                                    @break
                                @endif
                            @endforeach>
                </div>

                <div class="col-lx-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput5">Weight</label>
                    <input type="text"  class="form-control " name="Weight" id="exampleInput5" value="{{($patientData === null) ? '' : $patientData->weight }}" placeholder="patient weight"
                     @foreach ($formData as $item)
                                @if ($item->fieldName == 'Weight')
                                    value="{{trim($item->fieldValue)}}"
                                    @break
                                @endif
                            @endforeach>
                </div>

                <div class="col-lx-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput6">Body Mass Index (BMI)</label>
                    <input name="BodyMassIndex"  type="text" class="form-control " value="{{($DietData!=null)?(($DietData->bmi!=null)?$DietData->bmi:''):''}}" id="exampleInput6"  placeholder="patient BMI"
                     @foreach ($formData as $item)
                                @if ($item->fieldName == 'BodyMassIndex')
                                    value="{{trim($item->fieldValue)}}"
                                    @break
                                @endif
                            @endforeach>
                </div> --}}

                {{-- <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <br>
                    <label for="exampleInput7">Laboratory Values</label>
                    <input name="LaboratoryValues" type="text" class="form-control " id="exampleInput7" placeholder="patient laboratory values"
                     @foreach ($formData as $item)
                                @if ($item->fieldName == 'LaboratoryValues')
                                    value="{{trim($item->fieldValue)}}"
                                    @break
                                @endif
                            @endforeach>
                </div> --}}

                <div class="col-12">
                    <br>
                    <h4 class="text-left">3. Assessment (A)</h4>
                </div>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample7">Diagnosis</label>
                        <textarea class="form-control" id="textAreaExample7" rows="4"
                            placeholder="Identification and formulation of the patient's condition or suspected medical problem."
                            name="Diagnosis">
                              @foreach ($formData as $item)
@if ($item->fieldName == 'Diagnosis')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </li>
                </div>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample8">Differential Diagnosis</label>
                        <textarea class="form-control" id="textAreaExample8" rows="4"
                            placeholder="A list of potential diagnoses that are being considered based on the patient's presentation and available information."
                            name="DifferentialDiagnosis">
                            @foreach ($formData as $item)
@if ($item->fieldName == 'DifferentialDiagnosis')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </li>
                </div>

                {{-- <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample9">Problem List</label>
                        <textarea class="form-control" id="textAreaExample9" rows="4"
                            placeholder=" A summary of the patient's active medical issues or concerns." name="ProblemList">
                             @foreach ($formData as $item)
                                @if ($item->fieldName == 'ProblemList')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>
                </div> --}}

                {{-- <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample10">Prognosis</label>
                        <textarea class="form-control" id="textAreaExample10" rows="4"
                            placeholder=" An evaluation of the expected course of the condition and possible outcomes." name="Prognosis">
                              @foreach ($formData as $item)
                                @if ($item->fieldName == 'Prognosis')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>
                </div> --}}


                {{-- <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample11">Health Status</label>
                        <textarea class="form-control" id="textAreaExample11" rows="4"
                            placeholder="Any relevant conclusions or impressions regarding the patient's health status." name="HealthStatus">
                             @foreach ($formData as $item)
                                @if ($item->fieldName == 'HealthStatus')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>
                </div> --}}

                <div class="col-12">
                    <br>
                    <h4 class="text-left">4. Plan (P)</h4>
                </div>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    {{--  <br>
                    <li>
                        <label for="textAreaExample12">Medications</label>
                        <textarea class="form-control" id="textAreaExample12" rows="4"
                            placeholder="Prescription medications, dosage, frequency, and any changes in the existing medications." name="Medications2">
                             @foreach ($formData as $item)
                                @if ($item->fieldName == 'Medications2')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>  --}}
                    <br>
                    <li>
                        <div class="table-responsive">
                            <label for="textAreaExample12">Medications</label>
                            <div>
                                <button type="button" class="btn btn-primary" onclick="addMedication()">Add</button>
                                <button type="button" class="fa fa-plus btn btn-primary" onclick="addMedicationManualy()"></button>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td>Medication</td>
                                        <td>Dosage</td>
                                        <td>Frequency</td>
                                        <td>Route</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{ dd($formData) }} --}}
                                    @php
                                        // Initialize arrays for each field with default empty arrays
                                        $medicationArray = [];
                                        $dosageArray = [];
                                        $frequencyArray = [];
                                        $routeArray = [];

                                        // Accumulate values for each field
                                        foreach ($formData as $item) {
                                            if ($item->fieldName == 'medication') {
                                                $medicationArray = json_decode($item->fieldValue, true) ?? [];
                                            } elseif ($item->fieldName == 'dosage') {
                                                $dosageArray = json_decode($item->fieldValue, true) ?? [];
                                            } elseif ($item->fieldName == 'frequency') {
                                                $frequencyArray = json_decode($item->fieldValue, true) ?? [];
                                            } elseif ($item->fieldName == 'route') {
                                                $routeArray = json_decode($item->fieldValue, true) ?? [];
                                            }
                                        }

                                        // Get the maximum length of any array to handle rows with uneven lengths
                                        $maxLength = max(
                                            count($medicationArray),
                                            count($dosageArray),
                                            count($frequencyArray),
                                            count($routeArray),
                                        );
                                    @endphp

                                    @for ($i = 0; $i < $maxLength; $i++)
                                        <tr>
                                            <td>{{ $medicationArray[$i] ?? '' }}
                                                <input type="hidden" name="medication"
                                                    value="{{ $medicationArray[$i] ?? '' }}">
                                            </td>
                                            <td>{{ $dosageArray[$i] ?? '' }}</td>
                                            <td>{{ $frequencyArray[$i] ?? '' }}</td>
                                            <td>{{ $routeArray[$i] ?? '' }}</td>
                                        </tr>
                                    @endfor
                                </tbody>

                            </table>

                        </div>
                </div>
                </li>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample13">Tests and Consultations</label>
                        <textarea class="form-control" id="textAreaExample13" rows="4"
                            placeholder="Recommendations for further diagnostic tests, referrals to specialists, or consultations."
                            name="TestsandConsultations">
                                  @foreach ($formData as $item)
@if ($item->fieldName == 'TestsandConsultations')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
                        </textarea>
                    </li>
                </div>

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample14">Patient Education</label>
                        <textarea class="form-control" id="textAreaExample14" rows="4"
                            placeholder="Information provided to the patient about their condition, treatment options, lifestyle modifications, or self-care measures."
                            name="PatientEducation">
                              @foreach ($formData as $item)
@if ($item->fieldName == 'PatientEducation')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </li>
                </div>


                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample15">Follow-Up</label>
                        <textarea class="form-control" id="textAreaExample15" rows="4"
                            placeholder="Instructions regarding future appointments, monitoring, or scheduled re-evaluations."
                            name="Follow-Up">
                             @foreach ($formData as $item)
@if ($item->fieldName == 'Follow-Up')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
</textarea>
                    </li>
                </div>


                <div class="col-lx-12 col-lg-12 col-md-6 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="editor">Others</label>
                        <textarea class="form-control" id="editor" rows="4" placeholder="" name="editor">
                                @foreach ($formData as $item)
@if ($item->fieldName == 'editor')
{{ trim($item->fieldValue) }}
@break
@endif
@endforeach
                        </textarea>
                    </li>
                </div>
                {{-- <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <br>
                    <li>
                        <label for="textAreaExample16">Other Considerations</label>
                        <textarea class="form-control" id="textAreaExample16" rows="4"
                            placeholder=" Additional recommendations, such as lifestyle changes, counseling, or preventive measures." name="OtherConsiderations">
                                   @foreach ($formData as $item)
                                @if ($item->fieldName == 'OtherConsiderations')
                                    {{trim($item->fieldValue)}}
                                    @break
                                @endif
                            @endforeach</textarea>
                    </li>
                </div> --}}

                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-6 col-6">
                    <br>
                    <label for="exampleInput8">Attach File</label>
                    <input name="soapFormAttachment" type="file" class="form-control " id="exampleInput8"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'soapFormAttachment')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                    <input type="hidden" name="oldSoapFormAttachment"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'soapFormAttachment')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-6 col-6 mt-3">
                    <br>
                    <label>View Attachment</label>
                    <br>

                    @foreach ($formData as $item)
                        @if ($item->fieldName == 'soapFormAttachment')
                            <a href="/storage/Attachments/{{ trim($item->fieldValue) }}
                            "
                                target="_blank">Show Attachment</a>
                        @break
                    @endif
                @endforeach


            </div>

        </div>
        <hr>
        @role('Admin|Doctor')
            <input class="btn btn-primary" type="submit" value="SAVE" />
        @endrole
    </form>

</div>

<script>
    let allInput = document.getElementsByTagName("input");
    for (let index = 0; index < allInput.length; index++) {
        allInput[index].value = allInput[index].value.trim();
    }
    let allInput2 = document.getElementsByTagName("textarea");
    for (let index = 0; index < allInput2.length; index++) {
        allInput2[index].value = allInput2[index].value.trim();
    }
    $(function() {
        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd", // Format of the date
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0" // Allow selection of years from 100 years ago to the current year
        });
    });

    $(function() {
        $("#datepicker2").datepicker({
            dateFormat: "yy-mm-dd", // Format of the date
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0" // Allow selection of years from 100 years ago to the current year
        });
    });

    $(function() {
        $("#datepicker4").datepicker({
            dateFormat: "yy-mm-dd", // Format of the date
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0" // Allow selection of years from 100 years ago to the current year
        });
    });
</script>

<script>
    $(document).ready(function() {


        {{--  var apiUrl = "/patients/{{$data->id}}";  --}}

        $.ajax({
            type: "POST",
            url: apiUrl,
            success: function(response) {

                console.log("dataaaa:", response);
            },
            error: function(error) {

                console.error("Error dataaaaa:", error);
            }
        });
    });

    function addMedication() {
        // Get the table body
        var tbody = document.querySelector("table tbody");

        // Create a new table row
        var newRow = document.createElement("tr");

        // Create and append new td elements with inputs for each column
        newRow.innerHTML = `
        <td>
            <select name="medication[]" onchange="setMedicationId(this)">
                <option value="" disabled selected>Select Medication</option>
                @foreach ($medicines as $medicine)
                    <option value="{{ $medicine->name }}" data-id="{{ $medicine->id }}">
                        {{ $medicine->name }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="medication_id[]" class="medication-id" value=""/>
        </td>
        <td><input type="text" name="dosage[]"></td>
        <td><input type="text" name="frequency[]"></td>
        <td><input type="text" name="route[]"></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
    `;

        // Append the new row to the table body
        tbody.appendChild(newRow);
    }

    function addMedicationManualy() {
    // Get the table body
    var tbody = document.querySelector("table tbody");

    // Create a new table row
    var newRow = document.createElement("tr");

    // Create and append new td elements with inputs for each column and a delete button
    newRow.innerHTML = `
        <td><input type="text" name="medication[]" ></td>
        <td><input type="text" name="dosage[]"></td>
        <td><input type="text" name="frequency[]"></td>
        <td><input type="text" name="route[]"></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
    `;

    // Append the new row to the table body
    tbody.appendChild(newRow);
}

// Function to remove the row
function removeRow(button) {
    // Find the parent row (tr) and remove it
    var row = button.closest("tr");
    row.remove();
}

    // Function to set the hidden medication_id field
    function setMedicationId(selectElement) {
        // Get the selected option
        var selectedOption = selectElement.options[selectElement.selectedIndex];

        // Get the hidden input field that is a sibling of the select
        var hiddenInput = selectElement.nextElementSibling;

        // Set the value of the hidden input to the data-id attribute of the selected option
        hiddenInput.value = selectedOption.getAttribute('data-id');
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {})
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    });
</Script>
@endsection
