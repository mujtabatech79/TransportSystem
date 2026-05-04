<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckLink - Available Vehicles</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            overflow-x: hidden;
        }
        
        /* Enhanced Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .logo {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            background: rgba(255,255,255,0.05);
        }
        
        .sidebar .logo h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar .logo span {
            color: var(--secondary);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            margin: 8px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--secondary);
            transform: translateX(-10px);
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link:hover:before {
            transform: translateX(0);
        }
        
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .sidebar .nav-link.active:before {
            transform: translateX(0);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Enhanced Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
            background: transparent;
        }
        
        /* Enhanced Topbar */
        .topbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .topbar .search-box {
            position: relative;
            max-width: 400px;
        }
        
        .topbar .search-box input {
            border-radius: 25px;
            padding-left: 45px;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.8);
            transition: all 0.3s ease;
        }
        
        .topbar .search-box input:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            border-color: var(--secondary);
        }
        
        .topbar .search-box i {
            position: absolute;
            left: 20px;
            top: 12px;
            color: #6c757d;
        }
        
        .topbar .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid var(--secondary);
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
        }
        
        /* Enhanced Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* Enhanced Stat Cards */
        .stat-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
        }
        
        .stat-card i {
            font-size: 2.8rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .stat-card .count {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0;
            background: linear-gradient(135deg, var(--dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card .label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Color Variants for Stat Cards */
        .bg-primary-light { background: linear-gradient(135deg, rgba(52, 152, 219, 0.08) 0%, rgba(52, 152, 219, 0.02) 100%); }
        .bg-success-light { background: linear-gradient(135deg, rgba(39, 174, 96, 0.08) 0%, rgba(39, 174, 96, 0.02) 100%); }
        .bg-warning-light { background: linear-gradient(135deg, rgba(243, 156, 18, 0.08) 0%, rgba(243, 156, 18, 0.02) 100%); }
        .bg-danger-light { background: linear-gradient(135deg, rgba(231, 76, 60, 0.08) 0%, rgba(231, 76, 60, 0.02) 100%); }
        
        /* NEW: Vehicle Cards */
        .vehicle-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background: white;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .vehicle-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .vehicle-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--success);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .vehicle-badge.inactive {
            background: var(--danger-red);
        }
        
        .vehicle-info {
            padding: 20px;
        }
        
        .vehicle-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .vehicle-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .action-buttons .btn {
            flex: 1;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Detail Modal */
        .detail-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .detail-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .detail-modal-content {
            background-color: white;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
            position: relative;
        }
        
        .detail-modal.active .detail-modal-content {
            transform: translateY(0);
        }
        
        .detail-modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            border-radius: 12px 12px 0 0;
        }
        
        .detail-modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .detail-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }
        
        .detail-modal-body {
            padding: 25px;
            overflow-y: auto;
            max-height: calc(90vh - 100px);
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .detail-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 8px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .detail-item-large {
            display: flex;
            flex-direction: column;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: none !important;
        }
        
        .detail-label-large {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .detail-value-large {
            font-weight: 600;
            font-size: 1rem;
            color: var(--dark);
        }
        
        .detail-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-image-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none !important;
        }
        
        .detail-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .detail-image-label {
            padding: 10px;
            text-align: center;
            background-color: var(--light);
            font-weight: 500;
            font-size: 0.9rem;
            border: none !important;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: var(--text-light);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--text-light);
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            border-color: var(--secondary);
        }
        
        /* Enhanced Buttons */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-detail {
            background: transparent;
            border: 2px solid var(--secondary);
            color: var(--secondary);
            transition: all 0.3s ease;
        }
        
        .btn-detail:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-filter {
            background: transparent;
            border: 2px solid var(--secondary);
            color: var(--secondary);
            transition: all 0.3s ease;
        }
        
        .btn-filter:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Enhanced Footer */
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                text-align: center;
            }
            
            .sidebar .logo h3 span, 
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link {
                padding: 15px;
                text-align: center;
                margin: 5px 10px;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content, .footer {
                margin-left: 80px;
            }
            
            .content-area {
                padding: 20px 15px;
            }
            
            .vehicle-details {
                grid-template-columns: 1fr;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .detail-images {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-truck-moving"></i> <span>Truck</span>Link</h3>
            <small class="text-muted">Admin Panel</small>
        </div>
        <div class="sidebar-content">
            <nav class="nav flex-column mt-4">
                <a class="nav-link " href="{{route('admin.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <a class="nav-link" href="{{route('admin.users')}}">
                    <i class="fas fa-users"></i> <span>Users Mangement</span>
                </a>
                <a class="nav-link" href="{{route('admin.pendingVehicles')}}">
                    <i class="fas fa-truck"></i> <span>Vehicle Verification</span>
                </a>
                <a class="nav-link active" href="{{route('admin.availableVehicles')}}">
                    <i class="fas fa-clipboard-check"></i> <span>Available Vehicles</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-money-bill-wave"></i> <span>Payments & Finance</span>
                </a>
                <a class="nav-link" href="{{route('admin.complaints')}}">
    <i class="fas fa-comments"></i> <span>Complaints Center</span>
  
</a>
                <a class="nav-link" href="{{route('admin.ratings-reviews')}}">
                    <i class="fas fa-star"></i> <span>Ratings & Reviews</span>
                </a>
                
                <a class="nav-link " href="{{ route('admin.ai-reviews') }}"><i class="fas fa-robot"></i><span>AI Reviews</span></a>
                <a class="nav-link" href="{{route('fraud.pendingVehicles')}}">
                    <i class="fas fa-bell"></i> <span>fraud detection</span>
                    
                </a>
                <a class="nav-link" href="{{route('admin.see-bookings')}}">
                    <i class="fas fa-star"></i> <span>Active Booking</span>
                </a>
                 <a class="nav-link" href="{{ route('user.logout') }}">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
            </nav>
        </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search vehicles, owners, numbers...">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/45" alt="Admin">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">Admin </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Available Vehicles</h4>
                    <p class="text-muted mb-0">Manage and monitor all available vehicles in the system.</p>
                </div>
                <div>
                    <span class="badge bg-success fs-6">{{ count($availableVehicles) }} Available Vehicles</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ count($availableVehicles) }}</div>
                            <div class="label">Available Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-road"></i>
                            <div class="count">{{ $onTripCount ?? 42 }}</div>
                            <div class="label">On Trip</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ $totalActive ?? 8 }}</div>
                            <div class="label">Active Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-clipboard-check"></i>
                            <div class="count">{{ $totalCount ?? 734 }}</div>
                            <div class="label">Total Vehicles</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Vehicles</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="vehicleType" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicleType">
                                <option value="">All Types</option>
                                <option value="truck">Truck</option>
                                <option value="van">Van</option>
                                <option value="pickup">Pickup</option>
                                <option value="trailer">Trailer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="capacity" class="form-label">Capacity (kg)</label>
                            <select class="form-select" id="capacity">
                                <option value="">Any Capacity</option>
                                <option value="0-1000">Up to 1000 kg</option>
                                <option value="1000-5000">1000 - 5000 kg</option>
                                <option value="5000+">5000+ kg</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="sortBy" class="form-label">Sort By</label>
                            <select class="form-select" id="sortBy">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="type">Vehicle Type</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-filter" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Grid -->
            <div class="row mt-4" id="vehicleGrid">
                @if($availableVehicles->isEmpty())
                    <div class="col-12">
                        <div class="card empty-state">
                            <div class="card-body">
                                <i class="fas fa-truck"></i>
                                <h4>No Available Vehicles</h4>
                                <p>All vehicles are currently on trips or under maintenance.</p>
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($availableVehicles as $vehicle)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="vehicle-card">
                            <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}')">
                                <div class="vehicle-badge {{ $vehicle->is_active ? '' : 'inactive' }}">
                                    {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                                </div>
                            </div>
                            <div class="vehicle-info">
                                <div class="vehicle-title">{{ $vehicle->vehicle_type }} - {{ $vehicle->vehicle_number }}</div>
                                <div class="vehicle-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Owner</span>
                                        <span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Capacity</span>
                                        <span class="detail-value">{{ $vehicle->weight_capacity ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Can Carry</span>
                                        <span class="detail-value">{{ $vehicle->can_carry ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Chassis No</span>
                                        <span class="detail-value">{{ $vehicle->chassis_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-detail w-100" onclick="viewVehicleDetails({{ $vehicle->id }})">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                    <form action="{{ route('vehicle.toggleActive', $vehicle->id) }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        @if($vehicle->is_active)
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-pause me-1"></i> Disable
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-play me-1"></i> Enable
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Simple Pagination (if using pagination) -->
            @if(isset($availableVehicles->links) && method_exists($availableVehicles, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $availableVehicles->links() }}
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2023 TruckLink: Verified Goods.</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Admin Panel v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>System Online</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3 class="detail-modal-title">Vehicle Details</h3>
                <button class="detail-modal-close" id="closeDetailModal">&times;</button>
            </div>
            <div class="detail-modal-body" id="detailModalBody">
                <!-- Detail content will be dynamically populated here -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // DOM elements
        const detailModal = document.getElementById('detailModal');
        const detailModalBody = document.getElementById('detailModalBody');
        const closeDetailModal = document.getElementById('closeDetailModal');

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            closeDetailModal.addEventListener('click', function() {
                detailModal.classList.remove('active');
            });
            
            detailModal.addEventListener('click', function(e) {
                if (e.target === detailModal) {
                    detailModal.classList.remove('active');
                }
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-box input');
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value;
                    if (searchTerm) {
                        // In a real application, you would send search request to backend
                        alert(`Searching for: ${searchTerm}`);
                        // window.location.href = `?search=${encodeURIComponent(searchTerm)}`;
                    }
                }
            });
        }

        // View vehicle details
        function viewVehicleDetails(vehicleId) {
            // In a real application, you would fetch the specific vehicle details via AJAX
            // For now, we'll use the data already available in the page
            
            // Find the vehicle data from the current page
            const vehicleElements = document.querySelectorAll('.vehicle-card');
            let vehicleData = null;
            
            vehicleElements.forEach(card => {
                const detailButton = card.querySelector('button[onclick*="' + vehicleId + '"]');
                if (detailButton) {
                    const title = card.querySelector('.vehicle-title').textContent;
                    const details = card.querySelectorAll('.detail-value');
                    const vehicleImage = card.querySelector('.vehicle-image').style.backgroundImage;
                    const badge = card.querySelector('.vehicle-badge');
                    
                    // Extract data from the card
                    const titleParts = title.split(' - ');
                    vehicleData = {
                        id: vehicleId,
                        vehicle_type: titleParts[0],
                        vehicle_number: titleParts[1],
                        weight_capacity: details[1].textContent,
                        can_carry: details[2].textContent,
                        chassis_number: details[3].textContent,
                        user: {
                            name: details[0].textContent,
                            // These will be populated from backend data
                            email: '{{ $vehicle->user->email ?? "N/A" }}',
                            cnic: '{{ $vehicle->user->cnic ?? "N/A" }}'
                        },
                        vehicle_image: vehicleImage.replace('url("', '').replace('")', ''),
                        smartcard_image: 'https://images.unsplash.com/photo-1583752028088-91e3e9880b46?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', // Default image
                        is_active: badge.classList.contains('inactive') ? false : true,
                        status: badge.textContent.toLowerCase()
                    };
                }
            });
            
            if (!vehicleData) {
                alert('Vehicle details not found');
                return;
            }
            
            // Populate the modal with vehicle details
            detailModalBody.innerHTML = `
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Vehicle Type</span>
                            <span class="detail-value-large">${vehicleData.vehicle_type}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Vehicle Number</span>
                            <span class="detail-value-large">${vehicleData.vehicle_number}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Weight Capacity</span>
                            <span class="detail-value-large">${vehicleData.weight_capacity}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Can Carry</span>
                            <span class="detail-value-large">${vehicleData.can_carry}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Chassis Number</span>
                            <span class="detail-value-large">${vehicleData.chassis_number}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Status</span>
                            <span class="detail-value-large"><span class="badge ${vehicleData.is_active ? 'bg-success' : 'bg-danger'}">${vehicleData.status}</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Service Provider Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Name</span>
                            <span class="detail-value-large">${vehicleData.user.name}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Email</span>
                            <span class="detail-value-large">${vehicleData.user.email}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">CNIC</span>
                            <span class="detail-value-large">${vehicleData.user.cnic}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle Images</h4>
                    <div class="detail-images">
                        <div class="detail-image-container">
                            <img src="${vehicleData.vehicle_image}" alt="Vehicle" class="detail-image">
                            <div class="detail-image-label">Vehicle Image</div>
                        </div>
                        <div class="detail-image-container">
                            <img src="${vehicleData.smartcard_image}" alt="Smartcard" class="detail-image">
                            <div class="detail-image-label">Smartcard Image</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <form action="/vehicle/toggle-active/${vehicleData.id}" method="POST" class="d-inline me-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn ${vehicleData.is_active ? 'btn-warning' : 'btn-success'}">
                            <i class="fas ${vehicleData.is_active ? 'fa-pause' : 'fa-play'} me-1"></i> ${vehicleData.is_active ? 'Disable' : 'Enable'} Vehicle
                        </button>
                    </form>
                    <button class="btn btn-secondary" onclick="detailModal.classList.remove('active')">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            `;
            
            detailModal.classList.add('active');
        }
        
        // Filter functions
        function filterByStatus(status) {
            alert(`Filtering by status: ${status}`);
            // In a real application, you would fetch vehicles with this status
        }
        
        function applyFilters() {
            const vehicleType = document.getElementById('vehicleType').value;
            const capacity = document.getElementById('capacity').value;
            const sortBy = document.getElementById('sortBy').value;
            
            // In a real application, you would send these filters to the backend
            alert(`Applying filters: Type=${vehicleType}, Capacity=${capacity}, Sort=${sortBy}`);
            
            // For demo purposes, we'll just reload the page with filter parameters
            // In a real app, you would use AJAX to fetch filtered results
            let url = new URL(window.location.href);
            if (vehicleType) url.searchParams.set('type', vehicleType);
            if (capacity) url.searchParams.set('capacity', capacity);
            if (sortBy) url.searchParams.set('sort', sortBy);
            
            window.location.href = url.toString();
        }
    </script>
</body>
</html>