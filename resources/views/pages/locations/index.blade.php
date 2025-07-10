@extends('layouts.app')

@section('title', 'Location List')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Location Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Location List</a></div>
                    <div class="breadcrumb-item">Location Data</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Location List</h2>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="float-left">
                                    <a href="{{ route('locations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
                                </div>
                                <div class="float-right">
                                    <form action="{{ route('locations.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="clearfix mb-3"></div>
                                <div id="locationTableList">
                                    <div class="table-responsive">
                                        <table class="table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Location Code</th>
                                                    <th>Location Name</th>
                                                    <th>Address</th>
                                                    <th>Product Count</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="locations_list">
                                                @foreach ($locations as $location)
                                                    <tr>
                                                        <td>{{ $location->id }}</td>
                                                        <td>{{ $location->location_code }}</td>
                                                        <td>{{ $location->location_name }}</td>
                                                        <td>{{ $location->address ?? '-' }}</td>
                                                        <td>{{ $location->products->count() }}</td>
                                                        <td>
                                                            <a href="{{ route('locations.show', $location->id) }}" class="btn btn-info btn-sm">Detail</a>
                                                            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete-location" data-id="{{ $location->id }}">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right" id="pagination">
                                        {{ $locations->links() }}
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
    <script src="{{ asset('js/custom_js/pages/locations/locations_index.js') }}"></script>
@endpush 