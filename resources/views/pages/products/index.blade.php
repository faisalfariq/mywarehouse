@extends('layouts.app')

@section('title', 'Product List')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Product Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Product List</a></div>
                    <div class="breadcrumb-item">Product Data</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Product List</h2>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="float-left">
                                    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
                                </div>
                                <div class="float-right">
                                    <form action="{{ route('products.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="clearfix mb-3"></div>
                                <div id="productTableList">
                                    <div class="table-responsive">
                                        <table class="table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                    <th>Unit</th>
                                                    <th>Total Stock</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="products_list">
                                                @foreach ($products as $product)
                                                    <tr>
                                                        <td>{{ $product->id }}</td>
                                                        <td>{{ $product->product_code }}</td>
                                                        <td>{{ $product->product_name }}</td>
                                                        <td>{{ $product->category->name ?? '-' }}</td>
                                                        <td>{{ $product->unit->name ?? '-' }}</td>
                                                        <td>
                                                            @php
                                                                $totalStock = $product->locations->sum('pivot.stock');
                                                            @endphp
                                                            {{ $totalStock }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">Detail</a>
                                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete-product" data-id="{{ $product->id }}">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right" id="pagination">
                                        {{ $products->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('js/custom_js/pages/products/products_index.js') }}"></script>
@endpush 