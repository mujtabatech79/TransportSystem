<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - My Vehicles</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
        
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
            background: transparent;
        }
        
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
        
        .content-area {
            padding: 30px;
        }
        
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
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .vehicle-badge.approved {
            background: var(--success);
            color: white;
        }
        
        .vehicle-badge.pending {
            background: var(--warning);
            color: #000;
        }
        
        .vehicle-badge.inactive {
            background: var(--danger);
            color: white;
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
            color: white;
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
        
        .btn-edit {
            background: transparent;
            border: 2px solid var(--warning);
            color: var(--warning);
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background: var(--warning);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }
        
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
        }
        
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            border-radius: 16px 16px 0 0;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            border-color: var(--secondary);
            background: rgba(52, 152, 219, 0.05);
        }
        
        .file-upload i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
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
        
        .alert {
            border-radius: 10px;
            border: none;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
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
        
        /* Success Popup Modal Styles */
        .success-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 10000;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        
        .success-popup.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .success-popup-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: zoomIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-checkmark {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--success), #219653);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.3);
            animation: bounce 0.5s ease-out;
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .success-checkmark i {
            font-size: 50px;
            color: white;
        }
        
        .success-popup-content h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--success);
        }
        
        .success-popup-content p {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .success-popup-content .message {
            font-size: 1rem;
            margin: 15px 0;
        }
        
        .success-popup-content .email-note {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            color: var(--secondary);
            margin: 20px 0;
        }
        
        .success-popup-content .email-note i {
            margin-right: 8px;
        }
        
        .success-popup-content .btn-ok {
            background: linear-gradient(135deg, var(--success), #219653);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .success-popup-content .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        /* Update Success Popup - Different Color */
        .update-popup .success-checkmark {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
        }
        
        .update-popup h3 {
            color: var(--warning);
        }
        
        .update-popup .btn-ok {
            background: linear-gradient(135deg, var(--warning), #e67e22);
        }
        
        .update-popup .btn-ok:hover {
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }
        
        /* Detail Modal Styles */
        .vehicle-info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .vehicle-info-section h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary);
        }
        
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-label {
            width: 140px;
            font-weight: 600;
            color: #6c757d;
        }
        
        .info-value {
            flex: 1;
            color: var(--dark);
        }
        
        .booking-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-box {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .stat-box .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
        }
        
        .stat-box .label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* Auto-filled field highlight */
        .auto-filled-field {
            background: linear-gradient(135deg, rgba(39,174,96,0.08), rgba(39,174,96,0.02)) !important;
            border-color: #27ae60 !important;
            box-shadow: 0 0 0 2px rgba(39,174,96,0.2);
            transition: all 0.3s ease;
        }
        
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
            
            .booking-stats {
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
            <small class="text-muted">Service Provider</small>
        </div>
        <nav class="nav flex-column mt-4">
            <a class="nav-link" href="{{route('provider.login')}}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a class="nav-link active" href="{{route('my.vehicle')}}"><i class="fas fa-truck"></i> <span>My Vehicles</span></a>
            <a class="nav-link" href="{{route('booking.requests')}}"><i class="fas fa-bell"></i> <span>Booking Requests</span></a>
            <a class="nav-link" href="{{route('see.trip')}}"><i class="fas fa-clipboard-list"></i> <span>Active Bookings</span></a>
            <a class="nav-link" href="{{route('provider.bookings')}}"><i class="fas fa-money-bill-wave"></i> <span>All Bookings</span></a>
            <a class="nav-link" href="{{route('messages.conversations')}}"><i class="fas fa-comments"></i> <span>Messages</span></a>
            <a class="nav-link" href="{{route('provider.ratings-reviews')}}"><i class="fas fa-star"></i> <span>Ratings & Reviews</span></a>
            <a class="nav-link" href="{{route('provider.analytics')}}"><i class="fas fa-chart-line"></i> <span>Analytics</span></a>
            <a class="nav-link" href="{{route('provider.complaints')}}">
                        <i class="fas fa-comments"></i> <span>Complaints Center</span>
  
                        </a>
           
            <a class="nav-link" href="{{route('user.logout')}}"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Search vehicles...">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ session('user_name', 'Provider') }}</span>
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

        <div class="content-area">
            <!-- Success/Error Messages -->
            <div id="messageContainer">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">My Vehicles</h4>
                    <p class="text-muted mb-0">Manage your registered vehicles and add new ones.</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal" id="addVehicleBtn">
                        <i class="fas fa-plus me-2"></i> Add New Vehicle
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ $stats['total_vehicles'] ?? 0 }}</div>
                            <div class="label">Total Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle"></i>
                            <div class="count">{{ $stats['active_vehicles'] ?? 0 }}</div>
                            <div class="label">Active Vehicles</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <div class="count">{{ $stats['pending_vehicles'] ?? 0 }}</div>
                            <div class="label">Pending Approval</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-danger-light">
                        <div class="card-body">
                            <i class="fas fa-pause-circle"></i>
                            <div class="count">{{ $stats['inactive_vehicles'] ?? 0 }}</div>
                            <div class="label">Inactive Vehicles</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Grid -->
            <div class="row mt-4" id="vehicleGrid">
                @if(isset($vehicles) && $vehicles->count() > 0)
                    @foreach($vehicles as $vehicle)
                        <div class="col-xl-4 col-lg-6 col-md-6 vehicle-item" data-id="{{ $vehicle->id }}">
                            <div class="vehicle-card">
                                <div class="vehicle-image" style="background-image: url('{{ asset('uploads/vehicles/' . $vehicle->vehicle_image) }}')">
                                    <div class="vehicle-badge {{ $vehicle->status }}">
                                        @if($vehicle->status === 'pending')
                                            <i class="fas fa-clock me-1"></i> Pending
                                        @elseif($vehicle->status === 'approved')
                                            <i class="fas fa-check-circle me-1"></i> Approved
                                        @else
                                            <i class="fas fa-ban me-1"></i> Rejected
                                        @endif
                                    </div>
                                </div>
                                <div class="vehicle-info">
                                    <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }}</div>
                                    <div class="vehicle-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Registration No</span>
                                            <span class="detail-value">{{ strtoupper($vehicle->vehicle_number) }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Capacity</span>
                                            <span class="detail-value">{{ $vehicle->weight_capacity }} kg</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Chassis No</span>
                                            <span class="detail-value">{{ substr($vehicle->chassis_number, -6) }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Can Carry</span>
                                            <span class="detail-value">{{ $vehicle->can_carry }}</span>
                                        </div>
                                    </div>
                                    <div class="action-buttons">
                                        <button class="btn btn-detail w-100" onclick="showVehicleDetails({{ $vehicle->id }})">
                                            <i class="fas fa-eye me-1"></i> Details
                                        </button>
                                        @if($vehicle->status === 'approved')
                                            <button class="btn btn-warning w-100" onclick="updateVehicleStatus({{ $vehicle->id }}, 'inactive')">
                                                <i class="fas fa-pause me-1"></i> Disable
                                            </button>
                                        @elseif($vehicle->status === 'inactive')
                                            <button class="btn btn-success w-100" onclick="updateVehicleStatus({{ $vehicle->id }}, 'active')">
                                                <i class="fas fa-play me-1"></i> Enable
                                            </button>
                                        @else
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-truck"></i>
                            <h4>No Vehicles Found</h4>
                            <p>You haven't added any vehicles yet. Start by adding your first vehicle.</p>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                                <i class="fas fa-plus me-2"></i> Add Your First Vehicle
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2023 TruckLink</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Service Provider Panel v2.0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Add New Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addVehicleForm" method="POST" action="{{ route('vehicle_register') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Number *</label>
                                <input type="text" class="form-control" name="vehicle_number" id="vehicle_number_field" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Type *</label>
                                <input type="text" class="form-control" name="vehicle_type" id="vehicle_type_field" placeholder="Truck, Van, Pickup" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chassis Number *</label>
                                <input type="text" class="form-control" name="chassis_number" id="chassis_number_field" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Weight Capacity (kg) *</label>
                                <input type="number" class="form-control" name="weight_capacity" id="weight_capacity_field" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Can Carry *</label>
                                <input type="text" class="form-control" name="can_carry" id="can_carry_field" placeholder="e.g., Furniture, Electronics" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Vehicle Image *</label>
                                <div class="file-upload" onclick="document.getElementById('vehicle_image_input').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h5>Upload Vehicle Image</h5>
                                    <p class="text-muted">Click to upload (JPG, PNG, max 4MB)</p>
                                    <input type="file" id="vehicle_image_input" name="vehicle_image" accept="image/*" style="display: none;" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Smart Card / RC Image *</label>
                                <div class="file-upload" onclick="document.getElementById('smartcard_image_input').click()">
                                    <i class="fas fa-file-upload"></i>
                                    <h5>Upload Documents</h5>
                                    <p class="text-muted">Registration book, insurance (JPG, PNG, max 4MB)</p>
                                    <input type="file" id="smartcard_image_input" name="smartcard_image" accept="image/*" style="display: none;" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    After registration, your vehicle will be pending admin approval. You'll receive a confirmation email.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitVehicleForm()">
                        <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Detail Modal -->
    <div class="modal fade" id="vehicleDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Vehicle Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="vehicleDetailModalBody">
                    <div class="text-center p-4">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading vehicle details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-edit" id="editVehicleFromDetailBtn">
                        <i class="fas fa-edit me-1"></i> Edit Vehicle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editVehicleModalBody">
                    <div class="text-center p-4">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading edit form...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Success Popup Modal -->
    <div class="success-popup" id="successPopup">
        <div class="success-popup-content">
            <div class="success-checkmark">
                <i class="fas fa-check"></i>
            </div>
            <h3>Successfully Submitted!</h3>
            <p class="message">Your vehicle has been registered successfully!</p>
            <p>Confirmation email will be sent soon after Admin approval.</p>
            <div class="email-note">
                <i class="fas fa-envelope"></i>
                A confirmation email will be sent to your registered email address once admin approves your vehicle.
            </div>
            <button class="btn-ok" onclick="closeSuccessPopup()">
                <i class="fas fa-check me-2"></i> OK
            </button>
        </div>
    </div>

    <!-- Update Success Popup Modal -->
    <div class="success-popup update-popup" id="updateSuccessPopup">
        <div class="success-popup-content">
            <div class="success-checkmark">
                <i class="fas fa-sync-alt"></i>
            </div>
            <h3>Vehicle Updated Successfully!</h3>
            <p class="message">Your vehicle information has been updated successfully!</p>
            <p>It will be available after admin verification.</p>
            <div class="email-note">
                <i class="fas fa-envelope"></i>
                You will receive an email confirmation soon about this update.
            </div>
            <button class="btn-ok" onclick="closeUpdateSuccessPopup()">
                <i class="fas fa-check me-2"></i> OK
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentVehicleData = null;
        
        // ═══════════════════════════════════════════════════════════
        // ── SmartCard Auto-Fill Feature ──────────────────────────
        // ═══════════════════════════════════════════════════════════
        
        window.autoFillVehicleForm = function(data) {
            // Open the Add Vehicle modal
            const modal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
            modal.show();
            
            // Wait for modal to open then fill
            setTimeout(() => {
                fillVehicleFormFromSmartcard(data);
            }, 400);
        };
        
        function fillVehicleFormFromSmartcard(data) {
            if (!data) return;
            
            const form = document.getElementById('addVehicleForm');
            if (!form) return;
            
            // Fill each field if data exists
            const fields = {
                'vehicle_number'  : data.vehicle_number,
                'chassis_number'  : data.chassis_number,
                'vehicle_type'    : data.vehicle_type || (data.make ? `${data.make} ${data.model || ''}`.trim() : null),
                'weight_capacity' : null,
                'can_carry'       : null,
            };
            
            Object.entries(fields).forEach(([name, value]) => {
                if (value) {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input) {
                        input.value = value;
                        // Highlight auto-filled fields
                        input.classList.add('auto-filled-field');
                        input.style.background = 'linear-gradient(135deg, rgba(39,174,96,0.08), rgba(39,174,96,0.02))';
                        input.style.borderColor = '#27ae60';
                        
                        // Remove highlight after 5 seconds
                        setTimeout(() => {
                            input.classList.remove('auto-filled-field');
                            input.style.background = '';
                            input.style.borderColor = '';
                        }, 5000);
                    }
                }
            });
            
            // Check if alert already exists
            const existingAlert = document.querySelector('#addVehicleModal .modal-body .alert-success');
            if (!existingAlert) {
                // Show success alert inside modal
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.style.borderRadius = '10px';
                alertDiv.style.animation = 'slideIn 0.5s ease-out';
                alertDiv.innerHTML = `
                    <i class="fas fa-magic me-2"></i>
                    <strong>AI Auto-Fill!</strong> SmartCard se vehicle details fill ho gayi hain. 
                    <strong>Weight Capacity, Can Carry aur images manually fill karein.</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const modalBody = document.querySelector('#addVehicleModal .modal-body');
                modalBody.insertBefore(alertDiv, modalBody.firstChild);
                
                // Auto remove alert after 8 seconds
                setTimeout(() => {
                    if (alertDiv) alertDiv.remove();
                }, 8000);
            }
            
            // Clear stored data
            sessionStorage.removeItem('extracted_vehicle_data');
        }
        
        // Check on page load if there's extracted data waiting
        document.addEventListener('DOMContentLoaded', function() {
            const stored = sessionStorage.getItem('extracted_vehicle_data');
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    // Small delay to let page fully load
                    setTimeout(() => {
                        if (confirm('🚛 AI ne vehicle details detect ki hain! Registration form auto-fill karein?')) {
                            window.autoFillVehicleForm(data);
                        } else {
                            sessionStorage.removeItem('extracted_vehicle_data');
                        }
                    }, 800);
                } catch(e) {
                    sessionStorage.removeItem('extracted_vehicle_data');
                }
            }
        });
        
        // File upload preview
        document.getElementById('vehicle_image_input')?.addEventListener('change', function(e) {
            if(this.files.length > 0) {
                const parent = this.parentElement;
                parent.innerHTML = `<i class="fas fa-check text-success"></i><h5>Image Selected</h5><p class="text-muted">${this.files[0].name}</p>`;
                parent.appendChild(this);
            }
        });
        
        document.getElementById('smartcard_image_input')?.addEventListener('change', function(e) {
            if(this.files.length > 0) {
                const parent = this.parentElement;
                parent.innerHTML = `<i class="fas fa-check text-success"></i><h5>Document Selected</h5><p class="text-muted">${this.files[0].name}</p>`;
                parent.appendChild(this);
            }
        });
        
        // Show registration success popup
        function showSuccessPopup() {
            const popup = document.getElementById('successPopup');
            popup.classList.add('show');
            const checkmark = document.querySelector('#successPopup .success-checkmark');
            checkmark.style.animation = 'none';
            setTimeout(() => {
                checkmark.style.animation = 'bounce 0.5s ease-out';
            }, 10);
        }
        
        // Close registration success popup
        function closeSuccessPopup() {
            const popup = document.getElementById('successPopup');
            popup.classList.remove('show');
            window.location.reload();
        }
        
        // Show update success popup
        function showUpdateSuccessPopup() {
            const popup = document.getElementById('updateSuccessPopup');
            popup.classList.add('show');
            const checkmark = document.querySelector('#updateSuccessPopup .success-checkmark');
            checkmark.style.animation = 'none';
            setTimeout(() => {
                checkmark.style.animation = 'bounce 0.5s ease-out';
            }, 10);
        }
        
        // Close update success popup
        function closeUpdateSuccessPopup() {
            const popup = document.getElementById('updateSuccessPopup');
            popup.classList.remove('show');
            window.location.reload();
        }
        
        // Submit vehicle form
        function submitVehicleForm() {
            const form = document.getElementById('addVehicleForm');
            const formData = new FormData(form);
            const submitBtn = document.querySelector('#addVehicleModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            
            const vehicleImage = document.getElementById('vehicle_image_input').files[0];
            const smartcardImage = document.getElementById('smartcard_image_input').files[0];
            
            if(!vehicleImage) {
                alert('Please upload vehicle image');
                return;
            }
            if(!smartcardImage) {
                alert('Please upload smart card image');
                return;
            }
            if(vehicleImage.size > 4 * 1024 * 1024) {
                alert('Vehicle image must be less than 4MB');
                return;
            }
            if(smartcardImage.size > 4 * 1024 * 1024) {
                alert('Smart card image must be less than 4MB');
                return;
            }
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';
            submitBtn.disabled = true;
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(() => {
                        return { success: true, message: 'Vehicle registered successfully!' };
                    });
                }
            })
            .then(data => {
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addVehicleModal'));
                    modal.hide();
                    form.reset();
                    document.getElementById('vehicle_image_input').parentElement.innerHTML = `
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h5>Upload Vehicle Image</h5>
                        <p class="text-muted">Click to upload (JPG, PNG, max 4MB)</p>
                        <input type="file" id="vehicle_image_input" name="vehicle_image" accept="image/*" style="display: none;" required>
                    `;
                    document.getElementById('smartcard_image_input').parentElement.innerHTML = `
                        <i class="fas fa-file-upload"></i>
                        <h5>Upload Documents</h5>
                        <p class="text-muted">Registration book, insurance (JPG, PNG, max 4MB)</p>
                        <input type="file" id="smartcard_image_input" name="smartcard_image" accept="image/*" style="display: none;" required>
                    `;
                    showSuccessPopup();
                } else {
                    throw new Error(data.message || 'Failed to register vehicle');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Show vehicle details with full information
        async function showVehicleDetails(vehicleId) {
            const modal = new bootstrap.Modal(document.getElementById('vehicleDetailModal'));
            const modalBody = document.getElementById('vehicleDetailModalBody');
            
            modalBody.innerHTML = `
                <div class="text-center p-4">
                    <div class="loading-spinner"></div>
                    <p class="mt-3">Loading vehicle details...</p>
                </div>
            `;
            modal.show();
            
            try {
                const response = await fetch(`/vehicle/${vehicleId}`);
                const vehicle = await response.json();
                currentVehicleData = vehicle;
                
                const statusClass = vehicle.status === 'approved' ? 'success' : (vehicle.status === 'pending' ? 'warning' : 'danger');
                const statusIcon = vehicle.status === 'approved' ? 'check-circle' : (vehicle.status === 'pending' ? 'clock' : 'ban');
                const statusText = vehicle.status === 'approved' ? 'Active' : (vehicle.status === 'pending' ? 'Pending Approval' : 'Inactive');
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-5">
                            <div class="vehicle-info-section">
                                <img src="/uploads/vehicles/${vehicle.vehicle_image}" class="img-fluid rounded mb-3" style="width: 100%; max-height: 250px; object-fit: cover;">
                                ${vehicle.smartcard_image ? `
                                    <div class="mt-3">
                                        <h6><i class="fas fa-file-alt me-2"></i>Document Image</h6>
                                        <img src="/uploads/smartcards/${vehicle.smartcard_image}" class="img-fluid rounded" style="width: 100%; max-height: 150px; object-fit: cover;">
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="vehicle-info-section">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h4 class="mb-0">${escapeHtml(vehicle.vehicle_type)}</h4>
                                    <span class="badge bg-${statusClass} fs-6">
                                        <i class="fas fa-${statusIcon} me-1"></i> ${statusText}
                                    </span>
                                </div>
                                <p class="text-muted">${escapeHtml(vehicle.vehicle_number)}</p>
                                
                                <h6 class="mt-4"><i class="fas fa-info-circle me-2"></i>Vehicle Information</h6>
                                <div class="info-row">
                                    <div class="info-label">Registration Number:</div>
                                    <div class="info-value"><strong>${escapeHtml(vehicle.vehicle_number)}</strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Chassis Number:</div>
                                    <div class="info-value">${escapeHtml(vehicle.chassis_number)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Vehicle Type:</div>
                                    <div class="info-value">${escapeHtml(vehicle.vehicle_type)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Weight Capacity:</div>
                                    <div class="info-value">${vehicle.weight_capacity} kg</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Can Carry:</div>
                                    <div class="info-value">${escapeHtml(vehicle.can_carry)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Booked Status:</div>
                                    <div class="info-value">
                                        <span class="badge bg-${vehicle.is_booked === 'yes' ? 'warning' : 'secondary'}">
                                            ${vehicle.is_booked === 'yes' ? 'Currently Booked' : 'Available'}
                                        </span>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Registration Date:</div>
                                    <div class="info-value">${new Date(vehicle.created_at).toLocaleDateString()}</div>
                                </div>
                            </div>
                            
                            <div class="vehicle-info-section">
                                <h6><i class="fas fa-chart-line me-2"></i>Booking Statistics</h6>
                                <div class="booking-stats">
                                    <div class="stat-box">
                                        <div class="number">${vehicle.total_bookings || 0}</div>
                                        <div class="label">Total Bookings</div>
                                    </div>
                                    <div class="stat-box">
                                        <div class="number">${vehicle.completed_bookings || 0}</div>
                                        <div class="label">Completed</div>
                                    </div>
                                    <div class="stat-box">
                                        <div class="number">${vehicle.cancelled_bookings || 0}</div>
                                        <div class="label">Cancelled</div>
                                    </div>
                                </div>
                            </div>
                            
                            ${vehicle.status === 'pending' ? `
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Pending Approval:</strong> Your vehicle is under review. You will receive an email once approved.
                                </div>
                            ` : vehicle.status === 'approved' ? `
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Active Vehicle:</strong> Your vehicle is ready to accept bookings!
                                </div>
                            ` : `
                                <div class="alert alert-danger">
                                    <i class="fas fa-ban me-2"></i>
                                    <strong>Inactive Vehicle:</strong> This vehicle is currently disabled. You can enable it from the details page.
                                </div>
                            `}
                        </div>
                    </div>
                `;
                
                // Add edit button event
                document.getElementById('editVehicleFromDetailBtn').onclick = function() {
                    editVehicle(vehicleId);
                };
                
            } catch (error) {
                console.error('Error:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load vehicle details. Please try again.
                        <button class="btn btn-sm btn-outline-danger mt-3" onclick="showVehicleDetails(${vehicleId})">
                            <i class="fas fa-redo me-1"></i> Try Again
                        </button>
                    </div>
                `;
            }
        }
        
        // Edit vehicle
        async function editVehicle(vehicleId) {
            try {
                const response = await fetch(`/vehicle/${vehicleId}`);
                if (!response.ok) throw new Error('Failed to fetch vehicle details');
                const vehicle = await response.json();
                
                if (vehicle.is_booked === 'yes') {
                    alert('⚠️ Cannot edit vehicle information. Vehicle is currently booked and cannot be modified.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('vehicleDetailModal'));
                    modal.hide();
                    return;
                }
                
                await loadEditVehicleForm(vehicleId);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            }
        }
        
        // Load edit vehicle form
        async function loadEditVehicleForm(vehicleId) {
            try {
                const modalBody = document.getElementById('editVehicleModalBody');
                modalBody.innerHTML = `
                    <div class="text-center p-4">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading edit form...</p>
                    </div>
                `;
                
                const detailsModal = bootstrap.Modal.getInstance(document.getElementById('vehicleDetailModal'));
                detailsModal.hide();
                
                const editModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
                editModal.show();
                
                const response = await fetch(`/vehicle/${vehicleId}/edit`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const vehicle = await response.json();
                
                const editForm = `
                    <form id="editVehicleForm" method="POST" action="/vehicle/${vehicleId}/update" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Number *</label>
                                <input type="text" class="form-control" name="vehicle_number" value="${escapeHtml(vehicle.vehicle_number || '')}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Type *</label>
                                <input type="text" class="form-control" name="vehicle_type" value="${escapeHtml(vehicle.vehicle_type || '')}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chassis Number *</label>
                                <input type="text" class="form-control" name="chassis_number" value="${escapeHtml(vehicle.chassis_number || '')}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Weight Capacity (kg) *</label>
                                <input type="number" class="form-control" name="weight_capacity" value="${vehicle.weight_capacity || ''}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Can Carry *</label>
                                <input type="text" class="form-control" name="can_carry" value="${escapeHtml(vehicle.can_carry || '')}" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Current Vehicle Image</label>
                                <div class="mb-2">
                                    <img src="/uploads/vehicles/${vehicle.vehicle_image}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                                <div class="file-upload" onclick="document.getElementById('edit_vehicle_image').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h5>Upload New Vehicle Image</h5>
                                    <p class="text-muted">Optional - Click to upload new image</p>
                                    <input type="file" id="edit_vehicle_image" name="vehicle_image" accept="image/*" style="display: none;">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Current Document Image</label>
                                <div class="mb-2">
                                    <img src="/uploads/smartcards/${vehicle.smartcard_image}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                                <div class="file-upload" onclick="document.getElementById('edit_smartcard_image').click()">
                                    <i class="fas fa-file-upload"></i>
                                    <h5>Upload New Document</h5>
                                    <p class="text-muted">Optional - Click to upload new document</p>
                                    <input type="file" id="edit_smartcard_image" name="smartcard_image" accept="image/*" style="display: none;">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> After updating, the status will be set to "Pending" and will require admin approval again.
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="submitEditVehicleForm(${vehicleId})">
                                <i class="fas fa-save me-2"></i> Update Vehicle
                            </button>
                        </div>
                    </form>
                `;
                
                modalBody.innerHTML = editForm;
                
                // Add file upload handlers
                setTimeout(() => {
                    const editVehicleImage = document.getElementById('edit_vehicle_image');
                    if (editVehicleImage) {
                        editVehicleImage.addEventListener('change', function(e) {
                            if(this.files.length > 0) {
                                const parent = this.parentElement;
                                parent.innerHTML = `<i class="fas fa-check text-success"></i><h5>New Image Selected</h5><p class="text-muted">${this.files[0].name}</p>`;
                                parent.appendChild(this);
                            }
                        });
                    }
                    
                    const editSmartcardImage = document.getElementById('edit_smartcard_image');
                    if (editSmartcardImage) {
                        editSmartcardImage.addEventListener('change', function(e) {
                            if(this.files.length > 0) {
                                const parent = this.parentElement;
                                parent.innerHTML = `<i class="fas fa-check text-success"></i><h5>New Document Selected</h5><p class="text-muted">${this.files[0].name}</p>`;
                                parent.appendChild(this);
                            }
                        });
                    }
                }, 100);
                
            } catch (error) {
                console.error('Error loading edit form:', error);
                const modalBody = document.getElementById('editVehicleModalBody');
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Error Loading Edit Form</h5>
                        <p>${error.message}</p>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadEditVehicleForm(${vehicleId})">
                            <i class="fas fa-redo me-1"></i> Try Again
                        </button>
                    </div>
                `;
            }
        }
        
        // Submit edit vehicle form
        function submitEditVehicleForm(vehicleId) {
            const form = document.getElementById('editVehicleForm');
            
            if (form.checkValidity()) {
                if (confirm('Are you sure you want to update this vehicle? The status will be set to pending for admin approval.')) {
                    const submitBtn = form.querySelector('.btn-primary');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<div class="loading-spinner me-2"></div> Updating...';
                    submitBtn.disabled = true;
                    
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Close edit modal
                            bootstrap.Modal.getInstance(document.getElementById('editVehicleModal')).hide();
                            
                            // Show update success popup instead of alert
                            showUpdateSuccessPopup();
                        } else {
                            throw new Error(data.message || 'Failed to update vehicle');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                }
            } else {
                form.reportValidity();
            }
        }
        
        // Update vehicle status
        async function updateVehicleStatus(vehicleId, newStatus) {
            const statusText = newStatus === 'active' ? 'enable' : 'disable';
            if (!confirm(`Are you sure you want to ${statusText} this vehicle?`)) {
                return;
            }
            
            try {
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="loading-spinner"></span>';
                button.disabled = true;
                
                const response = await fetch(`/vehicle/${vehicleId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const result = await response.json();
                
                if(result.success) {
                    const messageContainer = document.getElementById('messageContainer');
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success alert-dismissible fade show';
                    successMsg.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> ${result.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    messageContainer.insertBefore(successMsg, messageContainer.firstChild);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update vehicle status: ' + error.message);
                const button = event.target;
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
        
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function(e) {
            const searchTerm = this.value.toLowerCase();
            const vehicles = document.querySelectorAll('.vehicle-item');
            
            vehicles.forEach(vehicle => {
                const text = vehicle.innerText.toLowerCase();
                vehicle.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => alert.remove(), 5000);
            });
        }, 100);
        
        // Helper function
        function escapeHtml(unsafe) {
            if(!unsafe) return '';
            return unsafe.replace(/[&<>]/g, function(m) {
                if(m === '&') return '&amp;';
                if(m === '<') return '&lt;';
                if(m === '>') return '&gt;';
                return m;
            });
        }
        
        // Close popup on ESC key
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                const regPopup = document.getElementById('successPopup');
                const updatePopup = document.getElementById('updateSuccessPopup');
                if(regPopup.classList.contains('show')) {
                    closeSuccessPopup();
                }
                if(updatePopup.classList.contains('show')) {
                    closeUpdateSuccessPopup();
                }
            }
        });
        
        // Close popup when clicking outside
        document.getElementById('successPopup')?.addEventListener('click', function(e) {
            if(e.target === this) {
                closeSuccessPopup();
            }
        });
        
        document.getElementById('updateSuccessPopup')?.addEventListener('click', function(e) {
            if(e.target === this) {
                closeUpdateSuccessPopup();
            }
        });
    </script>
</body>
</html>