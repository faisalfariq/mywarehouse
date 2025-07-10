@extends('layouts.app')

@section('title', 'Edit Mutation')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Mutation Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Mutations</a></div>
                    <div class="breadcrumb-item">Edit Mutation</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Edit Mutation Form</h2>
                            </div>
                            <form action="{{ route('mutations.update', $mutation->id) }}" id="form_edit_mutation" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">User</label>
                                            <select class="form-control select2" name="user_id">
                                                <option value="">Select</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{ $mutation->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback user_id_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Product Location</label>
                                            <select class="form-control select2" name="product_location_id">
                                                <option value="">Select</option>
                                                @foreach ($productLocations as $pl)
                                                    <option value="{{ $pl->id }}" {{ $mutation->product_location_id == $pl->id ? 'selected' : '' }}>{{ $pl->product->product_name }} @ {{ $pl->location->location_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback product_location_id_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Mutation Type</label>
                                            <select class="form-control" name="mutation_type" required>
                                                <option value="">Select</option>
                                                <option value="in" {{ $mutation->mutation_type == 'in' ? 'selected' : '' }}>In</option>
                                                <option value="out" {{ $mutation->mutation_type == 'out' ? 'selected' : '' }}>Out</option>
                                            </select>
                                            <div class="invalid-feedback mutation_type_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Quantity</label>
                                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity', $mutation->quantity) }}" required>
                                            <div class="invalid-feedback quantity_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-primary">Description</label>
                                            <textarea class="form-control" name="description">{{ old('description', $mutation->description) }}</textarea>
                                            <div class="invalid-feedback description_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a type="button" href="{{ route('mutations.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
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
    <script src="{{ asset('js/custom_js/pages/mutations/mutations_edit.js') }}"></script>
@endpush 