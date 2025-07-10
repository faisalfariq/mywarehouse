@extends('layouts.app')

@section('title', 'Edit Location')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Location Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Locations</a></div>
                    <div class="breadcrumb-item">Edit Location</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Edit Location Form</h2>
                            </div>
                            <form action="{{ route('locations.update', $location->id) }}" id="form_edit_location" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Location Name</label>
                                            <input type="text" class="form-control" name="location_name" value="{{ old('location_name', $location->location_name) }}" required>
                                            <div class="invalid-feedback location_name_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Location Code</label>
                                            <input type="text" class="form-control" name="location_code" value="{{ old('location_code', $location->location_code) }}" required>
                                            <div class="invalid-feedback location_code_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-primary">Address</label>
                                            <input type="text" class="form-control" name="address" value="{{ old('address', $location->address) }}">
                                            <div class="invalid-feedback address_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-primary">Description</label>
                                            <textarea class="form-control" name="description">{{ old('description', $location->description) }}</textarea>
                                            <div class="invalid-feedback description_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a type="button" href="{{ route('locations.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('js/custom_js/pages/locations/locations_edit.js') }}"></script>
@endpush 