@if ($row->dose_given_date === null)
    N/A
@endif

    <div>
        {{ \Carbon\Carbon::parse($row->dose_given_date)->translatedFormat('jS M, Y')}}
        {{ \Carbon\Carbon::parse($row->dose_given_date)->format('h:i A')}}
    </div>


