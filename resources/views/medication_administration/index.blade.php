@extends('layouts.app')
@section('title')
    Medication Administration List
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        @include('flash::message')
        @role('Admin|Nurse|Receptionist')
        <div class="col-md-12 mb-5 text-end">
            <a href="{{ route('medication.create') }}" target="_blank"><button class="btn btn-primary">Add Medication Administration Form</button></a>
        </div>
        @endrole
        <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Form No.</th>
                            <th>Paitent MR No.</th>
                            <th>Patient Name</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($medication as $m)
                            <tr>
                                <td>{{ $m->id }}</td>
                                <td>{{ $m->mr_number }}</td>
                                <td>{{ $m->patient->user->first_name }} {{ $m->patient->user->last_name }}</td>
                                <td class="d-flex justify-content-center gap-5">
                                    <a href="/medication-administration/{{$m->id}}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="5" class="text-danger">No Medication Administration stock</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div>
                    {{ $medication->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
