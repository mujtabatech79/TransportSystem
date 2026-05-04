<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Vehicles - GoodsMover</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
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
        
        /* Sidebar */
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
            text-decoration: none;
            display: block;
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
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
            background: transparent;
        }
        
        /* Topbar */
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
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
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
            background: var(--success);
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
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            flex: 1;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Rating Stars */
        .rating-stars {
            display: inline-flex;
            gap: 2px;
            margin-bottom: 5px;
        }
        
        .rating-stars i {
            font-size: 14px;
        }
        
        .vehicle-rating {
            margin-top: 5px;
            margin-bottom: 10px;
        }
        
        .vehicle-rating .rating-stars i {
            font-size: 16px;
        }
        
        /* Review Card Styles */
        .review-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
        }
        
        .review-card:hover {
            background: #f1f3f5;
            transform: translateX(5px);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        
        .reviewer-name {
            font-weight: 600;
            color: var(--primary);
        }
        
        .review-date {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .review-rating {
            margin-bottom: 8px;
        }
        
        .review-text {
            color: #495057;
            line-height: 1.5;
            font-size: 0.9rem;
        }
        
        /* Rating Bar Styles */
        .rating-bar-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .rating-star-label {
            width: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .rating-progress-container {
            flex: 1;
            margin: 0 10px;
        }
        
        .rating-progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .rating-progress-fill {
            height: 100%;
            background-color: #ffc107;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .rating-count {
            width: 40px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Average Rating Display */
        .average-rating {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .average-rating-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .total-reviews {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Empty Reviews */
        .empty-reviews {
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-reviews i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        /* Reviews Section Title */
        .reviews-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary);
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
            max-width: 900px;
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
            grid-template-columns: 1fr;
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
        
        /* Reviews Container */
        .reviews-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .reviews-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .reviews-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .reviews-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
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
        
        /* Buttons */
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
        
        .btn-book {
            background: linear-gradient(135deg, var(--success), #219653);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            color: white;
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            color: white;
        }
        
        .btn-contact {
            background: linear-gradient(135deg, var(--info), #138496);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            color: white;
        }
        
        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
            color: white;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        /* Footer */
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
        }
        
        /* Notification Badge */
        .notification-badge {
            position: relative;
        }
        
        .notification-badge .badge-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            text-align: center;
            padding: 40px;
        }
        
        .loading-spinner i {
            font-size: 2rem;
            color: var(--secondary);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .bg-success {
            background-color: var(--success) !important;
        }
        
        .me-1 { margin-right: 0.25rem; }
        .me-2 { margin-right: 0.5rem; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .p-3 { padding: 1rem; }
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .d-flex { display: flex; }
        .d-inline { display: inline; }
        .d-none { display: none; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-center { justify-content: center; }
        .justify-content-end { justify-content: flex-end; }
        .flex-column { flex-direction: column; }
        .gap-2 { gap: 0.5rem; }
        .rounded-circle { border-radius: 50%; }
    </style>
</head>
<body>
   <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-truck-moving"></i> <span>Truck</span>Link</h3>
            <small class="text-muted">Customer Dashboard</small>
        </div>
        <div class="sidebar-content">
            <nav class="nav flex-column mt-4">
                <a class="nav-link" href="{{route('customer.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <a class="nav-link active" href="{{route('all.vehicle')}}">
                    <i class="fas fa-search"></i> <span>All vehicle</span>
                </a>
                <a class="nav-link " href="{{route('find.vehicle')}}">
                    <i class="fas fa-search"></i> <span>Find Vehicles</span>
                </a>
                <a class="nav-link" href="{{route('mybookings')}}">
                    <i class="fas fa-clipboard-list"></i> <span>My Bookings</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-money-bill-wave"></i> <span>Payments</span>
                </a>
                <a class="nav-link" href="{{route('mybookingss')}}">
                    <i class="fas fa-map-marked-alt"></i> <span>Track Shipment</span>
                </a>
                <a class="nav-link notification-badge" href="{{route('messages.conversations')}}">
                    <i class="fas fa-comments"></i> <span>Messages</span>
                    <span class="badge-count" id="unreadMessageBadge" style="display: none;">0</span>
                </a>
                <a class="nav-link" href="{{ route('customer.complaints') }}">
                    <i class="fas fa-exclamation-circle"></i> <span>Complaints</span>
                </a>
               
               <a class="nav-link" href="{{route('customer.analytics')}}"><i class="fas fa-chart-line"></i> <span>Analytics</span></a>
                <a class="nav-link" href="{{route('user.logout')}}">
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
                <input type="text" class="form-control" placeholder="Search vehicles, locations..." id="searchVehicles">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ session('name', 'Customer') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">All Available Vehicles</h4>
                    <p class="text-muted mb-0">Browse all verified available vehicles.</p>
                </div>
                <div>
                    <span class="badge bg-success fs-6">{{ count($availableVehicles) }} Available Vehicles</span>
                </div>
            </div>

            <!-- Search Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Search Vehicles by Type</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('all.vehicle') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="vehicle_type" class="form-label">Select Vehicle Type</label>
                                <select class="form-select" id="vehicle_type" name="vehicle_type">
                                    <option value="">-- All Vehicles --</option>
                                    <option value="truck" {{ request('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                                    <option value="dumper" {{ request('vehicle_type') == 'dumper' ? 'selected' : '' }}>Dumper</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i> Search Vehicle
                                </button>
                            </div>
                            
                            @if(request()->has('vehicle_type') && !empty(request()->vehicle_type))
                            <div class="col-12">
                                <a href="{{ route('all.vehicle') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Clear Filter
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                    
                    @if(request()->has('vehicle_type') && !empty(request()->vehicle_type))
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Showing vehicles of type: <strong>{{ ucfirst(request()->vehicle_type) }}</strong>
                            ({{ $availableVehicles->count() }} vehicles found)
                        </div>
                    @endif
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
                        <div class="vehicle-card" data-vehicle-id="{{ $vehicle->id }}" data-owner-id="{{ $vehicle->user_id }}">
                            <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}')">
                                <div class="vehicle-badge">is_booked {{ $vehicle->is_booked }}</div>
                            </div>
                            <div class="vehicle-info">
                                <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }} - {{ $vehicle->vehicle_number }}</div>
                                <div class="vehicle-rating" id="vehicleRating-{{ $vehicle->id }}">
                                    <div class="rating-stars">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <small class="text-muted rating-text">Loading reviews...</small>
                                </div>
                                <div class="vehicle-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Driver</span>
                                        <span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Driver Email</span>
                                        <span class="detail-value">{{ $vehicle->user->email ?? 'N/A' }}</span>
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
                                    <div class="detail-item">
                                        <span class="detail-label">Driver CNIC</span>
                                        <span class="detail-value">{{ $vehicle->user->cnic ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-detail" onclick="viewVehicleDetails({{ $vehicle->id }})">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                    <button class="btn btn-contact" onclick="startConversation({{ $vehicle->user_id }}, null)">
                                        <i class="fas fa-file-signature me-1"></i> Contract
                                    </button>
                                    <form action="{{ route('trip.form') }}" method="GET" class="d-inline w-100">
                                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                        <button type="submit" class="btn btn-book w-100">
                                            <i class="fas fa-bookmark me-1"></i> Book Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2023 GoodsMover: Verified Goods.</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Customer Panel v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>System Online</span></p>
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

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        // DOM elements
        const detailModal = document.getElementById('detailModal');
        const detailModalBody = document.getElementById('detailModalBody');
        const closeDetailModal = document.getElementById('closeDetailModal');

        // Store vehicle reviews cache
        const vehicleReviewsCache = {};

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            checkUnreadMessages();
            setInterval(checkUnreadMessages, 30000);
            
            // Load ratings for all vehicles on the page
            loadAllVehicleRatings();
        });

        // Load ratings for all vehicles
        function loadAllVehicleRatings() {
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            vehicleCards.forEach(card => {
                const vehicleId = card.dataset.vehicleId;
                if (vehicleId) {
                    loadVehicleRating(vehicleId);
                }
            });
        }

        // Load rating for a single vehicle
        function loadVehicleRating(vehicleId) {
            $.ajax({
                url: '/vehicle/' + vehicleId + '/reviews',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                success: function(response) {
                    if (response.success) {
                        updateVehicleRatingDisplay(vehicleId, response);
                        // Cache the reviews with vehicle data
                        vehicleReviewsCache[vehicleId] = response;
                    } else {
                        updateVehicleRatingError(vehicleId);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading rating for vehicle ' + vehicleId, xhr);
                    updateVehicleRatingError(vehicleId);
                }
            });
        }

        // Update vehicle rating display on the card
        function updateVehicleRatingDisplay(vehicleId, data) {
            const ratingContainer = document.getElementById(`vehicleRating-${vehicleId}`);
            if (!ratingContainer) return;
            
            const avgRating = data.average_rating || 0;
            const totalReviews = data.total_reviews || 0;
            
            let starsHtml = '';
            const fullStars = Math.floor(avgRating);
            const halfStar = (avgRating - fullStars) >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star text-warning"></i>';
            }
            if (halfStar) {
                starsHtml += '<i class="fas fa-star-half-alt text-warning"></i>';
            }
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star text-warning"></i>';
            }
            
            let ratingText = '';
            if (totalReviews === 0) {
                ratingText = 'No reviews yet';
            } else {
                ratingText = `${avgRating.toFixed(1)} (${totalReviews} review${totalReviews !== 1 ? 's' : ''})`;
            }
            
            ratingContainer.innerHTML = `
                <div class="rating-stars">${starsHtml}</div>
                <small class="text-muted rating-text">${ratingText}</small>
            `;
        }

        function updateVehicleRatingError(vehicleId) {
            const ratingContainer = document.getElementById(`vehicleRating-${vehicleId}`);
            if (ratingContainer) {
                ratingContainer.innerHTML = `
                    <div class="rating-stars">
                        <i class="far fa-star text-muted"></i>
                        <i class="far fa-star text-muted"></i>
                        <i class="far fa-star text-muted"></i>
                        <i class="far fa-star text-muted"></i>
                        <i class="far fa-star text-muted"></i>
                    </div>
                    <small class="text-muted rating-text">No ratings available</small>
                `;
            }
        }

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
            
            const searchInput = document.getElementById('searchVehicles');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    filterVehicles(this.value);
                });
            }
        }
        
        // Filter vehicles based on search input
        function filterVehicles(searchTerm) {
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            const searchLower = searchTerm.toLowerCase();
            
            vehicleCards.forEach(card => {
                const title = card.querySelector('.vehicle-title')?.innerText.toLowerCase() || '';
                const driverName = card.querySelector('.detail-value')?.innerText.toLowerCase() || '';
                const vehicleNumber = card.querySelector('.detail-item .detail-value')?.innerText.toLowerCase() || '';
                
                if (title.includes(searchLower) || driverName.includes(searchLower) || vehicleNumber.includes(searchLower)) {
                    card.closest('.col-xl-4').style.display = '';
                } else {
                    card.closest('.col-xl-4').style.display = 'none';
                }
            });
        }
        
        // Start conversation with vehicle owner (Contract button)
        function startConversation(ownerId, bookingId) {
            $.ajax({
                url: '{{ route("messages.start") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    vehicle_owner_id: ownerId,
                    booking_id: bookingId
                },
                success: function(response) {
                    toastr.success('Conversation started! Redirecting to messages...');
                    setTimeout(function() {
                        window.location.href = '{{ route("messages.conversations") }}';
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Please login first');
                        window.location.href = '{{ route("login") }}';
                    } else {
                        toastr.error('Failed to start conversation. Please try again.');
                    }
                }
            });
        }
        
        // Check for unread messages
        function checkUnreadMessages() {
            $.ajax({
                url: '{{ route("messages.unread") }}',
                method: 'GET',
                success: function(response) {
                    const badge = document.getElementById('unreadMessageBadge');
                    if (response.count > 0) {
                        badge.textContent = response.count > 9 ? '9+' : response.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
        }

        // Generate stars HTML for rating
        function getStarsHtml(rating) {
            const fullStars = Math.floor(rating);
            const halfStar = (rating - fullStars) >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            
            let html = '';
            for (let i = 0; i < fullStars; i++) {
                html += '<i class="fas fa-star text-warning"></i>';
            }
            if (halfStar) {
                html += '<i class="fas fa-star-half-alt text-warning"></i>';
            }
            for (let i = 0; i < emptyStars; i++) {
                html += '<i class="far fa-star text-warning"></i>';
            }
            return html;
        }

        // Generate rating distribution HTML
        function getRatingDistributionHtml(distribution, totalReviews) {
            if (totalReviews === 0) {
                return '<div class="text-center text-muted">No ratings yet</div>';
            }
            
            let html = '';
            for (let i = 5; i >= 1; i--) {
                const count = distribution[i] || 0;
                const percentage = totalReviews > 0 ? (count / totalReviews * 100) : 0;
                html += `
                    <div class="rating-bar-item">
                        <div class="rating-star-label">${i} <i class="fas fa-star text-warning"></i></div>
                        <div class="rating-progress-container">
                            <div class="rating-progress-bar">
                                <div class="rating-progress-fill" style="width: ${percentage}%;"></div>
                            </div>
                        </div>
                        <div class="rating-count">(${count})</div>
                    </div>
                `;
            }
            return html;
        }

        // Generate reviews HTML
        function getReviewsHtml(reviews) {
            if (!reviews || reviews.length === 0) {
                return `
                    <div class="empty-reviews">
                        <i class="fas fa-comment-dots"></i>
                        <p class="mb-0">No reviews yet for this vehicle.</p>
                        <small class="text-muted">Be the first to leave a review after your trip!</small>
                    </div>
                `;
            }
            
            let html = '';
            reviews.forEach(review => {
                html += `
                    <div class="review-card">
                        <div class="review-header">
                            <span class="reviewer-name">
                                <i class="fas fa-user-circle me-1"></i> ${escapeHtml(review.customer_name)}
                            </span>
                            <span class="review-date">
                                <i class="far fa-calendar-alt me-1"></i> ${review.created_at}
                            </span>
                        </div>
                        <div class="review-rating">
                            ${getStarsHtml(review.rating)}
                        </div>
                        <div class="review-text">
                            "${escapeHtml(review.review || 'No comment provided.')}"
                        </div>
                    </div>
                `;
            });
            return html;
        }

        // Simple escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Get vehicle image URL
        function getVehicleImageUrl(vehicleImage) {
            if (vehicleImage && vehicleImage !== 'null' && vehicleImage !== '') {
                return vehicleImage;
            }
            return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
        }

        // View vehicle details with reviews
        function viewVehicleDetails(vehicleId) {
            // Show loading state
            detailModalBody.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-pulse"></i>
                    <p class="mt-3">Loading vehicle details...</p>
                </div>
            `;
            detailModal.classList.add('active');
            
            // Check if we have cached reviews
            if (vehicleReviewsCache[vehicleId]) {
                renderVehicleDetailsModal(vehicleReviewsCache[vehicleId]);
            } else {
                // Fetch reviews and vehicle details
                $.ajax({
                    url: '/vehicle/' + vehicleId + '/reviews',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    success: function(response) {
                        if (response.success) {
                            vehicleReviewsCache[vehicleId] = response;
                            renderVehicleDetailsModal(response);
                        } else {
                            renderVehicleDetailsModal(null, response.message || 'Failed to load vehicle details');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to load vehicle details';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        renderVehicleDetailsModal(null, errorMsg);
                    }
                });
            }
        }

        // Render vehicle details modal with reviews
        function renderVehicleDetailsModal(data, errorMessage = null) {
            if (errorMessage || !data || !data.success) {
                detailModalBody.innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${escapeHtml(errorMessage || 'Failed to load vehicle details. Please try again.')}
                    </div>
                    <div class="text-center mt-3 mb-3">
                        <button class="btn btn-secondary" onclick="document.getElementById('detailModal').classList.remove('active')">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                    </div>
                `;
                return;
            }
            
            const vehicle = data.vehicle;
            const avgRating = data.average_rating || 0;
            const totalReviews = data.total_reviews || 0;
            const distribution = data.rating_distribution || {};
            const reviews = data.reviews || [];
            
            const vehicleImage = getVehicleImageUrl(vehicle.vehicle_image);
            
            let averageRatingDisplay = '';
            let ratingDistributionHtml = '';
            let reviewsHtml = '';
            
            if (totalReviews === 0) {
                averageRatingDisplay = `
                    <div class="average-rating">
                        <div class="average-rating-number">0.0</div>
                        <div class="rating-stars justify-content-center">${getStarsHtml(0)}</div>
                        <div class="total-reviews">No reviews yet</div>
                    </div>
                `;
                reviewsHtml = `
                    <div class="empty-reviews">
                        <i class="fas fa-comment-dots"></i>
                        <p class="mb-0">No reviews yet for this vehicle.</p>
                        <small class="text-muted">Be the first to leave a review after your trip!</small>
                    </div>
                `;
            } else {
                averageRatingDisplay = `
                    <div class="average-rating">
                        <div class="average-rating-number">${avgRating.toFixed(1)}</div>
                        <div class="rating-stars justify-content-center">${getStarsHtml(avgRating)}</div>
                        <div class="total-reviews">Based on ${totalReviews} review${totalReviews !== 1 ? 's' : ''}</div>
                    </div>
                `;
                
                ratingDistributionHtml = `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Rating Distribution</h6>
                            ${getRatingDistributionHtml(distribution, totalReviews)}
                        </div>
                    </div>
                `;
                
                reviewsHtml = `
                    <div class="reviews-container">
                        ${getReviewsHtml(reviews)}
                    </div>
                `;
            }
            
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
                            <span class="detail-label-large">Chassis Number</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.chassis_number || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Weight Capacity</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.weight_capacity || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Can Carry</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.can_carry || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Status</span>
                            <span class="detail-value-large"><span class="badge bg-success">${escapeHtml(vehicle.status || 'Available')}</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Service Provider Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large">
                            <span class="detail-label-large">Driver/Owner Name</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.owner_name || vehicle.user?.name || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Driver Email</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.user?.email || 'N/A')}</span>
                        </div>
                        <div class="detail-item-large">
                            <span class="detail-label-large">Driver CNIC</span>
                            <span class="detail-value-large">${escapeHtml(vehicle.user?.cnic || 'N/A')}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Customer Reviews & Ratings</h4>
                    ${averageRatingDisplay}
                    ${ratingDistributionHtml}
                    <div class="reviews-section-title">
                        <i class="fas fa-comments me-2"></i>What Customers Say
                    </div>
                    ${reviewsHtml}
                </div>
                
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle Image</h4>
                    <div class="detail-images">
                        <div class="detail-image-container">
                            <img src="${vehicleImage}" alt="Vehicle" class="detail-image" onerror="this.src='https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
                            <div class="detail-image-label">Vehicle Image</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button class="btn btn-contact" onclick="startConversationFromModal(${vehicle.id})">
                        <i class="fas fa-file-signature me-1"></i> Contract
                    </button>
                    <form action="{{ route('trip.form') }}" method="GET" class="d-inline">
                        <input type="hidden" name="vehicle_id" value="${vehicle.id}">
                        <button type="submit" class="btn btn-book">
                            <i class="fas fa-bookmark me-1"></i> Book This Vehicle
                        </button>
                    </form>
                    <button class="btn btn-cancel" onclick="document.getElementById('detailModal').classList.remove('active')">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            `;
        }
        
        function startConversationFromModal(vehicleId) {
            // Find the owner ID from the vehicle card
            const vehicleCard = document.querySelector(`.vehicle-card[data-vehicle-id="${vehicleId}"]`);
            if (vehicleCard) {
                const ownerId = vehicleCard.dataset.ownerId;
                if (ownerId) {
                    detailModal.classList.remove('active');
                    startConversation(parseInt(ownerId), null);
                    return;
                }
            }
            detailModal.classList.remove('active');
            toastr.info('Please click the Contract button on the vehicle card to start a conversation.');
        }
    </script>
</body>
</html>