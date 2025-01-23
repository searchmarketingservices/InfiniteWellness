<div class="d-flex align-items-center mt-2">
    @if ($row->call_type == App\Models\CallLog::INCOMING)
        <span class="badge bg-light-success fs-7">{{ __('messages.call_log.incoming') }}</span>
    @else
        <span class="badge bg-light-danger fs-7">{{ __('messages.call_log.outgoing') }}</span>
    @endif
</div>

