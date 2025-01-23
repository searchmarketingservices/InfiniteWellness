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
                    <h2 class="text-center">Record date and time, Progress note, full signature and title</h2>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered border-dark">
                        <tr>
                            <td>Date</td>
                            <td>Time</td>
                            {{-- <td>Location</td> --}}
                            <td>Wt</td>
                            <td>Ht</td>
                            {{-- <td>FOC</td> --}}
                            <td>Temp</td>
                            <td>B.P</td>
                            <td>Pulse</td>
                            <td>Res</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="date" name="date" class="form-control" id="date"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'date')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                            </td>
                            <td>
                                <input type="time" name="time" class="form-control" id="time"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'time')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                            </td>
                            {{-- <td>
                                <input type="text" name="location" class="form-control" id="location"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'location')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                            </td> --}}
                            <td>
                                <input type="text" name="weight" class="form-control" id="weight"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'weight')
                                value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : (isset($nursingData) && $nursingData->weight ? $nursingData->weight : '') }}"

                                    @break
                                @endif @endforeach>
                            </td>
                            <td>
                                <input type="text" name="height" class="form-control" id="height"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'height')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) :(isset($nursingData) && $nursingData->height ? $nursingData->height : '') }}"
                                    @break
                                @endif @endforeach>
                            </td>
                            {{-- <td>
                                <input type="text" name="foc" class="form-control" id="foc"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'foc')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                            </td> --}}
                            <td>
                                <input type="text" name="temp" class="form-control" id="temp"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'temp')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : (isset($nursingData) && $nursingData->temperature ? $nursingData->temperature : '')  }}"
                                    @break
                                @endif @endforeach>
                            </td>
                            <td>
                                <input type="text" name="bp" class="form-control" id="bp"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'bp')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : (isset($nursingData) && $nursingData->blood_pressure ? $nursingData->blood_pressure : '') }}"
                                    @break
                                @endif @endforeach>
                            </td>
                            <td>
                                <input type="text" name="pulse" class="form-control" id="pulse"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'pulse')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) :  (isset($nursingData) && $nursingData->pulse ? $nursingData->pulse : '')}}"
                                    @break
                                @endif @endforeach>
                            </td>
                            <td>
                                <input type="text" name="res" class="form-control" id="res"
                                    placeholder="ENTER TEXT HERE"
                                    @foreach ($formData as $item)
                                @if ($item->fieldName == 'res')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : (isset($nursingData) && $nursingData->respiratory_rate ? $nursingData->respiratory_rate : '')}}"
                                    @break
                                @endif @endforeach>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3">
                    Drs Name
                    {{-- <input type="text" name="drsName" class="form-control" id="drsName" placeholder="ENTER TEXT HERE"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'drsName')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach> --}}
                    <select name="drsName" id="drsName" class="form-control">
                        @php
                            $docOldname = '';
                            foreach ($formData as $item) {
                                if ($item->fieldName == 'drsName') {
                                    $formData[0]->fieldValue = trim($item->fieldValue);
                                    $docOldname = trim($item->fieldValue);
                                    break;
                                }
                            }
                        @endphp

                        @foreach ($doctors as $dc)
                            @if ($dc->user)
                                <option value="{{ $dc->user->first_name . ' ' . $dc->user->last_name }}"
                                    class="form-control"
                                    {{ $dc->user->first_name . ' ' . $dc->user->last_name == $docOldname ? 'selected' : '' }}>
                                    {{ $dc->user->first_name . ' ' . $dc->user->last_name }}
                                </option>
                            @else
                                <option value="Unknown">Unknown</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    Pain Score
                    <input type="text" name="painScore" class="form-control" id="painScore" placeholder="ENTER TEXT HERE"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'painScore')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : (isset($nursingData) && $nursingData->pain_level ? $nursingData->pain_level : '') }}"
                                    @break
                                @endif @endforeach>
                </div>
                <div class="col-md-3">
                    Allergy
                    <input type="text" name="allergy" class="form-control" id="allergy" placeholder="ENTER TEXT HERE"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'allergy')
                                    value="{{ trim($item->fieldValue) }}"
                                    @break
                                @endif @endforeach>
                </div>
                {{-- <div class="col-md-3">

                    Sign
                    <input type="text" name="sign" class="form-control" id="sign" placeholder="ENTER TEXT HERE"
                        @foreach ($formData as $item)
                                @if ($item->fieldName == 'sign')
                                    value="{{ trim($item->fieldValue) ? trim($item->fieldValue) : $nursingData->signature }}"
                                    @break
                                @endif @endforeach>
                </div> --}}
            </div>


            <div class="row">
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-6 col-6">
                    <br>
                    <label for="exampleInput8">Attach File</label>
                    <input name="progressFormAttachment" type="file" class="form-control " id="exampleInput8"
                        @foreach ($formData as $item)
                            @if ($item->fieldName == 'progressFormAttachment')
                                value="{{ trim($item->fieldValue) }}"
                                @break
                            @endif @endforeach>
                    <input type="hidden" name="oldprogressFormAttachment"
                        @foreach ($formData as $item)
                            @if ($item->fieldName == 'progressFormAttachment')
                                value="{{ trim($item->fieldValue) }}"
                                @break
                            @endif @endforeach>
                </div>
                <div class="col-lx-6 col-lg-6 col-md-6 col-sm-6 col-6 mt-3">
                    <br>
                    <label>View Attachment</label>
                    <br>

                    @foreach ($formData as $item)
                        @if ($item->fieldName == 'progressFormAttachment')
                            <a href="/storage/Attachments/{{ trim($item->fieldValue) }}
                        "
                                target="_blank">Show Attachment</a>
                        @break
                    @endif
                @endforeach
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="desciption" class="form-label mt-5">Description</label>
                <textarea name="desciption" class="form-control" id="desciption" placeholder="ENTER TEXT HERE" rows="5" >
                    @foreach ($formData as $item)
                    @if ($item->fieldName == 'desciption')
                        {{ trim($item->fieldValue) }}
                        @break
                    @endif
                    @endforeach
                </textarea>
            </div>
        </div>
        <hr>
        @role('Admin|Doctor')
            <input class="btn btn-primary mt-5" type="submit" value="SAVE" />
        @endrole

    </form>

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
<!-- jQuery and CKEditor scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#desciption'))
            .then(editor => {
                console.log('Editor initialized successfully.');
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    });
</script>
@endsection
