@extends('layouts.app')

@section('title', 'Dashboard')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 class="text-primary">Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-box"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Products</h4></div>
                        <div class="card-body">{{ $productCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Locations</h4></div>
                        <div class="card-body">{{ $locationCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-random"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Mutations</h4></div>
                        <div class="card-body">{{ $mutationCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Users</h4></div>
                        <div class="card-body">{{ $userCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Recent Mutations</h4></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Mutation Type</th>
                                        <th>Quantity</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentMutations as $mutation)
                                        <tr>
                                            <td>{{ $mutation->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $mutation->user->name ?? '-' }}</td>
                                            <td>{{ ucfirst($mutation->mutation_type) }}</td>
                                            <td>{{ $mutation->quantity }}</td>
                                            <td>{{ $mutation->description }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">No recent mutations.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
