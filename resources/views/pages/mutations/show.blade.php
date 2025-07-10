@extends('layouts.app')

@section('title', 'Mutation Details')

@push('style')
    <!-- CSS Libraries -->
    <style>
         .table:not(.table-sm):not(.table-md):not(.dataTable) td, .table:not(.table-sm):not(.table-md):not(.dataTable) th {
         padding: 0 25px;
         height: 35px;
         vertical-align: middle;
     }
     </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Mutation Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Mutations</a></div>
                    <div class="breadcrumb-item">Mutation Details</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Mutation Details</h2>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td width="200" class="text-primary font-weight-bold">
                                                        <i class="fas fa-id-card"></i> ID
                                                    </td>
                                                    <td width="50" class="text-center">:</td>
                                                    <td>{{ $mutation->id }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-user"></i> User
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $mutation->user->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-cube"></i> Product Location
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $mutation->productLocation->product->product_name ?? '-' }} @ {{ $mutation->productLocation->location->location_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-random"></i> Mutation Type
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ ucfirst($mutation->mutation_type) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-sort-numeric-up"></i> Quantity
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $mutation->quantity }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-align-left"></i> Description
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $mutation->description }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a type="button" href="{{ route('mutations.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
                                <a type="button" href="{{ route('mutations.edit', $mutation->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
@endpush 