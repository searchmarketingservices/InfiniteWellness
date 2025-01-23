@if($row->document_url !== '')
    <a target="_blank" href="{{ url('expense-download').'/'.$row->id }}" class="text-decoration-none badge bg-light-info">Download</a>
@else
    <samp>{{ __('messages.common.n/a') }}</samp>
@endif

