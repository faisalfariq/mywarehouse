@extends('layouts.app')

@section('title', 'User Detail')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">User Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">Users</a></div>
                    <div class="breadcrumb-item">User Detail</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">User Detail</h2>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-primary">Name</label>
                                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-primary">Email</label>
                                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-primary">Created At</label>
                                        <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-primary">Updated At</label>
                                        <input type="text" class="form-control" value="{{ $user->updated_at->format('d/m/Y H:i') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a type="button" href="{{ route('users.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection 