@role('Admin|PharmacistAdmin')
<div style="display: flex;flex-direction: row; margin-right: 10px;align-items: center;">
    <a href="{{(url('medicines'.'/'. $row->id .'/edit'))}}" title="<?php echo __('messages.common.edit') ?>"
        class=" btn px-1 text-primary fs-3 ps-0">
                     <i class="fa-solid fa-pen-to-square"></i>
     </a>
     <a href="javascript:void(0)" title="<?php echo __('messages.common.delete') ?>" data-id="{{$row->id}}" wire:key="{{$row->id}}"
        class="deleteMedicineBtn  btn px-1 text-danger fs-3 ps-0">
                       <i class="fa-solid fa-trash"></i>
     </a>
     <a href="{{ route('medicines.products.history', $row->id) }}"
         target="_blank"
         aria-label="Detail"><i class="fa fa-history"></i></a>
</div>
@endrole

