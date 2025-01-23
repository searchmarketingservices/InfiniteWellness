@extends('layouts.app')

@section('title', 'Edit Functional Medicine')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3>Edit Functional Medicine Record</h3>
                <a href="{{ route('functional-medicine.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <form action="{{ route('functional-medicine.update', $functionalMedicine->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mt-5 mb-5 row">
                        <div class="col-md-12 mt-5">
                            <label for="patient_id" class="form-label">Patient</label>
                            <select name="patient_id" id="patient_id" class="form-select">
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $functionalMedicine->patient_id == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user->first_name }} {{ $patient->user->last_name }} | {{ $patient->MR }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Add all the fields with default values -->
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="help" class="form-label">How Can I Help You?</label>
                            <input type="text" name="help" id="help" class="form-control" value="{{ old('help', $functionalMedicine->help ?? '') }}">
                            @error('help')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="life_line" class="form-label">Life Line</label>
                            <input type="text" name="life_line" id="life_line" class="form-control" value="{{ old('life_line', $functionalMedicine->life_line ?? '') }}">
                            @error('life_line')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="food" class="form-label">Food (Allergy/Sensitive/Intolerance)</label>
                            <input type="text" name="food" id="food" class="form-control" value="{{ old('food', $functionalMedicine->food ?? '') }}">
                            @error('food')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="intellectual" class="form-label">Intellectual</label>
                            <input type="text" name="intellectual" id="intellectual" class="form-control" value="{{ old('intellectual', $functionalMedicine->intellectual ?? '') }}">
                            @error('intellectual')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="job_work" class="form-label">Job / Work</label>
                            <input type="text" name="job_work" id="job_work" class="form-control" value="{{ old('job_work', $functionalMedicine->job_work ?? '') }}">
                            @error('job_work')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="leisure" class="form-label">Leisure</label>
                            <input type="text" name="leisure" id="leisure" class="form-control" value="{{ old('leisure', $functionalMedicine->leisure ?? '') }}">
                            @error('leisure')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="physical" class="form-label">Physical</label>
                            <input type="text" name="physical" id="physical" class="form-control" value="{{ old('physical', $functionalMedicine->physical ?? '') }}">
                            @error('physical')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="relationship" class="form-label">Relationship / Family Life</label>
                            <input type="text" name="relationship" id="relationship" class="form-control" value="{{ old('relationship', $functionalMedicine->relationship ?? '') }}">
                            @error('relationship')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social" class="form-label">Social</label>
                            <input type="text" name="social" id="social" class="form-control" value="{{ old('social', $functionalMedicine->social ?? '') }}">
                            @error('social')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="spritual" class="form-label">Spiritual</label>
                            <input type="text" name="spritual" id="spritual" class="form-control" value="{{ old('spritual', $functionalMedicine->spritual ?? '') }}">
                            @error('spritual')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="interpretation" class="form-label">Interpretation of Patient's History and Nutritional Assessment Form</label>
                            <textarea name="interpretation" id="interpretation" class="form-control">{{ old('interpretation', $functionalMedicine->interpretation ?? '') }}</textarea>
                            @error('interpretation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="examination" class="form-label">Examination (Head to Toe)</label>
                            <textarea name="examination" id="examination" class="form-control">{{ old('examination', $functionalMedicine->examination ?? '') }}</textarea>
                            @error('examination')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="investigation" class="form-label">Investigations / Lab Advised</label>
                            <textarea name="investigation" id="investigation" class="form-control">{{ old('investigation', $functionalMedicine->investigation ?? '') }}</textarea>
                            @error('investigation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="details" class="form-label">Functional Medicine Details</label>
                            <textarea name="details" id="details" class="form-control">{{ old('details', $functionalMedicine->details) }}</textarea>
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
                            <textarea name="nutrition" id="nutrition" class="form-control">{{ old('nutrition', $functionalMedicine->nutrition) }}</textarea>
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
                            <textarea name="aerobics" id="aerobics" class="form-control">{{ old('aerobics', $functionalMedicine->aerobics) }}</textarea>
                            @error('aerobics')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="balance" class="form-label">Balance and Flexibility</label>
                            <textarea name="balance" id="balance" class="form-control">{{ old('balance', $functionalMedicine->balance) }}</textarea>
                            @error('balance')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="strength" class="form-label">Strength Training</label>
                            <textarea name="strength" id="strength" class="form-control">{{ old('strength', $functionalMedicine->strength) }}</textarea>
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
                            <textarea name="schedule_sleep" id="schedule_sleep" class="form-control">{{ old('schedule_sleep', $functionalMedicine->schedule_sleep) }}</textarea>
                            @error('schedule_sleep')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="quality_sleep" class="form-label">Quality of Sleep</label>
                            <textarea name="quality_sleep" id="quality_sleep" class="form-control">{{ old('quality_sleep', $functionalMedicine->quality_sleep) }}</textarea>
                            @error('quality_sleep')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="enivronment_sleep" class="form-label">Sleep Enivronment</label>
                            <textarea name="enivronment_sleep" id="enivronment_sleep" class="form-control">{{ old('enivronment_sleep', $functionalMedicine->enivronment_sleep) }}</textarea>
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
                            <textarea name="attitude" id="attitude" class="form-control">{{ old('attitude', $functionalMedicine->attitude) }}</textarea>
                            @error('attitude')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="stress" class="form-label">Stress Management</label>
                            <textarea name="stress" id="stress" class="form-control">{{ old('stress', $functionalMedicine->stress) }}</textarea>
                            @error('stress')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social_connection" class="form-label">Social Connection</label>
                            <textarea name="social_connection" id="social_connection" class="form-control">{{ old('social_connection', $functionalMedicine->social_connection) }}</textarea>
                            @error('social_connection')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="seeking_help" class="form-label">Seeking Help</label>
                            <textarea name="seeking_help" id="seeking_help" class="form-control">{{ old('seeking_help', $functionalMedicine->seeking_help) }}</textarea>
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
                            <textarea name="alcohol" id="alcohol" class="form-control">{{ old('alcohol', $functionalMedicine->alcohol) }}</textarea>
                            @error('alcohol')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="smoking" class="form-label">Smoking</label>
                            <textarea name="smoking" id="smoking" class="form-control">{{ old('smoking', $functionalMedicine->smoking) }}</textarea>
                            @error('smoking')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="abuse" class="form-label">Substance Abuse</label>
                            <textarea name="abuse" id="abuse" class="form-control">{{ old('abuse', $functionalMedicine->abuse) }}</textarea>
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
                            <textarea name="clean" id="clean" class="form-control">{{ old('clean', $functionalMedicine->clean) }}</textarea>
                            @error('clean')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="safety" class="form-label">Safety Practices</label>
                            <textarea name="safety" id="safety" class="form-control">{{ old('safety', $functionalMedicine->safety) }}</textarea>
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
                            <textarea name="leisure_activities" id="leisure_activities" class="form-control">{{ old('leisure_activities', $functionalMedicine->leisure_activities) }}</textarea>
                            @error('leisure_activities')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="family" class="form-label">Family Activities</label>
                            <textarea name="family" id="family" class="form-control">{{ old('family', $functionalMedicine->family) }}</textarea>
                            @error('family')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="social_time" class="form-label">Social Time</label>
                            <textarea name="social_time" id="social_time" class="form-control">{{ old('social_time', $functionalMedicine->social_time) }}</textarea>
                            @error('social_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="time_management" class="form-label">Time Management</label>
                            <textarea name="time_management" id="time_management" class="form-control">{{ old('time_management', $functionalMedicine->time_management) }}</textarea>
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
                            <textarea name="intermittent" id="intermittent" class="form-control">{{ old('intermittent', $functionalMedicine->intermittent) }}</textarea>
                            @error('intermittent')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 mt-5">
                            <label for="essential_herbs" class="form-label">Essential Herbs</label>
                            <textarea name="essential_herbs" id="essential_herbs" class="form-control">{{ old('essential_herbs', $functionalMedicine->essential_herbs) }}</textarea>
                            @error('essential_herbs')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            CKEDITOR.replace('details');

        });
    </script>
@endsection
