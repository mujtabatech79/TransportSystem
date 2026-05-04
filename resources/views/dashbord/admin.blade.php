<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            --purple: #9b59b6;
            --orange: #e67e22;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf2 100%);
            overflow-x: hidden;
        }
        
        /* Enhanced Sidebar - UNCHANGED */
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
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Enhanced Stat Cards - UNCHANGED */
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
        
        .bg-primary-light { background: linear-gradient(135deg, rgba(52, 152, 219, 0.08) 0%, rgba(52, 152, 219, 0.02) 100%); }
        .bg-success-light { background: linear-gradient(135deg, rgba(39, 174, 96, 0.08) 0%, rgba(39, 174, 96, 0.02) 100%); }
        .bg-warning-light { background: linear-gradient(135deg, rgba(243, 156, 18, 0.08) 0%, rgba(243, 156, 18, 0.02) 100%); }
        .bg-danger-light { background: linear-gradient(135deg, rgba(231, 76, 60, 0.08) 0%, rgba(231, 76, 60, 0.02) 100%); }
        .bg-info-light { background: linear-gradient(135deg, rgba(23, 162, 184, 0.08) 0%, rgba(23, 162, 184, 0.02) 100%); }
        
        /* Footer - UNCHANGED */
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
        }
        
        /* ========== NEW PROFESSIONAL UI COMPONENTS ========== */
        
        /* Recent Bookings - Modern Table Design */
        .recent-bookings-card {
            border-radius: 20px;
            overflow: hidden;
        }
        
        .recent-bookings-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 20px 25px;
        }
        
        .recent-bookings-card .card-header h5 {
            color: white;
        }
        
        .recent-bookings-card .card-header .btn-outline-light {
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .recent-bookings-card .card-header .btn-outline-light:hover {
            background: white;
            color: #667eea;
            border-color: white;
        }
        
        .booking-table {
            margin-bottom: 0;
        }
        
        .booking-table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 15px 12px;
        }
        
        .booking-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .booking-table tbody tr:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateX(5px);
        }
        
        .booking-table tbody td {
            padding: 16px 12px;
            vertical-align: middle;
            color: #555;
            font-size: 0.9rem;
        }
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .customer-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .customer-details {
            flex: 1;
        }
        
        .customer-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }
        
        .customer-email {
            font-size: 0.75rem;
            color: #888;
        }
        
        .vehicle-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f0f0f0;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .vehicle-badge i {
            color: var(--secondary);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge i {
            font-size: 0.75rem;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }
        
        .status-active {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        /* Pagination Controls */
        .booking-pagination {
            background: white;
            padding: 15px 20px;
            border-top: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pagination-buttons {
            display: flex;
            gap: 10px;
        }
        
        .page-btn {
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .page-btn-prev {
            background: #f0f0f0;
            color: #666;
            border: 1px solid #e0e0e0;
        }
        
        .page-btn-next {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
        }
        
        .page-btn-next:hover:not(:disabled) {
            transform: translateX(3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .page-btn-prev:hover:not(:disabled) {
            transform: translateX(-3px);
            background: #e8e8e8;
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .page-info {
            font-size: 0.85rem;
            color: #888;
        }
        
        /* Pending Vehicles Card - Modern Design */
        .pending-vehicles-card {
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
        }
        
        .pending-vehicles-card .card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-bottom: none;
        }
        
        .pending-vehicles-card .card-header h5 {
            color: white;
        }
        
        .pending-vehicles-card .card-header .badge {
            background: rgba(255,255,255,0.3);
            color: white;
            font-size: 0.9rem;
            padding: 5px 12px;
        }
        
        .pending-vehicles-list {
            max-height: 380px;
            overflow-y: auto;
        }
        
        .pending-vehicles-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .pending-vehicles-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .pending-vehicles-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            border-radius: 10px;
        }
        
        .vehicle-card {
            background: white;
            margin: 12px;
            padding: 15px;
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .vehicle-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            transform: translateX(-4px);
            transition: transform 0.3s ease;
        }
        
        .vehicle-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: transparent;
        }
        
        .vehicle-card:hover::before {
            transform: translateX(0);
        }
        
        .vehicle-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(240, 147, 251, 0.1), rgba(245, 87, 108, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #f5576c;
        }
        
        .vehicle-number {
            font-weight: 700;
            font-size: 1rem;
            color: #333;
            margin-bottom: 4px;
        }
        
        .vehicle-type {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 5px;
        }
        
        .vehicle-owner {
            font-size: 0.75rem;
            color: #aaa;
        }
        
        .status-pending-badge {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        /* Recent Complaints Card - Modern Design */
        .complaints-card {
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
        }
        
        .complaints-card .card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-bottom: none;
        }
        
        .complaints-card .card-header h5 {
            color: white;
        }
        
        .complaints-card .card-header .badge {
            background: rgba(255,255,255,0.3);
            color: white;
            font-size: 0.9rem;
            padding: 5px 12px;
        }
        
        .complaints-list {
            max-height: 380px;
            overflow-y: auto;
        }
        
        .complaints-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .complaints-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .complaints-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            border-radius: 10px;
        }
        
        .complaint-card {
            background: white;
            margin: 12px;
            padding: 15px;
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .complaint-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 50px 50px 0;
            border-color: transparent #4facfe transparent transparent;
            opacity: 0.1;
        }
        
        .complaint-card:hover {
            transform: translateX(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: transparent;
        }
        
        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .complaint-subject {
            font-weight: 700;
            color: #333;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }
        
        .complaint-type {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .type-high {
            background: #fee;
            color: #e74c3c;
        }
        
        .type-medium {
            background: #fff3cd;
            color: #f39c12;
        }
        
        .type-low {
            background: #d4edda;
            color: #27ae60;
        }
        
        .complaint-description {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .complaint-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.7rem;
            color: #999;
        }
        
        .complaint-meta i {
            margin-right: 4px;
        }
        
        .priority-flag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
        }
        
        .priority-high {
            color: #e74c3c;
        }
        
        .priority-medium {
            color: #f39c12;
        }
        
        .priority-low {
            color: #27ae60;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: #999;
            font-size: 0.9rem;
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
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card, .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Sidebar (UNCHANGED) -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-truck-moving"></i> <span>Truck</span>Link</h3>
            <small class="text-muted">Admin Panel</small>
        </div>
        <div class="sidebar-content">
            <nav class="nav flex-column mt-4">
                <a class="nav-link active" href="{{route('admin.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <a class="nav-link" href="{{route('admin.users')}}">
                    <i class="fas fa-users"></i> <span>Users Management</span>
                </a>
                <a class="nav-link" href="{{route('admin.pendingVehicles')}}">
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
                <a class="nav-link" href="{{ route('admin.ai-reviews') }}">
                    <i class="fas fa-robot"></i><span>AI Reviews</span>
                </a>
                <a class="nav-link" href="{{route('fraud.pendingVehicles')}}">
                    <i class="fas fa-bell"></i> <span>Fraud Detection</span>
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
        <!-- Topbar (UNCHANGED) -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search users, vehicles, bookings...">
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
                        <li><a class="dropdown-item text-danger" href="{{route('user.logout')}}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Admin Dashboard</h4>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
                </div>
                <div>
                    <button class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add New</button>
                </div>
            </div>

            <!-- Stats Cards (UNCHANGED) -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <div class="count">{{ $totalUsers }}</div>
                            <div class="label">Total Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ $verifiedVehicles }}</div>
                            <div class="label">Verified Vehicles</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-info-light">
                        <div class="card-body">
                            <i class="fas fa-truck-loading"></i>
                            <div class="count" id="pendingVehiclesCount">{{ $pendingVehiclesCount ?? 0 }}</div>
                            <div class="label">Pending Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-danger-light">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="count" id="pendingComplaintsCount">{{ $pendingComplaints ?? 0 }}</div>
                            <div class="label">Pending Complaints</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings - Professional Redesign -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card recent-bookings-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Recent Bookings</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-light page-btn-prev" id="prevPageBtn" disabled>
                                    <i class="fas fa-chevron-left me-1"></i> Previous
                                </button>
                                <button class="btn btn-sm btn-outline-light ms-2 page-btn-next" id="nextPageBtn">
                                    Next <i class="fas fa-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table booking-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Vehicle</th>
                                            <th>Vehicle Number</th>
                                            <th>Booking Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentBookingsTable">
                                        @foreach($vehicles as $vehicle)
                                        <tr>
                                            <td>
                                                <div class="customer-info">
                                                    <div class="customer-avatar">
                                                        {{ substr($vehicle->customer->name ?? 'U', 0, 1) }}
                                                    </div>
                                                    <div class="customer-details">
                                                        <div class="customer-name">{{ $vehicle->customer->name ?? 'N/A' }}</div>
                                                        <div class="customer-email">{{ $vehicle->customer->email ?? 'No email' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="vehicle-badge">
                                                    <i class="fas fa-truck"></i>
                                                    <span>{{ $vehicle->vehicle->vehicle_type ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td><strong>{{ $vehicle->vehicle->vehicle_number ?? 'N/A' }}</strong></td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($vehicle->booking_date)->format('M d, Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = $vehicle->status == 'completed' ? 'status-completed' : ($vehicle->status == 'pending' ? 'status-pending' : 'status-active');
                                                    $statusIcon = $vehicle->status == 'completed' ? 'fa-check-circle' : ($vehicle->status == 'pending' ? 'fa-clock' : 'fa-play-circle');
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    <i class="fas {{ $statusIcon }}"></i>
                                                    {{ ucfirst($vehicle->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-view" onclick="viewBookingDetails({{ $vehicle->id }})">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="booking-pagination">
                            <div class="page-info">
                                <i class="fas fa-chart-line me-1"></i>
                                Showing <span id="startRange">1</span> to <span id="endRange">{{ min(5, $vehicles->count()) }}</span> of <span id="totalBookings">{{ $vehicles->count() }}</span> bookings
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Vehicles and Recent Complaints Row -->
            <div class="row mt-4">
                <!-- Pending Vehicles Card - Redesigned -->
                <div class="col-lg-6">
                    <div class="card pending-vehicles-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Pending Verification</h5>
                            <span class="badge" id="pendingVehiclesBadge">{{ $pendingVehiclesCount ?? 0 }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="pending-vehicles-list" id="pendingVehiclesList">
                                @if(isset($pendingVehicles) && $pendingVehicles->count() > 0)
                                    @foreach($pendingVehicles as $vehicle)
                                    <div class="vehicle-card" onclick="viewVehicleDetails({{ $vehicle->id }})">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="vehicle-icon">
                                                <i class="fas fa-truck"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="vehicle-number">{{ $vehicle->vehicle_number }}</div>
                                                        <div class="vehicle-type">{{ $vehicle->vehicle_type }}</div>
                                                        <div class="vehicle-owner">
                                                            <i class="fas fa-user me-1"></i>{{ $vehicle->user->name ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="status-pending-badge">
                                                            <i class="fas fa-hourglass-half me-1"></i>Pending
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-weight-hanging me-1"></i>Capacity: {{ $vehicle->weight_capacity }}
                                                        <i class="fas fa-boxes ms-2 me-1"></i>Carry: {{ $vehicle->can_carry }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle"></i>
                                        <p>No pending vehicles</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Complaints Card - Redesigned -->
                <div class="col-lg-6">
                    <div class="card complaints-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Recent Complaints</h5>
                            <span class="badge" id="pendingComplaintsBadge">{{ $pendingComplaints ?? 0 }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="complaints-list" id="recentComplaintsList">
                                @if(isset($recentComplaints) && $recentComplaints->count() > 0)
                                    @foreach($recentComplaints as $complaint)
                                    @php
                                        $priority = 'medium';
                                        $highPriorityTypes = ['fraud', 'scam', 'damage', 'accident', 'safety'];
                                        $typeLower = strtolower($complaint->complaint_type ?? '');
                                        if (in_array($typeLower, $highPriorityTypes)) $priority = 'high';
                                        elseif ($typeLower == 'general') $priority = 'low';
                                    @endphp
                                    <div class="complaint-card" onclick="viewComplaintDetails({{ $complaint->id }})">
                                        <div class="complaint-header">
                                            <div>
                                                <div class="complaint-subject">{{ $complaint->subject ?? 'No Subject' }}</div>
                                                <span class="complaint-type type-{{ $priority }}">
                                                    <i class="fas fa-tag me-1"></i>{{ ucfirst($complaint->complaint_type ?? 'General') }}
                                                </span>
                                            </div>
                                            <div class="priority-flag priority-{{ $priority }}">
                                                <i class="fas fa-flag"></i>
                                                <span>{{ ucfirst($priority) }} Priority</span>
                                            </div>
                                        </div>
                                        <div class="complaint-description">
                                            {{ Str::limit($complaint->description, 100) }}
                                        </div>
                                        <div class="complaint-meta">
                                            <div>
                                                <i class="fas fa-user-circle"></i> {{ $complaint->customer->name ?? 'N/A' }}
                                            </div>
                                            <div>
                                                <i class="fas fa-clock"></i> {{ $complaint->created_at->diffForHumans() }}
                                            </div>
                                            <div>
                                                <span class="badge {{ $complaint->status_badge }}">{{ $complaint->status_text }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-comment-slash"></i>
                                        <p>No pending complaints</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <!-- Detail Modal -->
    <div class="detail-modal" id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div class="detail-modal-content" style="background: white; border-radius: 20px; width: 90%; max-width: 800px; max-height: 90vh; overflow: hidden;">
            <div class="detail-modal-header" style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="detail-modal-title" style="margin: 0;">Booking Details</h3>
                <button class="detail-modal-close" id="closeDetailModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div class="detail-modal-body" id="detailModalBody" style="padding: 20px; overflow-y: auto; max-height: calc(90vh - 120px);">
                <!-- Content -->
            </div>
            <div class="detail-modal-footer" style="padding: 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px;">
                <button class="btn btn-secondary" id="closeModalBtn">Close</button>
                <button class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Store all bookings data
        let allBookings = @json($vehicles);
        let currentPage = 1;
        const itemsPerPage = 5;
        
        // DOM elements
        const detailModal = document.getElementById('detailModal');
        const detailModalBody = document.getElementById('detailModalBody');
        const closeDetailModal = document.getElementById('closeDetailModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const saveChangesBtn = document.getElementById('saveChangesBtn');
        const prevPageBtn = document.getElementById('prevPageBtn');
        const nextPageBtn = document.getElementById('nextPageBtn');
        const recentBookingsTable = document.getElementById('recentBookingsTable');
        const startRangeSpan = document.getElementById('startRange');
        const endRangeSpan = document.getElementById('endRange');
        const totalBookingsSpan = document.getElementById('totalBookings');

        // Store vehicle data for JavaScript access
        const vehicleData = {
            @foreach($vehicles as $vehicle)
                {{ $vehicle->id }}: {
                    id: {{ $vehicle->id }},
                    customer_name: "{{ addslashes($vehicle->customer->name ?? 'N/A') }}",
                    customer_email: "{{ addslashes($vehicle->customer->email ?? 'N/A') }}",
                    customer_cnic: "{{ addslashes($vehicle->customer->cnic ?? 'N/A') }}",
                    vehicle_type: "{{ addslashes($vehicle->vehicle->vehicle_type ?? 'N/A') }}",
                    vehicle_number: "{{ addslashes($vehicle->vehicle->vehicle_number ?? 'N/A') }}",
                    booking_date: "{{ $vehicle->booking_date }}",
                    pickup_location: "{{ addslashes($vehicle->pickup_location) }}",
                    dropoff_location: "{{ addslashes($vehicle->dropoff_location) }}",
                    status: "{{ $vehicle->status }}",
                    provider_name: "{{ addslashes($vehicle->vehicle->user->name ?? 'N/A') }}",
                    provider_email: "{{ addslashes($vehicle->vehicle->user->email ?? 'N/A') }}",
                    provider_cnic: "{{ addslashes($vehicle->vehicle->user->cnic ?? 'N/A') }}",
                    vehicle_image: "{{ addslashes($vehicle->vehicle->vehicle_image ?? '') }}",
                    can_carry: "{{ addslashes($vehicle->vehicle->can_carry ?? 'N/A') }}",
                    weight_capacity: "{{ addslashes($vehicle->vehicle->weight_capacity ?? 'N/A') }}"
                },
            @endforeach
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updatePagination();
            fetchDashboardData();
        });

        function setupEventListeners() {
            closeDetailModal.addEventListener('click', function() { detailModal.style.display = 'none'; });
            closeModalBtn.addEventListener('click', function() { detailModal.style.display = 'none'; });
            detailModal.addEventListener('click', function(e) {
                if (e.target === detailModal) detailModal.style.display = 'none';
            });
            saveChangesBtn.addEventListener('click', function() {
                alert('Changes saved successfully!');
                detailModal.style.display = 'none';
            });
            
            if (prevPageBtn) {
                prevPageBtn.addEventListener('click', function() {
                    if (currentPage > 1) { currentPage--; updatePagination(); }
                });
            }
            
            if (nextPageBtn) {
                nextPageBtn.addEventListener('click', function() {
                    const totalPages = Math.ceil(allBookings.length / itemsPerPage);
                    if (currentPage < totalPages && allBookings.length > 0) { currentPage++; updatePagination(); }
                });
            }
            
            const searchInput = document.querySelector('.search-box input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') alert(`Searching for: ${this.value}`);
                });
            }
        }

        function updatePagination() {
            const totalItems = allBookings.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
            const currentPageBookings = allBookings.slice(startIndex, endIndex);
            
            if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
            if (nextPageBtn) nextPageBtn.disabled = currentPage === totalPages || totalItems === 0;
            
            if (startRangeSpan) startRangeSpan.textContent = totalItems === 0 ? 0 : startIndex + 1;
            if (endRangeSpan) endRangeSpan.textContent = endIndex;
            if (totalBookingsSpan) totalBookingsSpan.textContent = totalItems;
            
            renderBookingsTable(currentPageBookings);
        }
        
        function renderBookingsTable(bookings) {
            if (!recentBookingsTable) return;
            
            if (bookings.length === 0) {
                recentBookingsTable.innerHTML = `<tr><td colspan="6" class="text-center py-5"><i class="fas fa-calendar-times fa-3x mb-3 text-muted"></i><p class="text-muted">No bookings found</p></td></tr>`;
                return;
            }
            
            recentBookingsTable.innerHTML = bookings.map(booking => {
                const statusClass = booking.status === 'completed' ? 'status-completed' : (booking.status === 'pending' ? 'status-pending' : 'status-active');
                const statusIcon = booking.status === 'completed' ? 'fa-check-circle' : (booking.status === 'pending' ? 'fa-clock' : 'fa-play-circle');
                const firstLetter = (booking.customer?.name || 'U').charAt(0);
                
                return `
                <tr>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">${escapeHtml(firstLetter)}</div>
                            <div class="customer-details">
                                <div class="customer-name">${escapeHtml(booking.customer?.name || 'N/A')}</div>
                                <div class="customer-email">${escapeHtml(booking.customer?.email || 'No email')}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="vehicle-badge">
                            <i class="fas fa-truck"></i>
                            <span>${escapeHtml(booking.vehicle?.vehicle_type || 'N/A')}</span>
                        </div>
                    </td>
                    <td><strong>${escapeHtml(booking.vehicle?.vehicle_number || 'N/A')}</strong></td>
                    <td><i class="far fa-calendar-alt me-1 text-muted"></i> ${booking.booking_date || 'N/A'}</td>
                    <td><span class="status-badge ${statusClass}"><i class="fas ${statusIcon}"></i> ${ucfirst(booking.status || 'pending')}</span></td>
                    <td><button class="btn btn-view" onclick="viewBookingDetails(${booking.id})"><i class="fas fa-eye me-1"></i> View</button></td>
                </tr>`;
            }).join('');
        }
        
        function getStatusBadgeClass(status) {
            switch((status || '').toLowerCase()) {
                case 'pending': return 'bg-warning';
                case 'completed': return 'bg-success';
                case 'in progress': return 'bg-primary';
                case 'cancelled': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
        
        function ucfirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function fetchDashboardData() {
            // Fetch pending vehicles
            fetch('{{ route("admin.pending.verifications") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const pendingCountElem = document.getElementById('pendingVehiclesCount');
                        const pendingBadgeElem = document.getElementById('pendingVehiclesBadge');
                        if (pendingCountElem) pendingCountElem.textContent = data.count;
                        if (pendingBadgeElem) pendingBadgeElem.textContent = data.count;
                        
                        const pendingList = document.getElementById('pendingVehiclesList');
                        if (pendingList && data.vehicles && data.vehicles.length > 0) {
                            pendingList.innerHTML = data.vehicles.map(vehicle => `
                                <div class="vehicle-card" onclick="viewVehicleDetails(${vehicle.id})">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="vehicle-icon"><i class="fas fa-truck"></i></div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="vehicle-number">${escapeHtml(vehicle.vehicle_number)}</div>
                                                    <div class="vehicle-type">${escapeHtml(vehicle.vehicle_type)}</div>
                                                    <div class="vehicle-owner"><i class="fas fa-user me-1"></i>${escapeHtml(vehicle.owner_name)}</div>
                                                </div>
                                                <div><span class="status-pending-badge"><i class="fas fa-hourglass-half me-1"></i>Pending</span></div>
                                            </div>
                                            <div class="mt-2"><small class="text-muted"><i class="fas fa-weight-hanging me-1"></i>Capacity: ${escapeHtml(vehicle.weight_capacity)} <i class="fas fa-boxes ms-2 me-1"></i>Carry: ${escapeHtml(vehicle.can_carry)}</small></div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        } else if (pendingList) {
                            pendingList.innerHTML = `<div class="empty-state"><i class="fas fa-check-circle"></i><p>No pending vehicles</p></div>`;
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            
            // Fetch recent complaints
            fetch('{{ route("admin.recent.complaints") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const complaintsCountElem = document.getElementById('pendingComplaintsCount');
                        const complaintsBadgeElem = document.getElementById('pendingComplaintsBadge');
                        if (complaintsCountElem) complaintsCountElem.textContent = data.pending_count;
                        if (complaintsBadgeElem) complaintsBadgeElem.textContent = data.pending_count;
                        
                        const complaintsList = document.getElementById('recentComplaintsList');
                        if (complaintsList && data.complaints && data.complaints.length > 0) {
                            complaintsList.innerHTML = data.complaints.map(complaint => `
                                <div class="complaint-card" onclick="viewComplaintDetails(${complaint.id})">
                                    <div class="complaint-header">
                                        <div>
                                            <div class="complaint-subject">${escapeHtml(complaint.subject)}</div>
                                            <span class="complaint-type type-${complaint.priority}"><i class="fas fa-tag me-1"></i>${escapeHtml(complaint.complaint_type)}</span>
                                        </div>
                                        <div class="priority-flag priority-${complaint.priority}"><i class="fas fa-flag"></i><span>${complaint.priority.charAt(0).toUpperCase() + complaint.priority.slice(1)} Priority</span></div>
                                    </div>
                                    <div class="complaint-description">${escapeHtml(complaint.description.substring(0, 100))}</div>
                                    <div class="complaint-meta">
                                        <div><i class="fas fa-user-circle"></i> ${escapeHtml(complaint.customer_name)}</div>
                                        <div><i class="fas fa-clock"></i> ${escapeHtml(complaint.created_at)}</div>
                                        <div><span class="badge ${complaint.status === 'pending' ? 'bg-warning' : 'bg-info'}">${escapeHtml(complaint.status_label)}</span></div>
                                    </div>
                                </div>
                            `).join('');
                        } else if (complaintsList) {
                            complaintsList.innerHTML = `<div class="empty-state"><i class="fas fa-comment-slash"></i><p>No pending complaints</p></div>`;
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function viewBookingDetails(bookingId) {
            const bookingData = vehicleData[bookingId];
            if (!bookingData) { alert('Booking details not found'); return; }
            
            detailModalBody.innerHTML = `
                <div class="detail-section"><h4>Trip Information</h4><div class="detail-grid">${generateDetailItem('Booking ID', 'TL-'+bookingData.id)}${generateDetailItem('Booking Date', bookingData.booking_date)}${generateDetailItem('Pickup Location', bookingData.pickup_location)}${generateDetailItem('Drop Location', bookingData.dropoff_location)}${generateDetailItem('Status', '<span class="badge '+getStatusBadgeClass(bookingData.status)+'">'+bookingData.status+'</span>')}</div></div>
                <div class="detail-section"><h4>Customer Information</h4><div class="detail-grid">${generateDetailItem('Customer Name', bookingData.customer_name)}${generateDetailItem('Email', bookingData.customer_email)}${generateDetailItem('CNIC', bookingData.customer_cnic)}</div></div>
                <div class="detail-section"><h4>Service Provider Information</h4><div class="detail-grid">${generateDetailItem('Provider Name', bookingData.provider_name)}${generateDetailItem('Email', bookingData.provider_email)}${generateDetailItem('CNIC', bookingData.provider_cnic)}</div></div>
                <div class="detail-section"><h4>Vehicle Information</h4><div class="detail-grid">${generateDetailItem('Vehicle Type', bookingData.vehicle_type)}${generateDetailItem('Vehicle Number', bookingData.vehicle_number)}${generateDetailItem('Weight Capacity', bookingData.weight_capacity)}${generateDetailItem('Can Carry', bookingData.can_carry)}</div></div>
                ${bookingData.vehicle_image ? `<div class="detail-section"><h4>Vehicle Images</h4><img src="${bookingData.vehicle_image}" style="max-width:100%; border-radius:10px;"></div>` : ''}
            `;
            detailModal.style.display = 'flex';
        }
        
        function generateDetailItem(label, value) {
            return `<div class="detail-item"><strong>${label}:</strong><br>${value}</div>`;
        }
        
        function viewVehicleDetails(vehicleId) {
            window.location.href = `/admin/pending-vehicles/${vehicleId}`;
        }
        
        function viewComplaintDetails(complaintId) {
            window.location.href = `/admin/complaints/${complaintId}`;
        }
    </script>
    <style>
        .detail-modal { display: none; }
        .detail-item { padding: 10px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px; }
        .detail-section { margin-bottom: 25px; }
        .detail-section h4 { font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: #2c3e50; border-left: 3px solid #3498db; padding-left: 10px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
    </style>
</body>
</html>