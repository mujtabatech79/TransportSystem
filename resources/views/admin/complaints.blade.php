<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Admin Complaints Center</title>
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
        
        /* Table Styles */
        .complaints-table {
            font-size: 0.9rem;
        }
        
        .complaints-table td {
            vertical-align: middle;
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
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
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
        
        /* Map Container */
        #complaintDetailMap {
            height: 350px;
            width: 100%;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        
        /* Tabs */
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
        
        .provider-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary);
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
        
        .btn-resolve {
            background: linear-gradient(135deg, var(--success) 0%, #219653 100%);
            border: none;
            color: white;
        }
        
        .btn-resolve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        .btn-email {
            background: linear-gradient(135deg, var(--info) 0%, #138496 100%);
            border: none;
            color: white;
        }
        
        .btn-email:hover {
            transform: translateY(-2px);
        }
        
        /* Search and Filter Bar */
        .search-bar {
            background: white;
            border-radius: 50px;
            padding: 5px 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .search-bar input {
            border: none;
            padding: 10px 0;
            outline: none;
            width: 250px;
        }
        
        .search-bar button {
            background: transparent;
            border: none;
            color: var(--secondary);
        }
        
        .status-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .status-filter-btn {
            padding: 5px 15px;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            background: white;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .status-filter-btn.active {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }
        
        .status-filter-btn:hover {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
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
                <a class="nav-link" href="{{route('admin.availableVehicles')}}">
                    <i class="fas fa-clipboard-check"></i> <span>Available Vehicles</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-money-bill-wave"></i> <span>Payments & Finance</span>
                </a>
                <a class="nav-link active" href="{{route('admin.complaints')}}">
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
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Complaints Management</h5>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">Admin</span>
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
                    <p class="text-muted mb-0">Manage and resolve customer complaints by category</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="search-bar">
                        <i class="fas fa-search text-muted"></i>
                        <input type="text" id="searchInput" placeholder="Search complaints..." onkeyup="filterComplaints()">
                    </div>
                    <button class="btn btn-blue-border" onclick="refreshComplaints()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                    <button class="btn btn-blue-border" onclick="expandAllCategories()">
                        <i class="fas fa-expand-alt me-2"></i>Expand All
                    </button>
                    <button class="btn btn-blue-border" onclick="collapseAllCategories()">
                        <i class="fas fa-compress-alt me-2"></i>Collapse All
                    </button>
                </div>
            </div>

            <!-- Stats Cards by Category -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value late-value" id="lateDeliveryCount">0</div>
                        <div class="stat-label"><i class="fas fa-clock me-1"></i> Late Delivery</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value damaged-value" id="damagedGoodsCount">0</div>
                        <div class="stat-label"><i class="fas fa-box-open me-1"></i> Damaged Goods</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value rude-value" id="rudeDriversCount">0</div>
                        <div class="stat-label"><i class="fas fa-user-friends me-1"></i> Rude Drivers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="stat-value other-value" id="otherCount">0</div>
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
                            <button class="status-filter-btn active" data-status-filter="all" onclick="setStatusFilter('all')">All</button>
                            <button class="status-filter-btn" data-status-filter="pending" onclick="setStatusFilter('pending')">Pending</button>
                            <button class="status-filter-btn" data-status-filter="in_review" onclick="setStatusFilter('in_review')">Under Review</button>
                            <button class="status-filter-btn" data-status-filter="resolved" onclick="setStatusFilter('resolved')">Resolved</button>
                            <button class="status-filter-btn" data-status-filter="rejected" onclick="setStatusFilter('rejected')">Rejected</button>
                        </div>
                        <div class="text-muted" id="totalComplaintsInfo">Total: 0 complaints</div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Loading complaints...</p>
            </div>

            <!-- Category: Late Delivery -->
            <div class="category-section late-delivery" id="lateDeliverySection">
                <div class="category-header" onclick="toggleCategory('lateDelivery')">
                    <h5><i class="fas fa-clock me-2"></i>Late Delivery Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="lateDeliveryBadge">0</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="lateDeliveryIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="lateDeliveryContent">
                    <div class="complaints-list" id="lateDeliveryList">
                        <!-- Late delivery complaints will be loaded here -->
                        <div class="empty-category">
                            <i class="fas fa-check-circle"></i>
                            <p>No late delivery complaints</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Damaged Goods -->
            <div class="category-section damaged-goods" id="damagedGoodsSection">
                <div class="category-header" onclick="toggleCategory('damagedGoods')">
                    <h5><i class="fas fa-box-open me-2"></i>Damaged Goods Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="damagedGoodsBadge">0</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="damagedGoodsIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="damagedGoodsContent">
                    <div class="complaints-list" id="damagedGoodsList">
                        <div class="empty-category">
                            <i class="fas fa-check-circle"></i>
                            <p>No damaged goods complaints</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Rude Drivers -->
            <div class="category-section rude-drivers" id="rudeDriversSection">
                <div class="category-header" onclick="toggleCategory('rudeDrivers')">
                    <h5><i class="fas fa-user-friends me-2"></i>Rude Drivers Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="rudeDriversBadge">0</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="rudeDriversIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="rudeDriversContent">
                    <div class="complaints-list" id="rudeDriversList">
                        <div class="empty-category">
                            <i class="fas fa-check-circle"></i>
                            <p>No rude drivers complaints</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Other Complaints -->
            <div class="category-section other-complaints" id="otherSection">
                <div class="category-header" onclick="toggleCategory('other')">
                    <h5><i class="fas fa-ellipsis-h me-2"></i>Other Complaints</h5>
                    <div>
                        <span class="badge bg-secondary badge-count" id="otherBadge">0</span>
                        <i class="fas fa-chevron-down collapse-icon ms-2" id="otherIcon"></i>
                    </div>
                </div>
                <div class="category-card" id="otherContent">
                    <div class="complaints-list" id="otherList">
                        <div class="empty-category">
                            <i class="fas fa-check-circle"></i>
                            <p>No other complaints</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaint Details Modal -->
    <div class="modal fade" id="complaintDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i>Complaint Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="complaintDetailBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolve Complaint Modal -->
    <div class="modal fade" id="resolveComplaintModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Resolve Complaint</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="resolveComplaintId">
                    <div class="mb-3">
                        <label class="form-label">Admin Response *</label>
                        <textarea class="form-control" id="resolveResponse" rows="5" placeholder="Write your response to the customer..."></textarea>
                        <div class="info-text mt-2">
                            <i class="fas fa-info-circle"></i> This response will be emailed to the customer.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmResolveBtn">Resolve Complaint</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notify Owner Modal -->
    <div class="modal fade" id="notifyOwnerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Notify Vehicle Owner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="notifyComplaintId">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will send an email to the vehicle owner informing them about this complaint.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vehicle Owner Email</label>
                        <input type="text" class="form-control" id="ownerEmailDisplay" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="confirmNotifyBtn">Send Email</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Resolution Email Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Send Resolution Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="emailComplaintId">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        This will send the resolution email to the customer.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Email</label>
                        <input type="text" class="form-control" id="customerEmailDisplay" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Response</label>
                        <textarea class="form-control" id="responseDisplay" rows="4" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="confirmSendEmailBtn">Send Email</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Global variables
        let allComplaints = [];
        let currentStatusFilter = 'all';
        let currentSearchTerm = '';
        let map = null;
        
        // Complaint type mapping
        const complaintTypeMapping = {
            'late_delivery': 'Late Delivery',
            'late delivery': 'Late Delivery',
            'damaged_goods': 'Damaged Goods',
            'damaged goods': 'Damaged Goods',
            'rude_driver': 'Rude Drivers',
            'rude driver': 'Rude Drivers',
            'rude drivers': 'Rude Drivers',
            'other': 'Other'
        };
        
        // Load complaints on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadComplaints();
            
            // Resolve button
            document.getElementById('confirmResolveBtn').onclick = resolveComplaint;
            document.getElementById('confirmNotifyBtn').onclick = sendOwnerNotification;
            document.getElementById('confirmSendEmailBtn').onclick = sendResolutionEmail;
        });
        
        function loadComplaints() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            
            fetch(`/admin/complaints/data?per_page=100`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                if(data.success) {
                    allComplaints = data.complaints || [];
                    filterAndDisplayComplaints();
                } else {
                    showError('Failed to load complaints');
                }
            })
            .catch(err => {
                loadingIndicator.style.display = 'none';
                console.error(err);
                showError('Error loading complaints');
            });
        }
        
        function filterAndDisplayComplaints() {
            let filtered = [...allComplaints];
            
            // Apply status filter
            if (currentStatusFilter !== 'all') {
                filtered = filtered.filter(c => c.status === currentStatusFilter);
            }
            
            // Apply search filter
            if (currentSearchTerm.trim() !== '') {
                const search = currentSearchTerm.toLowerCase();
                filtered = filtered.filter(c => 
                    c.subject?.toLowerCase().includes(search) ||
                    c.description?.toLowerCase().includes(search) ||
                    c.complaint_type?.toLowerCase().includes(search) ||
                    c.customer?.name?.toLowerCase().includes(search) ||
                    c.booking_id?.toString().includes(search) ||
                    c.id?.toString().includes(search)
                );
            }
            
            // Group by complaint type
            const lateDelivery = filtered.filter(c => isLateDeliveryComplaint(c.complaint_type));
            const damagedGoods = filtered.filter(c => isDamagedGoodsComplaint(c.complaint_type));
            const rudeDrivers = filtered.filter(c => isRudeDriversComplaint(c.complaint_type));
            const other = filtered.filter(c => isOtherComplaint(c.complaint_type));
            
            // Update counts
            updateCategoryCounts(lateDelivery.length, damagedGoods.length, rudeDrivers.length, other.length);
            
            // Update total complaints info
            document.getElementById('totalComplaintsInfo').innerHTML = `Total: ${filtered.length} complaints ${currentStatusFilter !== 'all' ? `(filtered by ${currentStatusFilter})` : ''}`;
            
            // Render each category
            renderComplaintList('lateDeliveryList', lateDelivery, 'late_delivery');
            renderComplaintList('damagedGoodsList', damagedGoods, 'damaged_goods');
            renderComplaintList('rudeDriversList', rudeDrivers, 'rude_driver');
            renderComplaintList('otherList', other, 'other');
        }
        
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
        
        function updateCategoryCounts(late, damaged, rude, other) {
            document.getElementById('lateDeliveryCount').textContent = late;
            document.getElementById('damagedGoodsCount').textContent = damaged;
            document.getElementById('rudeDriversCount').textContent = rude;
            document.getElementById('otherCount').textContent = other;
            
            document.getElementById('lateDeliveryBadge').textContent = late;
            document.getElementById('damagedGoodsBadge').textContent = damaged;
            document.getElementById('rudeDriversBadge').textContent = rude;
            document.getElementById('otherBadge').textContent = other;
        }
        
        function renderComplaintList(containerId, complaints, categoryType) {
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
                const customerName = complaint.customer?.name || (complaint.booking?.customer?.name || 'N/A');
                const statusBadge = complaint.status_badge || getStatusBadge(complaint.status);
                const statusText = complaint.status_text || getStatusText(complaint.status);
                const createdDate = complaint.created_at_formatted || complaint.created_at || '';
                
                html += `
                    <div class="complaint-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="complaint-subject">
                                    #${complaint.id} - ${escapeHtml(complaint.subject)}
                                    <span class="badge ${statusBadge} ms-2">${statusText}</span>
                                </div>
                                <div class="complaint-details">
                                    <i class="fas fa-user me-1"></i> ${escapeHtml(customerName)} &nbsp;|&nbsp;
                                    <i class="fas fa-calendar me-1"></i> ${escapeHtml(createdDate)} &nbsp;|&nbsp;
                                    <i class="fas fa-hashtag me-1"></i> Booking #${complaint.booking_id}
                                </div>
                                <div class="complaint-details mt-1">
                                    <small class="text-muted">${escapeHtml(complaint.description?.substring(0, 100))}${complaint.description?.length > 100 ? '...' : ''}</small>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info btn-action" onclick="viewComplaintDetails(${complaint.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${complaint.status !== 'resolved' ? `
                                    <button class="btn btn-sm btn-success btn-action" onclick="openResolveModal(${complaint.id})" title="Resolve Complaint">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                ` : `
                                    <button class="btn btn-sm btn-info btn-action" onclick="openSendEmailModal(${complaint.id})" title="Send Resolution Email">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                `}
                                <button class="btn btn-sm btn-warning btn-action" onclick="openNotifyOwnerModal(${complaint.id}, '${escapeHtml(complaint.provider?.email || '')}')" title="Notify Vehicle Owner">
                                    <i class="fas fa-truck"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
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
        
        function setStatusFilter(status) {
            currentStatusFilter = status;
            
            // Update active button styling
            document.querySelectorAll('.status-filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-status-filter') === status) {
                    btn.classList.add('active');
                }
            });
            
            filterAndDisplayComplaints();
        }
        
        function filterComplaints() {
            currentSearchTerm = document.getElementById('searchInput').value;
            filterAndDisplayComplaints();
        }
        
        function toggleCategory(category) {
            const content = document.getElementById(`${category}Content`);
            const icon = document.getElementById(`${category}Icon`);
            
            if (content.classList.contains('collapsed-category')) {
                content.classList.remove('collapsed-category');
                icon.classList.remove('rotated');
            } else {
                content.classList.add('collapsed-category');
                icon.classList.add('rotated');
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
        
        function refreshComplaints() {
            loadComplaints();
        }
        
        function viewComplaintDetails(complaintId) {
            const modal = new bootstrap.Modal(document.getElementById('complaintDetailsModal'));
            const modalBody = document.getElementById('complaintDetailBody');
            modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading complaint details...</p></div>`;
            modal.show();
            
            fetch(`/admin/complaints/${complaintId}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    renderComplaintDetails(data.complaint);
                } else {
                    modalBody.innerHTML = `<div class="alert alert-danger">Failed to load complaint details</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = `<div class="alert alert-danger">Error loading details</div>`;
            });
        }
        
        function renderComplaintDetails(complaint) {
            const modalBody = document.getElementById('complaintDetailBody');
            const statusBadge = complaint.status_badge || getStatusBadge(complaint.status);
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-card">
                            <h6><i class="fas fa-exclamation-triangle text-danger me-2"></i>Complaint Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Complaint ID:</strong> #${complaint.id}<br>
                                    <strong>Booking ID:</strong> #${complaint.booking_id}<br>
                                    <strong>Type:</strong> <span class="badge bg-secondary">${escapeHtml(complaint.complaint_type)}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong> <span class="badge ${statusBadge}">${complaint.status_text || getStatusText(complaint.status)}</span><br>
                                    <strong>Date Filed:</strong> ${complaint.created_at || 'N/A'}<br>
                                    ${complaint.resolved_at ? `<strong>Resolved At:</strong> ${complaint.resolved_at}` : ''}
                                </div>
                            </div>
                            <hr>
                            <strong>Subject:</strong> ${escapeHtml(complaint.subject)}<br>
                            <strong>Description:</strong><br>
                            <p class="mt-2">${escapeHtml(complaint.description)}</p>
                            ${complaint.admin_response ? `
                                <div class="alert alert-success mt-3">
                                    <strong><i class="fas fa-check-circle"></i> Admin Response:</strong><br>
                                    ${escapeHtml(complaint.admin_response)}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
                
                <div class="info-tabs">
                    <div class="info-tab active" onclick="switchDetailTab('customer')">Customer Info</div>
                    <div class="info-tab" onclick="switchDetailTab('provider')">Vehicle Owner Info</div>
                    <div class="info-tab" onclick="switchDetailTab('booking')">Booking Details</div>
                </div>
                
                <div id="customerTab" class="tab-content active">
                    <div class="info-card">
                        <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
                        ${complaint.customer ? `
                            <table class="table table-borderless">
                                <tr><td width="150"><strong>Name:</strong></td><td>${escapeHtml(complaint.customer.name)}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${escapeHtml(complaint.customer.email)}</td></tr>
                                <tr><td><strong>Mobile:</strong></td><td>${escapeHtml(complaint.customer.mobile || 'N/A')}</td></tr>
                                <tr><td><strong>CNIC:</strong></td><td>${escapeHtml(complaint.customer.cnic || 'N/A')}</td></tr>
                            </table>
                        ` : '<p class="text-muted">Customer information not available</p>'}
                    </div>
                </div>
                
                <div id="providerTab" class="tab-content">
                    <div class="info-card">
                        <h6><i class="fas fa-truck me-2"></i>Vehicle Owner Information</h6>
                        ${complaint.provider ? `
                            <table class="table table-borderless">
                                <tr><td width="150"><strong>Name:</strong></td><td>${escapeHtml(complaint.provider.name)}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${escapeHtml(complaint.provider.email)}</td></tr>
                                <tr><td><strong>Mobile:</strong></td><td>${escapeHtml(complaint.provider.mobile || 'N/A')}</td></tr>
                                <tr><td><strong>CNIC:</strong></td><td>${escapeHtml(complaint.provider.cnic || 'N/A')}</td></tr>
                            </table>
                        ` : '<p class="text-muted">Vehicle owner information not available</p>'}
                    </div>
                </div>
                
                <div id="bookingTab" class="tab-content">
                    ${complaint.booking ? `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-location-dot me-2"></i>Route Details</h6>
                                    <table class="table table-sm">
                                        <tr><td width="120"><strong>Pickup:</strong></td><td>${escapeHtml(complaint.booking.pickup_location)}</td></tr>
                                        <tr><td><strong>Dropoff:</strong></td><td>${escapeHtml(complaint.booking.dropoff_location)}</td></tr>
                                        <tr><td><strong>Pickup Time:</strong></td><td>${escapeHtml(complaint.booking.pickup_time || 'N/A')}</td></tr>
                                        <tr><td><strong>Booking Date:</strong></td><td>${escapeHtml(complaint.booking.booking_date)}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-box me-2"></i>Goods Details</h6>
                                    <table class="table table-sm">
                                        <tr><td width="120"><strong>Goods Type:</strong></td><td>${escapeHtml(complaint.booking.goods_type || 'N/A')}</td></tr>
                                        <tr><td><strong>Weight:</strong></td><td>${complaint.booking.goods_weight || 0} kg</td></tr>
                                        <tr><td><strong>Special Instructions:</strong></td><td>${escapeHtml(complaint.booking.special_instructions || 'None')}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-truck me-2"></i>Vehicle Details</h6>
                                    <table class="table table-sm">
                                        <tr><td width="120"><strong>Type:</strong></td><td>${escapeHtml(complaint.booking.vehicle?.vehicle_type || 'N/A')}</td></tr>
                                        <tr><td><strong>Number:</strong></td><td>${escapeHtml(complaint.booking.vehicle?.vehicle_number || 'N/A')}</td></tr>
                                        <tr><td><strong>Capacity:</strong></td><td>${complaint.booking.vehicle?.weight_capacity || 0} kg</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-credit-card me-2"></i>Payment Details</h6>
                                    <table class="table table-sm">
                                        <tr><td width="120"><strong>Est. Fare:</strong></td><td>Rs ${complaint.booking.estimated_fare || 0}</td></tr>
                                        <tr><td><strong>Actual Fare:</strong></td><td>Rs ${complaint.booking.actual_fare || 0}</td></tr>
                                        <tr><td><strong>Payment Method:</strong></td><td>${escapeHtml(complaint.booking.payment_method || 'N/A')}</td></tr>
                                        <tr><td><strong>Payment Status:</strong></td><td><span class="badge bg-${complaint.booking.payment_status === 'paid' ? 'success' : 'warning'}">${complaint.booking.payment_status || 'pending'}</span></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    ` : '<p class="text-muted">Booking details not available</p>'}
                </div>
            `;
        }
        
        function switchDetailTab(tabName) {
            document.querySelectorAll('.info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            if(tabName === 'customer') {
                document.querySelector('.info-tab:first-child').classList.add('active');
                document.getElementById('customerTab').classList.add('active');
            } else if(tabName === 'provider') {
                document.querySelectorAll('.info-tab')[1].classList.add('active');
                document.getElementById('providerTab').classList.add('active');
            } else if(tabName === 'booking') {
                document.querySelectorAll('.info-tab')[2].classList.add('active');
                document.getElementById('bookingTab').classList.add('active');
            }
        }
        
        function openResolveModal(complaintId) {
            document.getElementById('resolveComplaintId').value = complaintId;
            document.getElementById('resolveResponse').value = '';
            new bootstrap.Modal(document.getElementById('resolveComplaintModal')).show();
        }
        
        function resolveComplaint() {
            const complaintId = document.getElementById('resolveComplaintId').value;
            const response = document.getElementById('resolveResponse').value.trim();
            
            if(!response) {
                alert('Please enter a response');
                return;
            }
            
            const btn = document.getElementById('confirmResolveBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Resolving...';
            btn.disabled = true;
            
            fetch(`/admin/complaints/${complaintId}/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ admin_response: response })
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    bootstrap.Modal.getInstance(document.getElementById('resolveComplaintModal')).hide();
                    loadComplaints();
                } else {
                    alert('❌ ' + data.message);
                }
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            })
            .catch(err => {
                console.error(err);
                alert('Error resolving complaint');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }
        
        function openNotifyOwnerModal(complaintId, ownerEmail) {
            document.getElementById('notifyComplaintId').value = complaintId;
            document.getElementById('ownerEmailDisplay').value = ownerEmail || 'Email not available';
            
            if(!ownerEmail) {
                alert('Vehicle owner email not found');
                return;
            }
            
            new bootstrap.Modal(document.getElementById('notifyOwnerModal')).show();
        }
        
        function sendOwnerNotification() {
            const complaintId = document.getElementById('notifyComplaintId').value;
            
            const btn = document.getElementById('confirmNotifyBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
            btn.disabled = true;
            
            fetch(`/admin/complaints/${complaintId}/notify-owner`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    bootstrap.Modal.getInstance(document.getElementById('notifyOwnerModal')).hide();
                } else {
                    alert('❌ ' + data.message);
                }
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            })
            .catch(err => {
                console.error(err);
                alert('Error sending email');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }
        
        function openSendEmailModal(complaintId) {
            fetch(`/admin/complaints/${complaintId}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success && data.complaint) {
                    const complaint = data.complaint;
                    document.getElementById('emailComplaintId').value = complaintId;
                    document.getElementById('customerEmailDisplay').value = complaint.customer?.email || 'Email not available';
                    document.getElementById('responseDisplay').value = complaint.admin_response || 'No response saved';
                    
                    if(!complaint.customer?.email) {
                        alert('Customer email not found');
                        return;
                    }
                    
                    new bootstrap.Modal(document.getElementById('sendEmailModal')).show();
                }
            });
        }
        
        function sendResolutionEmail() {
            const complaintId = document.getElementById('emailComplaintId').value;
            
            const btn = document.getElementById('confirmSendEmailBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
            btn.disabled = true;
            
            fetch(`/admin/complaints/${complaintId}/notify-customer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ ' + data.message);
                    bootstrap.Modal.getInstance(document.getElementById('sendEmailModal')).hide();
                } else {
                    alert('❌ ' + data.message);
                }
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            })
            .catch(err => {
                console.error(err);
                alert('Error sending email');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }
        
        function showError(message) {
            const lateList = document.getElementById('lateDeliveryList');
            if (lateList) {
                lateList.innerHTML = `<div class="empty-category text-danger"><i class="fas fa-exclamation-triangle"></i><p>${message}</p></div>`;
            }
        }
        
        function escapeHtml(text) {
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>