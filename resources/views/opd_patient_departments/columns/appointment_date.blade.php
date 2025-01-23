@if ($row->appointment_date === null)
    N/A
@endif
<div>

    <span>
        {{ \Carbon\Carbon::parse($row->appointment_date)->isoFormat('Do MMMM YYYY')}}
        {{ \Carbon\Carbon::parse($row->appointment_date)->format('h:i A')}}
    </span>

</div>
