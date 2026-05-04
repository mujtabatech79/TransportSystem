<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Pending Vehicle Verification</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        
        /* Vehicle Cards */
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
            background: var(--warning);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
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
            color: #6c757d;
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
            color: #6c757d;
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
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #6c757d;
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
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
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
                <a class="nav-link active" href="{{route('admin.pendingVehicles')}}">
                    <i class="fas fa-truck"></i> <span>Vehicle Verification</span>
                </a>
                <a class="nav-link" href="{{route('admin.availableVehicles')}}">
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
                <input type="text" class="form-control" id="searchInput" placeholder="Search vehicles, owners, numbers...">
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
                    <h4 class="mb-1 fw-bold">Pending Vehicle Verification</h4>
                    <p class="text-muted mb-0">Review and verify vehicle registration requests from service providers.</p>
                </div>
                <div>
                    <span class="badge bg-warning fs-6">{{ count($pendingVehicles) }} Pending Vehicles</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <div class="count">{{ count($pendingVehicles) }}</div>
                            <div class="label">Pending Verification</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle"></i>
                            <div class="count">{{ $approvedCount ?? 0 }}</div>
                            <div class="label">Approved Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-danger-light">
                        <div class="card-body">
                            <i class="fas fa-times-circle"></i>
                            <div class="count">{{ $rejectedCount ?? 0 }}</div>
                            <div class="label">Rejected Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ $totalCount ?? 0 }}</div>
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
                @if($pendingVehicles->isEmpty())
                    <div class="col-12">
                        <div class="card empty-state">
                            <div class="card-body">
                                <i class="fas fa-truck"></i>
                                <h4>No Pending Vehicles</h4>
                                <p>All vehicles have been processed. Check back later for new submissions.</p>
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($pendingVehicles as $vehicle)
                    <div class="col-xl-4 col-lg-6 col-md-6 vehicle-item" data-id="{{ $vehicle->id }}">
                        <div class="vehicle-card">
                            <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}')">
                                <div class="vehicle-badge">Pending</div>
                            </div>
                            <div class="vehicle-info">
                                <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }} - {{ strtoupper($vehicle->vehicle_number) }}</div>
                                <div class="vehicle-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Owner</span>
                                        <span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Capacity</span>
                                        <span class="detail-value">{{ $vehicle->weight_capacity ?? 'N/A' }} kg</span>
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
                                    <button class="btn btn-success w-100" onclick="approveVehicle({{ $vehicle->id }})">
                                        <i class="fas fa-check-circle me-1"></i> Approve
                                    </button>
                                    <button class="btn btn-danger w-100" onclick="rejectVehicle({{ $vehicle->id }})">
                                        <i class="fas fa-times-circle me-1"></i> Reject
                                    </button>
                                    <button class="btn btn-detail w-100" onclick="viewVehicleDetails({{ $vehicle->id }})">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Pagination -->
            @if(method_exists($pendingVehicles, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $pendingVehicles->links() }}
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

    <!-- Reject Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Vehicle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rejectVehicleId">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" id="rejectionReason" rows="5" placeholder="Please provide a reason for rejection..."></textarea>
                        <div class="info-text mt-2 text-muted">
                            <i class="fas fa-info-circle me-1"></i> This reason will be emailed to the vehicle owner.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">
                        <i class="fas fa-times-circle me-2"></i> Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store vehicle data for AJAX - Properly encode JSON
        let vehiclesData = {!! json_encode($pendingVehicles->map(function($vehicle) {
            return [
                'id' => $vehicle->id,
                'vehicle_type' => $vehicle->vehicle_type,
                'vehicle_number' => $vehicle->vehicle_number,
                'weight_capacity' => $vehicle->weight_capacity,
                'can_carry' => $vehicle->can_carry,
                'chassis_number' => $vehicle->chassis_number,
                'vehicle_image' => $vehicle->vehicle_image,
                'smartcard_image' => $vehicle->smartcard_image,
                'status' => $vehicle->status,
                'created_at' => $vehicle->created_at ? $vehicle->created_at->toISOString() : null,
                'user' => $vehicle->user ? [
                    'name' => $vehicle->user->name,
                    'email' => $vehicle->user->email,
                    'cnic' => $vehicle->user->cnic,
                    'role' => $vehicle->user->role,
                    'email_verified' => $vehicle->user->email_verified
                ] : null
            ];
        })) !!};
        
        // DOM elements
        const detailModal = document.getElementById('detailModal');
        const detailModalBody = document.getElementById('detailModalBody');
        const closeDetailModal = document.getElementById('closeDetailModal');
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        
        // Setup event listeners
        document.addEventListener('DOMContentLoaded', function() {
            if(closeDetailModal) {
                closeDetailModal.addEventListener('click', function() {
                    detailModal.classList.remove('active');
                });
            }
            
            if(detailModal) {
                detailModal.addEventListener('click', function(e) {
                    if (e.target === detailModal) {
                        detailModal.classList.remove('active');
                    }
                });
            }
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    filterVehiclesBySearch(this.value);
                });
            }
            
            // Confirm reject button
            document.getElementById('confirmRejectBtn')?.addEventListener('click', confirmRejection);
        });
        
        // Filter vehicles by search
        function filterVehiclesBySearch(searchTerm) {
            const vehicleItems = document.querySelectorAll('.vehicle-item');
            let visibleCount = 0;
            
            vehicleItems.forEach(item => {
                const text = item.innerText.toLowerCase();
                if(text.includes(searchTerm.toLowerCase())) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Approve vehicle with AJAX
        async function approveVehicle(vehicleId) {
            const result = await Swal.fire({
                title: 'Approve Vehicle?',
                text: 'Are you sure you want to approve this vehicle? The owner will receive a confirmation email.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            });
            
            if(result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Approving vehicle and sending email...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                try {
                    const response = await fetch(`/admin/vehicle/${vehicleId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if(data.success) {
                        Swal.fire({
                            title: 'Vehicle Approved!',
                            html: `✅ <strong>Vehicle approved successfully!</strong><br><br>${data.message}<br><br>A confirmation email has been sent to the vehicle owner.`,
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Remove the vehicle card from the page
                            const vehicleCard = document.querySelector(`.vehicle-item[data-id="${vehicleId}"]`);
                            if(vehicleCard) {
                                vehicleCard.remove();
                            }
                            
                            // Update pending count
                            const pendingCount = document.querySelectorAll('.vehicle-item:visible').length;
                            const pendingCountSpan = document.getElementById('pendingCount');
                            if(pendingCountSpan) pendingCountSpan.textContent = pendingCount;
                            
                            // Update stat cards
                            const pendingStat = document.querySelector('.bg-warning-light .count');
                            const approvedStat = document.querySelector('.bg-success-light .count');
                            if(pendingStat) pendingStat.textContent = pendingCount;
                            if(approvedStat) approvedStat.textContent = parseInt(approvedStat.textContent) + 1;
                            
                            // Check if no vehicles left
                            if(pendingCount === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Failed to approve vehicle');
                    }
                } catch(error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        }
        
        // Reject vehicle - show reason modal
        function rejectVehicle(vehicleId) {
            document.getElementById('rejectVehicleId').value = vehicleId;
            document.getElementById('rejectionReason').value = '';
            rejectModal.show();
        }
        
        // Confirm rejection
        async function confirmRejection() {
            const vehicleId = document.getElementById('rejectVehicleId').value;
            const reason = document.getElementById('rejectionReason').value.trim();
            
            if(!reason) {
                Swal.fire({
                    title: 'Reason Required',
                    text: 'Please provide a reason for rejecting this vehicle.',
                    icon: 'warning',
                    confirmButtonColor: '#f39c12'
                });
                return;
            }
            
            rejectModal.hide();
            
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Rejecting vehicle and sending email...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await fetch(`/admin/vehicle/${vehicleId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rejection_reason: reason })
                });
                
                const data = await response.json();
                
                if(data.success) {
                    Swal.fire({
                        title: 'Vehicle Rejected!',
                        html: `⚠️ <strong>Vehicle rejected successfully!</strong><br><br>${data.message}<br><br>A rejection email has been sent to the vehicle owner with the reason.`,
                        icon: 'info',
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        // Remove the vehicle card from the page
                        const vehicleCard = document.querySelector(`.vehicle-item[data-id="${vehicleId}"]`);
                        if(vehicleCard) {
                            vehicleCard.remove();
                        }
                        
                        // Update pending count
                        const pendingCount = document.querySelectorAll('.vehicle-item:visible').length;
                        const pendingCountSpan = document.getElementById('pendingCount');
                        if(pendingCountSpan) pendingCountSpan.textContent = pendingCount;
                        
                        // Update stat cards
                        const pendingStat = document.querySelector('.bg-warning-light .count');
                        const rejectedStat = document.querySelector('.bg-danger-light .count');
                        if(pendingStat) pendingStat.textContent = pendingCount;
                        if(rejectedStat) rejectedStat.textContent = parseInt(rejectedStat.textContent) + 1;
                        
                        // Check if no vehicles left
                        if(pendingCount === 0) {
                            location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to reject vehicle');
                }
            } catch(error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
        
        // View vehicle details with complete service provider information
        function viewVehicleDetails(vehicleId) {
            // Find vehicle data
            const vehicle = vehiclesData.find(v => v.id == vehicleId);
            
            if(!vehicle) {
                Swal.fire('Error', 'Vehicle details not found', 'error');
                return;
            }
            
            // Debug: Log vehicle data to console to verify user information
            console.log('Vehicle Data:', vehicle);
            console.log('User Data:', vehicle.user);
            
            const vehicleImage = vehicle.vehicle_image ? 
                `{{ asset('uploads/vehicles') }}/${vehicle.vehicle_image}` : 
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500';
            
            const smartcardImage = vehicle.smartcard_image ? 
                `{{ asset('uploads/smartcards') }}/${vehicle.smartcard_image}` : 
                'https://images.unsplash.com/photo-1583752028088-91e3e9880b46?w=500';
            
            // Check if user data exists
            const hasUserData = vehicle.user && vehicle.user.name;
            
            // Populate modal with complete service provider information
            detailModalBody.innerHTML = `
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Vehicle Type</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.vehicle_type || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Vehicle Number</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.vehicle_number || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Weight Capacity</span>
                            <span class="detail-value-large">${vehicle.weight_capacity || 0} kg</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Can Carry</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.can_carry || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Chassis Number</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.chassis_number || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Status</span>
                            <span class="detail-value-large"><span class="badge bg-warning">Pending</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Service Provider Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Provider Name</span>
                            <span class="detail-value-large">
                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                ${escapeHtml(hasUserData ? vehicle.user.name : 'N/A')}
                            </span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Email Address</span>
                            <span class="detail-value-large">
                                <i class="fas fa-envelope me-2 text-info"></i>
                                ${escapeHtml(hasUserData && vehicle.user.email ? vehicle.user.email : 'N/A')}
                            </span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">CNIC Number</span>
                            <span class="detail-value-large">
                                <i class="fas fa-id-card me-2 text-warning"></i>
                                ${escapeHtml(hasUserData && vehicle.user.cnic ? vehicle.user.cnic : 'N/A')}
                            </span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Account Role</span>
                            <span class="detail-value-large">
                                <i class="fas fa-user-tag me-2 text-success"></i>
                                ${escapeHtml(hasUserData && vehicle.user.role ? vehicle.user.role : 'Service Provider')}
                            </span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Email Verification</span>
                            <span class="detail-value-large">
                                ${hasUserData && vehicle.user.email_verified ? 
                                    '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Verified</span>' : 
                                    '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>'}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle Images</h4>
                    <div class="detail-images">
                        <div class="detail-image-container">
                            <img src="${vehicleImage}" alt="Vehicle" class="detail-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                            <div class="detail-image-label">Vehicle Image</div>
                        </div>
                        <div class="detail-image-container">
                            <img src="${smartcardImage}" alt="Smartcard" class="detail-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                            <div class="detail-image-label">Smartcard Image</div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Additional Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Registration Date</span>
                            <span class="detail-value-large">${vehicle.created_at ? new Date(vehicle.created_at).toLocaleDateString() : 'N/A'}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Vehicle ID</span>
                            <span class="detail-value-large">#${vehicle.id}</span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button class="btn btn-success" onclick="approveVehicle(${vehicle.id}); detailModal.classList.remove('active');">
                        <i class="fas fa-check-circle me-1"></i> Approve Vehicle
                    </button>
                    <button class="btn btn-danger" onclick="rejectVehicle(${vehicle.id}); detailModal.classList.remove('active');">
                        <i class="fas fa-times-circle me-1"></i> Reject Vehicle
                    </button>
                </div>
            `;
            
            detailModal.classList.add('active');
        }
        
        // Filter functions
        function applyFilters() {
            const vehicleType = document.getElementById('vehicleType').value.toLowerCase();
            const capacity = document.getElementById('capacity').value;
            const sortBy = document.getElementById('sortBy').value;
            
            let filteredVehicles = Array.from(document.querySelectorAll('.vehicle-item'));
            
            if(vehicleType) {
                filteredVehicles = filteredVehicles.filter(item => 
                    item.innerText.toLowerCase().includes(vehicleType)
                );
            }
            
            if(capacity === '0-1000') {
                filteredVehicles = filteredVehicles.filter(item => {
                    const capText = item.innerText.match(/(\d+)\s*kg/);
                    if(capText) return parseInt(capText[1]) <= 1000;
                    return true;
                });
            } else if(capacity === '1000-5000') {
                filteredVehicles = filteredVehicles.filter(item => {
                    const capText = item.innerText.match(/(\d+)\s*kg/);
                    if(capText) {
                        const cap = parseInt(capText[1]);
                        return cap >= 1000 && cap <= 5000;
                    }
                    return true;
                });
            } else if(capacity === '5000+') {
                filteredVehicles = filteredVehicles.filter(item => {
                    const capText = item.innerText.match(/(\d+)\s*kg/);
                    if(capText) return parseInt(capText[1]) >= 5000;
                    return true;
                });
            }
            
            // Apply sorting
            if(sortBy === 'newest') {
                // Assuming newest first based on order in DOM
            } else if(sortBy === 'oldest') {
                filteredVehicles.reverse();
            } else if(sortBy === 'type') {
                filteredVehicles.sort((a, b) => {
                    const typeA = a.querySelector('.vehicle-title')?.innerText || '';
                    const typeB = b.querySelector('.vehicle-title')?.innerText || '';
                    return typeA.localeCompare(typeB);
                });
            }
            
            // Show/hide vehicles
            document.querySelectorAll('.vehicle-item').forEach(vehicle => vehicle.style.display = 'none');
            filteredVehicles.forEach(vehicle => vehicle.style.display = '');
            
            // Show message if no results
            const vehicleGrid = document.getElementById('vehicleGrid');
            let noResultsMsg = document.getElementById('noResultsMessage');
            
            if(filteredVehicles.length === 0) {
                if(!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'col-12';
                    vehicleGrid.appendChild(noResultsMsg);
                }
                noResultsMsg.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-filter"></i>
                        <h4>No vehicles match filters</h4>
                        <p>Try changing your filter criteria</p>
                        <button class="btn btn-primary mt-3" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i> Reset Filters
                        </button>
                    </div>
                `;
            } else if(noResultsMsg) {
                noResultsMsg.remove();
            }
        }
        
        function resetFilters() {
            document.getElementById('vehicleType').value = '';
            document.getElementById('capacity').value = '';
            document.getElementById('sortBy').value = 'newest';
            
            document.querySelectorAll('.vehicle-item').forEach(vehicle => vehicle.style.display = '');
            
            const noResultsMsg = document.getElementById('noResultsMessage');
            if(noResultsMsg) noResultsMsg.remove();
        }
        
        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            if(!unsafe) return '';
            return unsafe.toString().replace(/[&<>]/g, function(m) {
                if(m === '&') return '&amp;';
                if(m === '<') return '&lt;';
                if(m === '>') return '&gt;';
                return m;
            });
        }
    </script>
</body>
</html>