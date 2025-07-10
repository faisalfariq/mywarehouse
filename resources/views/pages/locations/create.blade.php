@extends('layouts.app')

@section('title', 'Create Location')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Create Location</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('locations.index') }}">Location List</a></div>
                    <div class="breadcrumb-item">Create Location</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Create New Location</h2>
                            </div>
                            <div class="card-body">
                                <form id="createLocationForm" method="POST" action="{{ route('locations.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_code">Location Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="location_code" name="location_code" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_name">Location Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="location_name" name="location_name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Create Location</button>
                                        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
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
    <script src="{{ asset('js/custom_js/pages/locations/locations_create.js') }}"></script>
@endpush 