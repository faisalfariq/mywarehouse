<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Welcome to MyWarehouse</title>
    <link rel="icon" href="{{ asset('img/logo-mywarehouse.svg') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('img/logo-mywarehouse.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: #f8fafc;
        }
        .welcome-hero {
            background: linear-gradient(rgba(103,119,239,0.7), rgba(174,181,237,0.7)), url('{{ asset('img/unsplash/login-bg.jpg') }}') center/cover no-repeat;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            position: relative;
        }
        .welcome-card {
            background: rgba(255,255,255,0.97);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            padding: 2.5rem 2rem;
            max-width: 600px;
            margin: 2rem auto;
            color: #222;
        }
        .welcome-illustration {
            max-width: 350px;
            width: 100%;
            margin-left: 2rem;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #6777ef;
            margin-bottom: 1rem;
        }
        @media (max-width: 991px) {
            .welcome-illustration { display: none; }
        }
    </style> 
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
</head>
<body> 
    <div class="welcome-hero">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-7">
                    <div class="welcome-card text-center text-lg-left">
                        <img src="{{ asset('img/logo-mywarehouse.svg') }}" alt="MyWarehouse Logo" style="width:80px; height:80px; margin-bottom:1.5rem;">
                        <h1 class="display-4 font-weight-bold mb-3 text-primary">Welcome to MyWarehouse</h1>
                        <p class="lead mb-4">A modern platform for managing your warehouse inventory, products, locations, and stock mutations. Streamline your warehouse operations with ease and efficiency.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary px-5 py-3 shadow"><i class="fas fa-warehouse"></i> Get Started</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <img src="{{ asset('img/drawkit/drawkit-full-stack-man-colour.svg') }}" alt="Warehouse Illustration" class="welcome-illustration">
                </div>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="row text-center justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-boxes-stacked"></i></div>
                        <h5 class="card-title font-weight-bold">Product Management</h5>
                        <p class="card-text">Easily manage all your products, categories, and units in one place.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-map-marker-alt"></i></div>
                        <h5 class="card-title font-weight-bold">Location Management</h5>
                        <p class="card-text">Organize and track your warehouse locations and stock levels efficiently.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-exchange-alt"></i></div>
                        <h5 class="card-title font-weight-bold">Stock Mutation</h5>
                        <p class="card-text">Record and monitor all stock movements and mutations with full traceability.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-chart-bar"></i></div>
                        <h5 class="card-title font-weight-bold">Reporting & Analytics</h5>
                        <p class="card-text">Get real-time insights and reports on your inventory and warehouse performance.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-user-shield"></i></div>
                        <h5 class="card-title font-weight-bold">Secure & Reliable</h5>
                        <p class="card-text">Your data is protected with enterprise-grade security and reliable cloud infrastructure.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="feature-icon mb-2"><i class="fas fa-users"></i></div>
                        <h5 class="card-title font-weight-bold">User Management</h5>
                        <p class="card-text">Control access and roles for your warehouse staff and managers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html> 