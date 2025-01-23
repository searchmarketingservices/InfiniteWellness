@extends('layouts.app')
@section('title')
    Functional Medicine Record
@endsection
<style>
    p  {
        margin-top: 1rem !important;
        margin-bottom: -0.1rem !important;
    }

    .card-body .two-text-align {
    display: flex;
    gap: 0px;
    flex-direction: column;
}

.card-body .two-text-align .details p {
    margin: 0 !important;!i;!;
}
</style>
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Functional Medicine Record</h3>
            </div>
            <div class="card-body">
                <p><strong>Patient:</strong>
                    {{ optional($functionalMedicine->patient->user)->first_name ?? '' }} |
                    {{ optional($functionalMedicine->patient)->MR ?? '' }}
                </p>
                <p><strong>How Can I Help You?:</strong>
                    {{ $functionalMedicine->help ?? '' }}
                </p>
                <p><strong>Life Line:</strong>
                    {{ $functionalMedicine->life_line ?? '' }}
                </p>
                <p><strong>Food (Allergy/Sensitive/Intolerance):</strong>
                    {{ $functionalMedicine->food ?? '' }}
                </p>
                <p><strong>Intellectual:</strong>
                    {{ $functionalMedicine->intellectual ?? '' }}
                </p>
                <p><strong>Job / Work:</strong>
                    {{ $functionalMedicine->job_work ?? '' }}
                </p>
                <p><strong>Leisure:</strong>
                    {{ $functionalMedicine->leisure ?? '' }}
                </p>
                <p><strong>Physical:</strong>
                    {{ $functionalMedicine->physical ?? '' }}
                </p>
                <p><strong>Relationship / Family Life:</strong>
                    {{ $functionalMedicine->relationship ?? '' }}
                </p>
                <p><strong>Social:</strong>
                    {{ $functionalMedicine->social ?? '' }}
                </p>
                <p><strong>Spiritual:</strong>
                    {{ $functionalMedicine->spritual ?? '' }}
                </p>
                <p><strong>Interpretation of Patient's History and Nutritional Assessment Form:</strong>
                    {{ $functionalMedicine->interpretation ?? '' }}
                </p>
                <p><strong>Examination (Head to Toe):</strong>
                    {{ $functionalMedicine->examination ?? '' }}
                </p>
                <p><strong>Investigations / Lab Advised:</strong>
                    {{ $functionalMedicine->investigation ?? '' }}
                </p>

                <div class="two-text-align">
                    <p><strong>Details:</strong></p>

                    <div class="details">{!! $functionalMedicine->details ?? '' !!}</div>
                </div>

                {{-- Additional fields --}}
                <p><strong>Nutrition:</strong></p>
                <div>{{ $functionalMedicine->nutrition ?? '' }}</div>
                <p><strong>Aerobics:</strong> </p>
                <div>{{ $functionalMedicine->aerobics ?? '' }}</div>

                <p><strong>Balance:</strong></p>
                <div>{{ $functionalMedicine->balance ?? '' }}</div>

                <p><strong>Strength:</strong></p>
                <div> {{ $functionalMedicine->strength ?? '' }}</div>

                <p><strong>Sleep Schedule:</strong></p>
                <div>{{ $functionalMedicine->schedule_sleep ?? '' }}</div>

                <p><strong>Quality of Sleep:</strong></p>
                <div>{{ $functionalMedicine->quality_sleep ?? '' }}</div>

                <p><strong>Sleep Environment:</strong></p>
                <div> {{ $functionalMedicine->enivronment_sleep ?? '' }}</div>
                <p><strong>Attitude:</strong> </p>
                <div> {{ $functionalMedicine->attitude ?? '' }}</div>

                <p><strong>Stress:</strong></p>
                <div> {{ $functionalMedicine->stress ?? '' }}</div>

                <p><strong>Social Connection:</strong></p>
                <div> {{ $functionalMedicine->social_connection ?? '' }}</div>

                <p><strong>Seeking Help:</strong></p>
                <div> {{ $functionalMedicine->seeking_help ?? '' }}</div>

                <p><strong>Alcohol:</strong></p>
                <div> {{ $functionalMedicine->alcohol ?? '' }}</div>

                <p><strong>Smoking:</strong></p>
                <div> {{ $functionalMedicine->smoking ?? '' }}</div>

                <p><strong>Substance Abuse:</strong></p>
                <div> {{ $functionalMedicine->abuse ?? '' }}</div>

                <p><strong>Clean And Organize Living Space:</strong></p>
                <div> {{ $functionalMedicine->clean ?? '' }}</div>

                <p><strong>Safety Practices:</strong></p>
                <div> {{ $functionalMedicine->safety ?? '' }}</div>

                <p><strong>Leisure Activities:</strong></p>
                <div> {{ $functionalMedicine->leisure_activities ?? '' }}</div>

                <p><strong>Family Activities:</strong></p>
                <div> {{ $functionalMedicine->family ?? '' }}</div>

                <p><strong>Social Time:</strong></p>
                <div> {{ $functionalMedicine->social_time ?? '' }}</div>

                <p><strong>Time Management:</strong></p>
                <div> {{ $functionalMedicine->time_management ?? '' }}</div>

                <p><strong>Intermittent Fasting:</strong></p>
                <div> {{ $functionalMedicine->intermittent ?? '' }}</div>

                <p><strong>Essential Herbs:</strong></p>
                <div> {{ $functionalMedicine->essential_herbs ?? '' }}</div>
            </div>
            <div class="card-footer">
                <a href="{{ route('functional-medicine.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection
