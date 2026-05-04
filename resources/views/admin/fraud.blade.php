<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Fraud Detection System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --fraud-red: #e74c3c;
            --safe-green: #27ae60;
            --pending-yellow: #f39c12;
            --purple-start: #8e44ad;
            --purple-end: #6c3483;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf2 100%); 
            overflow-x: hidden;
        }

        /* Sidebar Styles */
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

        .sidebar .logo h3 { margin: 0; font-weight: 700; font-size: 1.5rem; }
        .sidebar .logo span { color: var(--secondary); }

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
            background: rgba(52,152,219,0.15);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link:hover:before,
        .sidebar .nav-link.active:before { transform: translateX(0); }

        .sidebar .nav-link.active {
            background: rgba(52,152,219,0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(52,152,219,0.3);
        }

        .sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }

        /* Main Content */
        .main-content { margin-left: 280px; transition: all 0.3s; min-height: 100vh; }

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
            transition: all 0.3s ease;
        }

        .topbar .search-box input:focus {
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
            border-color: var(--secondary);
            outline: none;
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
            border: 3px solid var(--secondary);
        }

        .content-area { padding: 30px; }

        /* Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            background: white;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }

        .card-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Stat Cards */
        .stat-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
            overflow: hidden;
            border-radius: 20px;
        }

        .stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .stat-card.fraud-stat:before { background: linear-gradient(90deg, #e74c3c, #c0392b); }
        .stat-card.safe-stat:before { background: linear-gradient(90deg, #27ae60, #229954); }
        .stat-card.pending-stat:before { background: linear-gradient(90deg, #f39c12, #d68910); }
        .stat-card.total-stat:before { background: linear-gradient(90deg, #3498db, #2980b9); }

        .stat-card i { font-size: 2.5rem; margin-bottom: 15px; }
        .stat-card .count { font-size: 2.2rem; font-weight: 800; margin: 10px 0; }
        .stat-card .label { color: #6c757d; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        .bg-fraud-light { background: linear-gradient(135deg, rgba(231,76,60,0.08) 0%, rgba(231,76,60,0.02) 100%); }
        .bg-safe-light { background: linear-gradient(135deg, rgba(39,174,96,0.08) 0%, rgba(39,174,96,0.02) 100%); }
        .bg-pending-light { background: linear-gradient(135deg, rgba(243,156,18,0.08) 0%, rgba(243,156,18,0.02) 100%); }
        .bg-total-light { background: linear-gradient(135deg, rgba(52,152,219,0.08) 0%, rgba(52,152,219,0.02) 100%); }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .cat-tab {
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            background: white;
            color: #6c757d;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .cat-tab i { margin-right: 8px; }

        .cat-tab.active {
            background: linear-gradient(135deg, var(--purple-start), var(--purple-end));
            color: white;
            box-shadow: 0 6px 20px rgba(142,68,173,0.3);
            transform: translateY(-2px);
        }

        .cat-tab.pending-tab.active { background: linear-gradient(135deg, #f39c12, #e67e22); box-shadow: 0 6px 20px rgba(243,156,18,0.3); }
        .cat-tab.fraud-tab.active { background: linear-gradient(135deg, #e74c3c, #c0392b); box-shadow: 0 6px 20px rgba(231,76,60,0.3); }
        .cat-tab.safe-tab.active { background: linear-gradient(135deg, #27ae60, #1e8449); box-shadow: 0 6px 20px rgba(39,174,96,0.3); }

        .tab-count {
            display: inline-block;
            background: rgba(0,0,0,0.1);
            border-radius: 30px;
            padding: 2px 10px;
            font-size: 0.75rem;
            margin-left: 8px;
        }

        .active .tab-count { background: rgba(255,255,255,0.25); }

        /* Vehicle Grid */
        .vehicle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
        }

        .vehicle-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
        }

        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .vehicle-card.fraud-card { border-left: 4px solid #e74c3c; }
        .vehicle-card.safe-card { border-left: 4px solid #27ae60; }
        .vehicle-card.pending-card { border-left: 4px solid #f39c12; }

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
            padding: 6px 14px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
        }

        .badge-fraud { background: rgba(231,76,60,0.95); color: white; }
        .badge-safe { background: rgba(39,174,96,0.95); color: white; }
        .badge-pending { background: rgba(243,156,18,0.95); color: white; }

        .vehicle-info { padding: 20px; }

        .vehicle-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        .vehicle-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .detail-item { display: flex; flex-direction: column; }
        .detail-label { font-size: 0.7rem; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
        .detail-value { font-weight: 600; font-size: 0.85rem; color: #2c3e50; margin-top: 2px; }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.3s ease;
            font-size: 0.8rem;
            border: none;
        }

        .btn-ai-scan {
            background: linear-gradient(135deg, #8e44ad, #6c3483);
            color: white;
            box-shadow: 0 2px 8px rgba(142,68,173,0.3);
        }

        .btn-ai-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(142,68,173,0.4);
            color: white;
        }

        .btn-quick {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 2px 8px rgba(52,152,219,0.3);
        }

        .btn-quick:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52,152,219,0.4);
            color: white;
        }

        .btn-detail {
            background: white;
            border: 2px solid #3498db;
            color: #3498db;
        }

        .btn-detail:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #1e8449);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-analyze-all {
            background: linear-gradient(135deg, #8e44ad, #6c3483);
            color: white;
            padding: 12px 24px;
            font-size: 0.9rem;
        }

        .btn-analyze-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(142,68,173,0.4);
            color: white;
        }

        /* Modal */
        .detail-modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .detail-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .detail-modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 850px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .detail-modal.active .detail-modal-content { transform: translateY(0); }

        .detail-modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8f9fa, white);
        }

        .detail-modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .detail-modal-close {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .detail-modal-close:hover { color: #e74c3c; transform: scale(1.1); }

        .detail-modal-body {
            padding: 25px;
            overflow-y: auto;
            flex: 1;
        }

        .detail-section { margin-bottom: 25px; }
        .detail-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2c3e50;
            border-left: 3px solid #3498db;
            padding-left: 12px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        .detail-item-large {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .detail-label-large {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block;
            margin-bottom: 4px;
        }

        .detail-value-large {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
        }

        .detail-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-image-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .detail-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .detail-image-label {
            padding: 10px;
            text-align: center;
            background: #f8f9fa;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .extracted-tag {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .mismatch-tag {
            background: #ffebee;
            color: #c62828;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        .empty-state h4 { color: #4a5568; margin-bottom: 10px; }
        .empty-state p { color: #a0aec0; }

        /* Footer */
        .footer {
            background: rgba(255,255,255,0.9);
            padding: 18px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link { padding: 15px; text-align: center; margin: 5px 10px; }
            .sidebar .nav-link i { margin-right: 0; }
            .main-content, .footer { margin-left: 80px; }
            .content-area { padding: 20px 15px; }
            .vehicle-grid { grid-template-columns: 1fr; }
            .detail-images { grid-template-columns: 1fr; }
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
            <a class="nav-link" href="{{route('admin.login')}}">
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
            <a class="nav-link active" href="{{route('fraud.pendingVehicles')}}">
                <i class="fas fa-shield-alt"></i> <span>Fraud Detection</span>
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
            <input type="text" class="form-control" id="searchInput" placeholder="Search vehicles, chassis, owners...">
        </div>
        <div class="user-info d-flex align-items-center">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://via.placeholder.com/45" alt="Admin">
                    <span class="ms-2 d-none d-sm-inline fw-semibold">Admin User</span>
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

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold"><i class="fas fa-shield-alt me-2 text-danger"></i>Fraud Detection System</h4>
                <p class="text-muted mb-0">AI-powered smartcard verification with advanced fraud detection algorithms</p>
            </div>
            <button class="btn btn-analyze-all" onclick="analyzeAllPending()">
                <i class="fas fa-robot me-2"></i> Analyze All Pending
            </button>
        </div>

        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card pending-stat bg-pending-light">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning"></i>
                        <div class="count text-warning" id="pendingCount">{{ count($pendingVehicles) }}</div>
                        <div class="label">Pending Verification</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card fraud-stat bg-fraud-light">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        <div class="count text-danger" id="fraudCount">{{ $fraudCount ?? 0 }}</div>
                        <div class="label">Fraud Detected</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card safe-stat bg-safe-light">
                    <div class="card-body">
                        <i class="fas fa-check-shield text-success"></i>
                        <div class="count text-success" id="notFraudCount">{{ $notFraudCount ?? 0 }}</div>
                        <div class="label">Not Fraud (Verified)</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card total-stat bg-total-light">
                    <div class="card-body">
                        <i class="fas fa-truck text-primary"></i>
                        <div class="count text-primary">{{ $totalCount ?? 0 }}</div>
                        <div class="label">Total Vehicles</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scoring System Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                <span>Fraud Detection Scoring System</span>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#fff3cd,#ffeaa7)"><div class="fw-bold fs-3 text-warning">50</div><div class="small text-muted">Chassis Number</div><span class="badge bg-warning bg-opacity-25 text-warning mt-1">Must Match</span></div></div>
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#cce5ff,#b8daff)"><div class="fw-bold fs-3 text-primary">30</div><div class="small text-muted">Registration No</div><span class="badge bg-primary bg-opacity-25 text-primary mt-1">90% Similarity</span></div></div>
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#e2d9f3,#d4c5f0)"><div class="fw-bold fs-3" style="color:#6f42c1">10</div><div class="small text-muted">Image Quality</div><span class="badge bg-secondary bg-opacity-25 mt-1">≥100KB</span></div></div>
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#d4edda,#c3e6cb)"><div class="fw-bold fs-3 text-success">5</div><div class="small text-muted">Owner Name</div><span class="badge bg-success bg-opacity-25 text-success mt-1">70% Similar</span></div></div>
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#f8d7da,#f5c6cb)"><div class="fw-bold fs-3 text-danger">5</div><div class="small text-muted">Vehicle Type</div><span class="badge bg-danger bg-opacity-25 text-danger mt-1">Match Required</span></div></div>
                    <div class="col-md-2 col-6"><div class="p-3 rounded-3" style="background:linear-gradient(135deg,#2c3e50,#34495e);color:white"><div class="fw-bold fs-3">≥60</div><div class="small text-white-50">Threshold</div><span class="badge bg-success mt-1">NOT FRAUD</span></div></div>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <button class="cat-tab pending-tab active" onclick="switchTab('pending')">
                <i class="fas fa-clock"></i> Pending Analysis
                <span class="tab-count" id="tab-pending-count">{{ count($pendingVehicles) }}</span>
            </button>
            <button class="cat-tab fraud-tab" onclick="switchTab('fraud')">
                <i class="fas fa-exclamation-triangle"></i> Fraud Detected
                <span class="tab-count" id="tab-fraud-count">{{ $fraudCount ?? 0 }}</span>
            </button>
            <button class="cat-tab safe-tab" onclick="switchTab('notfraud')">
                <i class="fas fa-check-circle"></i> Not Fraud
                <span class="tab-count" id="tab-notfraud-count">{{ $notFraudCount ?? 0 }}</span>
            </button>
        </div>

        <!-- Tab: Pending Analysis -->
        <div id="tab-pending" class="tab-content-panel">
            <div class="vehicle-grid" id="pendingGrid">
                @if($pendingVehicles->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-check-circle text-success"></i>
                        <h4>No Pending Vehicles</h4>
                        <p>All vehicles have been analyzed. Check fraud/not-fraud tabs.</p>
                    </div>
                @else
                    @foreach($pendingVehicles as $vehicle)
                    <div class="vehicle-item" data-id="{{ $vehicle->id }}" data-tab="pending">
                        <div class="vehicle-card pending-card" id="vcard-{{ $vehicle->id }}">
                            <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}');">
                                <div class="vehicle-badge badge-pending"><i class="fas fa-hourglass-half me-1"></i> Pending</div>
                            </div>
                            <div class="vehicle-info">
                                <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }} • {{ strtoupper($vehicle->vehicle_number) }}</div>
                                <div class="vehicle-details">
                                    <div class="detail-item"><span class="detail-label">Owner</span><span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span></div>
                                    <div class="detail-item"><span class="detail-label">Capacity</span><span class="detail-value">{{ $vehicle->weight_capacity ?? 'N/A' }} kg</span></div>
                                    <div class="detail-item"><span class="detail-label">Chassis</span><span class="detail-value" style="font-size:0.7rem">{{ $vehicle->chassis_number ?? 'N/A' }}</span></div>
                                    <div class="detail-item"><span class="detail-label">Can Carry</span><span class="detail-value">{{ $vehicle->can_carry ?? 'N/A' }}</span></div>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-ai-scan" onclick="aiScanVehicle({{ $vehicle->id }}, this)"><i class="fas fa-robot me-1"></i> AI Scan</button>
                                    <button class="btn btn-quick" onclick="quickAnalyze({{ $vehicle->id }}, this)"><i class="fas fa-bolt me-1"></i> Quick</button>
                                    <button class="btn btn-detail" onclick="viewDetails({{ $vehicle->id }})"><i class="fas fa-eye"></i> Details</button>
                                </div>
                                <div class="action-buttons mt-2">
                                    <button class="btn btn-success btn-sm" onclick="approveVehicle({{ $vehicle->id }})"><i class="fas fa-check me-1"></i> Approve</button>
                                    <button class="btn btn-danger btn-sm" onclick="rejectVehicle({{ $vehicle->id }})"><i class="fas fa-times me-1"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Tab: Fraud -->
        <div id="tab-fraud" class="tab-content-panel d-none">
            <div class="vehicle-grid" id="fraudGrid">
                @forelse($fraudVehicles as $vehicle)
                <div class="vehicle-item" data-id="{{ $vehicle->id }}" data-tab="fraud">
                    <div class="vehicle-card fraud-card">
                        <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}');">
                            <div class="vehicle-badge badge-fraud"><i class="fas fa-exclamation-triangle me-1"></i> FRAUD</div>
                        </div>
                        <div class="vehicle-info">
                            <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }} • {{ strtoupper($vehicle->vehicle_number) }}</div>
                            <div class="vehicle-details">
                                <div class="detail-item"><span class="detail-label">Owner</span><span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span></div>
                                <div class="detail-item"><span class="detail-label">Capacity</span><span class="detail-value">{{ $vehicle->weight_capacity ?? 'N/A' }} kg</span></div>
                                <div class="detail-item"><span class="detail-label">Chassis</span><span class="detail-value" style="font-size:0.7rem">{{ $vehicle->chassis_number ?? 'N/A' }}</span></div>
                                <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value text-capitalize">{{ $vehicle->status }}</span></div>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-ai-scan" onclick="aiScanVehicle({{ $vehicle->id }}, this)"><i class="fas fa-redo me-1"></i> Re-Scan</button>
                                <button class="btn btn-detail" onclick="viewDetails({{ $vehicle->id }})"><i class="fas fa-eye me-1"></i> Details</button>
                                <button class="btn btn-success btn-sm" onclick="approveVehicle({{ $vehicle->id }})"><i class="fas fa-check"></i></button>
                                <button class="btn btn-danger btn-sm" onclick="rejectVehicle({{ $vehicle->id }})"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-shield-check text-success"></i><h4>No Fraud Detected</h4><p>No vehicles have been flagged as fraud yet.</p></div>
                @endforelse
            </div>
        </div>

        <!-- Tab: Not Fraud -->
        <div id="tab-notfraud" class="tab-content-panel d-none">
            <div class="vehicle-grid" id="notFraudGrid">
                @forelse($notFraudVehicles as $vehicle)
                <div class="vehicle-item" data-id="{{ $vehicle->id }}" data-tab="notfraud">
                    <div class="vehicle-card safe-card">
                        <div class="vehicle-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}');">
                            <div class="vehicle-badge badge-safe"><i class="fas fa-check-circle me-1"></i> VERIFIED</div>
                        </div>
                        <div class="vehicle-info">
                            <div class="vehicle-title">{{ ucfirst($vehicle->vehicle_type) }} • {{ strtoupper($vehicle->vehicle_number) }}</div>
                            <div class="vehicle-details">
                                <div class="detail-item"><span class="detail-label">Owner</span><span class="detail-value">{{ $vehicle->user->name ?? 'N/A' }}</span></div>
                                <div class="detail-item"><span class="detail-label">Capacity</span><span class="detail-value">{{ $vehicle->weight_capacity ?? 'N/A' }} kg</span></div>
                                <div class="detail-item"><span class="detail-label">Chassis</span><span class="detail-value" style="font-size:0.7rem">{{ $vehicle->chassis_number ?? 'N/A' }}</span></div>
                                <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value text-capitalize">{{ $vehicle->status }}</span></div>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-ai-scan" onclick="aiScanVehicle({{ $vehicle->id }}, this)"><i class="fas fa-redo me-1"></i> Re-Scan</button>
                                <button class="btn btn-detail" onclick="viewDetails({{ $vehicle->id }})"><i class="fas fa-eye me-1"></i> Details</button>
                                <button class="btn btn-success btn-sm" onclick="approveVehicle({{ $vehicle->id }})"><i class="fas fa-check me-1"></i> Approve</button>
                                <button class="btn btn-danger btn-sm" onclick="rejectVehicle({{ $vehicle->id }})"><i class="fas fa-times me-1"></i> Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-clock text-warning"></i><h4>No Verified Vehicles Yet</h4><p>Run AI scan on pending vehicles to verify them.</p></div>
                @endforelse
            </div>
        </div>

    </div><!-- /content-area -->

    <!-- Footer -->
    <div class="footer">
        <div class="row align-items-center">
            <div class="col-md-6"><p class="mb-0"><strong>© 2023 TruckLink: Verified Goods.</strong> All rights reserved.</p></div>
            <div class="col-md-6 text-end"><p class="mb-0 text-muted">Fraud Detection v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>AI Online</span></p></div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="detail-modal" id="detailModal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h3 class="detail-modal-title" id="detailModalTitle">Vehicle Details</h3>
            <button class="detail-modal-close" id="closeDetailModal">&times;</button>
        </div>
        <div class="detail-modal-body" id="detailModalBody"></div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Vehicle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectVehicleId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectionReason" rows="5" placeholder="Please provide a detailed reason for rejection..."></textarea>
                    <div class="mt-2 text-muted small"><i class="fas fa-info-circle me-1"></i> This reason will be emailed to the vehicle owner.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn"><i class="fas fa-times-circle me-2"></i> Confirm Rejection</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Vehicle data from server
let vehiclesData = {!! json_encode(
    collect($pendingVehicles->items())
        ->merge($fraudVehicles)
        ->merge($notFraudVehicles)
        ->unique('id')
        ->map(fn($v) => [
            'id' => $v->id,
            'vehicle_type' => $v->vehicle_type,
            'vehicle_number' => $v->vehicle_number,
            'weight_capacity' => $v->weight_capacity,
            'can_carry' => $v->can_carry,
            'chassis_number' => $v->chassis_number,
            'vehicle_image' => $v->vehicle_image,
            'smartcard_image' => $v->smartcard_image,
            'status' => $v->status,
            'fraud_status' => $v->fraud_status,
            'fraud_score' => $v->fraud_score,
            'fraud_reasons' => $v->fraud_reasons ? json_decode($v->fraud_reasons) : [],
            'smartcard_extracted' => $v->smartcard_extracted ? json_decode($v->smartcard_extracted, true) : null,
            'created_at' => $v->created_at ? $v->created_at->toISOString() : null,
            'user' => $v->user ? [
                'name' => $v->user->name,
                'email' => $v->user->email,
                'cnic' => $v->user->cnic,
                'role' => $v->user->role,
                'email_verified' => $v->user->email_verified,
            ] : null
        ])->values()
) !!};

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));

// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.add('d-none'));
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('d-none');
    if (tab === 'pending') document.querySelector('.pending-tab').classList.add('active');
    else if (tab === 'fraud') document.querySelector('.fraud-tab').classList.add('active');
    else if (tab === 'notfraud') document.querySelector('.safe-tab').classList.add('active');
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.vehicle-item').forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(query) ? '' : 'none';
    });
});

// AI Full Scan
async function aiScanVehicle(vehicleId, btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Scanning...';
    btn.disabled = true;

    try {
        const response = await fetch(`/fraud/extract-analyze/${vehicleId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.success) {
            await showFraudResult(vehicleId, data);
            updateVehicleCard(vehicleId, data);
            updateTabCounts();
        } else {
            Swal.fire('Error', data.message || 'Scan failed.', 'error');
        }
    } catch(e) {
        Swal.fire('Error', 'Network error: ' + e.message, 'error');
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// Quick Analyze
async function quickAnalyze(vehicleId, btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
    btn.disabled = true;

    try {
        const response = await fetch(`/fraud/analyze/${vehicleId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            await showFraudResult(vehicleId, data);
            updateVehicleCard(vehicleId, data);
            updateTabCounts();
        }
    } catch(e) {
        Swal.fire('Error', e.message, 'error');
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// Analyze All Pending
async function analyzeAllPending() {
    const { isConfirmed } = await Swal.fire({
        title: '🤖 Analyze All Pending Vehicles?',
        html: 'This will run AI fraud detection on all pending vehicles.<br><strong>This may take a few minutes.</strong>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Analyze All',
        confirmButtonColor: '#8e44ad',
        cancelButtonText: 'Cancel'
    });
    if (!isConfirmed) return;

    Swal.fire({ title: 'Analyzing...', html: 'Running fraud detection on all pending vehicles...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const response = await fetch('/fraud/analyze-all', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            await Swal.fire({
                title: 'Analysis Complete!',
                html: `✅ Analyzed: <strong>${data.analyzed}</strong> vehicles<br>⚠️ Fraud detected: <strong>${data.fraud_found}</strong>`,
                icon: 'success',
                confirmButtonColor: '#27ae60'
            });
            location.reload();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch(e) {
        Swal.fire('Error', e.message, 'error');
    }
}

// Show Fraud Result Popup
async function showFraudResult(vehicleId, data) {
    const icon = data.is_fraud ? 'warning' : 'success';
    const color = data.is_fraud ? '#e74c3c' : '#27ae60';
    const title = data.is_fraud ? '⚠️ FRAUD DETECTED' : '✅ NOT FRAUD';

    let reasonsHtml = '';
    if (data.reasons && data.reasons.length) {
        reasonsHtml = '<div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-top:15px;text-align:left;max-height:250px;overflow-y:auto">';
        data.reasons.forEach(r => {
            const iconClass = r.startsWith('✅') ? 'fa-check-circle' : (r.startsWith('⚠️') ? 'fa-exclamation-triangle' : 'fa-times-circle');
            const colorClass = r.startsWith('✅') ? '#27ae60' : (r.startsWith('⚠️') ? '#f39c12' : '#e74c3c');
            reasonsHtml += `<div style="color:${colorClass};font-size:0.85rem;padding:6px 0;display:flex;align-items:center;gap:10px;border-bottom:1px solid #eee;"><i class="fas ${iconClass}" style="width:20px"></i> ${escapeHtml(r)}</div>`;
        });
        reasonsHtml += '</div>';
    }

    let extractedHtml = '';
    if (data.extracted_data) {
        const e = data.extracted_data;
        extractedHtml = `<div style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:12px;padding:15px;margin-top:15px;text-align:left;font-size:0.85rem">
            <div style="font-weight:700;color:#1e8449;margin-bottom:10px;font-size:0.9rem"><i class="fas fa-robot me-2"></i> 🤖 AI Extracted from Smartcard:</div>
            <div class="row">
                ${e.vehicle_number ? `<div class="col-md-6 mb-2"><i class="fas fa-id-card me-2 text-primary"></i> <strong>Reg No:</strong> ${escapeHtml(e.vehicle_number)}</div>` : ''}
                ${e.chassis_number ? `<div class="col-md-6 mb-2"><i class="fas fa-microchip me-2 text-primary"></i> <strong>Chassis:</strong> ${escapeHtml(e.chassis_number)}</div>` : ''}
                ${e.owner_name ? `<div class="col-md-6 mb-2"><i class="fas fa-user me-2 text-primary"></i> <strong>Owner:</strong> ${escapeHtml(e.owner_name)}</div>` : ''}
                ${e.vehicle_type ? `<div class="col-md-6 mb-2"><i class="fas fa-truck me-2 text-primary"></i> <strong>Type:</strong> ${escapeHtml(e.vehicle_type)}</div>` : ''}
            </div>
        </div>`;
    }

    await Swal.fire({
        title: title,
        html: `<div style="text-align:center">
                   <div style="font-size:3.5rem;font-weight:800;color:${color}">${data.score}<span style="font-size:1.2rem">/100</span></div>
                   <div style="color:#6c757d;margin-bottom:8px;font-weight:500">Fraud Detection Score</div>
                   <div style="background:#e9ecef;height:10px;border-radius:10px;overflow:hidden;margin:15px 0">
                       <div style="height:100%;width:${data.score}%;background:${color};border-radius:10px;transition:width 1s"></div>
                   </div>
                   <div style="padding:8px 12px;background:${data.score >= 60 ? '#d4edda' : '#f8d7da'};border-radius:8px;display:inline-block;margin-top:5px">
                       <small style="color:${data.score >= 60 ? '#155724' : '#721c24'}">Threshold: 60/100 — ${data.score >= 60 ? 'PASS ✅' : 'FAIL ❌'}</small>
                   </div>
               </div>
               ${reasonsHtml}${extractedHtml}`,
        icon: icon,
        confirmButtonColor: color,
        confirmButtonText: 'Got it',
        width: '600px'
    });
}

// Update Vehicle Card - Only updates badge, no fraud banner
function updateVehicleCard(vehicleId, data) {
    const card = document.getElementById('vcard-' + vehicleId);
    if (!card) return;

    const isFraud = data.is_fraud;
    
    // Update card styling
    card.className = `vehicle-card ${isFraud ? 'fraud-card' : 'safe-card'}`;
    
    // Update badge only - NO FRAUD BANNER on the card
    const badge = card.querySelector('.vehicle-badge');
    if (badge) {
        badge.className = `vehicle-badge ${isFraud ? 'badge-fraud' : 'badge-safe'}`;
        badge.innerHTML = isFraud ? '<i class="fas fa-exclamation-triangle me-1"></i> FRAUD' : '<i class="fas fa-check-circle me-1"></i> VERIFIED';
    }
    
    // Update local data
    const vd = vehiclesData.find(v => v.id == vehicleId);
    if (vd) {
        vd.fraud_status = isFraud ? 'fraud' : 'not_fraud';
        vd.fraud_score = data.score;
        vd.fraud_reasons = data.reasons;
        if (data.extracted_data) vd.smartcard_extracted = data.extracted_data;
    }
}

// Update Tab Counts
function updateTabCounts() {
    const pendingCount = document.querySelectorAll('.vehicle-item[data-tab="pending"]').length;
    const fraudCount = document.querySelectorAll('.vehicle-item[data-tab="fraud"]').length;
    const notFraudCount = document.querySelectorAll('.vehicle-item[data-tab="notfraud"]').length;
    
    document.getElementById('tab-pending-count').textContent = pendingCount;
    document.getElementById('tab-fraud-count').textContent = fraudCount;
    document.getElementById('tab-notfraud-count').textContent = notFraudCount;
    document.getElementById('pendingCount').textContent = pendingCount;
    document.getElementById('fraudCount').textContent = fraudCount;
    document.getElementById('notFraudCount').textContent = notFraudCount;
}

// View Details Modal - Shows all information including fraud analysis
function viewDetails(vehicleId) {
    const vehicle = vehiclesData.find(v => v.id == vehicleId);
    if (!vehicle) { Swal.fire('Error', 'Vehicle not found', 'error'); return; }

    const vImg = vehicle.vehicle_image ? `{{ asset('uploads/vehicles') }}/${vehicle.vehicle_image}` : 'https://via.placeholder.com/400x200?text=No+Image';
    const sImg = vehicle.smartcard_image ? `{{ asset('uploads/smartcards') }}/${vehicle.smartcard_image}` : 'https://via.placeholder.com/400x200?text=No+SmartCard';

    let extractedHtml = '';
    if (vehicle.smartcard_extracted) {
        const e = vehicle.smartcard_extracted;
        const rows = [['Registration Number', e.vehicle_number], ['Chassis Number', e.chassis_number], ['Owner Name', e.owner_name], ['Vehicle Type', e.vehicle_type], ['Make', e.make], ['Year', e.year], ['Color', e.color]].filter(r => r[1]);
        if (rows.length) {
            extractedHtml = `<div class="detail-section"><h4 class="detail-section-title"><i class="fas fa-robot me-2 text-primary"></i>AI Extracted from Smartcard</h4><div class="detail-grid">${rows.map(([lbl, val]) => {
                let mismatch = false;
                if (lbl === 'Chassis Number' && vehicle.chassis_number && val) {
                    const a = val.replace(/[^A-Z0-9]/gi,'').toUpperCase();
                    const b = vehicle.chassis_number.replace(/[^A-Z0-9]/gi,'').toUpperCase();
                    if (a !== b) mismatch = true;
                }
                if (lbl === 'Registration Number' && vehicle.vehicle_number && val) {
                    const a = val.replace(/[\s\-]/g,'').toUpperCase();
                    const b = vehicle.vehicle_number.replace(/[\s\-]/g,'').toUpperCase();
                    if (a !== b) mismatch = true;
                }
                return `<div class="detail-item-large"><span class="detail-label-large">${escapeHtml(lbl)}</span><span class="detail-value-large">${mismatch ? `<span class="mismatch-tag"><i class="fas fa-times-circle me-1"></i>${escapeHtml(String(val))}</span>` : `<span class="extracted-tag"><i class="fas fa-check-circle me-1"></i>${escapeHtml(String(val))}</span>`}</span></div>`;
            }).join('')}</div></div>`;
        }
    }

    let fraudHtml = '';
    if (vehicle.fraud_status) {
        const isFraud = vehicle.fraud_status === 'fraud';
        const reasons = Array.isArray(vehicle.fraud_reasons) ? vehicle.fraud_reasons : [];
        const score = vehicle.fraud_score || 0;
        
        fraudHtml = `<div class="detail-section">
            <h4 class="detail-section-title"><i class="fas fa-shield-alt me-2 ${isFraud ? 'text-danger' : 'text-success'}"></i>Fraud Analysis Result</h4>
            <div style="background:${isFraud ? '#fff5f5' : '#f0fff4'};border-radius:12px;padding:15px;margin-bottom:15px;border-left:4px solid ${isFraud ? '#e74c3c' : '#27ae60'}">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                    <div style="font-weight:700;font-size:1rem;color:${isFraud ? '#c0392b' : '#1e8449'}">
                        ${isFraud ? '<i class="fas fa-exclamation-triangle me-2"></i> FRAUD DETECTED' : '<i class="fas fa-check-circle me-2"></i> NOT FRAUD'}
                    </div>
                    <div style="font-weight:700;font-size:1.2rem;color:${isFraud ? '#e74c3c' : '#27ae60'}">Score: ${score}/100</div>
                </div>
                ${score > 0 ? `<div class="score-bar-wrap mt-3"><div class="score-bar-label"><span>Detection Score</span><span>${score}/100</span></div><div class="score-bar"><div class="score-bar-fill ${score >= 60 ? 'safe-fill' : 'fraud-fill'}" style="width:${score}%"></div></div></div>` : ''}
            </div>
            ${reasons.length ? `<div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-top:10px"><div style="font-weight:700;margin-bottom:10px"><i class="fas fa-list-ul me-2"></i>Detailed Analysis Results:</div>${reasons.map(r => { 
                const iconClass = r.startsWith('✅') ? 'fa-check-circle' : (r.startsWith('⚠️') ? 'fa-exclamation-triangle' : 'fa-times-circle');
                const cls = r.startsWith('✅') ? 'check-ok' : (r.startsWith('⚠️') ? 'check-warn' : 'check-fail');
                return `<div class="fraud-reason-item ${cls}" style="padding:6px 0"><i class="fas ${iconClass} fa-fw me-2"></i> ${escapeHtml(r)}</div>`;
            }).join('')}</div>` : ''}
        </div>`;
    }

    document.getElementById('detailModalTitle').innerHTML = `<i class="fas fa-truck me-2"></i>${escapeHtml(vehicle.vehicle_type?.toUpperCase() || 'Vehicle')} — ${escapeHtml(vehicle.vehicle_number?.toUpperCase() || 'N/A')}`;
    document.getElementById('detailModalBody').innerHTML = `
        ${fraudHtml}
        <div class="detail-section"><h4 class="detail-section-title"><i class="fas fa-info-circle me-2"></i>Vehicle Information</h4><div class="detail-grid">
            <div class="detail-item-large"><span class="detail-label-large">Vehicle Type</span><span class="detail-value-large">${escapeHtml(vehicle.vehicle_type||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Vehicle Number</span><span class="detail-value-large">${escapeHtml(vehicle.vehicle_number||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Weight Capacity</span><span class="detail-value-large">${vehicle.weight_capacity||0} kg</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Can Carry</span><span class="detail-value-large">${escapeHtml(vehicle.can_carry||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Chassis Number</span><span class="detail-value-large">${escapeHtml(vehicle.chassis_number||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Status</span><span class="detail-value-large"><span class="badge bg-warning">${escapeHtml(vehicle.status||'Pending')}</span></span></div>
        </div></div>
        <div class="detail-section"><h4 class="detail-section-title"><i class="fas fa-user-circle me-2"></i>Service Provider</h4><div class="detail-grid">
            <div class="detail-item-large"><span class="detail-label-large">Name</span><span class="detail-value-large"><i class="fas fa-user me-1 text-primary"></i>${escapeHtml(vehicle.user?.name||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Email</span><span class="detail-value-large"><i class="fas fa-envelope me-1 text-info"></i>${escapeHtml(vehicle.user?.email||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">CNIC</span><span class="detail-value-large"><i class="fas fa-id-card me-1 text-warning"></i>${escapeHtml(vehicle.user?.cnic||'N/A')}</span></div>
            <div class="detail-item-large"><span class="detail-label-large">Email Verified</span><span class="detail-value-large">${vehicle.user?.email_verified ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-warning">Pending</span>'}</span></div>
        </div></div>
        ${extractedHtml}
        <div class="detail-section"><h4 class="detail-section-title"><i class="fas fa-images me-2"></i>Vehicle Images</h4><div class="detail-images">
            <div class="detail-image-container"><img src="${vImg}" class="detail-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'"><div class="detail-image-label"><i class="fas fa-truck me-1"></i> Vehicle Photo</div></div>
            <div class="detail-image-container"><img src="${sImg}" class="detail-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+SmartCard'"><div class="detail-image-label"><i class="fas fa-id-card me-1"></i> Smartcard Image</div></div>
        </div></div>
        <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
            <button class="btn btn-ai-scan" onclick="aiScanVehicle(${vehicle.id}, this); document.getElementById('detailModal').classList.remove('active')"><i class="fas fa-robot me-1"></i> AI Full Scan</button>
            <button class="btn btn-success" onclick="approveVehicle(${vehicle.id}); document.getElementById('detailModal').classList.remove('active')"><i class="fas fa-check me-1"></i> Approve</button>
            <button class="btn btn-danger" onclick="rejectVehicle(${vehicle.id}); document.getElementById('detailModal').classList.remove('active')"><i class="fas fa-times me-1"></i> Reject</button>
        </div>
    `;
    document.getElementById('detailModal').classList.add('active');
}

// Approve Vehicle
async function approveVehicle(vehicleId) {
    const { isConfirmed } = await Swal.fire({
        title: 'Approve Vehicle?',
        text: 'The owner will receive a confirmation email.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes, Approve'
    });
    if (!isConfirmed) return;

    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res = await fetch(`/admin/vehicle/${vehicleId}/approve`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ title: 'Approved!', html: data.message, icon: 'success', confirmButtonColor: '#28a745' });
            document.querySelector(`.vehicle-item[data-id="${vehicleId}"]`)?.remove();
            updateTabCounts();
        } else {
            throw new Error(data.message);
        }
    } catch(e) {
        Swal.fire('Error!', e.message, 'error');
    }
}

// Reject Vehicle
function rejectVehicle(vehicleId) {
    document.getElementById('rejectVehicleId').value = vehicleId;
    document.getElementById('rejectionReason').value = '';
    rejectModal.show();
}

document.getElementById('confirmRejectBtn').addEventListener('click', async function() {
    const vehicleId = document.getElementById('rejectVehicleId').value;
    const reason = document.getElementById('rejectionReason').value.trim();
    if (!reason) { Swal.fire('Required', 'Please provide a rejection reason.', 'warning'); return; }
    rejectModal.hide();
    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res = await fetch(`/admin/vehicle/${vehicleId}/reject`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ rejection_reason: reason })
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ title: 'Rejected!', html: data.message, icon: 'info', confirmButtonColor: '#dc3545' });
            document.querySelector(`.vehicle-item[data-id="${vehicleId}"]`)?.remove();
            updateTabCounts();
        } else { throw new Error(data.message); }
    } catch(e) { Swal.fire('Error!', e.message, 'error'); }
});

// Modal Close
document.getElementById('closeDetailModal').addEventListener('click', () => { document.getElementById('detailModal').classList.remove('active'); });
document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });

// HTML Escape
function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
</script>
</body>
</html>