<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Vehicle Owner Bookings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Routing Machine -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <!-- Rating CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
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
        
        /* Filter Buttons */
        .filter-btn {
            border: 2px solid #e9ecef;
            background: transparent;
            color: #6c757d;
            padding: 8px 20px;
            border-radius: 30px;
            margin: 0 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            border-color: var(--secondary);
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }
        
        .filter-btn.active {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }
        
        /* Booking Cards */
        .booking-card {
            padding: 20px;
            border-left: 4px solid var(--secondary);
            margin-bottom: 15px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .booking-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 30px 30px 0;
            border-color: transparent var(--secondary) transparent transparent;
            opacity: 0.1;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .booking-card.request { border-left-color: var(--warning); }
        .booking-card.accept { border-left-color: var(--success); }
        .booking-card.reject { border-left-color: var(--danger); }
        .booking-card.complete { border-left-color: var(--info); }
        
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .bg-warning { background-color: var(--warning); color: #000; }
        .bg-success { background-color: var(--success); color: #fff; }
        .bg-danger { background-color: var(--danger); color: #fff; }
        .bg-info { background-color: var(--info); color: #fff; }
        
        /* Buttons */
        .btn-blue-border {
            background: transparent !important;
            border: 2px solid #e9ecef !important;
            color: #6c757d !important;
            transition: all 0.3s ease;
            border-radius: 25px !important;
        }
        
        .btn-blue-border:hover {
            background: #3498db !important;
            color: white !important;
            border-color: #3498db !important;
            transform: translateY(-2px);
        }
        
        .btn-accept {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 5px 15px;
            font-size: 0.85rem;
        }
        
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
            color: white;
        }
        
        .btn-reject {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 5px 15px;
            font-size: 0.85rem;
        }
        
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
            color: white;
        }
        
        /* Timeline */
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
        }
        
        .timeline-item {
            position: relative;
            padding-left: 50px;
            margin-bottom: 25px;
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
            0% { box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(243, 156, 18, 0); }
            100% { box-shadow: 0 0 0 0 rgba(243, 156, 18, 0); }
        }
        
        /* Map Container */
        #detailMap {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid var(--secondary);
        }
        
        /* Turn by Turn Container */
        .turn-by-turn {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            max-height: 300px;
            overflow-y: auto;
            border-left: 4px solid var(--secondary);
        }
        
        .turn-by-turn h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .turn-item {
            display: flex;
            align-items: flex-start;
            padding: 12px;
            margin-bottom: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .turn-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        
        .turn-number {
            background: var(--secondary);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .turn-content {
            flex: 1;
        }
        
        .turn-instruction {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .turn-distance {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Customer Card */
        .customer-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--secondary);
            display: flex;
            align-items: center;
        }
        
        .customer-image {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary);
            margin-right: 15px;
        }
        
        /* Info Tabs */
        .info-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .info-tab {
            padding: 10px 20px;
            font-weight: 500;
            color: #6c757d;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }
        
        .info-tab:hover { color: var(--secondary); }
        .info-tab.active { color: var(--secondary); }
        .info-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary);
        }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Route Info Card */
        .route-info-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .route-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .route-info-label { color: #2c3e50; font-weight: 500; }
        .route-info-value { font-weight: 600; color: var(--secondary); }
        
        /* Fare Card */
        .fare-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
        }
        
        .fare-amount { font-size: 2rem; font-weight: 700; }
        .distance-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .rejection-reason {
            background: #fff3f3;
            border-left: 4px solid var(--danger);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .review-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
        }
        
        .complaint-item {
            background: #fff8f0;
            border-radius: 10px;
            padding: 12px;
            border-left: 3px solid var(--danger);
        }
        
        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }
            .main-content { margin-left: 80px; }
            .content-area { padding: 20px 15px; }
        }
        
        .modal-content { border-radius: 20px; border: none; }
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--secondary); }
        .stat-label { font-size: 0.85rem; color: #6c757d; }
        
        .map-container { height: 400px; border-radius: 12px; overflow: hidden; border: 2px solid #dee2e6; position: relative; }
        
        .complaints-table {
            font-size: 0.9rem;
        }
        
        .complaints-table td {
            vertical-align: middle;
        }
        
        .info-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .status-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
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
            <a class="nav-link " href="{{route('provider.login')}}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a class="nav-link" href="{{route('my.vehicle')}}"><i class="fas fa-truck"></i> <span>My Vehicles</span></a>
            <a class="nav-link" href="{{route('booking.requests')}}"><i class="fas fa-bell"></i> <span>Booking Requests</span></a>
            <a class="nav-link" href="{{route('see.trip')}}"><i class="fas fa-clipboard-list"></i> <span>Active Bookings</span></a>
            <a class="nav-link active" href="{{route('provider.bookings')}}"><i class="fas fa-money-bill-wave"></i> <span>All Bookings</span></a>
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
                <input type="text" class="form-control" id="searchInput" placeholder="Search bookings...">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ $userName ?? 'Provider' }}</span>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">My Bookings</h4>
                    <p class="text-muted mb-0">View and manage all booking requests for your vehicles</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3"><div class="card stat-item"><div class="stat-value" id="totalBookingsCount">0</div><div class="stat-label">Total Bookings</div></div></div>
                <div class="col-md-3"><div class="card stat-item"><div class="stat-value" id="pendingBookingsCount">0</div><div class="stat-label">Pending</div></div></div>
                <div class="col-md-3"><div class="card stat-item"><div class="stat-value" id="acceptedBookingsCount">0</div><div class="stat-label">Accepted</div></div></div>
                <div class="col-md-3"><div class="card stat-item"><div class="stat-value" id="completedBookingsCount">0</div><div class="stat-label">Completed</div></div></div>
            </div>

            <!-- Filter Buttons -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <span class="me-3 fw-semibold"><i class="fas fa-filter me-2"></i>Filter by:</span>
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="request">Pending</button>
                        <button class="filter-btn" data-filter="accept">Accepted</button>
                        <button class="filter-btn" data-filter="reject">Rejected</button>
                        <button class="filter-btn" data-filter="complete">Completed</button>
                    </div>
                </div>
            </div>

            <!-- Bookings Grid -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Booking List</h5>
                            <div class="d-flex align-items-center">
                                <div class="btn-group me-2">
                                    <button class="btn btn-sm btn-blue-border" id="prevBookingPage" disabled><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn btn-sm btn-blue-border" id="nextBookingPage"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                <span class="text-muted" id="bookingPaginationInfo">Page 1</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="bookingsGrid" class="row">
                                <div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading...</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer" style="margin-left: 280px; padding: 20px 30px; background: rgba(255,255,255,0.9); border-top: 1px solid rgba(0,0,0,0.05);">
            <div class="row"><div class="col-md-6"><p class="mb-0"><strong>© 2023 TruckLink</strong> All rights reserved.</p></div><div class="col-md-6 text-end"><p class="mb-0 text-muted">Provider Panel v2.0</p></div></div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="fas fa-truck me-2"></i>Booking Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="modalBody"><div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>
    
    <div class="modal fade" id="actionModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Confirm Action</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="actionModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="confirmActionBtn">Confirm</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    
    <script>
        // ==================== GLOBAL VARIABLES ====================
        let currentPage = 1, lastPage = 1, bookings = [], currentFilter = 'all';
        let map = null, selectedBookingId = null, currentRoutingControl = null;
        let searchTimer = null;
        let pendingAction = null;

        // ==================== INITIALIZATION ====================
        document.addEventListener('DOMContentLoaded', function() {
            loadBookings(currentPage, currentFilter);
            
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    currentPage = 1;
                    loadBookings(currentPage, currentFilter);
                });
            });
            
            document.getElementById('searchInput').addEventListener('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => filterBookingsBySearch(this.value), 500);
            });
            
            document.getElementById('prevBookingPage').onclick = () => { if(currentPage > 1) loadBookings(currentPage - 1, currentFilter); };
            document.getElementById('nextBookingPage').onclick = () => { if(currentPage < lastPage) loadBookings(currentPage + 1, currentFilter); };
            document.getElementById('confirmActionBtn').onclick = confirmAction;
        });

        // ==================== BOOKINGS FUNCTIONS ====================
        function loadBookings(page, filter) {
            const grid = document.getElementById('bookingsGrid');
            grid.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p>Loading...</p></div>`;
            
            let url = `/provider/bookings-data?page=${page}&per_page=6`;
            if(filter !== 'all') url += `&filter=${filter}`;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    bookings = data.bookings;
                    currentPage = data.current_page;
                    lastPage = data.last_page;
                    document.getElementById('bookingPaginationInfo').textContent = `Page ${currentPage} of ${lastPage}`;
                    document.getElementById('prevBookingPage').disabled = currentPage <= 1;
                    document.getElementById('nextBookingPage').disabled = currentPage >= lastPage;
                    updateStats(bookings);
                    renderBookings(bookings);
                }
            })
            .catch(err => { console.error(err); grid.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-4x text-danger"></i><h5>Error Loading</h5></div>`; });
        }

        function updateStats(bookings) {
            document.getElementById('totalBookingsCount').textContent = bookings.length;
            document.getElementById('pendingBookingsCount').textContent = bookings.filter(b => b.status === 'request').length;
            document.getElementById('acceptedBookingsCount').textContent = bookings.filter(b => b.status === 'accept').length;
            document.getElementById('completedBookingsCount').textContent = bookings.filter(b => b.status === 'complete').length;
        }

        function renderBookings(bookings) {
            const grid = document.getElementById('bookingsGrid');
            if(bookings.length === 0) {
                grid.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-clipboard-list fa-4x text-muted"></i><h5>No Bookings Found</h5><p class="text-muted">No booking requests for your vehicles yet.</p></div>`;
                return;
            }
            
            let html = '';
            bookings.forEach(b => {
                const date = b.booking_date ? new Date(b.booking_date).toLocaleDateString() : 'Date not set';
                html += `<div class="col-md-6 col-lg-4 mb-4"><div class="booking-card ${b.status}" onclick="openBookingDetails(${b.id})">
                    <div class="d-flex justify-content-between mb-3"><span class="badge ${b.badge_class}">${b.status_text}</span><small><i class="far fa-calendar"></i> ${date}</small></div>
                    <h6 class="mb-2"><i class="fas fa-user"></i> ${escapeHtml(b.customer_name || 'N/A')}</h6>
                    <p class="mb-2 text-truncate"><i class="fas fa-map-marker-alt text-danger"></i> ${escapeHtml(b.pickup_location)}<br><i class="fas fa-arrow-right mx-2"></i> <i class="fas fa-map-marker-alt text-success"></i> ${escapeHtml(b.dropoff_location)}</p>
                    <div class="row mb-2"><div class="col-6"><small>Goods</small><br><span class="fw-semibold">${escapeHtml(b.goods_type) || 'N/A'}</span></div><div class="col-6"><small>Weight</small><br><span class="fw-semibold">${b.goods_weight || 0} Ton</span></div></div>
                    <div class="d-flex justify-content-between"><div><small>Vehicle</small><br><span><i class="fas fa-truck"></i> ${escapeHtml(b.vehicle_type)}</span></div><div><small>Fare</small><br><span>Rs ${b.estimated_fare || 0}</span></div></div>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-blue-border flex-grow-1" onclick="event.stopPropagation(); openBookingDetails(${b.id})"><i class="fas fa-eye"></i> View</button>
                        ${b.status === 'request' ? `
                            <button class="btn btn-sm btn-accept" onclick="event.stopPropagation(); showActionModal(${b.id}, 'accept')"><i class="fas fa-check"></i> Accept</button>
                            <button class="btn btn-sm btn-reject" onclick="event.stopPropagation(); showActionModal(${b.id}, 'reject')"><i class="fas fa-times"></i> Reject</button>
                        ` : ''}
                    </div>
                </div></div>`;
            });
            grid.innerHTML = html;
        }

        // ==================== BOOKING DETAILS WITH MAP ROUTE ====================
        function openBookingDetails(bookingId) {
            selectedBookingId = bookingId;
            new bootstrap.Modal(document.getElementById('bookingDetailsModal')).show();
            loadBookingDetails(bookingId);
        }

        function loadBookingDetails(bookingId) {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading booking details...</p></div>`;
            
            fetch(`/provider/booking/${bookingId}/details`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    renderBookingDetails(data.booking);
                    loadBookingTimeline(bookingId);
                    loadBookingReviews(bookingId);
                    loadBookingComplaints(bookingId);
                    drawRouteOnMap(data.booking);
                }
            })
            .catch(err => { console.error(err); modalBody.innerHTML = `<div class="alert alert-danger">Error loading details</div>`; });
        }

        function drawRouteOnMap(booking) {
            const mapContainer = document.getElementById('detailMap');
            if(!mapContainer) return;
            
            if(map) map.remove();
            
            map = L.map('detailMap');
            
            let pickupLat = parseFloat(booking.pickup_lat), pickupLng = parseFloat(booking.pickup_lng);
            let dropoffLat = parseFloat(booking.dropoff_lat), dropoffLng = parseFloat(booking.dropoff_lng);
            
            if(isNaN(pickupLat) || isNaN(dropoffLat)) {
                map.setView([30.3753, 69.3451], 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
                L.marker([30.3753, 69.3451]).bindPopup('Location not available').addTo(map);
                return;
            }
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
            
            L.marker([pickupLat, pickupLng], {
                icon: L.divIcon({ html: '<div style="background:#dc3545;width:20px;height:20px;border-radius:50%;border:3px solid white"></div>', iconSize: [20,20] })
            }).bindPopup(`<b>Pickup</b><br>${escapeHtml(booking.pickup_location)}`).addTo(map);
            
            L.marker([dropoffLat, dropoffLng], {
                icon: L.divIcon({ html: '<div style="background:#28a745;width:20px;height:20px;border-radius:50%;border:3px solid white"></div>', iconSize: [20,20] })
            }).bindPopup(`<b>Destination</b><br>${escapeHtml(booking.dropoff_location)}`).addTo(map);
            
            if(booking.route_polyline) {
                try {
                    let polylineData = typeof booking.route_polyline === 'string' ? JSON.parse(booking.route_polyline) : booking.route_polyline;
                    let latlngs = [];
                    
                    if(polylineData.coordinates) {
                        latlngs = polylineData.coordinates.map(coord => [coord[1], coord[0]]);
                    } else if(Array.isArray(polylineData)) {
                        latlngs = polylineData.map(coord => [coord[1], coord[0]]);
                    }
                    
                    if(latlngs.length > 0) {
                        L.polyline(latlngs, { color: '#3498db', weight: 5, opacity: 0.8 }).addTo(map);
                        map.fitBounds(L.latLngBounds(latlngs), { padding: [50, 50] });
                    } else {
                        map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
                    }
                } catch(e) {
                    console.error('Error drawing polyline:', e);
                    map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
                }
            } else {
                L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], { color: '#3498db', weight: 4, dashArray: '5,10' }).addTo(map);
                map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
            }
            
            displayDirections(booking);
        }

        function displayDirections(booking) {
            const turnContainer = document.getElementById('turnByTurnContainer');
            const turnList = document.getElementById('turnByTurnList');
            const fullDirectionsList = document.getElementById('fullDirectionsList');
            
            let directions = [];
            
            if(booking.route_directions) {
                try {
                    directions = typeof booking.route_directions === 'string' ? JSON.parse(booking.route_directions) : booking.route_directions;
                } catch(e) {}
            }
            
            if(directions.length === 0 && booking.estimated_distance) {
                directions = generateFallbackDirections(booking);
            }
            
            if(directions.length > 0) {
                const directionsHtml = renderDirectionsList(directions);
                if(turnList) turnList.innerHTML = directionsHtml;
                if(fullDirectionsList) fullDirectionsList.innerHTML = directionsHtml;
                if(turnContainer) turnContainer.style.display = 'block';
            } else {
                if(turnContainer) turnContainer.style.display = 'none';
                if(fullDirectionsList) fullDirectionsList.innerHTML = '<p class="text-muted text-center">No turn-by-turn directions available</p>';
            }
            
            const routeInfoCard = document.getElementById('routeInfoCard');
            if(routeInfoCard && booking.estimated_distance) {
                document.getElementById('routeDistance').textContent = booking.estimated_distance + ' km';
                const minutes = Math.round((booking.estimated_distance / 40) * 60);
                const hours = Math.floor(minutes / 60), mins = minutes % 60;
                document.getElementById('routeDuration').textContent = hours > 0 ? `${hours} hr ${mins} min` : `${mins} min`;
                document.getElementById('routeToll').textContent = `Rs ${booking.toll_cost || 0}`;
                routeInfoCard.style.display = 'block';
            }
        }

        function renderDirectionsList(directions) {
            if(!directions || directions.length === 0) return '<p class="text-muted text-center">No directions available</p>';
            
            let html = '';
            directions.forEach((step, index) => {
                let instruction = step.instruction || step.text || 'Continue';
                instruction = instruction.replace(/<[^>]*>/g, '');
                instruction = instruction.charAt(0).toUpperCase() + instruction.slice(1);
                
                let distance = step.distance || 0;
                let distanceText = '';
                if(distance > 1000) distanceText = (distance/1000).toFixed(1) + ' km';
                else if(distance > 0) distanceText = Math.round(distance) + ' m';
                
                let icon = 'fa-arrow-right';
                let instLower = instruction.toLowerCase();
                if(instLower.includes('left')) icon = 'fa-arrow-left';
                else if(instLower.includes('right')) icon = 'fa-arrow-right';
                else if(instLower.includes('straight')) icon = 'fa-arrow-up';
                else if(instLower.includes('destination')) icon = 'fa-flag-checkered';
                
                html += `<div class="turn-item"><div class="turn-number">${index+1}</div><div class="turn-content"><div class="turn-instruction"><i class="fas ${icon} me-2" style="color:#3498db"></i>${instruction}</div>${distanceText ? `<div class="turn-distance"><i class="fas fa-road"></i> ${distanceText}</div>` : ''}</div></div>`;
            });
            return html;
        }

        function generateFallbackDirections(booking) {
            const directions = [];
            directions.push({ instruction: `Start from ${booking.pickup_location}`, distance: 0 });
            if(booking.estimated_distance) {
                directions.push({ instruction: `Continue towards ${booking.dropoff_location}`, distance: booking.estimated_distance * 1000 });
            }
            directions.push({ instruction: `Arrive at destination: ${booking.dropoff_location}`, distance: 0 });
            return directions;
        }

        function renderBookingDetails(booking) {
            const modalBody = document.getElementById('modalBody');
            let statusClass = booking.status === 'request' ? 'bg-warning' : (booking.status === 'accept' ? 'bg-success' : (booking.status === 'reject' ? 'bg-danger' : 'bg-info'));
            let paymentClass = booking.payment_status === 'paid' ? 'bg-success' : 'bg-warning';
            
            // Action buttons based on booking status (only accept/reject for pending)
            let actionButtons = '';
            if(booking.status === 'request') {
                actionButtons = `
                    <div class="status-actions text-center">
                        <button class="btn btn-accept me-2" onclick="showActionModal(${booking.id}, 'accept')"><i class="fas fa-check"></i> Accept Booking</button>
                        <button class="btn btn-reject" onclick="showActionModal(${booking.id}, 'reject')"><i class="fas fa-times"></i> Reject Booking</button>
                    </div>
                `;
            }
            
            modalBody.innerHTML = `
                <div id="detailMap" class="mb-4"></div>
                <div id="turnByTurnContainer" class="turn-by-turn" style="display:none"><h6><i class="fas fa-turn-down me-2"></i>Turn-by-Turn Directions</h6><div id="turnByTurnList"></div></div>
                <div id="routeInfoCard" class="route-info-card" style="display:none"><div class="route-info-item"><span class="route-info-label"><i class="fas fa-road"></i> Distance:</span><span class="route-info-value" id="routeDistance">0 km</span></div><div class="route-info-item"><span class="route-info-label"><i class="fas fa-clock"></i> Duration:</span><span class="route-info-value" id="routeDuration">0 min</span></div><div class="route-info-item"><span class="route-info-label"><i class="fas fa-toll"></i> Toll:</span><span class="route-info-value" id="routeToll">Rs 0</span></div></div>
                <div class="d-flex justify-content-between mb-4"><h5><i class="fas fa-truck"></i> Booking #${booking.id}</h5><div><span class="badge ${statusClass}">${booking.status_text}</span> <span class="badge ${paymentClass}">${booking.payment_status || 'Pending'}</span></div></div>
                <div class="customer-card"><img src="${booking.customer_image || 'https://randomuser.me/api/portraits/men/32.jpg'}" class="customer-image"><div><h6>${escapeHtml(booking.customer_name || 'N/A')}</h6><p><i class="fas fa-phone"></i> ${booking.customer_mobile || 'N/A'}<br><i class="fas fa-envelope"></i> ${booking.customer_email || 'N/A'}</p></div></div>
                <div class="info-tabs"><div class="info-tab active" onclick="switchTab('details')">Details</div><div class="info-tab" onclick="switchTab('timeline')">Timeline</div><div class="info-tab" onclick="switchTab('vehicle')">Vehicle</div><div class="info-tab" onclick="switchTab('payment')">Payment</div><div class="info-tab" onclick="switchTab('reviews')">Reviews</div><div class="info-tab" onclick="switchTab('complaints')">Complaints</div><div class="info-tab" onclick="switchTab('directions')">Directions</div></div>
                <div id="detailsTab" class="tab-content active"><div class="row"><div class="col-md-6"><h6>Location Details</h6><table class="table table-sm"><tr><th>Pickup:</th><td>${escapeHtml(booking.pickup_location)}</td></tr><tr><th>Dropoff:</th><td>${escapeHtml(booking.dropoff_location)}</td></tr><tr><th>Pickup Time:</th><td>${booking.pickup_time || 'N/A'}</td></tr><tr><th>Booking Date:</th><td>${booking.booking_date || 'N/A'}</td></tr></table></div><div class="col-md-6"><h6>Goods Details</h6><table class="table table-sm"><tr><th>Type:</th><td>${escapeHtml(booking.goods_type) || 'N/A'}</td></tr><tr><th>Weight:</th><td>${booking.goods_weight || 0} Ton</td></tr><tr><th>Instructions:</th><td>${escapeHtml(booking.special_instructions) || 'None'}</td></tr></table></div></div><div class="row mt-3"><div class="col-md-6"><h6>Route Info</h6><table class="table table-sm"><tr><th>Est. Distance:</th><td>${booking.estimated_distance || 0} km</td></tr><tr><th>Est. Fare:</th><td>Rs ${booking.estimated_fare || 0}</td></tr>${booking.selected_route_name ? `<tr><th>Route:</th><td>${escapeHtml(booking.selected_route_name)}</td></tr>` : ''}${booking.has_tolls ? `<tr><th>Toll Cost:</th><td>Rs ${booking.toll_cost || 0}</td></tr>` : ''}</table></div><div class="col-md-6"><h6>Actual Details</h6><table class="table table-sm"><tr><th>Actual Distance:</th><td>${booking.actual_distance || 0} km</td></tr><tr><th>Actual Fare:</th><td>Rs ${booking.actual_fare || 0}</td></tr></table></div></div>${booking.status === 'reject' && booking.rejection_reason ? `<div class="rejection-reason"><h6><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h6><p>${escapeHtml(booking.rejection_reason)}</p></div>` : ''}</div>
                <div id="timelineTab" class="tab-content"><div class="timeline" id="modalTimeline"><div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div></div></div>
                <div id="vehicleTab" class="tab-content">${booking.vehicle ? `<div class="row"><div class="col-md-6"><h6>Vehicle Details</h6><table class="table table-sm"><tr><th>Type:</th><td>${escapeHtml(booking.vehicle.vehicle_type)}</td></tr><tr><th>Number:</th><td>${escapeHtml(booking.vehicle.vehicle_number || 'N/A')}</td></tr><tr><th>Capacity:</th><td>${booking.vehicle.weight_capacity || 0} kg</td></tr></table></div></div>` : '<p>No vehicle info</p>'}</div>
                <div id="paymentTab" class="tab-content"><div class="row"><div class="col-md-6"><h6>Payment Summary</h6><table class="table table-sm"><tr><th>Method:</th><td>${booking.payment_method || 'Not specified'}</td></tr><tr><th>Status:</th><td><span class="badge ${paymentClass}">${booking.payment_status || 'pending'}</span></td></tr><tr><th>Est. Fare:</th><td>Rs ${booking.estimated_fare || 0}</td></tr><tr><th>Actual Fare:</th><td>Rs ${booking.actual_fare || 0}</td></tr></table></div><div class="col-md-6"><h6>Timestamps</h6><table class="table table-sm"><tr><th>Created:</th><td>${booking.created_at || 'N/A'}</td></tr>${booking.accepted_at ? `<tr><th>Accepted:</th><td>${booking.accepted_at}</td></tr>` : ''}${booking.rejected_at ? `<tr><th>Rejected:</th><td>${booking.rejected_at}</td></tr>` : ''}${booking.delivered_at ? `<tr><th>Delivered:</th><td>${booking.delivered_at}</td></tr>` : ''}</table></div></div></div>
                <div id="reviewsTab" class="tab-content"><div id="reviewsList"><p class="text-muted text-center">Loading reviews...</p></div></div>
                <div id="complaintsTab" class="tab-content"><div id="complaintsList"><p class="text-muted text-center">Loading complaints...</p></div></div>
                <div id="directionsTab" class="tab-content"><div class="turn-by-turn" style="margin-top:0"><h6><i class="fas fa-turn-down"></i> Complete Directions</h6><div id="fullDirectionsList"><p class="text-muted text-center">Loading directions...</p></div></div></div>
                ${actionButtons}
            `;
        }

        function loadBookingTimeline(bookingId) {
            fetch(`/provider/booking/${bookingId}/tracking`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if(data.success && data.booking.timeline) {
                    const timeline = document.getElementById('modalTimeline');
                    if(timeline) {
                        let html = '';
                        data.booking.timeline.forEach(item => {
                            html += `<div class="timeline-item ${item.status}"><div class="timeline-marker"></div><div class="timeline-content"><h6>${item.title}</h6><p>${item.description}</p>${item.timestamp ? `<small>${item.timestamp}</small>` : ''}</div></div>`;
                        });
                        timeline.innerHTML = html;
                    }
                }
            })
            .catch(err => console.error(err));
        }

        function loadBookingReviews(bookingId) {
            fetch(`/provider/booking/${bookingId}/reviews`)
                .then(r => r.json())
                .then(data => {
                    const reviewsList = document.getElementById('reviewsList');
                    if(reviewsList) {
                        if(data.success && data.data && data.data.length > 0) {
                            let html = '<div class="reviews-container">';
                            data.data.forEach(review => {
                                html += `
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <strong>${escapeHtml(review.customer?.name || 'Customer')}</strong>
                                            <span class="text-warning">${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}</span>
                                        </div>
                                        <p class="mt-2 mb-1">${escapeHtml(review.review || 'No comment')}</p>
                                        <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            reviewsList.innerHTML = html;
                        } else {
                            reviewsList.innerHTML = '<p class="text-muted text-center">No reviews yet</p>';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    const reviewsList = document.getElementById('reviewsList');
                    if(reviewsList) reviewsList.innerHTML = '<p class="text-muted text-center">Failed to load reviews</p>';
                });
        }

        function loadBookingComplaints(bookingId) {
            fetch(`/provider/booking/${bookingId}/complaints`)
                .then(r => r.json())
                .then(data => {
                    const complaintsList = document.getElementById('complaintsList');
                    if(complaintsList) {
                        if(data.success && data.data && data.data.length > 0) {
                            let html = '<div class="complaints-container">';
                            data.data.forEach(complaint => {
                                html += `
                                    <div class="complaint-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge ${complaint.status_badge} me-2">${complaint.status_text}</span>
                                                <strong>${escapeHtml(complaint.subject)}</strong>
                                            </div>
                                            <small class="text-muted">${new Date(complaint.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <p class="mt-2 mb-1">${escapeHtml(complaint.description)}</p>
                                        ${complaint.admin_response ? `<div class="alert alert-info mt-2 mb-0"><small><strong>Admin Response:</strong> ${escapeHtml(complaint.admin_response)}</small></div>` : ''}
                                    </div>
                                `;
                            });
                            html += '</div>';
                            complaintsList.innerHTML = html;
                        } else {
                            complaintsList.innerHTML = '<p class="text-muted text-center">No complaints filed for this booking</p>';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    const complaintsList = document.getElementById('complaintsList');
                    if(complaintsList) complaintsList.innerHTML = '<p class="text-muted text-center">Failed to load complaints</p>';
                });
        }

        // ==================== ACTION FUNCTIONS (Accept/Reject only) ====================
        function showActionModal(bookingId, action) {
            pendingAction = { bookingId, action };
            const modalBody = document.getElementById('actionModalBody');
            const modalTitle = document.querySelector('#actionModal .modal-title');
            
            let message = '';
            let buttonClass = '';
            
            if(action === 'accept') {
                message = 'Are you sure you want to accept this booking? The vehicle will be marked as booked.';
                buttonClass = 'btn-success';
                modalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i>Accept Booking';
            } else if(action === 'reject') {
                message = 'Are you sure you want to reject this booking? Please provide a reason for rejection.';
                buttonClass = 'btn-danger';
                modalTitle.innerHTML = '<i class="fas fa-times-circle me-2"></i>Reject Booking';
            }
            
            if(action === 'reject') {
                modalBody.innerHTML = `
                    <p>${message}</p>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Please provide reason for rejection..."></textarea>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `<p>${message}</p>`;
            }
            
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.className = `btn ${buttonClass}`;
            
            new bootstrap.Modal(document.getElementById('actionModal')).show();
        }
        
        function confirmAction() {
            if(!pendingAction) return;
            
            const { bookingId, action } = pendingAction;
            const btn = document.getElementById('confirmActionBtn');
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            btn.disabled = true;
            
            let url = '';
            let method = 'POST';
            let body = {};
            
            if(action === 'accept') {
                url = `/provider/booking/${bookingId}/accept`;
                body = { _token: document.querySelector('meta[name="csrf-token"]').content };
            } else if(action === 'reject') {
                const reason = document.getElementById('rejectionReason')?.value || '';
                if(!reason.trim()) {
                    alert('Please provide a rejection reason');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    return;
                }
                url = `/provider/booking/${bookingId}/reject`;
                body = { _token: document.querySelector('meta[name="csrf-token"]').content, reason: reason };
            }
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    bootstrap.Modal.getInstance(document.getElementById('actionModal')).hide();
                    loadBookings(currentPage, currentFilter);
                    if(selectedBookingId) {
                        loadBookingDetails(selectedBookingId);
                    }
                } else {
                    alert('❌ ' + data.message);
                }
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                pendingAction = null;
            })
            .catch(err => {
                console.error(err);
                alert('Error processing action');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }

        // ==================== UTILITY FUNCTIONS ====================
        function switchTab(tabName) {
            document.querySelectorAll('.info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            event.target.classList.add('active');
            
            const tabIds = { details:'detailsTab', timeline:'timelineTab', vehicle:'vehicleTab', payment:'paymentTab', reviews:'reviewsTab', complaints:'complaintsTab', directions:'directionsTab' };
            document.getElementById(tabIds[tabName]).classList.add('active');
        }

        function filterBookingsBySearch(term) {
            if(!term.trim()) { loadBookings(1, currentFilter); return; }
            const filtered = bookings.filter(b => 
                (b.pickup_location && b.pickup_location.toLowerCase().includes(term.toLowerCase())) ||
                (b.dropoff_location && b.dropoff_location.toLowerCase().includes(term.toLowerCase())) ||
                (b.customer_name && b.customer_name.toLowerCase().includes(term.toLowerCase())) ||
                (b.goods_type && b.goods_type.toLowerCase().includes(term.toLowerCase()))
            );
            renderBookings(filtered);
            updateStats(filtered);
            document.getElementById('bookingPaginationInfo').textContent = `Filtered: ${filtered.length}`;
        }
        
        function escapeHtml(text) { if(!text) return ''; const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
    </script>
</body>
</html>