<div class="d-flex align-items-center justify-content-center">
    @if ($row->payment_mode == 1)
        <span class="badge bg-light-success">{{ $row->payment_mode_name }}</span>
    @elseif ($row->payment_mode == 2)
        <span class="badge" style="color:#999999 ; background-color: #232631">{{ $row->payment_mode_name }}</span>
    @endif
</div>

