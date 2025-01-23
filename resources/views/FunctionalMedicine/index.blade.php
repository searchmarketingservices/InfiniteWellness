@extends('layouts.app')
@section('title')
    Functional Medicine
@endsection
@section('content') 
<div class="container-fluid">
    <div class="d-flex flex-column">
        @include('flash::message')
        @role('Admin|Nurse|Receptionist')
        <div class="mb-5 col-md-12 text-end">
            <a href="{{ route('functional-medicine.create') }}" target="_blank"><button class="btn btn-primary">Add+</button></a>
        </div>
        @endrole
        <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#.</th>
                            <th>Paitent.</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($FunctionalMedicine as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->patient->user->first_name }} {{ $data->patient->user->last_name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('functional-medicine.show', $data->id) }}" target="_blank" class="me-1 ms-1 btn btn-sm btn-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('functional-medicine.edit', $data->id) }}" target="_blank" class="me-1 ms-1 btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('functional-medicine.destroy', $data->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn me-1 ms-1 btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $FunctionalMedicine->links() }}
            </div>
        </div>
    </div>
@endsection
