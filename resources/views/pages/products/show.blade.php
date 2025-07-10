@extends('layouts.app')

@section('title', 'Product Details')

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
                <h1 class="text-primary">Product Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Products</a></div>
                    <div class="breadcrumb-item">Product Details</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Product Details</h2>
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
                                                    <td>{{ $product->id }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-cube"></i> Product Name
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $product->product_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-barcode"></i> Product Code
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $product->product_code }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-tags"></i> Category
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $product->category }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-balance-scale"></i> Unit
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $product->unit }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-primary font-weight-bold">
                                                        <i class="fas fa-align-left"></i> Description
                                                    </td>
                                                    <td class="text-center">:</td>
                                                    <td>{{ $product->description }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a type="button" href="{{ route('products.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
                                <a type="button" href="{{ route('products.edit', $product->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
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