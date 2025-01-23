<a  href="{{route('users.edit',$row->id)}}" title="{{ __('messages.common.edit')}} "
    class="me-1 ms-1 btn btn-sm btn-primary user-edit-btn" data-id="{{$row->id}}">
    <i class="bi bi-pencil"></i>
</a>

@if ($row->department->name != 'Admin')
<a href="javascript:void(0)" data-id="{{ $row->id }}" title="{{ __('messages.common.delete') }}" wire:key="{{$row->id}}"
   class="btn me-1 ms-1 btn-sm btn-danger user-delete-btn">
   <i class="bi bi-trash"></i>
</a>
@endif
