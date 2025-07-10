@extends('layouts.app')

@section('title', 'Create App Log')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Data App Logs</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">App Logs</a></div>
                    <div class="breadcrumb-item">Create App Log</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">Form Create New App Log</h2>
                            </div>
                            <form action="{{ route('app-logs.store') }}" id="form_add_app_log" method="POST">
                                @csrf
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">User</label>
                                            <select class="form-control select2" name="user_id">
                                                <option value="">Pilih</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback user_id_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Action</label>
                                            <input type="text" class="form-control" name="action" value="{{ old('action') }}" required>
                                            <div class="invalid-feedback action_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">Module</label>
                                            <input type="text" class="form-control" name="module" value="{{ old('module') }}" required>
                                            <div class="invalid-feedback module_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-primary">IP Address</label>
                                            <input type="text" class="form-control" name="ip_address" value="{{ old('ip_address') }}">
                                            <div class="invalid-feedback ip_address_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a type="button" href="{{ route('app-logs.index') }}" class="btn btn-danger"><i class="fas fa-angle-left"></i> Back</a>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Save</button>
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
    <script src="{{ asset('js/custom_js/pages/app_logs/app_logs_create.js') }}"></script>
@endpush