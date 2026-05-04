<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Complaints - TruckLink Vehicle Owner Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --danger: #e74c3c;
            --info: #17a2b8;
            --late-bg: #fff3e0;
            --damaged-bg: #ffe0e0;
            --rude-bg: #ffe0f0;
            --other-bg: #e8f4f8;
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
        
        /* Category Section Styles */
        .category-section {
            margin-bottom: 40px;
        }
        
        .category-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            padding: 15px 20px;
            border-radius: 16px 16px 0 0;
            border-bottom: 3px solid;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .category-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .category-header .badge-count {
            font-size: 1rem;
            padding: 5px 12px;
            border-radius: 30px;
        }
        
        .category-card {
            border: none;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .category-card.collapsed-category {
            display: none;
        }
        
        /* Late Delivery Section */
        .late-delivery .category-header {
            border-bottom-color: #f39c12;
            background: linear-gradient(135deg, #fff8f0 0%, #fff3e0 100%);
        }
        
        .late-delivery .category-header h5 {
            color: #e67e22;
        }
        
        /* Damaged Goods Section */
        .damaged-goods .category-header {
            border-bottom-color: #e74c3c;
            background: linear-gradient(135deg, #ffe8e8 0%, #ffe0e0 100%);
        }
        
        .damaged-goods .category-header h5 {
            color: #c0392b;
        }
        
        /* Rude Drivers Section */
        .rude-drivers .category-header {
            border-bottom-color: #9b59b6;
            background: linear-gradient(135deg, #f5e8ff 0%, #f0e0ff 100%);
        }
        
        .rude-drivers .category-header h5 {
            color: #8e44ad;
        }
        
        /* Other Section */
        .other-complaints .category-header {
            border-bottom-color: #3498db;
            background: linear-gradient(135deg, #e8f4f8 0%, #e0eff8 100%);
        }
        
        .other-complaints .category-header h5 {
            color: #2980b9;
        }
        
        .complaint-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .complaint-item:hover {
            background: #f8f9fa;
        }
        
        .complaint-item:last-child {
            border-bottom: none;
        }
        
        .complaint-subject {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .complaint-details {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .empty-category {
            padding: 40px;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-category i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .bg-warning { background-color: var(--warning); color: #000; }
        .bg-success { background-color: var(--success); color: #fff; }
        .bg-info { background-color: var(--info); color: #fff; }
        .bg-danger { background-color: var(--danger); color: #fff; }
        .bg-secondary { background-color: #6c757d; color: #fff; }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            flex-shrink: 0;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .modal-body {
            max-height: calc(85vh - 130px);
            overflow-y: auto;
            padding: 20px;
        }
        
        /* Info Cards */
        .info-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--secondary);
        }
        
        /* Stats Cards */
        .stat-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 16px;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .late-value { color: #e67e22; }
        .damaged-value { color: #e74c3c; }
        .rude-value { color: #9b59b6; }
        .other-value { color: #3498db; }
        
        .pending-value { color: #f39c12; }
        .review-value { color: #17a2b8; }
        .resolved-value { color: #27ae60; }
        
        /* Tabs - Fixed at top of modal body */
        .info-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding-top: 5px;
        }
        
        .info-tab {
            padding: 10px 20px;
            font-weight: 500;
            color: #6c757d;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }
        
        .info-tab:hover {
            color: var(--secondary);
        }
        
        .info-tab.active {
            color: var(--secondary);
        }
        
        .info-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .btn-action {
            margin: 0 3px;
        }
        
        .collapse-icon {
            transition: transform 0.3s ease;
        }
        
        .collapse-icon.rotated {
            transform: rotate(180deg);
        }
        
        .btn-blue-border {
            background: transparent;
            border: 2px solid #e9ecef;
            color: #6c757d;
            transition: all 0.3s ease;
            border-radius: 25px;
        }
        
        .btn-blue-border:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
            transform: translateY(-2px);
        }
        
        /* Admin Response Card */
        .admin-response-card {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #4caf50;
        }
        
        .awaiting-response {
            background: #fff3e0;
            border-left-color: #ff9800;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .vehicle-info {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 8px;
        }
        
        /* Fixed Complaint Info Card */
        .fixed-complaint-info {
            margin-bottom: 20px;
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
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            .main-content {
                margin-left: 80px;
            }
            .content-area {
                padding: 20px 15px;
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
            <a class="nav-link" href="{{route('my.vehicle')}}"><i class="fas fa-truck"></i> <span>My Vehicles</span></a>
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
            <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Complaints Against My Vehicles</h5>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=3498db&color=fff" alt="Provider">
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

        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h4 class="mb-1 fw-bold">Complaints Center</h4>
                    <p class="text-muted mb-0">View all complaints filed against your vehicles</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-blue-border" onclick="expandAllCategories()">
                        <i class="fas fa-expand-alt me-2"></i>Expand All
                    </button>
                    <button class="btn btn-blue-border" onclick="collapseAllCategories()">
                        <i class="fas fa-compress-alt me-2"></i>Collapse All
                    </button>
                </div>
            </div>

            <!-- Summary Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value pending-value" id="totalComplaints">{{ $totalComplaints }}</div>
                        <div class="stat-label"><i class="fas fa-total me-1"></i> Total Complaints</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value pending-value" id="pendingCount">{{ $pendingComplaints }}</div>
                        <div class="stat-label"><i class="fas fa-clock me-1"></i> Pending</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value review-value" id="inReviewCount">{{ $inReviewComplaints }}</div>
                        <div class="stat-label"><i class="fas fa-search me-1"></i> Under Review</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value resolved-value" id="resolvedCount">{{ $resolvedComplaints }}</div>
                        <div class="stat-label"><i class="fas fa-check-circle me-1"></i> Resolved</div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards by Category -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value late-value" id="lateDeliveryCount">{{ $lateDeliveryCount }}</div>
                        <div class="stat-label"><i class="fas fa-clock me-1"></i> Late Delivery</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value damaged-value" id="damagedGoodsCount">{{ $damagedGoodsCount }}</div>
                        <div class="stat-label"><i class="fas fa-box-open me-1"></i> Damaged Goods</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value rude-value" id="rudeDriversCount">{{ $rudeDriversCount }}</div>
                        <div class="stat-label"><i class="fas fa-user-friends me-1"></i> Rude Drivers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value other-value" id="otherCount">{{ $otherCount }}</div>
                        <div class="stat-label"><i class="fas fa-ellipsis-h me-1"></i> Others</div>
                    </div>
                </div>
            </div>

            <!-- Status Filter Row -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="status-filter">
                            <span class="fw-semibold me-2"><i class="fas fa-filter me-1"></i>Status:</span>
                            <button class="btn-filter active" data-status="all" onclick="setStatusFilter('all')">All</button>
                            <button class="btn-filter" data-status="pending" onclick="setStatusFilter('pending')">Pending</button>
                            <button class="btn-filter" data-status="in_review" onclick="setStatusFilter('in_review')">Under Review</button>
                            <button class="btn-filter" data-status="resolved" onclick="setStatusFilter('resolved')">Resolved</button>
                        </div>
                        <div class="text-muted" id="totalComplaintsInfo">Total: {{ $totalComplaints }} complaints</div>
                    </div>
                </div>
            </div>

            <!-- Category: Late Delivery -->
            <div class="category-section late-delivery" id="lateDeliverySection">
                <div class="category-header" onclick="toggleCategory('lateDelivery')">
                    <h5><i class="fas fa-clock me-2"></i>Late Delivery Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="lateDeliveryBadge">{{ $lateDeliveryCount }}</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="lateDeliveryIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="lateDeliveryContent">
                    <div class="complaints-list" id="lateDeliveryList"></div>
                </div>
            </div>

            <!-- Category: Damaged Goods -->
            <div class="category-section damaged-goods" id="damagedGoodsSection">
                <div class="category-header" onclick="toggleCategory('damagedGoods')">
                    <h5><i class="fas fa-box-open me-2"></i>Damaged Goods Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="damagedGoodsBadge">{{ $damagedGoodsCount }}</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="damagedGoodsIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="damagedGoodsContent">
                    <div class="complaints-list" id="damagedGoodsList"></div>
                </div>
            </div>

            <!-- Category: Rude Drivers -->
            <div class="category-section rude-drivers" id="rudeDriversSection">
                <div class="category-header" onclick="toggleCategory('rudeDrivers')">
                    <h5><i class="fas fa-user-friends me-2"></i>Rude Drivers Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="rudeDriversBadge">{{ $rudeDriversCount }}</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="rudeDriversIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="rudeDriversContent">
                    <div class="complaints-list" id="rudeDriversList"></div>
                </div>
            </div>

            <!-- Category: Other Complaints -->
            <div class="category-section other-complaints" id="otherSection">
                <div class="category-header" onclick="toggleCategory('other')">
                    <h5><i class="fas fa-ellipsis-h me-2"></i>Other Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="otherBadge">{{ $otherCount }}</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="otherIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="otherContent">
                    <div class="complaints-list" id="otherList"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaint Details Modal - Fixed Layout -->
    <div class="modal fade" id="complaintDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i>Complaint Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="complaintDetailBody">
                    <!-- Fixed Complaint Info Card will be inserted here -->
                    <div class="fixed-complaint-info" id="fixedComplaintInfo"></div>
                    
                    <!-- Tabs -->
                    <div class="info-tabs">
                        <div class="info-tab active" data-tab="booking">Booking Details</div>
                        <div class="info-tab" data-tab="vehicle">Vehicle Info</div>
                        <div class="info-tab" data-tab="customer">Customer Info</div>
                        <div class="info-tab" data-tab="timeline">Timeline</div>
                    </div>
                    
                    <!-- Tab Contents -->
                    <div id="bookingTab" class="tab-content active"></div>
                    <div id="vehicleTab" class="tab-content"></div>
                    <div id="customerTab" class="tab-content"></div>
                    <div id="timelineTab" class="tab-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Global variables
        let allComplaints = [];
        let currentStatusFilter = 'all';
        let currentComplaintData = null;
        
        // PHP complaints data
        const phpComplaints = @json($complaints);
        
        // Helper functions
        function isLateDeliveryComplaint(type) {
            if (!type) return false;
            const lowerType = type.toLowerCase();
            return lowerType === 'late_delivery' || lowerType === 'late delivery';
        }
        
        function isDamagedGoodsComplaint(type) {
            if (!type) return false;
            const lowerType = type.toLowerCase();
            return lowerType === 'damaged_goods' || lowerType === 'damaged goods';
        }
        
        function isRudeDriversComplaint(type) {
            if (!type) return false;
            const lowerType = type.toLowerCase();
            return lowerType === 'rude_driver' || lowerType === 'rude driver' || lowerType === 'rude drivers';
        }
        
        function isOtherComplaint(type) {
            if (!type) return true;
            const lowerType = type.toLowerCase();
            return !isLateDeliveryComplaint(type) && !isDamagedGoodsComplaint(type) && !isRudeDriversComplaint(type);
        }
        
        function getStatusBadge(status) {
            const badges = {
                'pending': 'bg-warning',
                'in_review': 'bg-info',
                'resolved': 'bg-success',
                'rejected': 'bg-danger'
            };
            return badges[status] || 'bg-secondary';
        }
        
        function getStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'in_review': 'Under Review',
                'resolved': 'Resolved',
                'rejected': 'Rejected'
            };
            return texts[status] || status;
        }
        
        // Format complaints from PHP
        function formatComplaints() {
            if (!phpComplaints || phpComplaints.length === 0) return [];
            
            return phpComplaints.map(complaint => ({
                id: complaint.id,
                booking_id: complaint.booking_id,
                complaint_type: complaint.complaint_type,
                subject: complaint.subject,
                description: complaint.description,
                status: complaint.status,
                status_text: complaint.status_text,
                status_badge: complaint.status_badge,
                created_at: complaint.created_at ? new Date(complaint.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A',
                admin_response: complaint.admin_response,
                resolved_at: complaint.resolved_at,
                customer: complaint.customer,
                booking: complaint.booking
            }));
        }
        
        function loadAndDisplayComplaints() {
            allComplaints = formatComplaints();
            filterAndDisplayComplaints();
        }
        
        function filterAndDisplayComplaints() {
            let filtered = [...allComplaints];
            
            // Apply status filter
            if (currentStatusFilter !== 'all') {
                filtered = filtered.filter(c => c.status === currentStatusFilter);
            }
            
            // Group by complaint type
            const lateDelivery = filtered.filter(c => isLateDeliveryComplaint(c.complaint_type));
            const damagedGoods = filtered.filter(c => isDamagedGoodsComplaint(c.complaint_type));
            const rudeDrivers = filtered.filter(c => isRudeDriversComplaint(c.complaint_type));
            const other = filtered.filter(c => isOtherComplaint(c.complaint_type));
            
            // Update count displays
            document.getElementById('lateDeliveryCount').textContent = lateDelivery.length;
            document.getElementById('damagedGoodsCount').textContent = damagedGoods.length;
            document.getElementById('rudeDriversCount').textContent = rudeDrivers.length;
            document.getElementById('otherCount').textContent = other.length;
            
            document.getElementById('lateDeliveryBadge').textContent = lateDelivery.length;
            document.getElementById('damagedGoodsBadge').textContent = damagedGoods.length;
            document.getElementById('rudeDriversBadge').textContent = rudeDrivers.length;
            document.getElementById('otherBadge').textContent = other.length;
            
            document.getElementById('totalComplaintsInfo').innerHTML = `Total: ${filtered.length} complaints ${currentStatusFilter !== 'all' ? `(filtered by ${currentStatusFilter})` : ''}`;
            
            // Render each category
            renderComplaintList('lateDeliveryList', lateDelivery);
            renderComplaintList('damagedGoodsList', damagedGoods);
            renderComplaintList('rudeDriversList', rudeDrivers);
            renderComplaintList('otherList', other);
        }
        
        function renderComplaintList(containerId, complaints) {
            const container = document.getElementById(containerId);
            
            if (!complaints || complaints.length === 0) {
                container.innerHTML = `
                    <div class="empty-category">
                        <i class="fas fa-check-circle"></i>
                        <p>No complaints in this category</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            complaints.forEach(complaint => {
                const customerName = complaint.customer?.name || 'N/A';
                const statusBadge = getStatusBadge(complaint.status);
                const statusText = getStatusText(complaint.status);
                const createdDate = complaint.created_at || '';
                const vehicleNumber = complaint.booking?.vehicle?.vehicle_number || 'N/A';
                const vehicleType = complaint.booking?.vehicle?.vehicle_type || 'N/A';
                
                // Show admin response preview if available
                const hasResponse = complaint.admin_response && complaint.admin_response.trim() !== '';
                
                html += `
                    <div class="complaint-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="complaint-subject">
                                    #${complaint.id} - ${escapeHtml(complaint.subject)}
                                    <span class="badge ${statusBadge} ms-2">${statusText}</span>
                                </div>
                                <div class="complaint-details">
                                    <i class="fas fa-calendar me-1"></i> ${escapeHtml(createdDate)} &nbsp;|&nbsp;
                                    <i class="fas fa-hashtag me-1"></i> Booking #${complaint.booking_id}
                                </div>
                                <div class="vehicle-info mt-1">
                                    <i class="fas fa-truck me-1"></i> Vehicle: ${escapeHtml(vehicleNumber)} (${escapeHtml(vehicleType)})
                                </div>
                                <div class="complaint-details mt-1">
                                    <i class="fas fa-user me-1"></i> Customer: ${escapeHtml(customerName)} 
                                </div>
                                <div class="complaint-details mt-1">
                                    <small class="text-muted">${escapeHtml(complaint.description?.substring(0, 100))}${complaint.description?.length > 100 ? '...' : ''}</small>
                                </div>
                                ${hasResponse ? `
                                    <div class="mt-2">
                                        <small class="text-success"><i class="fas fa-check-circle"></i> Admin responded on: ${new Date(complaint.resolved_at).toLocaleDateString()}</small>
                                    </div>
                                ` : complaint.status === 'pending' ? `
                                    <div class="mt-2">
                                        <small class="text-warning"><i class="fas fa-hourglass-half"></i> Awaiting admin response</small>
                                    </div>
                                ` : complaint.status === 'in_review' ? `
                                    <div class="mt-2">
                                        <small class="text-info"><i class="fas fa-search"></i> Under review by admin</small>
                                    </div>
                                ` : ''}
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info btn-action" onclick="viewComplaintDetails(${complaint.id})" title="View Details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        function setStatusFilter(status) {
            currentStatusFilter = status;
            
            // Update active button styling
            document.querySelectorAll('.btn-filter').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-status') === status) {
                    btn.classList.add('active');
                }
            });
            
            filterAndDisplayComplaints();
        }
        
        function toggleCategory(category) {
            const content = document.getElementById(`${category}Content`);
            const icon = document.getElementById(`${category}Icon`);
            
            if (content) {
                if (content.classList.contains('collapsed-category')) {
                    content.classList.remove('collapsed-category');
                    if (icon) icon.classList.remove('rotated');
                } else {
                    content.classList.add('collapsed-category');
                    if (icon) icon.classList.add('rotated');
                }
            }
        }
        
        function expandAllCategories() {
            const categories = ['lateDelivery', 'damagedGoods', 'rudeDrivers', 'other'];
            categories.forEach(cat => {
                const content = document.getElementById(`${cat}Content`);
                const icon = document.getElementById(`${cat}Icon`);
                if (content) {
                    content.classList.remove('collapsed-category');
                    if (icon) icon.classList.remove('rotated');
                }
            });
        }
        
        function collapseAllCategories() {
            const categories = ['lateDelivery', 'damagedGoods', 'rudeDrivers', 'other'];
            categories.forEach(cat => {
                const content = document.getElementById(`${cat}Content`);
                const icon = document.getElementById(`${cat}Icon`);
                if (content) {
                    content.classList.add('collapsed-category');
                    if (icon) icon.classList.add('rotated');
                }
            });
        }
        
        function viewComplaintDetails(complaintId) {
            const complaint = allComplaints.find(c => c.id === complaintId);
            if (!complaint) {
                alert('Complaint not found');
                return;
            }
            
            currentComplaintData = complaint;
            
            // Render fixed complaint info (only once)
            renderFixedComplaintInfo(complaint);
            
            // Render all tab contents
            renderBookingTab(complaint);
            renderVehicleTab(complaint);
            renderCustomerTab(complaint);
            renderTimelineTab(complaint);
            
            // Show first tab by default
            showTab('booking');
            
            const modal = new bootstrap.Modal(document.getElementById('complaintDetailsModal'));
            modal.show();
        }
        
        function renderFixedComplaintInfo(complaint) {
            const statusBadge = getStatusBadge(complaint.status);
            const statusText = getStatusText(complaint.status);
            
            const container = document.getElementById('fixedComplaintInfo');
            container.innerHTML = `
                <div class="info-card">
                    <h6><i class="fas fa-exclamation-triangle text-danger me-2"></i>Complaint Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Complaint ID:</strong> #${complaint.id}<br>
                            <strong>Booking ID:</strong> #${complaint.booking_id}<br>
                            <strong>Type:</strong> <span class="badge bg-secondary">${escapeHtml(complaint.complaint_type || 'General')}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> <span class="badge ${statusBadge}">${statusText}</span><br>
                            <strong>Date Filed:</strong> ${complaint.created_at || 'N/A'}<br>
                            ${complaint.resolved_at ? `<strong>Resolved At:</strong> ${new Date(complaint.resolved_at).toLocaleDateString()}` : ''}
                        </div>
                    </div>
                    <hr>
                    <strong>Subject:</strong> ${escapeHtml(complaint.subject)}<br>
                    <strong>Description:</strong><br>
                    <p class="mt-2">${escapeHtml(complaint.description)}</p>
                    ${complaint.admin_response ? `
                        <div class="admin-response-card mt-3">
                            <strong><i class="fas fa-reply-all text-success"></i> Admin Response:</strong><br>
                            <p class="mt-2 mb-0">${escapeHtml(complaint.admin_response)}</p>
                            ${complaint.resolved_at ? `<small class="text-muted mt-2 d-block">Resolved on: ${new Date(complaint.resolved_at).toLocaleString()}</small>` : ''}
                        </div>
                    ` : complaint.status === 'pending' ? `
                        <div class="admin-response-card awaiting-response mt-3">
                            <strong><i class="fas fa-hourglass-half text-warning"></i> Awaiting Response:</strong><br>
                            <p class="mt-2 mb-0">This complaint is pending review. The admin will respond shortly.</p>
                        </div>
                    ` : complaint.status === 'in_review' ? `
                        <div class="admin-response-card awaiting-response mt-3">
                            <strong><i class="fas fa-search text-info"></i> Under Review:</strong><br>
                            <p class="mt-2 mb-0">This complaint is currently being reviewed by our admin team.</p>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        function renderBookingTab(complaint) {
            const booking = complaint.booking || {};
            const container = document.getElementById('bookingTab');
            
            container.innerHTML = `
                <div class="info-card">
                    <h6><i class="fas fa-file-alt me-2"></i>Booking Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td width="140"><strong>Booking ID:</strong></td><td>#${booking.id || complaint.booking_id}</td></tr>
                                <tr><td><strong>Pickup Location:</strong></td><td>${escapeHtml(booking.pickup_location || 'N/A')}</td></tr>
                                <tr><td><strong>Dropoff Location:</strong></td><td>${escapeHtml(booking.dropoff_location || 'N/A')}</td></tr>
                                <tr><td><strong>Pickup Time:</strong></td><td>${escapeHtml(booking.pickup_time || 'N/A')}</td></tr>
                                <tr><td><strong>Booking Date:</strong></td><td>${escapeHtml(booking.booking_date || 'N/A')}</td></tr>
                                <tr><td><strong>Booking Status:</strong></td><td><span class="badge bg-secondary">${escapeHtml(booking.status || 'N/A')}</span></td></tr>
                                </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td width="140"><strong>Goods Type:</strong></td><td>${escapeHtml(booking.goods_type || 'N/A')}</td></tr>
                                <tr><td><strong>Goods Weight:</strong></td><td>${booking.goods_weight || 0} kg</td></tr>
                                <tr><td><strong>Estimated Fare:</strong></td><td>Rs ${booking.estimated_fare || 0}</td></tr>
                                <tr><td><strong>Actual Fare:</strong></td><td>Rs ${booking.actual_fare || 0}</td></tr>
                                <tr><td><strong>Penalty Applied:</strong></td><td>Rs ${booking.penalty_amount || 0}</td></tr>
                                </table>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function renderVehicleTab(complaint) {
            const vehicle = complaint.booking?.vehicle || {};
            const container = document.getElementById('vehicleTab');
            
            container.innerHTML = `
                <div class="info-card">
                    <h6><i class="fas fa-truck me-2"></i>Vehicle Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td width="140"><strong>Vehicle Number:</strong></td><td>${escapeHtml(vehicle.vehicle_number || 'N/A')}</td></tr>
                                <tr><td><strong>Vehicle Type:</strong></td><td>${escapeHtml(vehicle.vehicle_type || 'N/A')}</td></tr>
                                <tr><td><strong>Weight Capacity:</strong></td><td>${vehicle.weight_capacity || 0} kg</td></tr>
                                <tr><td><strong>Chassis Number:</strong></td><td>${escapeHtml(vehicle.chassis_number || 'N/A')}</td></tr>
                                </table>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function renderCustomerTab(complaint) {
            const customer = complaint.customer || complaint.booking?.customer || {};
            const container = document.getElementById('customerTab');
            
            container.innerHTML = `
                <div class="info-card">
                    <h6><i class="fas fa-user me-2"></i>Customer Information (Who Filed Complaint)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td width="140"><strong>Customer Name:</strong></td><td>${escapeHtml(customer.name || 'N/A')}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${escapeHtml(customer.email || 'N/A')}</td></tr>
                                <tr><td><strong>Mobile:</strong></td><td>${escapeHtml(customer.mobile || customer.cnic || 'N/A')}</td></tr>
                                </table>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function renderTimelineTab(complaint) {
            const container = document.getElementById('timelineTab');
            let timelineHtml = `
                <div class="info-card">
                    <h6><i class="fas fa-history me-2"></i>Complaint Timeline</h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <strong>Filed:</strong> ${complaint.created_at || 'N/A'}
                        </div>
            `;
            
            if (complaint.status === 'in_review') {
                timelineHtml += `
                    <div class="timeline-item mt-2">
                        <strong>Under Review:</strong> Complaint is being reviewed by admin
                    </div>
                `;
            }
            
            if (complaint.resolved_at) {
                timelineHtml += `
                    <div class="timeline-item mt-2">
                        <strong>Resolved:</strong> ${new Date(complaint.resolved_at).toLocaleString()}
                    </div>
                `;
            }
            
            if (!complaint.resolved_at && complaint.status !== 'in_review') {
                timelineHtml += `
                    <div class="timeline-item mt-2">
                        <strong>Current Status:</strong> ${getStatusText(complaint.status)} - Awaiting resolution
                    </div>
                `;
            }
            
            timelineHtml += `
                    </div>
                </div>
            `;
            
            container.innerHTML = timelineHtml;
        }
        
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show selected tab content
            if (tabName === 'booking') {
                document.getElementById('bookingTab').classList.add('active');
            } else if (tabName === 'vehicle') {
                document.getElementById('vehicleTab').classList.add('active');
            } else if (tabName === 'customer') {
                document.getElementById('customerTab').classList.add('active');
            } else if (tabName === 'timeline') {
                document.getElementById('timelineTab').classList.add('active');
            }
        }
        
        function escapeHtml(text) {
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Tab click handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Setup tab click listeners
            const tabs = document.querySelectorAll('.info-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Show corresponding tab content
                    const tabName = this.getAttribute('data-tab');
                    showTab(tabName);
                });
            });
            
            loadAndDisplayComplaints();
            
            // Set active filter button
            document.querySelectorAll('.btn-filter').forEach(btn => {
                if (!btn.classList.contains('active') && btn.getAttribute('data-status') === 'all') {
                    btn.classList.add('active');
                }
            });
        });
        
        // Add filter button styles
        const style = document.createElement('style');
        style.textContent = `
            .btn-filter {
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 13px;
                border: 1px solid #ddd;
                background: white;
                transition: all 0.3s;
                margin: 0 3px;
            }
            .btn-filter.active {
                background: #3498db;
                color: white;
                border-color: #3498db;
            }
            .btn-filter:hover {
                background: #3498db;
                color: white;
                border-color: #3498db;
            }
            .status-filter {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }
            .timeline-item {
                padding: 8px 0;
                border-bottom: 1px solid #eee;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>