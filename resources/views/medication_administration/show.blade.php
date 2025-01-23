@extends('layouts.app3')
@section('title')
    Nursing From List
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3>Nursing Assessment Form</h3>
                <a href="/medication-administration" class="btn btn-secondary">Back</a>
            </div>
            <form action="{{route('medication.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mt-5 mb-5 row">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date</label>
                            <input class="form-control" value="{{$medication->date}}" disabled/>
                        </div>
                        <div class="col-md-6">
                            <label for="patient_mr_number" class="form-label">MR #</label>
                            <input class="form-control" value="{{$patients->MR}} - {{$patients->user->first_name}} {{$patients->user->last_name}}" disabled/>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-5 mt-5">
                                <label for="time" class="form-label">Time</label>
                                <input readonly type="text" name="time" value="{{$medication->time}}" id="time" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-5 mt-5">
                                <label for="patient_name" class="form-label">Patient Name</label>
                                <input readonly type="text" name="patient_name" value="{{$medication->patient_name}}" id="patient_name" disabled class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            <div class="card-body">
                <h4>VITAL SIGNS:</h4>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-5">
                                <label for="blood_pressure" class="form-label">Blood Pressure</label>
                                <input class="form-control" value="{{$medication->bp}}" disabled/>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mt-5">
                            mmHg
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-5">
                                <label for="heart_rate" class="form-label">Heart Rate</label>
                                <input class="form-control" value="{{$medication->heart_rate}}" disabled/>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mt-5">
                                bpm
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-5">
                                <label for="respiratory_rate" class="form-label">Respiratory Rate</label>
                                <input class="form-control" value="{{$medication->respiratory_rate}}" disabled/>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mt-5">
                                breaths/min
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="temperature" class="form-label">Temperature</label>
                                <input class="form-control" value="{{$medication->temperature}}" disabled/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mt-5">
                                °C/°F
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-5">
                                <label for="pbs" class="form-label">SpO2</label>
                                <input type="text" value="{{ $medication->spo_2 }}" name="spo_2" id="spo_2" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mt-5">
                                %
                            </div>
                        </div>

                    </div>

                </div>
                <div class="card-body">
                        <h4>Current Medications:</h4>

                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <td>Drug Name</td>
                                <td>Dose</td>
                                <td>Route </td>
                                <td>Staff Name</td>

                            </tr>
                        </thead>
                        <tbody id="current_medications_table" >
                            @foreach ($medication_details as $med)
                                <tr>
                                    <td><input class="form-control" value="{{$med->drug_name}}" disabled/></td>
                                    <td><input class="form-control" value="{{$med->dose}}" disabled/></td>
                                    <td><input class="form-control" value="{{$med->route}}" disabled/></td>
                                    <td><input class="form-control" value="{{$med->staff_name}}" disabled/></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-body">

                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="mb-5 mt-5">
                                <h2>Nursing Notes</h2>
                                {{-- <label for="details" class="form-label">Details</label> --}}
                                <p id="details">{!! $medication->note !!}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 mb-5 text-center row">
                        <div class="col-md-12">
                            <a href="/medication-administration" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    function deleteRowCMT(){
        var table = document.querySelector("#current_medications_table");
        var lastRow = table.lastElementChild;
        table.removeChild(lastRow);
    }

    function deleteRowAllergy(){
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

    function Addmore(){
        var tableRow = document.getElementById('current_medications_table');
            var a = tableRow.rows.length;
            console.log('AAA '+a);
            $('#current_medications_table').append(`
            <tr id="medicine-row${a}">
                                <td><input type="text" name="medications[${a}][medication_name]" class="form-control"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][dosage]"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][frequency]"></td>
                                <td><input type="text" class="form-control" name="medications[${a}][prescribing_physician]"></td>
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

        $.ajax({
            type: "get",
            url: "/nursing-form/opd/list",
            data: {
                patient_mr_number: $(this).val()
            },
            dataType: "json",
            success: function(response) {
                $("#opd_id").empty();
                console.log(response);
                if (response.data.length !== 0) {
                    $('#opd_id').append(
                        `<option selected disabled>Select OPD </option>`);
                    $.each(response.data, function(index, value) {
                        $("#opd_id").append(
                            `
                    <option value="${value.opd_number}" data-doctor="${value.doctor.user.full_name}"  data-patient="${value.patient.user.full_name}" >
                    ${value.opd_number}
                    </option>`
                        );
                    });
                }
                if (response.data2.length !== 0) {
                    $.each(response.data2, function(index, value2) {
                        console.log(value2);
                        $("#opd_id").append(
                            `
                    <option value="${value2.opd_number}" data-patient="${value2.patient.user.full_name}" >
                    ${value2.opd_number}
                    </option>`
                        );
                    });
                }
                else {
                    $("#opd_id").html(
                        `<option value="" class="text-danger" selected disabled>No any opd found!</option>`
                    );
                }
            }
        });
    });
});

    </script>

@endsection
