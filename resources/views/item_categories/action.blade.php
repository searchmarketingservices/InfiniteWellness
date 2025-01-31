@role('Admin|PharmacistAdmin')
<div class="d-flex align-items-center" style="padding-right: 15px !important;">
    <a href="javascript:void(0)" title="{{__('messages.common.edit') }}" data-id="{{ $row->id }}"
       class="editItemCategoryBtn btn px-1 text-primary fs-3 ps-0">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a href="javascript:void(0)" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
       class="deleteItemCategoryBtn btn px-1 text-danger fs-3 pe-0" wire:key="{{$row->id}}">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
@endrole
