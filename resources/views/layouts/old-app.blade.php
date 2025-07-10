<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Vehicle Tracker')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Vehicle Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Tambahkan menu navigasi sesuai kebutuhan -->
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('vehicles.index') }}">Kendaraan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('drivers.index') }}">Driver</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('bookings.index') }}">Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('regions.index') }}">Region</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">Role</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">User</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('settings.index') }}">Setting</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
