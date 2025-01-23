@extends('layouts.app2')
@section('title')
    {{ __('messages.patients') }}
@endsection
<style>
    .form-logo img {
        height: 5rem;
    }

    .forem {
        width: 100%;
    }

    .input_box label {
        text-wrap: nowrap;
    }

    .input_box {
        padding: 1rem 0;
    }

    .input_box input,
    .input_box textarea {
        border: 0;
        outline: 0;
        width: 100%;
        padding: 0rem 1rem;
        border-bottom: 1px #000 solid;
    }

    @media print {

        /* Hide everything except the specific section */
        body * {
            visibility: hidden;
        }

        section.container,
        section.container * {
            visibility: visible;
        }

        section.container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        /* Hide the SAVE button inside the form when printing */
        form .btn-primary {
            display: none;
        }
    }
</style>

<script>
    function printSection() {
        window.print();
    }
</script>
@section('content')
    <div class="container my-3">
        <button class="btn btn-primary" onclick="printSection()">Print</button>
        <section class="container p-5">
            <div class="d-flex flex-column gap-3 justify-content-center align-items-center">
                <header>
                    <div class="form-logo d-flex justify-content-center align-items-center">
                        <img src="http://infinitewellnesspk.com/wp-content/uploads/2024/02/infinite-.svg" alt="Image Here">
                    </div>
                    <p class="text-center mt-3">Plot No.35/135. CP & Berar Cooperative Housing Society, PECHS, Block 7/8,
                        Karachi East.</p>
                    <p class="text-center">0348-1349769</p>
                    <p class="text-center">0325-8331133</p>
                    <div class="text-center py-5">
                        <h1>MEDICAL CERTIFICATE</h1>
                        <h5>To whom it may concern</h5>
                    </div>
                </header>
                {{-- {{ dd($patientData) }} --}}
                <form class="forem" action="{{ request()->url() }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2 input_box">
                        <label for="dr">I, Dr.</label>
                        <input type="text" id="dr" name="drName"
                            @foreach ($formData as $item)
                        @if ($item->fieldName == 'drName')
                        value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}"
                        @break
                    @endif @endforeach>
                    </div>
                    <h5>Certify That</h5>
                    <div class="d-flex gap-2 input_box col-7">
                        <label for="pn">Patient's Name</label>
                        <input type="text" id="pn" name="patient_name"
                            @foreach ($formData as $item)
                    @if ($item->fieldName == 'patient_name')
                        value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : $patientData->user->first_name . ' ' . $patientData->user->last_name }}"
                        @break
                    @endif @endforeach
                            readonly>
                    </div>
                    <div class="d-flex gap-2 input_box col-3">
                        <label for="age">Age</label>
                        <input type="text" min="1" id="age" name="age"
                            @foreach ($formData as $item)
                                @if ($item->fieldName == 'age')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : $age }}"
                                    @break
                                @endif @endforeach
                            readonly>
                    </div>
                    <div class="d-flex gap-2 input_box col-3">
                        <label for="gen">Gender</label>
                        <input type="text" id="gen" name="gender"
                            @foreach ($formData as $item)
                            @if ($item->fieldName == 'gender') value="{{ trim($item->fieldValue) ?: ($patientData->user->gender == 0 ? 'Male' : ($patientData->user->gender == 1 ? 'Female' : 'Other')) }}"
                        @break @endif @endforeach
                            readonly>
                    </div>
                    <div class="d-flex gap-2 input_box">
                        <label for="dis">Is suffering from</label>
                        <textarea type="text" id="dis" rows="5" name="suffering_from">
@foreach ($formData as $item)
@if ($item->fieldName == 'suffering_from')
{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}
@break
@endif
@endforeach
</textarea>
                    </div>
                    <div class="d-flex gap-2 input_box">
                        <label for="advise">He/She is advised to refrain from work starting</label>
                        <input type="text" id="advise" name="advise"
                            @foreach ($formData as $item)
                        @if ($item->fieldName == 'advise')
                        value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}"
                        @break
                    @endif @endforeach>
                    </div>
                    <div class="d-flex gap-2 input_box col-3">
                        <label for="until">Until</label>
                        <input type="text" id="until" name="until"
                            @foreach ($formData as $item)
                        @if ($item->fieldName == 'until')
                        value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}"
                        @break
                    @endif @endforeach>
                    </div>
                    <div class="d-flex gap-2 input_box">
                        <label for="rem">Remarks</label>
                        <textarea type="text" id="rem" rows="3" name="remarks">
                            @foreach ($formData as $item)
@if ($item->fieldName == 'remarks')
{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}
@break
@endif
@endforeach
                        </textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="d-flex flex-column justify-content-center align-items-center gap-2 input_box">
                            <input type="date" id="date" name="date"
                                @foreach ($formData as $item)
                            @if ($item->fieldName == 'date')
                            value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}"
                            @break
                        @endif @endforeach>
                            <label for="date">(Date)</label>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center gap-2 input_box">
                            <input type="text" id="sig" name="signature"
                                @foreach ($formData as $item)
                            @if ($item->fieldName == 'signature')
                            value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : '' }}"
                            @break
                        @endif @endforeach>
                            <label for="sig">(Signature)</label>
                        </div>
                    </div>
                    @role('Admin|Doctor')
                        <input class="btn btn-primary mt-5" type="submit" value="SAVE">
                    @endrole
                </form>
            </div>
        </section>

    </div>

    <script>
        let allInput = document.getElementsByTagName("input");
        for (let index = 0; index < allInput.length; index++) {
            allInput[index].value = allInput[index].value.trim();
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
        //   });

        let allInput = document.getElementsByTagName("input");
        for (let index = 0; index < allInput.length; index++) {
            allInput[index].value = allInput[index].value.trim();
        }
        let allInput2 = document.getElementsByTagName("textarea");
        for (let index = 0; index < allInput2.length; index++) {
            allInput2[index].value = allInput2[index].value.trim();
        }
    </script>
@endsection
