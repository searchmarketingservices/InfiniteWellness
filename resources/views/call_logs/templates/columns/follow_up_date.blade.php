<div class="d-flex align-items-center mt-2">
    @if ($row->follow_up_date === null)

        <div class="badge bg-light-danger">
            N/A
        </div>
    @else
        <div class="badge" style="background-color: rgba(153, 153, 153, 0.2); color :#999999;">
            <div>
                {{ \Carbon\Carbon::parse($row->follow_up_date)->translatedFormat('jS M,Y')}}
            </div>
        </div>
    @endif
</div>

