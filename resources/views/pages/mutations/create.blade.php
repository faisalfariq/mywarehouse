@extends('layouts.app')

@section('title', 'Create Mutation')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Create Mutation</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('mutations.index') }}">Mutation List</a></div>
                    <div class="breadcrumb-item">Create Mutation</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Create New Mutation</h2>
                            </div>
                            <div class="card-body">
                                <form id="createMutationForm" method="POST" action="{{ route('mutations.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id">User <span class="text-danger">*</span></label>
                                                <select class="form-control" id="user_id" name="user_id" required>
                                                    <option value="">Select User</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="date" name="date" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_id">Product <span class="text-danger">*</span></label>
                                                <select class="form-control" id="product_id" name="product_id" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->product_code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_id">Location <span class="text-danger">*</span></label>
                                                <select class="form-control" id="location_id" name="location_id" required>
                                                    <option value="">Select Location</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{ $location->id }}">{{ $location->location_name }} ({{ $location->location_code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mutation_type">Mutation Type <span class="text-danger">*</span></label>
                                                <select class="form-control" id="mutation_type" name="mutation_type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="in">In</option>
                                                    <option value="out">Out</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Create Mutation</button>
                                        <a href="{{ route('mutations.index') }}" class="btn btn-secondary">Cancel</a>
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
    <script src="{{ asset('js/custom_js/pages/mutations/mutations_create.js') }}"></script>
@endpush 