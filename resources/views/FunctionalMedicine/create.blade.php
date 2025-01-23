@extends('layouts.app')
@section('title')
    Functional Medicine
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="mb-0 text-center">Approach To Functional Medicine</h3>
                <a href="{{ route('functional-medicine.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <div class="row ms-5 ps-3">
                <h4>G- Great</h4>
                <h4>A- Ask</h4>
                <h4>T- Tell</h4>
                <h4>H- Help</h4>
                <h4>E- Explain</h4>
                <h4>R- Return</h4>
            </div>
            <form action="{{ route('functional-medicine.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="help" class="form-label">How Can i Help You?</label>
                            <textarea name="help" id="help" class="form-control" rows="1" cols="5"></textarea>
                            @error('help')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">

                            <h3 >Life Line</h3>
                            <p>Tell me about your life since birth, any problem/concern you have faced and how did you
                                address it?</p>
                            <label for="life_line" class="form-label">Environmental</label>
                            <textarea name="life_line" id="life_line" class="form-control" rows="1" cols="5"></textarea>
                            @error('life_line')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="food" class="form-label">Food (Allergy/Sensitive/Intolerance)</label>
                            <textarea name="food" id="food" class="form-control" rows="1" cols="5"></textarea>
                            @error('food')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="intellectual" class="form-label">Intellectual</label>
                            <textarea name="intellectual" id="intellectual" class="form-control" rows="1" cols="5"></textarea>
                            @error('intellectual')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="job_work" class="form-label">Job / Work</label>
                            <textarea name="job_work" id="job_work" class="form-control" rows="1" cols="5"></textarea>
                            @error('job_work')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="leisure" class="form-label">Leisure</label>
                            <textarea name="leisure" id="leisure" class="form-control" rows="1" cols="5"></textarea>
                            @error('leisure')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="physical" class="form-label">Physical</label>
                            <textarea name="physical" id="physical" class="form-control" rows="1" cols="5"></textarea>
                            @error('physical')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="relationship" class="form-label">Relationship / Family Life</label>
                            <textarea name="relationship" id="relationship" class="form-control" rows="1" cols="5"></textarea>
                            @error('relationship')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social" class="form-label">Social</label>
                            <textarea name="social" id="social" class="form-control" rows="1" cols="5"></textarea>
                            @error('social')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="spritual" class="form-label">Spritual</label>
                            <textarea name="spritual" id="spritual" class="form-control" rows="1" cols="5"></textarea>
                            @error('spritual')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="interpretation" class="form-label">Interpretation of Patients history and
                                Nutritional Assesment Form</label>
                            <textarea name="interpretation" id="interpretation" class="form-control" rows="1" cols="5"></textarea>
                            @error('interpretation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="examination" class="form-label">Examination (Head to Toe)</label>
                            <textarea name="examination" id="examination" class="form-control" rows="1" cols="5"></textarea>
                            @error('examination')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="investigation" class="form-label">Investigations / Lab advised</label>
                            <textarea name="investigation" id="investigation" class="form-control" rows="1" cols="5"></textarea>
                            @error('investigation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-5 mb-5 row">
                        <div class="col-md-12 mt-5">
                            <label for="patient_id" class="form-label">Patient</label>
                            <select name="patient_id" id="patient_id" class="form-select">
                                <option value="">Select patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->user->first_name }}
                                        {{ $patient->user->last_name }} |
                                        {{ $patient->MR }}</option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="details" class="form-label">Functional Medicine</label>
                            <textarea name="details" id="details" class="form-control"></textarea>
                            @error('details')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Priniciples of Repair in Functional Medicine</h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="nutrition" class="form-label">Nutrition</label>
                            <textarea name="nutrition" id="nutrition" class="form-control"></textarea>
                            @error('nutrition')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Ragular physical activity</h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="aerobics" class="form-label">Aerobics</label>
                            <textarea name="aerobics" id="aerobics" class="form-control"></textarea>
                            @error('aerobics')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="balance" class="form-label">Balance and Flexibility</label>
                            <textarea name="balance" id="balance" class="form-control"></textarea>
                            @error('balance')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="strength" class="form-label">Strength Training</label>
                            <textarea name="strength" id="strength" class="form-control"></textarea>
                            @error('strength')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Adequate sleep</h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="schedule_sleep" class="form-label">Consistent Sleep Schedule</label>
                            <textarea name="schedule_sleep" id="schedule_sleep" class="form-control"></textarea>
                            @error('schedule_sleep')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="quality_sleep" class="form-label">Quality of Sleep</label>
                            <textarea name="quality_sleep" id="quality_sleep" class="form-control"></textarea>
                            @error('quality_sleep')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="enivronment_sleep" class="form-label">Sleep Enivronment</label>
                            <textarea name="enivronment_sleep" id="enivronment_sleep" class="form-control"></textarea>
                            @error('enivronment_sleep')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Mental And Emotional Well Being </h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="attitude" class="form-label">Positive Attitude</label>
                            <textarea name="attitude" id="attitude" class="form-control"></textarea>
                            @error('attitude')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="stress" class="form-label">Stress Management</label>
                            <textarea name="stress" id="stress" class="form-control"></textarea>
                            @error('stress')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social_connection" class="form-label">Social Connection</label>
                            <textarea name="social_connection" id="social_connection" class="form-control"></textarea>
                            @error('social_connection')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="seeking_help" class="form-label">Seeking Help</label>
                            <textarea name="seeking_help" id="seeking_help" class="form-control"></textarea>
                            @error('seeking_help')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Avoid Harmful Habits </h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="alcohol" class="form-label">Alcohol</label>
                            <textarea name="alcohol" id="alcohol" class="form-control"></textarea>
                            @error('alcohol')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="smoking" class="form-label">Smoking</label>
                            <textarea name="smoking" id="smoking" class="form-control"></textarea>
                            @error('smoking')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="abuse" class="form-label">Substance Abuse</label>
                            <textarea name="abuse" id="abuse" class="form-control"></textarea>
                            @error('abuse')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Healthy Environment </h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="clean" class="form-label">Clean And Organize Living Space</label>
                            <textarea name="clean" id="clean" class="form-control"></textarea>
                            @error('clean')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="safety" class="form-label">Safety Practices</label>
                            <textarea name="safety" id="safety" class="form-control"></textarea>
                            @error('safety')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Work And Personal life Balance </h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="leisure_activities" class="form-label">Leisure Activities</label>
                            <textarea name="leisure_activities" id="leisure_activities" class="form-control"></textarea>
                            @error('leisure_activities')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="family" class="form-label">Family Activities</label>
                            <textarea name="family" id="family" class="form-control"></textarea>
                            @error('family')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social_time" class="form-label">Social Time</label>
                            <textarea name="social_time" id="social_time" class="form-control"></textarea>
                            @error('social_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="time_management" class="form-label">Time Management</label>
                            <textarea name="time_management" id="time_management" class="form-control"></textarea>
                            @error('time_management')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <h3 class="pt-5">Macro And Micro Nutrients</h3>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="intermittent" class="form-label">Intermittent Fasting</label>
                            <textarea name="intermittent" id="intermittent" class="form-control"></textarea>
                            @error('intermittent')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="essential_herbs" class="form-label">Essential Herbs</label>
                            <textarea name="essential_herbs" id="essential_herbs" class="form-control"></textarea>
                            @error('essential_herbs')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="submit" disabled class="btn btn-primary">Save</button>
                    </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        // document.addEventListener("DOMContentLoaded", function() {
        //         CKEDITOR.replace('details');
        //     });

        document.addEventListener("DOMContentLoaded", function() {
            // Replace the textarea with CKEditor
            CKEDITOR.replace('details');

            var submitBtn = document.getElementById('submit');
            var patientId = document.getElementById('patient_id');
            var details = document.getElementById('details');

            if (submitBtn && patientId && details) {

                function enableSubmitButton() {
                    if (patientId.value && CKEDITOR.instances.details.getData()) {
                        submitBtn.disabled = false;
                        console.log("Submit button enabled.");
                    } else {
                        submitBtn.disabled = true;
                    }
                }

                patientId.addEventListener('change', enableSubmitButton);
                CKEDITOR.instances.details.on('change', enableSubmitButton);

            } else {
                console.error("One or more elements not found.");
            }
        });
    </script>
@endsection
