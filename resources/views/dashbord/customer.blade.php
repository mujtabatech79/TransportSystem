<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Customer Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --danger: #e74c3c;
            --info: #17a2b8;
            --blue-border: #3498db;
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
        
        /* Booking Cards */
        .booking-card {
            padding: 20px;
            border-left: 4px solid var(--secondary);
            margin-bottom: 15px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        /* Status ke hisaab se border colors */
        .booking-card.request {
            border-left-color: var(--warning);
        }
        
        .booking-card.accept {
            border-left-color: var(--success);
        }
        
        .booking-card.reject {
            border-left-color: var(--danger);
        }
        
        .booking-card.complete {
            border-left-color: var(--info);
        }
        
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Status Badge Colors */
        .bg-warning { background-color: var(--warning); color: #000; }
        .bg-success { background-color: var(--success); color: #fff; }
        .bg-danger { background-color: var(--danger); color: #fff; }
        .bg-info { background-color: var(--info); color: #fff; }
        .bg-primary { background-color: var(--primary); color: #fff; }
        .bg-secondary { background-color: #6c757d; color: #fff; }
        
        /* NEW: Blue Border Buttons with Hover Effect */
        .btn-blue-border {
            background: transparent !important;
            border: 2px solid #e9ecef !important;
            color: #6c757d !important;
            position: relative;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .btn-blue-border:hover {
            background: #3498db !important;
            color: white !important;
            border-color: #3498db !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Button hover animation */
        .btn-blue-border::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(52, 152, 219, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-blue-border:hover::before {
            width: 300px;
            height: 300px;
        }
        
        /* Specific button styles */
        .btn-new-booking {
            border: 2px solid #e9ecef !important;
            background: transparent !important;
            color: #6c757d !important;
        }
        
        .btn-new-booking:hover {
            border-color: #3498db !important;
            background: #3498db !important;
            color: white !important;
        }
        
        .btn-view-all {
            border: 2px solid #e9ecef !important;
            background: transparent !important;
            color: #6c757d !important;
        }
        
        .btn-view-all:hover {
            border-color: #3498db !important;
            background: #3498db !important;
            color: white !important;
        }
        
        .btn-detail {
            border: 2px solid #e9ecef !important;
            background: transparent !important;
            color: #6c757d !important;
        }
        
        .btn-detail:hover {
            border-color: #3498db !important;
            background: #3498db !important;
            color: white !important;
        }
        
        .btn-track-shipment {
            border: 2px solid #e9ecef !important;
            background: transparent !important;
            color: #6c757d !important;
        }
        
        .btn-track-shipment:hover {
            border-color: #3498db !important;
            background: #3498db !important;
            color: white !important;
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
            height: 180px;
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
            background: var(--danger);
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
        
        .btn-outline-primary {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--secondary);
            color: white;
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
        
        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Progress Bar */
        .progress {
            height: 10px;
            border-radius: 10px;
            background-color: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        /* Tracking Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            left: 21px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: linear-gradient(to bottom, var(--secondary) 0%, var(--secondary) 100%);
            z-index: 0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 50px;
            margin-bottom: 25px;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-marker {
            position: absolute;
            left: 12px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #e9ecef;
            border: 4px solid white;
            box-shadow: 0 0 0 2px #dee2e6;
            z-index: 1;
        }
        
        .timeline-item.completed .timeline-marker {
            background-color: var(--success);
            box-shadow: 0 0 0 2px var(--success);
        }
        
        .timeline-item.active .timeline-marker {
            background-color: var(--warning);
            box-shadow: 0 0 0 2px var(--warning);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(243, 156, 18, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(243, 156, 18, 0);
            }
        }
        
        .timeline-content {
            padding-bottom: 10px;
        }
        
        /* Rejection Reason Styling */
        .rejection-reason {
            background-color: #fff3f3;
            border-left: 4px solid var(--danger);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .rejection-reason h6 {
            color: var(--danger);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .rejection-reason p {
            margin-bottom: 0;
            color: #333;
        }
        
        .rejection-reason small {
            color: #666;
            display: block;
            margin-top: 8px;
        }
        
        /* Active booking highlight */
        .booking-item {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .booking-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            background-color: #f8f9fa;
        }
        
        .booking-item.active {
            background-color: #f0f7ff;
            border-left: 4px solid var(--secondary) !important;
        }
        
        /* Chat Styles */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 4px 0;
        }
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background-color: #6c757d;
            border-radius: 50%;
            animation: typing-bounce 1.4s infinite ease-in-out;
        }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        .suggestion-chip {
            font-size: 0.75rem;
            padding: 4px 10px;
            transition: all 0.2s ease;
        }
        .suggestion-chip:hover {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        .bot-response {
            line-height: 1.5;
        }
        .bot-response strong {
            color: #2c3e50;
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
        }
        
        /* Hover effect for empty state */
        .empty-state-btn {
            border: 2px dashed #dee2e6 !important;
            background: transparent !important;
            color: #6c757d !important;
            transition: all 0.3s ease;
        }
        
        .empty-state-btn:hover {
            border: 2px solid #3498db !important;
            background: #3498db !important;
            color: white !important;
            transform: translateY(-2px);
        }
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
                <a class="nav-link active" href="{{route('customer.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
               <a class="nav-link" href="{{route('all.vehicle')}}">
                    <i class="fas fa-search"></i> <span>All vehicle</span>
                </a>
                <a class="nav-link" href="{{route('find.vehicle')}}">
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
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Admin</h5>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=3498db&color=fff" alt="Customer">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ $userName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
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
                    <h4 class="mb-1 fw-bold">Customer Dashboard</h4>
                    <p class="text-muted mb-0">Welcome back, {{ $userName ?? 'Customer' }}! Here's your logistics overview.</p>
                </div>
                <div>
                    <!-- New Booking Button with Blue Border Effect -->
                    <button class="btn btn-new-booking btn-blue-border" id="newBookingBtn">
                        <i class="fas fa-plus me-2"></i> New Booking
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="count">{{ $totalBookings ?? 0 }}</div>
                            <div class="label">Total Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle"></i>
                            <div class="count">{{ $completedBookings ?? 0 }}</div>
                            <div class="label">Completed</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <div class="count">{{ $inProgressBookings ?? 0 }}</div>
                            <div class="label">In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-danger-light">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave"></i>
                            <div class="count">Rs {{ ($reject ?? 0) }}</div>
                            <div class="label">Reject Bookings</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings and Tracking Section -->
            <div class="row mt-4">
                <div class="col-lg-8">
                    <!-- Recent Bookings with Pagination -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Bookings</h5>
                            <div class="d-flex align-items-center">
                                <!-- Pagination Controls -->
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-blue-border" id="prevBookingPage" disabled>
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-blue-border" id="nextBookingPage">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <span class="text-muted me-2" id="bookingPaginationInfo">Page 1</span>
                                <!-- View All Button -->
                                <a href="{{ route('mybookings') }}" class="btn btn-sm btn-view-all btn-blue-border">View All</a>
                            </div>
                        </div>
                        <div class="card-body" id="bookingsContainer">
                            <!-- Bookings will be loaded here via AJAX -->
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading bookings...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Shipment Tracking Section -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Active Shipment Tracking</h5>
                        </div>
                        <div class="card-body" id="trackingContainer">
                            <!-- Default message - shown when no booking selected -->
                            <div class="text-center py-3" id="defaultTrackingMessage">
                                <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                                <h5>Select a Booking to Track</h5>
                                <p class="text-muted">Click on any booking above to view its real-time tracking details</p>
                            </div>
                            
                            <!-- Tracking content will be dynamically inserted here -->
                            <div id="trackingContent" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('find.vehicle') }}" class="btn btn-blue-border">
                                    <i class="fas fa-search me-2"></i> Find Vehicles
                                </a>
                                <button class="btn btn-blue-border" id="trackShipmentBtn">
                                    <i class="fas fa-map-marker-alt me-2"></i> Track Shipment
                                </button>
                                <button class="btn btn-blue-border" id="chatSupportBtn">
                                    <i class="fas fa-comment me-2"></i> Chat Support
                                </button>
                                <button class="btn btn-blue-border" id="paymentHistoryBtn">
                                    <i class="fas fa-file-invoice me-2"></i> Payment History
                                </button>
                                <button class="btn btn-blue-border" id="rateServiceBtn">
                                    <i class="fas fa-star me-2"></i> Rate Service
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- AI Chat Assistant - Enhanced Version -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-robot text-primary me-2"></i>AI Chat Assistant
                            </h5>
                            <button class="btn btn-sm btn-blue-border" id="clearChatBtn">
                                <i class="fas fa-trash-alt me-1"></i> Clear
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <!-- Chat Messages Container -->
                            <div id="chatMessages" class="p-3" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
                                <!-- Welcome Message -->
                                <div class="d-flex mb-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                                        <i class="fas fa-robot text-white" style="font-size: 14px;"></i>
                                    </div>
                                    <div class="ms-2">
                                        <div class="bg-white rounded-3 p-2 shadow-sm" style="max-width: 85%;">
                                            <small class="text-muted">TruckLink AI</small>
                                            <p class="mb-0 small">Hi {{ $userName ?? 'Customer' }}! 👋 I'm your AI assistant. Ask me about vehicle recommendations, fare calculation, booking help, tracking, or anything else!</p>
                                        </div>
                                        <small class="text-muted ms-2">{{ date('h:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Suggestions -->
                            <div class="px-3 pb-2 border-top" id="quickSuggestions">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Recommend a vehicle for heavy goods">
                                        <i class="fas fa-truck me-1"></i> Heavy goods?
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Calculate fare from Lahore to Islamabad">
                                        <i class="fas fa-calculator me-1"></i> Fare estimate
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="How to book a vehicle?">
                                        <i class="fas fa-question-circle me-1"></i> How to book?
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Track my active shipment">
                                        <i class="fas fa-map-marker-alt me-1"></i> Track shipment
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Show top-rated vehicles">
                                        <i class="fas fa-star me-1"></i> Top rated
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Input Area -->
                            <div class="p-3 border-top bg-white">
                                <div class="input-group">
                                    <input type="text" id="chatInput" class="form-control" placeholder="Type your question... (e.g., Recommend a cheap vehicle, How to book?, Calculate fare...)" autocomplete="off">
                                    <button class="btn btn-primary" id="sendChatBtn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i> Try: "Best vehicle for 5000kg goods", "How is fare calculated?", "Submit a complaint"
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="mb-0 text-muted">&copy; 2024 TruckLink. All rights reserved.</p>
            </div>
            <div>
                <a href="#" class="text-muted me-3">Privacy Policy</a>
                <a href="#" class="text-muted me-3">Terms of Service</a>
                <a href="#" class="text-muted">Help</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Detail Button ke liye Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bookingDetailsContent">
                    <!-- Details yahan load hongi -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading booking details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Global variables
        let currentPage = 1;
        let lastPage = 1;
        let bookings = [];
        let selectedBookingId = null;

        // Load bookings on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadBookings(currentPage);
        });

        // Function to load bookings via AJAX
        function loadBookings(page) {
            const bookingsContainer = document.getElementById('bookingsContainer');
            
            // Show loading state
            bookingsContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading bookings...</p>
                </div>
            `;
            
            // Make AJAX request
            fetch(`/customer/bookings?page=${page}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    bookings = data.bookings;
                    currentPage = data.current_page;
                    lastPage = data.last_page;
                    
                    // Update pagination info
                    document.getElementById('bookingPaginationInfo').textContent = `Page ${currentPage} of ${lastPage}`;
                    
                    // Enable/disable navigation buttons
                    document.getElementById('prevBookingPage').disabled = (currentPage <= 1);
                    document.getElementById('nextBookingPage').disabled = (currentPage >= lastPage);
                    
                    // Render bookings
                    renderBookings(bookings);
                } else {
                    bookingsContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                            <h5>Error Loading Bookings</h5>
                            <p class="text-muted">Please try again later.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                bookingsContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <h5>Error Loading Bookings</h5>
                        <p class="text-muted">Please try again later.</p>
                    </div>
                `;
            });
        }

        // Function to render bookings
        function renderBookings(bookings) {
            const bookingsContainer = document.getElementById('bookingsContainer');
            
            if (bookings.length === 0) {
                bookingsContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5>No Bookings Yet</h5>
                        <p class="text-muted">Start by booking your first vehicle</p>
                        <a href="{{ route('find.vehicle') }}" class="btn btn-blue-border empty-state-btn">
                            <i class="fas fa-plus me-2"></i> Book Now
                        </a>
                    </div>
                `;
                return;
            }
            
            let html = '';
            bookings.forEach(booking => {
                html += `
                    <div class="booking-card ${booking.status} booking-item" data-booking-id="${booking.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6>${escapeHtml(booking.pickup_location)} to ${escapeHtml(booking.dropoff_location)}</h6>
                                <p class="mb-1">
                                    ${escapeHtml(booking.goods_type || 'Goods')} • 
                                    ${booking.goods_weight || 0} Ton • 
                                    ${escapeHtml(booking.vehicle_type || 'Vehicle')}
                                </p>
                                <small class="text-muted">
                                    Trip Date: ${new Date(booking.booking_date).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })}
                                    ${booking.provider_name ? `• Service Provider: ${escapeHtml(booking.provider_name)}` : ''}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge ${booking.badge_class}">
                                    ${booking.status_text}
                                </span>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-detail btn-blue-border view-details" 
                                            data-booking-id="${booking.id}">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            bookingsContainer.innerHTML = html;
            
            // Add click event to booking cards for tracking
            document.querySelectorAll('.booking-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking on a button
                    if (e.target.closest('button')) return;
                    
                    const bookingId = this.getAttribute('data-booking-id');
                    
                    // Remove active class from all bookings
                    document.querySelectorAll('.booking-item').forEach(b => {
                        b.classList.remove('active');
                        b.style.backgroundColor = '';
                    });
                    
                    // Add active class to clicked booking
                    this.classList.add('active');
                    
                    // Load tracking info
                    loadTrackingInfo(bookingId);
                });
            });
            
            // Re-attach detail button event listeners
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const bookingId = this.getAttribute('data-booking-id');
                    fetchBookingDetails(bookingId);
                });
            });
        }

        // Function to load tracking information
        function loadTrackingInfo(bookingId) {
            selectedBookingId = bookingId;
            
            const defaultMessage = document.getElementById('defaultTrackingMessage');
            const trackingContent = document.getElementById('trackingContent');
            
            // Show loading
            trackingContent.style.display = 'block';
            trackingContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading tracking information...</p>
                </div>
            `;
            defaultMessage.style.display = 'none';
            
            // Fetch tracking info
            fetch(`/customer/tracking/${bookingId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTrackingInfo(data.booking);
                } else {
                    trackingContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Unable to load tracking information.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                trackingContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading tracking information.
                    </div>
                `;
            });
        }

        // Function to render tracking information
        function renderTrackingInfo(booking) {
            const trackingContent = document.getElementById('trackingContent');
            
            if (booking.status === 'request') {
                trackingContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h5>Booking Request Pending</h5>
                        <p class="text-muted">Your booking request is awaiting confirmation from the service provider.</p>
                        <p class="mb-0"><strong>Booking ID:</strong> #${booking.id}</p>
                    </div>
                `;
                return;
            }
            
            if (booking.status === 'reject') {
                trackingContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                        <h5>Booking Rejected</h5>
                        <p class="text-muted">This booking request was rejected by the service provider.</p>
                        <p class="mb-0"><strong>Booking ID:</strong> #${booking.id}</p>
                    </div>
                `;
                return;
            }
            
            if (booking.status === 'complete') {
                trackingContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Booking Completed</h5>
                        <p class="text-muted">This shipment has been successfully delivered.</p>
                        <p class="mb-0"><strong>Booking ID:</strong> #${booking.id}</p>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-blue-border" id="downloadReceiptBtn">
                                <i class="fas fa-download me-1"></i> Download Receipt
                            </button>
                        </div>
                    </div>
                `;
                document.getElementById('downloadReceiptBtn')?.addEventListener('click', function() {
                    alert('Download receipt feature will be implemented soon.');
                });
                return;
            }
            
            // For accepted bookings - show timeline
            if (booking.status === 'accept') {
                let timelineHtml = '';
                
                if (booking.timeline && booking.timeline.length) {
                    booking.timeline.forEach((item, index) => {
                        let itemClass = '';
                        if (item.status === 'completed') itemClass = 'completed';
                        if (item.status === 'active') itemClass = 'active';
                        
                        timelineHtml += `
                            <div class="timeline-item ${itemClass}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">${escapeHtml(item.title)}</h6>
                                    <p class="mb-0 text-muted">${escapeHtml(item.description)}</p>
                                    ${item.timestamp ? `<small>${escapeHtml(item.timestamp)}</small>` : ''}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    timelineHtml = `
                        <div class="text-center py-3">
                            <p class="text-muted">Tracking updates will appear here soon.</p>
                        </div>
                    `;
                }
                
                trackingContent.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">${escapeHtml(booking.pickup_location)} to ${escapeHtml(booking.dropoff_location)}</h6>
                        <span class="badge bg-success">${booking.delivery_status_text || 'In Transit'}</span>
                    </div>
                    
                    ${booking.vehicle ? `
                        <div class="alert alert-info py-2 mb-3">
                            <small>
                                <i class="fas fa-truck me-1"></i> 
                                Vehicle: ${escapeHtml(booking.vehicle.vehicle_type)} (${escapeHtml(booking.vehicle.registration_number)})
                            </small>
                        </div>
                    ` : ''}
                    
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: ${booking.progress_percentage}%" 
                             aria-valuenow="${booking.progress_percentage}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    
                    <div class="timeline">
                        ${timelineHtml}
                    </div>
                `;
            }
        }

        // Pagination event listeners
        document.getElementById('prevBookingPage').addEventListener('click', function() {
            if (currentPage > 1) {
                loadBookings(currentPage - 1);
            }
        });

        document.getElementById('nextBookingPage').addEventListener('click', function() {
            if (currentPage < lastPage) {
                loadBookings(currentPage + 1);
            }
        });

        // Detail button functionality
        function fetchBookingDetails(bookingId) {
            // Show modal with loading state
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            const modalBody = document.getElementById('bookingDetailsContent');
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading booking details...</p>
                </div>
            `;
            modal.show();
            
            // AJAX request to fetch booking details
            fetch(`/booking/details/${bookingId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let booking = data.booking;
                
                // Status badge class
                let statusBadgeClass = 'bg-secondary';
                if (booking.status === 'request') statusBadgeClass = 'bg-warning';
                if (booking.status === 'accept') statusBadgeClass = 'bg-success';
                if (booking.status === 'reject') statusBadgeClass = 'bg-danger';
                if (booking.status === 'complete') statusBadgeClass = 'bg-info';
                
                // Format dates
                let bookingDate = booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-US', { 
                    year: 'numeric', month: 'long', day: 'numeric' 
                }) : 'N/A';
                
                let createdDate = booking.created_at ? new Date(booking.created_at).toLocaleString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                }) : 'N/A';
                
                let acceptedDate = booking.accepted_at ? new Date(booking.accepted_at).toLocaleString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                }) : null;
                
                let rejectedDate = booking.rejected_at ? new Date(booking.rejected_at).toLocaleString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                }) : null;
                
                // Build HTML for modal content
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Booking Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Booking ID:</th>
                                    <td>#${booking.id}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge ${statusBadgeClass}">${booking.status_text || booking.status}</span></td>
                                </tr>
                                <tr>
                                    <th>Request Status:</th>
                                    <td><span class="badge ${booking.request_status === 'accepted' ? 'bg-success' : (booking.request_status === 'rejected' ? 'bg-danger' : 'bg-warning')}">${booking.request_status || 'pending'}</span></td>
                                </tr>
                                <tr>
                                    <th>Delivery Status:</th>
                                    <td>${booking.delivery_status || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Booking Date:</th>
                                    <td>${bookingDate}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>${createdDate}</td>
                                </tr>
                `;
                
                if (acceptedDate) {
                    html += `
                        <tr>
                            <th>Accepted At:</th>
                            <td>${acceptedDate}</td>
                        </tr>
                    `;
                }
                
                if (rejectedDate) {
                    html += `
                        <tr>
                            <th>Rejected At:</th>
                            <td>${rejectedDate}</td>
                        </tr>
                    `;
                }
                
                html += `
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Location Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Pickup:</th>
                                    <td>${escapeHtml(booking.pickup_location || 'N/A')}</td>
                                </tr>
                                <tr>
                                    <th>Dropoff:</th>
                                    <td>${escapeHtml(booking.dropoff_location || 'N/A')}</td>
                                </tr>
                                <tr>
                                    <th>Pickup Time:</th>
                                    <td>${booking.pickup_time || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Goods Type:</th>
                                    <td>${escapeHtml(booking.goods_type || 'N/A')}</td>
                                </tr>
                                <tr>
                                    <th>Goods Weight:</th>
                                    <td>${booking.goods_weight || 0} Ton</td>
                                </tr>
                                <tr>
                                    <th>Special Instructions:</th>
                                    <td>${escapeHtml(booking.special_instructions || 'None')}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                
                if (booking.vehicle) {
                    html += `
                        <h6 class="border-bottom pb-2 mt-3">Vehicle Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Vehicle Type:</th>
                                        <td>${escapeHtml(booking.vehicle.vehicle_type || 'N/A')}</td>
                                    </tr>
                                    <tr>
                                        <th>Registration:</th>
                                        <td>${escapeHtml(booking.vehicle.registration_number || 'N/A')}</td>
                                    </tr>
                                    <tr>
                                        <th>Capacity:</th>
                                        <td>${booking.vehicle.weight_capacity || 0} kg</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Can Carry:</th>
                                        <td>${escapeHtml(booking.vehicle.can_carry || 'N/A')}</td>
                                    </tr>
                                    <tr>
                                        <th>Service Provider:</th>
                                        <td>${booking.vehicle.user ? escapeHtml(booking.vehicle.user.name) : 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact:</th>
                                        <td>${booking.vehicle.user ? escapeHtml(booking.vehicle.user.mobile) : 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    `;
                }
                
                if (booking.estimated_fare || booking.actual_fare) {
                    html += `
                        <h6 class="border-bottom pb-2 mt-3">Pricing Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Estimated Distance:</th>
                                        <td>${booking.estimated_distance || 0} km</td>
                                    </tr>
                                    <tr>
                                        <th>Estimated Fare:</th>
                                        <td>Rs ${booking.estimated_fare || 0}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Actual Distance:</th>
                                        <td>${booking.actual_distance || 0} km</td>
                                    </tr>
                                    <tr>
                                        <th>Actual Fare:</th>
                                        <td>Rs ${booking.actual_fare || 0}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    `;
                }
                
                html += `
                    <h6 class="border-bottom pb-2 mt-3">Payment Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Payment Method:</th>
                            <td>${booking.payment_method || 'Not specified'}</td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td><span class="badge ${booking.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${booking.payment_status || 'pending'}</span></td>
                        </tr>
                    </table>
                `;
                
                if (booking.status === 'reject' && booking.rejection_reason) {
                    html += `
                        <div class="rejection-reason">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Rejection Reason</h6>
                            <p>${escapeHtml(booking.rejection_reason)}</p>
                            <small>Rejected on: ${rejectedDate || 'N/A'}</small>
                        </div>
                    `;
                }
                
                modalBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching booking details:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading booking details. Please try again.
                    </div>
                `;
            });
        }

        // New Booking button functionality
        document.getElementById('newBookingBtn').addEventListener('click', function() {
            window.location.href = "{{ route('find.vehicle') }}";
        });

        // Quick action buttons functionality
        document.getElementById('trackShipmentBtn')?.addEventListener('click', function() {
            if (selectedBookingId) {
                loadTrackingInfo(selectedBookingId);
            } else {
                alert('Please select a booking from the list first.');
            }
        });
        
        document.getElementById('chatSupportBtn')?.addEventListener('click', function() {
            document.getElementById('chatInput')?.focus();
        });
        
        document.getElementById('paymentHistoryBtn')?.addEventListener('click', function() {
            alert('Payment history feature will be available soon.');
        });
        
        document.getElementById('rateServiceBtn')?.addEventListener('click', function() {
            alert('Rate service feature will be available soon.');
        });

        // Search functionality
        const searchInput = document.querySelector('.search-box input');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value;
                    if (searchTerm) {
                        alert(`Searching for: ${searchTerm}`);
                    }
                }
            });
        }

        // Helper function
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ============================================
        // AI Chat Assistant JavaScript - Updated Version with DB Persistence
        // ============================================

        (function() {
            const chatMessages = document.getElementById('chatMessages');
            const chatInput = document.getElementById('chatInput');
            const sendBtn = document.getElementById('sendChatBtn');
            const clearChatBtn = document.getElementById('clearChatBtn');
            const suggestionBtns = document.querySelectorAll('.suggestion-btn');

            let isTyping = false;

            // ── Init ─────────────────────────────────────────────────────────────────
            function initChat() {
                loadChatHistory();          // Load from DB (this customer only)

                if (sendBtn) sendBtn.addEventListener('click', sendMessage);
                if (chatInput) chatInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') sendMessage(); });
                if (clearChatBtn) clearChatBtn.addEventListener('click', clearChat);

                suggestionBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        chatInput.value = this.getAttribute('data-question');
                        sendMessage();
                    });
                });
            }

            // ── Load history from server (DB) ────────────────────────────────────────
            async function loadChatHistory() {
                try {
                    const res = await fetch('/chatbot/history', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (data.success && data.history && data.history.length > 0) {
                        // Keep welcome message, append history after it
                        data.history.forEach(h => {
                            if (h.sender === 'user') addUserMessage(h.message, false);
                            else addBotMessage(h.message, [], false);
                        });
                        scrollToBottom();
                    }
                } catch (e) {
                    console.error('History load error:', e);
                }
            }

            // ── Send message ─────────────────────────────────────────────────────────
            async function sendMessage() {
                if (isTyping) return;
                const message = chatInput.value.trim();
                if (!message) return;

                chatInput.value = '';
                addUserMessage(message);
                showTypingIndicator();
                isTyping = true;

                try {
                    const res = await fetch('/chatbot/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: message })
                    });
                    const data = await res.json();

                    removeTypingIndicator();

                    if (data.success) {
                        addBotMessage(data.response, data.suggestions || []);
                    } else {
                        addBotMessage("Unable to connect. Please try again.", []);
                    }
                } catch (err) {
                    removeTypingIndicator();
                    addBotMessage("Connection error. Please check your network.", []);
                } finally {
                    isTyping = false;
                }
            }

            // ── Add user message ──────────────────────────────────────────────────────
            function addUserMessage(message, scroll = true) {
                const div = document.createElement('div');
                div.className = 'd-flex mb-3 justify-content-end';
                div.innerHTML = `
                    <div class="me-2 text-end">
                        <div class="bg-primary text-white rounded-3 p-2 shadow-sm d-inline-block" style="max-width:85%;">
                            <small class="text-white-50 d-block">You</small>
                            <p class="mb-0 small">${escapeHtml(message)}</p>
                        </div>
                        <small class="text-muted">${getCurrentTime()}</small>
                    </div>
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                        <i class="fas fa-user text-white" style="font-size:14px;"></i>
                    </div>`;
                chatMessages.appendChild(div);
                if (scroll) scrollToBottom();
            }

            // ── Add bot message ───────────────────────────────────────────────────────
            function addBotMessage(response, suggestions = [], scroll = true) {
                const div = document.createElement('div');
                div.className = 'd-flex mb-3';
                div.innerHTML = `
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                        <i class="fas fa-robot text-white" style="font-size:14px;"></i>
                    </div>
                    <div class="ms-2" style="max-width:90%;">
                        <div class="bg-white rounded-3 p-2 shadow-sm">
                            <small class="text-muted d-block">TruckLink AI</small>
                            <div class="small bot-response">${formatResponse(response)}</div>
                        </div>
                        <small class="text-muted ms-1">${getCurrentTime()}</small>
                        ${suggestions.length ? renderChips(suggestions) : ''}
                    </div>`;
                chatMessages.appendChild(div);

                // Attach chip listeners
                div.querySelectorAll('.suggestion-chip').forEach(chip => {
                    chip.addEventListener('click', function() {
                        chatInput.value = this.getAttribute('data-q');
                        sendMessage();
                    });
                });

                if (scroll) scrollToBottom();
            }

            // ── Format bot response (markdown-lite) ──────────────────────────────────
            function formatResponse(text) {
                let t = escapeHtml(text);
                t = t.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                t = t.replace(/\n/g, '<br>');
                return t;
            }

            // ── Suggestion chips ──────────────────────────────────────────────────────
            function renderChips(suggestions) {
                return '<div class="d-flex flex-wrap gap-1 mt-2">' +
                    suggestions.map(s =>
                        `<button class="btn btn-sm btn-outline-secondary suggestion-chip" data-q="${escapeHtml(s)}">${escapeHtml(s)}</button>`
                    ).join('') + '</div>';
            }

            // ── Typing indicator ──────────────────────────────────────────────────────
            function showTypingIndicator() {
                const div = document.createElement('div');
                div.id = 'typingIndicator';
                div.className = 'd-flex mb-3';
                div.innerHTML = `
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                        <i class="fas fa-robot text-white" style="font-size:14px;"></i>
                    </div>
                    <div class="ms-2">
                        <div class="bg-white rounded-3 p-2 shadow-sm">
                            <small class="text-muted">TruckLink AI is typing</small>
                            <div class="typing-indicator"><span></span><span></span><span></span></div>
                        </div>
                    </div>`;
                chatMessages.appendChild(div);
                scrollToBottom();
            }

            function removeTypingIndicator() {
                document.getElementById('typingIndicator')?.remove();
            }

            // ── Clear chat ────────────────────────────────────────────────────────────
            async function clearChat() {
                if (!confirm('Clear all chat history?')) return;
                try {
                    await fetch('/chatbot/clear', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    // Remove all messages except welcome
                    while (chatMessages.children.length > 1) {
                        chatMessages.removeChild(chatMessages.lastChild);
                    }
                    addBotMessage("Chat history cleared! How can I assist you today?", ['Available vehicles', 'My bookings', 'Calculate fare']);
                } catch (e) {
                    console.error('Clear error:', e);
                }
            }

            // ── Helpers ───────────────────────────────────────────────────────────────
            function getCurrentTime() {
                return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function scrollToBottom() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', initChat);
        })();
    </script>
</body>
</html>