@extends('layouts.app')

@section('title', 'Mutation List')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Mutation Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Mutation List</a></div>
                    <div class="breadcrumb-item">Mutation Data</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Mutation List</h2>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="float-left">
                                    <a href="{{ route('mutations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
                                </div>
                                <div class="float-right">
                                    <form action="{{ route('mutations.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="clearfix mb-3"></div>
                                <div id="mutationTableList">
                                    <div class="table-responsive">
                                        <table class="table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Date</th>
                                                    <th>User</th>
                                                    <th>Product</th>
                                                    <th>Location</th>
                                                    <th>Type</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mutations_list">
                                                @foreach ($mutations as $mutation)
                                                    <tr>
                                                        <td>{{ $mutation->id }}</td>
                                                        <td>{{ $mutation->date->format('d/m/Y') }}</td>
                                                        <td>{{ $mutation->user->name }}</td>
                                                        <td>{{ $mutation->product->product_name }}</td>
                                                        <td>{{ $mutation->location->location_name }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $mutation->mutation_type == 'in' ? 'success' : 'danger' }}">
                                                                {{ ucfirst($mutation->mutation_type) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $mutation->quantity }}</td>
                                                        <td>
                                                            <a href="{{ route('mutations.show', $mutation->id) }}" class="btn btn-info btn-sm">Detail</a>
                                                            <a href="{{ route('mutations.edit', $mutation->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete-mutation" data-id="{{ $mutation->id }}">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right" id="pagination">
                                        {{ $mutations->links() }}
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
    <script src="{{ asset('js/custom_js/pages/mutations/mutations_index.js') }}"></script>
@endpush 