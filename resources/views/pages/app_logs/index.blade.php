@extends('layouts.app')

@section('title', 'App Log List')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 class="text-primary">Data App Logs</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="#">App Log List</a></div>
                    <div class="breadcrumb-item">Data App Logs</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="section-title text-primary m-0">App Log List</h2>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="float-left">
                                    <a href="{{ route('app-logs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
                                </div>
                                <div class="float-right">
                                    <form action="{{ route('app-logs.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="clearfix mb-3"></div>
                                <div id="appLogTableList">
                                    <div class="table-responsive">
                                        <table class="table-striped table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>User</th>
                                                    <th>Action</th>
                                                    <th>Module</th>
                                                    <th>IP Address</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="app_logs_list">
                                                @foreach ($logs as $log)
                                                    <tr>
                                                        <td>{{ $log->id }}</td>
                                                        <td>{{ $log->user->name ?? '-' }}</td>
                                                        <td>{{ $log->action }}</td>
                                                        <td>{{ $log->module }}</td>
                                                        <td>{{ $log->ip_address }}</td>
                                                        <td>
                                                            <a href="{{ route('app-logs.show', $log->id) }}" class="btn btn-info btn-sm">Detail</a>
                                                            <a href="{{ route('app-logs.edit', $log->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete-app-log" data-id="{{ $log->id }}">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right" id="pagination">
                                        {{ $logs->links() }}
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
    <script src="{{ asset('js/custom_js/pages/app_logs/app_logs_index.js') }}"></script>
@endpush