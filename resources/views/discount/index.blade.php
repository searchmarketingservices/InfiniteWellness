@extends('layouts.app')
@section('title')
    Discount
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            @role('Admin|Nurse|Receptionist')
                <div class="mb-5 col-md-12 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDiscountModal">Create</button>
                </div>
            @endrole

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif



            <div class="modal" tabindex="-1" id="addDiscountModal" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Create Discount</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close2">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('discount.store') }}" method="POST">
                            <div class="modal-body">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Discount Name
                                        <span class="required"></span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mt-3">
                                    <label for="amount" class="form-label">
                                        Discount Amount (in %)
                                        <span class="required"></span>
                                    </label>
                                    <input type="text" class="form-control" id="amount" name="amount_per" required>
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label for="active">
                                        Discount Status
                                        <span class="required"></span>
                                    </label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                                <button type="button" class="btn btn-secondary" id="close" data-dismiss="modal"
                                    aria-label="Close">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="modal" tabindex="-1" id="updateDiscountModal" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Discount</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close3">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="updateForm" action="" method="POST">
                            <div class="modal-body">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Discount Name
                                        <span class="required"></span>
                                    </label>
                                    <input type="text" class="form-control" id="nameUpdate" name="name" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mt-3">
                                    <label for="amount" class="form-label">
                                        Discount Amount (in %)
                                        <span class="required"></span>
                                    </label>
                                    <input type="text" class="form-control" id="amountUpdate" name="amount_per"
                                        required>
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label for="active">
                                        Discount Status
                                        <span class="required"></span>
                                    </label>
                                    <select name="active" id="activeUpdate" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                                <button type="button" class="btn btn-secondary" id="close4" data-dismiss="modal"
                                    aria-label="Close">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#.</th>
                            <th>Discount Name.</th>
                            <th>Discount Amount (in %).</th>
                            <th>Status.</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($discount as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->amount_per }} %</td>
                                <td>{{ $data->active == 1 ? 'Active' : 'Inactive' }}</td>
                                <td class="text-center">
                                    <button id="btnEdit" class="me-1 ms-1 btn btn-sm btn-primary edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('discount.destroy', $data->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn me-1 ms-1 btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<script>
    $(document).ready(function() {
        $('#active').select2();
        $('#close').click(function() {
            $('#addDiscountModal').modal('hide');
        });
        $('#close2').click(function() {
            $('#addDiscountModal').modal('hide');
        });
        $(".edit").click(function() {
            $('#updateDiscountModal').modal('show');
            var id = $(this).closest('tr').find('td:first').text();
            $('#updateForm').attr('action', "{{ route('discount.update', ':id') }}".replace(':id',
            id));
            var name = $(this).closest('tr').find('td:nth-child(2)').text();
            var amount = $(this).closest('tr').find('td:nth-child(3)').text();
            var active = $(this).closest('tr').find('td:nth-child(4)').text();
            var status = active == 'Active' ? 1 : 0;
            $('#nameUpdate').val(name);
            $('#amountUpdate').val(amount);
            $('#activeUpdate').val(status).trigger('change');
            $('#idUpdate').val(id);
        });
        $('#close3').click(function() {
            $('#updateDiscountModal').modal('hide');
        });
        $('#close4').click(function() {
            $('#updateDiscountModal').modal('hide');
        });
    });
</script>
