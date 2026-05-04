<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Admin Bookings Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
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
        }
        
        .sidebar .logo {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar .logo h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar .logo h3 span {
            color: var(--secondary);
        }
        
        .sidebar .logo small {
            font-size: 0.7rem;
            opacity: 0.7;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52,152,219,0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(52,152,219,0.3);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 22px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }
        
        /* Topbar */
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .topbar .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary);
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        
        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        /* Filter Buttons */
        .filter-btn {
            border: 2px solid #e9ecef;
            background: white;
            padding: 8px 20px;
            border-radius: 30px;
            margin: 0 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            border-color: var(--secondary);
            background: var(--secondary);
            color: white;
        }
        
        .filter-btn.active {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }
        
        /* Booking Cards */
        .booking-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
            cursor: pointer;
            border-left: 4px solid var(--secondary);
            position: relative;
            overflow: hidden;
        }
        
        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .booking-card.request { border-left-color: var(--warning); }
        .booking-card.accept { border-left-color: var(--success); }
        .booking-card.reject { border-left-color: var(--danger); }
        .booking-card.complete { border-left-color: var(--info); }
        
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
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
        
        /* Info Tabs */
        .info-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .info-tab {
            padding: 10px 20px;
            font-weight: 500;
            color: #6c757d;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s;
        }
        
        .info-tab:hover {
            background: #f8f9fa;
            color: var(--secondary);
        }
        
        .info-tab.active {
            color: var(--secondary);
            border-bottom: 3px solid var(--secondary);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Map Container */
        #detailMap {
            height: 350px;
            width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        /* Turn by Turn */
        .turn-by-turn {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .turn-item {
            display: flex;
            align-items: flex-start;
            padding: 10px;
            margin-bottom: 8px;
            background: white;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .turn-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .turn-number {
            background: var(--secondary);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .turn-instruction {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .turn-distance {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--secondary), #dee2e6);
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
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: white;
            border: 3px solid #dee2e6;
            z-index: 1;
        }
        
        .timeline-item.completed .timeline-marker {
            background: var(--success);
            border-color: var(--success);
        }
        
        .timeline-item.active .timeline-marker {
            background: var(--warning);
            border-color: var(--warning);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(243,156,18,0.4); }
            70% { box-shadow: 0 0 0 10px rgba(243,156,18,0); }
            100% { box-shadow: 0 0 0 0 rgba(243,156,18,0); }
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .timeline-content p {
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .timeline-content small {
            font-size: 0.7rem;
            color: #adb5bd;
        }
        
        /* Customer/Provider Cards */
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--secondary);
        }
        
        .info-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary);
        }
        
        /* Review Item */
        .review-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .rating-stars {
            color: #ffc107;
            font-size: 0.9rem;
        }
        
        /* Complaint Item */
        .complaint-item {
            background: #fff8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid var(--warning);
        }
        
        /* Payment Card */
        .payment-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }
            .main-content { margin-left: 80px; }
        }
        
        @media (max-width: 768px) {
            .content-area { padding: 15px; }
            .stat-card .stat-value { font-size: 1.3rem; }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
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
                <a class="nav-link " href="{{route('admin.login')}}">
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
                <a class="nav-link active" href="{{route('admin.see-bookings')}}">
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
            <div>
                <h5 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Manage Bookings</h5>
                <small class="text-muted">View and monitor all booking activities</small>
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Admin">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ $adminName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i> Admin Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-shield-alt me-2"></i> System Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-area">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card" style="border-left-color: var(--secondary)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" id="totalBookingsCount">{{ $totalBookings }}</div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card" style="border-left-color: var(--warning)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" id="pendingBookingsCount">{{ $pendingBookings }}</div>
                                <div class="stat-label">Pending</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card" style="border-left-color: var(--success)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" id="acceptedBookingsCount">{{ $acceptedBookings }}</div>
                                <div class="stat-label">Accepted</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card" style="border-left-color: var(--info)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-value" id="completedBookingsCount">{{ $completedBookings }}</div>
                                <div class="stat-label">Completed</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-flag-checkered"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-3 fw-semibold"><i class="fas fa-filter me-2"></i>Filter by Status:</span>
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="request">Pending</button>
                            <button class="filter-btn" data-filter="accept">Accepted</button>
                            <button class="filter-btn" data-filter="reject">Rejected</button>
                            <button class="filter-btn" data-filter="complete">Completed</button>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <div class="input-group" style="max-width: 300px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search bookings...">
                                <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Grid -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Booking List</h5>
                    <div class="d-flex align-items-center">
                        <div class="btn-group me-2">
                            <button class="btn btn-sm btn-outline-secondary" id="prevBookingPage" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="nextBookingPage">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <span class="text-muted small" id="bookingPaginationInfo">Page 1</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="bookingsGrid" class="row">
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-3">Loading bookings...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer p-3 bg-white border-top text-center">
            <p class="mb-0 text-muted small">&copy; 2024 TruckLink Admin Panel. All rights reserved.</p>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading booking details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let currentPage = 1;
        let lastPage = 1;
        let bookings = [];
        let currentFilter = 'all';
        let searchTerm = '';
        let map = null;
        let currentRoutingControl = null;
        let selectedBookingId = null;
        let searchTimer = null;

        // ==================== INITIALIZATION ====================
        document.addEventListener('DOMContentLoaded', function() {
            loadBookings(currentPage, currentFilter, searchTerm);
            
            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    currentPage = 1;
                    loadBookings(currentPage, currentFilter, searchTerm);
                });
            });
            
            // Search input
            document.getElementById('searchInput').addEventListener('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    searchTerm = this.value;
                    currentPage = 1;
                    loadBookings(currentPage, currentFilter, searchTerm);
                }, 500);
            });
            
            // Pagination buttons
            document.getElementById('prevBookingPage').onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    loadBookings(currentPage, currentFilter, searchTerm);
                }
            };
            
            document.getElementById('nextBookingPage').onclick = () => {
                if (currentPage < lastPage) {
                    currentPage++;
                    loadBookings(currentPage, currentFilter, searchTerm);
                }
            };
        });

        // ==================== LOAD BOOKINGS ====================
        function loadBookings(page, filter, search = '') {
            const grid = document.getElementById('bookingsGrid');
            grid.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p>Loading bookings...</p></div>`;
            
            let url = `/admin/bookings-data?page=${page}&per_page=6`;
            if (filter !== 'all') url += `&filter=${filter}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bookings = data.bookings;
                    currentPage = data.current_page;
                    lastPage = data.last_page;
                    
                    document.getElementById('bookingPaginationInfo').textContent = `Page ${currentPage} of ${lastPage}`;
                    document.getElementById('prevBookingPage').disabled = currentPage <= 1;
                    document.getElementById('nextBookingPage').disabled = currentPage >= lastPage;
                    
                    renderBookings(bookings);
                } else {
                    grid.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-4x text-danger"></i><h5 class="mt-3">Error Loading Bookings</h5></div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                grid.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-4x text-danger"></i><h5 class="mt-3">Failed to Load Bookings</h5></div>`;
            });
        }

        // ==================== RENDER BOOKINGS ====================
        function renderBookings(bookings) {
            const grid = document.getElementById('bookingsGrid');
            
            if (!bookings || bookings.length === 0) {
                grid.innerHTML = `<div class="col-12 text-center py-5"><i class="fas fa-inbox fa-4x text-muted"></i><h5 class="mt-3">No Bookings Found</h5><p class="text-muted">No bookings match your criteria.</p></div>`;
                return;
            }
            
            let html = '';
            bookings.forEach(booking => {
                const date = booking.booking_date || new Date(booking.created_at).split(' ')[0];
                html += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="booking-card ${booking.status}" onclick="openBookingDetails(${booking.id})">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge-status ${booking.badge_class}">${booking.status_text}</span>
                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>${date}</small>
                            </div>
                            <div class="mb-2">
                                <h6 class="mb-1"><i class="fas fa-user me-2 text-secondary"></i>${escapeHtml(booking.customer_name)}</h6>
                                <small class="text-muted"><i class="fas fa-building me-1"></i>Provider: ${escapeHtml(booking.provider_name)}</small>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-map-marker-alt text-danger me-2" style="font-size: 12px;"></i>
                                    <span class="small text-truncate">${escapeHtml(booking.pickup_location)}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-success me-2" style="font-size: 12px;"></i>
                                    <span class="small text-truncate">${escapeHtml(booking.dropoff_location)}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Goods</small>
                                    <div class="fw-semibold small">${escapeHtml(booking.goods_type) || 'N/A'}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Weight</small>
                                    <div class="fw-semibold small">${booking.goods_weight || 0} Ton</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Vehicle</small>
                                    <div class="fw-semibold small">${escapeHtml(booking.vehicle_type)}<br>${escapeHtml(booking.vehicle_number)}</div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Fare</small>
                                    <div class="fw-bold text-primary">Rs ${booking.estimated_fare || 0}</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm w-100" onclick="event.stopPropagation(); openBookingDetails(${booking.id})">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            grid.innerHTML = html;
        }

        // ==================== OPEN BOOKING DETAILS ====================
        function openBookingDetails(bookingId) {
            selectedBookingId = bookingId;
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            modal.show();
            loadBookingDetails(bookingId);
        }

        // ==================== LOAD BOOKING DETAILS ====================
        function loadBookingDetails(bookingId) {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading booking details...</p></div>`;
            
            fetch(`/admin/booking/${bookingId}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderBookingDetailsModal(data.booking);
                    loadBookingTimeline(bookingId);
                    loadBookingReviews(bookingId);
                    loadBookingComplaints(bookingId);
                    loadBookingPayment(bookingId);
                    drawRouteOnMap(data.booking);
                } else {
                    modalBody.innerHTML = `<div class="alert alert-danger">Failed to load booking details: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = `<div class="alert alert-danger">Error loading booking details</div>`;
            });
        }

        // ==================== RENDER BOOKING DETAILS MODAL ====================
        function renderBookingDetailsModal(booking) {
            const modalBody = document.getElementById('modalBody');
            
            const statusClass = booking.status === 'request' ? 'warning' : 
                               (booking.status === 'accept' ? 'success' : 
                               (booking.status === 'reject' ? 'danger' : 'info'));
            
            const paymentStatusClass = booking.payment_status === 'paid' ? 'success' : 'warning';
            
            modalBody.innerHTML = `
                <!-- Map Container -->
                <div id="detailMap" class="mb-4"></div>
                
                <!-- Route Info -->
                <div id="routeInfoCard" class="route-info-card bg-light p-3 rounded mb-4" style="display:none;">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Distance</small>
                            <div class="fw-bold" id="routeDistance">-- km</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Duration</small>
                            <div class="fw-bold" id="routeDuration">-- min</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Toll Cost</small>
                            <div class="fw-bold" id="routeToll">Rs 0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Turn by Turn Directions -->
                <div id="turnByTurnContainer" class="turn-by-turn mb-4" style="display:none;">
                    <h6 class="mb-3"><i class="fas fa-turn-down me-2"></i>Turn-by-Turn Directions</h6>
                    <div id="turnByTurnList"></div>
                </div>
                
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h4 class="mb-0"><i class="fas fa-truck me-2"></i>Booking #${booking.id}</h4>
                    <div>
                        <span class="badge bg-${statusClass} me-2">${booking.status_text}</span>
                        <span class="badge bg-${paymentStatusClass}">${booking.payment_status || 'pending'}</span>
                    </div>
                </div>
                
                <!-- Info Tabs -->
                <div class="info-tabs">
                    <div class="info-tab active" onclick="switchTab('details')">📋 Details</div>
                    <div class="info-tab" onclick="switchTab('customer')">👤 Customer</div>
                    <div class="info-tab" onclick="switchTab('provider')">🏢 Provider</div>
                    <div class="info-tab" onclick="switchTab('vehicle')">🚛 Vehicle</div>
                    <div class="info-tab" onclick="switchTab('payment')">💰 Payment</div>
                    <div class="info-tab" onclick="switchTab('reviews')">⭐ Reviews</div>
                    <div class="info-tab" onclick="switchTab('complaints')">⚠️ Complaints</div>
                    <div class="info-tab" onclick="switchTab('timeline')">📅 Timeline</div>
                    <div class="info-tab" onclick="switchTab('directions')">🗺️ Directions</div>
                </div>
                
                <!-- Tab: Details -->
                <div id="detailsTab" class="tab-content active">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-pin me-2"></i>Location Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="35%"><strong>Pickup:</strong></td><td>${escapeHtml(booking.pickup_location)}</td></tr>
                                <tr><td><strong>Dropoff:</strong></td><td>${escapeHtml(booking.dropoff_location)}</td></tr>
                                <tr><td><strong>Pickup Time:</strong></td><td>${booking.pickup_time || 'N/A'}</td></tr>
                                <tr><td><strong>Booking Date:</strong></td><td>${booking.booking_date || 'N/A'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-box me-2"></i>Goods Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="35%"><strong>Type:</strong></td><td>${escapeHtml(booking.goods_type) || 'N/A'}</td></tr>
                                <tr><td><strong>Weight:</strong></td><td>${booking.goods_weight || 0} Ton</td></tr>
                                <tr><td><strong>Instructions:</strong></td><td>${escapeHtml(booking.special_instructions) || 'None'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fas fa-road me-2"></i>Route Info</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>Est. Distance:</strong></td><td>${booking.estimated_distance || 0} km</td></tr>
                                <tr><td><strong>Est. Fare:</strong></td><td>Rs ${booking.estimated_fare || 0}</td></tr>
                                ${booking.selected_route_name ? `<tr><td><strong>Route:</strong></td><td>${escapeHtml(booking.selected_route_name)}</td></tr>` : ''}
                                ${booking.has_tolls ? `<tr><td><strong>Toll Cost:</strong></td><td>Rs ${booking.toll_cost || 0}</td></tr>` : ''}
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-flag-checkered me-2"></i>Actual Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>Actual Distance:</strong></td><td>${booking.actual_distance || 0} km</td></tr>
                                <tr><td><strong>Actual Fare:</strong></td><td>Rs ${booking.actual_fare || 0}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${booking.status === 'reject' && booking.rejection_reason ? `
                        <div class="alert alert-danger mt-3">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Rejection Reason:</strong><br>
                            ${escapeHtml(booking.rejection_reason)}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Tab: Customer -->
                <div id="customerTab" class="tab-content">
                    ${booking.customer ? `
                        <div class="info-card d-flex align-items-center">
                            <img src="${booking.customer.profile_image}" alt="Customer" class="me-3">
                            <div>
                                <h5 class="mb-1">${escapeHtml(booking.customer.name)}</h5>
                                <p class="mb-0 small"><i class="fas fa-envelope me-2"></i>${booking.customer.email}</p>
                                <p class="mb-0 small"><i class="fas fa-phone me-2"></i>${booking.customer.mobile}</p>
                                ${booking.customer.cnic ? `<p class="mb-0 small"><i class="fas fa-id-card me-2"></i>${booking.customer.cnic}</p>` : ''}
                            </div>
                        </div>
                    ` : '<p class="text-muted">Customer information not available</p>'}
                </div>
                
                <!-- Tab: Provider -->
                <div id="providerTab" class="tab-content">
                    ${booking.provider ? `
                        <div class="info-card d-flex align-items-center">
                            <img src="${booking.provider.profile_image}" alt="Provider" class="me-3">
                            <div>
                                <h5 class="mb-1">${escapeHtml(booking.provider.name)}</h5>
                                <p class="mb-0 small"><i class="fas fa-envelope me-2"></i>${booking.provider.email}</p>
                                <p class="mb-0 small"><i class="fas fa-phone me-2"></i>${booking.provider.mobile}</p>
                                ${booking.provider.cnic ? `<p class="mb-0 small"><i class="fas fa-id-card me-2"></i>${booking.provider.cnic}</p>` : ''}
                            </div>
                        </div>
                    ` : '<p class="text-muted">Provider information not available</p>'}
                </div>
                
                <!-- Tab: Vehicle -->
                <div id="vehicleTab" class="tab-content">
                    ${booking.vehicle ? `
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr><td width="40%"><strong>Vehicle Type:</strong></td><td>${escapeHtml(booking.vehicle.vehicle_type)}</td></tr>
                                    <tr><td><strong>Vehicle Number:</strong></td><td>${escapeHtml(booking.vehicle.vehicle_number)}</td></tr>
                                    <tr><td><strong>Chassis Number:</strong></td><td>${escapeHtml(booking.vehicle.chassis_number)}</td></tr>
                                    <tr><td><strong>Weight Capacity:</strong></td><td>${booking.vehicle.weight_capacity} kg</td></tr>
                                    <tr><td><strong>Can Carry:</strong></td><td>${escapeHtml(booking.vehicle.can_carry)}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td>${booking.vehicle.status}</td></tr>
                                    <tr><td><strong>Is Booked:</strong></td><td>${booking.vehicle.is_booked === 'yes' ? 'Yes' : 'No'}</td></tr>
                                </table>
                            </div>
                        </div>
                    ` : '<p class="text-muted">Vehicle information not available</p>'}
                </div>
                
                <!-- Tab: Payment -->
                <div id="paymentTab" class="tab-content">
                    <div class="text-center py-4" id="paymentLoading">
                        <div class="spinner-border spinner-border-sm"></div> Loading payment details...
                    </div>
                    <div id="paymentContent" style="display:none;"></div>
                </div>
                
                <!-- Tab: Reviews -->
                <div id="reviewsTab" class="tab-content">
                    <div id="reviewsList" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm"></div> Loading reviews...
                    </div>
                </div>
                
                <!-- Tab: Complaints -->
                <div id="complaintsTab" class="tab-content">
                    <div id="complaintsList" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm"></div> Loading complaints...
                    </div>
                </div>
                
                <!-- Tab: Timeline -->
                <div id="timelineTab" class="tab-content">
                    <div class="timeline" id="timelineContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm"></div> Loading timeline...
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Directions -->
                <div id="directionsTab" class="tab-content">
                    <div class="turn-by-turn" style="margin-top:0">
                        <h6 class="mb-3"><i class="fas fa-list me-2"></i>Complete Directions</h6>
                        <div id="fullDirectionsList">
                            <p class="text-muted text-center">Loading directions...</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // ==================== DRAW ROUTE ON MAP ====================
        function drawRouteOnMap(booking) {
            const mapContainer = document.getElementById('detailMap');
            if (!mapContainer) return;
            
            if (map) {
                map.remove();
            }
            
            map = L.map('detailMap');
            
            let pickupLat = parseFloat(booking.pickup_lat);
            let pickupLng = parseFloat(booking.pickup_lng);
            let dropoffLat = parseFloat(booking.dropoff_lat);
            let dropoffLng = parseFloat(booking.dropoff_lng);
            
            // Default center for Pakistan if coordinates invalid
            if (isNaN(pickupLat) || isNaN(dropoffLat)) {
                map.setView([30.3753, 69.3451], 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                L.marker([30.3753, 69.3451]).bindPopup('Location coordinates not available').addTo(map);
                return;
            }
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Custom markers
            const pickupIcon = L.divIcon({
                html: '<div style="background:#dc3545;width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 5px rgba(0,0,0,0.2)"></div>',
                iconSize: [24, 24],
                className: 'custom-marker'
            });
            
            const dropoffIcon = L.divIcon({
                html: '<div style="background:#28a745;width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 5px rgba(0,0,0,0.2)"></div>',
                iconSize: [24, 24],
                className: 'custom-marker'
            });
            
            L.marker([pickupLat, pickupLng], { icon: pickupIcon })
                .bindPopup(`<b>Pickup Location</b><br>${escapeHtml(booking.pickup_location)}`)
                .addTo(map);
            
            L.marker([dropoffLat, dropoffLng], { icon: dropoffIcon })
                .bindPopup(`<b>Dropoff Location</b><br>${escapeHtml(booking.dropoff_location)}`)
                .addTo(map);
            
            // Draw route
            if (booking.route_polyline) {
                try {
                    let polylineData = typeof booking.route_polyline === 'string' ? JSON.parse(booking.route_polyline) : booking.route_polyline;
                    let latlngs = [];
                    
                    if (polylineData.coordinates) {
                        latlngs = polylineData.coordinates.map(coord => [coord[1], coord[0]]);
                    } else if (Array.isArray(polylineData)) {
                        latlngs = polylineData.map(coord => [coord[1], coord[0]]);
                    }
                    
                    if (latlngs.length > 0) {
                        L.polyline(latlngs, { color: '#3498db', weight: 5, opacity: 0.8 }).addTo(map);
                        map.fitBounds(L.latLngBounds(latlngs), { padding: [50, 50] });
                    } else {
                        map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
                    }
                } catch (e) {
                    console.error('Error drawing polyline:', e);
                    map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
                }
            } else {
                L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], { 
                    color: '#3498db', 
                    weight: 4, 
                    dashArray: '5,10' 
                }).addTo(map);
                map.fitBounds(L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]), { padding: [50, 50] });
            }
            
            // Display directions
            displayDirections(booking);
        }
        
        function displayDirections(booking) {
            const turnContainer = document.getElementById('turnByTurnContainer');
            const turnList = document.getElementById('turnByTurnList');
            const fullDirectionsList = document.getElementById('fullDirectionsList');
            const routeInfoCard = document.getElementById('routeInfoCard');
            
            let directions = [];
            
            if (booking.route_directions) {
                try {
                    directions = typeof booking.route_directions === 'string' ? JSON.parse(booking.route_directions) : booking.route_directions;
                } catch (e) {}
            }
            
            // Generate fallback directions if needed
            if (directions.length === 0 && booking.estimated_distance) {
                directions = generateFallbackDirections(booking);
            }
            
            if (directions.length > 0) {
                const directionsHtml = renderDirectionsList(directions);
                if (turnList) turnList.innerHTML = directionsHtml;
                if (fullDirectionsList) fullDirectionsList.innerHTML = directionsHtml;
                if (turnContainer) turnContainer.style.display = 'block';
            } else {
                if (turnContainer) turnContainer.style.display = 'none';
                if (fullDirectionsList) fullDirectionsList.innerHTML = '<p class="text-muted text-center">No turn-by-turn directions available</p>';
            }
            
            // Update route info card
            if (routeInfoCard && booking.estimated_distance) {
                document.getElementById('routeDistance').textContent = booking.estimated_distance + ' km';
                const minutes = Math.round((booking.estimated_distance / 40) * 60);
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                document.getElementById('routeDuration').textContent = hours > 0 ? `${hours}h ${mins}m` : `${mins} min`;
                document.getElementById('routeToll').textContent = `Rs ${booking.toll_cost || 0}`;
                routeInfoCard.style.display = 'block';
            }
        }
        
        function renderDirectionsList(directions) {
            if (!directions || directions.length === 0) return '<p class="text-muted text-center">No directions available</p>';
            
            let html = '';
            directions.forEach((step, index) => {
                let instruction = step.instruction || step.text || 'Continue';
                instruction = instruction.replace(/<[^>]*>/g, '');
                instruction = instruction.charAt(0).toUpperCase() + instruction.slice(1);
                
                let distance = step.distance || 0;
                let distanceText = '';
                if (distance > 1000) distanceText = (distance/1000).toFixed(1) + ' km';
                else if (distance > 0) distanceText = Math.round(distance) + ' m';
                
                let icon = 'fa-arrow-right';
                let instLower = instruction.toLowerCase();
                if (instLower.includes('left')) icon = 'fa-arrow-left';
                else if (instLower.includes('right')) icon = 'fa-arrow-right';
                else if (instLower.includes('straight')) icon = 'fa-arrow-up';
                else if (instLower.includes('destination')) icon = 'fa-flag-checkered';
                
                html += `
                    <div class="turn-item">
                        <div class="turn-number">${index+1}</div>
                        <div class="turn-content">
                            <div class="turn-instruction"><i class="fas ${icon} me-2 text-primary"></i>${instruction}</div>
                            ${distanceText ? `<div class="turn-distance"><i class="fas fa-road me-1"></i>${distanceText}</div>` : ''}
                        </div>
                    </div>
                `;
            });
            return html;
        }
        
        function generateFallbackDirections(booking) {
            const directions = [];
            directions.push({ instruction: `Start from ${booking.pickup_location}`, distance: 0 });
            if (booking.estimated_distance) {
                directions.push({ instruction: `Continue towards ${booking.dropoff_location}`, distance: booking.estimated_distance * 1000 });
            }
            directions.push({ instruction: `Arrive at destination: ${booking.dropoff_location}`, distance: 0 });
            return directions;
        }

        // ==================== LOAD TIMELINE ====================
        function loadBookingTimeline(bookingId) {
            fetch(`/admin/booking/${bookingId}/tracking`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const timelineContainer = document.getElementById('timelineContainer');
                if (timelineContainer && data.success && data.booking.timeline) {
                    let html = '';
                    data.booking.timeline.forEach(item => {
                        html += `
                            <div class="timeline-item ${item.status}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6><i class="fas ${item.icon || 'fa-info-circle'} me-2"></i>${item.title}</h6>
                                    <p>${escapeHtml(item.description)}</p>
                                    ${item.timestamp ? `<small><i class="far fa-clock me-1"></i>${item.timestamp}</small>` : ''}
                                </div>
                            </div>
                        `;
                    });
                    timelineContainer.innerHTML = html;
                }
            })
            .catch(error => console.error('Error loading timeline:', error));
        }

        // ==================== LOAD REVIEWS ====================
        function loadBookingReviews(bookingId) {
            fetch(`/admin/booking/${bookingId}/reviews`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const reviewsList = document.getElementById('reviewsList');
                if (reviewsList) {
                    if (data.success && data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(review => {
                            html += `
                                <div class="review-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>${escapeHtml(review.customer.name)}</strong>
                                            <div class="rating-stars">${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}</div>
                                        </div>
                                        <small class="text-muted">${review.created_at}</small>
                                    </div>
                                    <p class="mb-0">${escapeHtml(review.review || 'No comment')}</p>
                                </div>
                            `;
                        });
                        reviewsList.innerHTML = html;
                    } else {
                        reviewsList.innerHTML = '<p class="text-muted text-center">No reviews found for this booking.</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading reviews:', error);
                const reviewsList = document.getElementById('reviewsList');
                if (reviewsList) reviewsList.innerHTML = '<p class="text-muted text-center">Failed to load reviews.</p>';
            });
        }

        // ==================== LOAD COMPLAINTS ====================
        function loadBookingComplaints(bookingId) {
            fetch(`/admin/booking/${bookingId}/complaints`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const complaintsList = document.getElementById('complaintsList');
                if (complaintsList) {
                    if (data.success && data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(complaint => {
                            html += `
                                <div class="complaint-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge ${complaint.status_badge} me-2">${complaint.status_text}</span>
                                            <strong>${escapeHtml(complaint.subject)}</strong>
                                        </div>
                                        <small class="text-muted">${complaint.created_at}</small>
                                    </div>
                                    <p class="mb-2">${escapeHtml(complaint.description)}</p>
                                    ${complaint.admin_response ? `
                                        <div class="alert alert-info mb-0 mt-2">
                                            <small><strong>Admin Response:</strong> ${escapeHtml(complaint.admin_response)}</small>
                                        </div>
                                    ` : ''}
                                </div>
                            `;
                        });
                        complaintsList.innerHTML = html;
                    } else {
                        complaintsList.innerHTML = '<p class="text-muted text-center">No complaints found for this booking.</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading complaints:', error);
                const complaintsList = document.getElementById('complaintsList');
                if (complaintsList) complaintsList.innerHTML = '<p class="text-muted text-center">Failed to load complaints.</p>';
            });
        }

        // ==================== LOAD PAYMENT ====================
        function loadBookingPayment(bookingId) {
            fetch(`/admin/booking/${bookingId}/payment`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const paymentLoading = document.getElementById('paymentLoading');
                const paymentContent = document.getElementById('paymentContent');
                
                if (paymentLoading) paymentLoading.style.display = 'none';
                
                if (paymentContent && data.success) {
                    const breakdown = data.payment_breakdown;
                    const payment = data.payment;
                    
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="payment-card mb-3">
                                    <h6 class="mb-3">Payment Summary</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Estimated Fare:</span>
                                        <span class="fw-bold">Rs ${breakdown.estimated_fare}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Actual Fare:</span>
                                        <span class="fw-bold">Rs ${breakdown.actual_fare}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Penalty Amount:</span>
                                        <span class="fw-bold text-danger">Rs ${breakdown.penalty_amount}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Toll Cost:</span>
                                        <span>Rs ${breakdown.toll_cost}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Net Payable:</span>
                                        <span class="fw-bold fs-5">Rs ${breakdown.net_payable}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Payment Details</h6>
                                        <table class="table table-sm">
                                            <tr><td width="40%">Payment Method:</td><td>${data.booking.payment_method || 'Not specified'}</td></tr>
                                            <tr><td>Payment Status:</td><td><span class="badge ${data.booking.payment_status_badge}">${data.booking.payment_status || 'pending'}</span></td></tr>
                    `;
                    
                    if (payment) {
                        html += `
                            <tr><td>Transaction ID:</td><td><code>${payment.transaction_id || 'N/A'}</code></td></tr>
                            <tr><td>Paid At:</td><td>${payment.paid_at || 'N/A'}</td></tr>
                            ${payment.card_number_masked ? `<tr><td>Card Number:</td><td>${payment.card_number_masked}</td></tr>` : ''}
                        `;
                    }
                    
                    html += `
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    paymentContent.innerHTML = html;
                    paymentContent.style.display = 'block';
                } else if (paymentContent) {
                    paymentContent.innerHTML = '<p class="text-muted text-center">Payment information not available</p>';
                    paymentContent.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading payment:', error);
                const paymentLoading = document.getElementById('paymentLoading');
                const paymentContent = document.getElementById('paymentContent');
                if (paymentLoading) paymentLoading.style.display = 'none';
                if (paymentContent) {
                    paymentContent.innerHTML = '<p class="text-muted text-center">Failed to load payment details</p>';
                    paymentContent.style.display = 'block';
                }
            });
        }

        // ==================== TAB SWITCHING ====================
        function switchTab(tabName) {
            // Update tab active state
            document.querySelectorAll('.info-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Hide all tab contents
            const tabIds = ['details', 'customer', 'provider', 'vehicle', 'payment', 'reviews', 'complaints', 'timeline', 'directions'];
            tabIds.forEach(id => {
                const element = document.getElementById(`${id}Tab`);
                if (element) element.classList.remove('active');
            });
            
            // Show selected tab
            const selectedTab = document.getElementById(`${tabName}Tab`);
            if (selectedTab) selectedTab.classList.add('active');
        }

        // ==================== UTILITY FUNCTIONS ====================
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>