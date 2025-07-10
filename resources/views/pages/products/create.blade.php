@extends('layouts.app')

@section('title', 'Create Product')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Create Product</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('products.index') }}">Product List</a></div>
                    <div class="breadcrumb-item">Create Product</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Create New Product</h2>
                            </div>
                            <div class="card-body">
                                <form id="createProductForm" method="POST" action="{{ route('products.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_name">Product Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_code">Product Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="product_code" name="product_code" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-primary">Category</label>
                                                <select class="form-control" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback category_id_error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-primary">Unit</label>
                                                <select class="form-control" name="unit_id" required>
                                                    <option value="">Select Unit</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback unit_id_error"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Locations and Stock</label>
                                        <div id="locationContainer">
                                            <div class="row location-row">
                                                <div class="col-md-5">
                                                    <select class="form-control location-select" name="location_ids[]">
                                                        <option value="">Select Location</option>
                                                        @foreach($locations as $location)
                                                            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" class="form-control" name="stock[]" placeholder="Stock" min="0" value="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-remove-location">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success" id="addLocation">Add Location</button>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Create Product</button>
                                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
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
    <script src="{{ asset('js/custom_js/pages/products/products_create.js') }}"></script>
@endpush 