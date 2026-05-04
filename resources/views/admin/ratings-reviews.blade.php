<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Admin Ratings & Reviews</title>
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
        
        .card-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* Stats Cards */
        .stat-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 16px;
            transition: all 0.3s;
            height: 100%;
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
        
        /* Rating Stars */
        .rating-stars {
            color: #f39c12;
            font-size: 1rem;
            letter-spacing: 2px;
        }
        
        .rating-stars.large {
            font-size: 1.5rem;
        }
        
        /* Review Card */
        .review-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: white;
        }
        
        .review-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .booking-badge {
            background: #e9ecef;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .review-text {
            color: #4a5568;
            line-height: 1.6;
            margin: 15px 0;
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
        
        .rating-filter-btn {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 3px;
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
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
            border-left: 3px solid var(--secondary);
        }
        
        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-weight: 600;
            margin-top: 5px;
        }
        
        /* Chart Container */
        .chart-container {
            max-width: 300px;
            margin: 0 auto;
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
        
        .vehicle-info {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .vehicle-info i {
            width: 20px;
            color: var(--secondary);
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
                <a class="nav-link" href="{{route('admin.complaints')}}">
    <i class="fas fa-comments"></i> <span>Complaints Center</span>
  
</a>
                <a class="nav-link active" href="{{route('admin.ratings-reviews')}}">
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
            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Ratings & Reviews Management</h5>
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
                    <h4 class="mb-1 fw-bold">Ratings & Reviews</h4>
                    <p class="text-muted mb-0">Manage and monitor customer feedback</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="search-bar">
                        <i class="fas fa-search text-muted"></i>
                        <input type="text" id="searchInput" placeholder="Search reviews..." onkeyup="searchReviews()">
                    </div>
                    <button class="btn btn-blue-border" onclick="refreshReviews()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-primary" id="totalReviews">0</div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-warning" id="avgRating">0</div>
                        <div class="stat-label">Average Rating</div>
                        <div class="rating-stars" id="avgRatingStars"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-success" id="approvedReviews">0</div>
                        <div class="stat-label">Approved Reviews</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-warning" id="pendingReviews">0</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution Chart & Summary -->
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Rating Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="ratingChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Rating Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div id="ratingBreakdown">
                                <!-- Rating bars will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-star me-1"></i>Rating Filter</label>
                            <div>
                                <button class="filter-btn rating-filter-btn active" data-rating="all" onclick="setRatingFilter('all')">All</button>
                                <button class="filter-btn rating-filter-btn" data-rating="5" onclick="setRatingFilter(5)">★★★★★ (5)</button>
                                <button class="filter-btn rating-filter-btn" data-rating="4" onclick="setRatingFilter(4)">★★★★☆ (4)</button>
                                <button class="filter-btn rating-filter-btn" data-rating="3" onclick="setRatingFilter(3)">★★★☆☆ (3)</button>
                                <button class="filter-btn rating-filter-btn" data-rating="2" onclick="setRatingFilter(2)">★★☆☆☆ (2)</button>
                                <button class="filter-btn rating-filter-btn" data-rating="1" onclick="setRatingFilter(1)">★☆☆☆☆ (1)</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-check-circle me-1"></i>Status Filter</label>
                            <div>
                                <button class="filter-btn active" data-status-filter="all" onclick="setStatusFilter('all')">All</button>
                                <button class="filter-btn" data-status-filter="approved" onclick="setStatusFilter('approved')">Approved</button>
                                <button class="filter-btn" data-status-filter="pending" onclick="setStatusFilter('pending')">Pending Approval</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-sort-amount-down me-1"></i>Sort By</label>
                            <select class="form-select" id="sortBy" onchange="sortReviews()">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="highest">Highest Rating</option>
                                <option value="lowest">Lowest Rating</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Reviews</h5>
                    <span class="text-muted" id="reviewsCount">Showing 0 reviews</span>
                </div>
                <div class="card-body">
                    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading reviews...</p>
                    </div>
                    <div id="reviewsList">
                        <!-- Reviews will be loaded here -->
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted" id="paginationInfo">Page 1 of 1</div>
                        <nav>
                            <ul class="pagination mb-0" id="paginationControls">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Details Modal -->
    <div class="modal fade" id="reviewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-star me-2"></i>Review Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewDetailBody">
                    <!-- Dynamic content -->
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
        let currentPage = 1;
        let lastPage = 1;
        let currentRatingFilter = 'all';
        let currentStatusFilter = 'all';
        let currentSearchTerm = '';
        let currentSort = 'newest';
        let allReviews = [];
        let ratingChart = null;
        
        // Load reviews on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadReviews();
        });
        
        function loadReviews() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            
            let url = `/admin/ratings-reviews/data?page=${currentPage}&per_page=10&rating=${currentRatingFilter}&status=${currentStatusFilter}&sort=${currentSort}`;
            if (currentSearchTerm) {
                url += `&search=${encodeURIComponent(currentSearchTerm)}`;
            }
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                if(data.success) {
                    allReviews = data.reviews || [];
                    updateStats(data.stats);
                    renderReviews(allReviews);
                    updatePagination(data.current_page, data.last_page, data.total);
                    updateRatingChart(data.stats);
                    updateRatingBreakdown(data.stats);
                } else {
                    showError('Failed to load reviews');
                }
            })
            .catch(err => {
                loadingIndicator.style.display = 'none';
                console.error(err);
                showError('Error loading reviews');
            });
        }
        
        function updateStats(stats) {
            if (!stats) return;
            
            document.getElementById('totalReviews').textContent = stats.total || 0;
            document.getElementById('avgRating').textContent = stats.avg_rating || 0;
            document.getElementById('approvedReviews').textContent = stats.approved || 0;
            document.getElementById('pendingReviews').textContent = stats.pending_approval || 0;
            
            // Update average rating stars
            const avgRating = stats.avg_rating || 0;
            const starsHtml = generateStarRating(avgRating);
            document.getElementById('avgRatingStars').innerHTML = starsHtml;
        }
        
        function generateStarRating(rating) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = (rating - fullStars) >= 0.5;
            let stars = '';
            
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="fas fa-star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    stars += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    stars += '<i class="far fa-star"></i>';
                }
            }
            return stars;
        }
        
        function updateRatingChart(stats) {
            const ctx = document.getElementById('ratingChart').getContext('2d');
            
            if (ratingChart) {
                ratingChart.destroy();
            }
            
            ratingChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['5 Star', '4 Star', '3 Star', '2 Star', '1 Star'],
                    datasets: [{
                        data: [
                            stats.five_star || 0,
                            stats.four_star || 0,
                            stats.three_star || 0,
                            stats.two_star || 0,
                            stats.one_star || 0
                        ],
                        backgroundColor: [
                            '#27ae60',
                            '#3498db',
                            '#f39c12',
                            '#e67e22',
                            '#e74c3c'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        function updateRatingBreakdown(stats) {
            const total = stats.total || 1;
            const breakdownHtml = `
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star text-success"></i> 5 Star</span>
                        <span>${stats.five_star || 0} (${Math.round((stats.five_star || 0) / total * 100)}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: ${(stats.five_star || 0) / total * 100}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star text-primary"></i> 4 Star</span>
                        <span>${stats.four_star || 0} (${Math.round((stats.four_star || 0) / total * 100)}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: ${(stats.four_star || 0) / total * 100}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star text-warning"></i> 3 Star</span>
                        <span>${stats.three_star || 0} (${Math.round((stats.three_star || 0) / total * 100)}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: ${(stats.three_star || 0) / total * 100}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star text-orange"></i> 2 Star</span>
                        <span>${stats.two_star || 0} (${Math.round((stats.two_star || 0) / total * 100)}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-orange" style="background-color: #e67e22; width: ${(stats.two_star || 0) / total * 100}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-star text-danger"></i> 1 Star</span>
                        <span>${stats.one_star || 0} (${Math.round((stats.one_star || 0) / total * 100)}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: ${(stats.one_star || 0) / total * 100}%"></div>
                    </div>
                </div>
            `;
            
            document.getElementById('ratingBreakdown').innerHTML = breakdownHtml;
        }
        
        function renderReviews(reviews) {
            const container = document.getElementById('reviewsList');
            
            if (!reviews || reviews.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No reviews found</p>
                    </div>
                `;
                document.getElementById('reviewsCount').innerHTML = 'Showing 0 reviews';
                return;
            }
            
            let html = '';
            reviews.forEach(review => {
                const customerName = review.customer?.name || 'N/A';
                const customerEmail = review.customer?.email || 'N/A';
                const providerName = review.provider?.name || 'N/A';
                const providerEmail = review.provider?.email || 'N/A';
                const vehicleNumber = review.booking?.vehicle?.vehicle_number || 'N/A';
                const vehicleType = review.booking?.vehicle?.vehicle_type || 'N/A';
                const bookingId = review.booking_id;
                const pickupLocation = review.booking?.pickup_location || 'N/A';
                const dropoffLocation = review.booking?.dropoff_location || 'N/A';
                const reviewText = review.review || 'No review text provided';
                const rating = review.rating;
                const createdAt = new Date(review.created_at).toLocaleDateString();
                const isApproved = review.is_approved;
                
                html += `
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    ${customerName.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h6 class="mb-0">${escapeHtml(customerName)}</h6>
                                    <small class="text-muted">${escapeHtml(customerEmail)}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="rating-stars mb-1">${generateStarRating(rating)}</div>
                                <small class="text-muted">${createdAt}</small>
                            </div>
                        </div>
                        
                        <div class="review-text">
                            <p class="mb-2">"${escapeHtml(reviewText)}"</p>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-hashtag"></i> Booking ID</div>
                                <div class="info-value">#${bookingId}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-truck"></i> Vehicle</div>
                                <div class="info-value">${escapeHtml(vehicleNumber)} (${escapeHtml(vehicleType)})</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-user-tie"></i> Vehicle Owner</div>
                                <div class="info-value">${escapeHtml(providerName)}</div>
                                <small class="text-muted">${escapeHtml(providerEmail)}</small>
                            </div>
                           
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button class="btn btn-sm btn-info" onclick="viewReviewDetails(${review.id})">
                                <i class="fas fa-eye me-1"></i> View Details
                            </button>
                            ${!isApproved ? `
                                <button class="btn btn-sm btn-success" onclick="toggleReviewStatus(${review.id})">
                                    <i class="fas fa-check-circle me-1"></i> Approve
                                </button>
                            ` : `
                                <button class="btn btn-sm btn-warning" onclick="toggleReviewStatus(${review.id})">
                                    <i class="fas fa-eye-slash me-1"></i> Hide
                                </button>
                            `}
                            <button class="btn btn-sm btn-danger" onclick="deleteReview(${review.id})">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('reviewsCount').innerHTML = `Showing ${reviews.length} reviews`;
        }
        
        function viewReviewDetails(reviewId) {
            const review = allReviews.find(r => r.id === reviewId);
            if (!review) return;
            
            const modalBody = document.getElementById('reviewDetailBody');
            
            modalBody.innerHTML = `
                <div class="info-card mb-3">
                    <h6><i class="fas fa-star text-warning me-2"></i>Review Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Rating:</strong> ${generateStarRating(review.rating)} (${review.rating}/5)<br>
                            <strong>Date:</strong> ${new Date(review.created_at).toLocaleString()}<br>
                            <strong>Status:</strong> <span class="badge ${review.is_approved ? 'bg-success' : 'bg-warning'}">${review.is_approved ? 'Approved' : 'Pending Approval'}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Booking ID:</strong> #${review.booking_id}<br>
                            <strong>Review ID:</strong> #${review.id}
                        </div>
                    </div>
                </div>
                
                <div class="info-card mb-3">
                    <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
                    <table class="table table-borderless">
                        <tr><td width="120"><strong>Name:</strong></td><td>${escapeHtml(review.customer?.name || 'N/A')}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${escapeHtml(review.customer?.email || 'N/A')}</td></tr>
                        <tr><td><strong>CNIC:</strong></td><td>${escapeHtml(review.customer?.cnic || 'N/A')}</td></tr>
                    </table>
                </div>
                
                <div class="info-card mb-3">
                    <h6><i class="fas fa-user-tie me-2"></i>Vehicle Owner Information</h6>
                    <table class="table table-borderless">
                        <tr><td width="120"><strong>Name:</strong></td><td>${escapeHtml(review.provider?.name || 'N/A')}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${escapeHtml(review.provider?.email || 'N/A')}</td></tr>
                        <tr><td><strong>CNIC:</strong></td><td>${escapeHtml(review.provider?.cnic || 'N/A')}</td></tr>
                    </table>
                </div>
                
                <div class="info-card mb-3">
                    <h6><i class="fas fa-truck me-2"></i>Vehicle Information</h6>
                    <table class="table table-borderless">
                        <tr><td width="120"><strong>Vehicle Number:</strong></td><td>${escapeHtml(review.booking?.vehicle?.vehicle_number || 'N/A')}</td></tr>
                        <tr><td><strong>Vehicle Type:</strong></td><td>${escapeHtml(review.booking?.vehicle?.vehicle_type || 'N/A')}</td></tr>
                        <tr><td><strong>Chassis Number:</strong></td><td>${escapeHtml(review.booking?.vehicle?.chassis_number || 'N/A')}</td></tr>
                        <tr><td><strong>Weight Capacity:</strong></td><td>${review.booking?.vehicle?.weight_capacity || 0} kg</td></tr>
                    </table>
                </div>
                
                <div class="info-card mb-3">
                    <h6><i class="fas fa-route me-2"></i>Booking Information</h6>
                    <table class="table table-borderless">
                        <tr><td width="120"><strong>Pickup Location:</strong></td><td>${escapeHtml(review.booking?.pickup_location || 'N/A')}</td></tr>
                        <tr><td><strong>Dropoff Location:</strong></td><td>${escapeHtml(review.booking?.dropoff_location || 'N/A')}</td></tr>
                        <tr><td><strong>Goods Type:</strong></td><td>${escapeHtml(review.booking?.goods_type || 'N/A')}</td></tr>
                        <tr><td><strong>Goods Weight:</strong></td><td>${review.booking?.goods_weight || 0} kg</td></tr>
                        <tr><td><strong>Estimated Fare:</strong></td><td>Rs ${review.booking?.estimated_fare || 0}</td></tr>
                        <tr><td><strong>Actual Fare:</strong></td><td>Rs ${review.booking?.actual_fare || 0}</td></tr>
                        <tr><td><strong>Payment Status:</strong></td><td><span class="badge ${review.booking?.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${review.booking?.payment_status || 'pending'}</span></td></tr>
                        <tr><td><strong>Booking Status:</strong></td><td><span class="badge bg-info">${review.booking?.status || 'N/A'}</span></td></tr>
                        <tr><td><strong>Booking Date:</strong></td><td>${review.booking?.booking_date || 'N/A'}</td></tr>
                    </table>
                </div>
                
                <div class="info-card">
                    <h6><i class="fas fa-comment me-2"></i>Review Text</h6>
                    <p class="mb-0">"${escapeHtml(review.review || 'No review text provided')}"</p>
                </div>
            `;
            
            new bootstrap.Modal(document.getElementById('reviewDetailsModal')).show();
        }
        
        function toggleReviewStatus(reviewId) {
            fetch(`/admin/reviews/${reviewId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadReviews();
                } else {
                    showToast(data.message || 'Failed to update review status', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error updating review status', 'error');
            });
        }
        
        function deleteReview(reviewId) {
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                fetch(`/admin/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        loadReviews();
                    } else {
                        showToast(data.message || 'Failed to delete review', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Error deleting review', 'error');
                });
            }
        }
        
        function setRatingFilter(rating) {
            currentRatingFilter = rating;
            currentPage = 1;
            
            document.querySelectorAll('.rating-filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-rating') == rating) {
                    btn.classList.add('active');
                }
            });
            
            loadReviews();
        }
        
        function setStatusFilter(status) {
            currentStatusFilter = status;
            currentPage = 1;
            
            document.querySelectorAll('[data-status-filter]').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-status-filter') == status) {
                    btn.classList.add('active');
                }
            });
            
            loadReviews();
        }
        
        function sortReviews() {
            currentSort = document.getElementById('sortBy').value;
            currentPage = 1;
            loadReviews();
        }
        
        function searchReviews() {
            currentSearchTerm = document.getElementById('searchInput').value;
            currentPage = 1;
            loadReviews();
        }
        
        function refreshReviews() {
            currentPage = 1;
            loadReviews();
        }
        
        function updatePagination(current, last, total) {
            currentPage = current;
            lastPage = last;
            
            const paginationInfo = document.getElementById('paginationInfo');
            paginationInfo.textContent = `Page ${current} of ${last} (${total} total reviews)`;
            
            let prevDisabled = current <= 1 ? 'disabled' : '';
            let nextDisabled = current >= last ? 'disabled' : '';
            
            let html = `
                <li class="page-item ${prevDisabled}">
                    <a class="page-link" href="#" onclick="changePage(${current-1}); return false;">Previous</a>
                </li>
            `;
            
            let startPage = Math.max(1, current - 2);
            let endPage = Math.min(last, current + 2);
            
            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1); return false;">1</a></li>`;
                if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                         </li>`;
            }
            
            if (endPage < last) {
                if (endPage < last - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${last}); return false;">${last}</a></li>`;
            }
            
            html += `
                <li class="page-item ${nextDisabled}">
                    <a class="page-link" href="#" onclick="changePage(${current+1}); return false;">Next</a>
                </li>
            `;
            
            document.getElementById('paginationControls').innerHTML = html;
        }
        
        function changePage(page) {
            if (page < 1 || page > lastPage) return;
            currentPage = page;
            loadReviews();
        }
        
        function showToast(message, type = 'success') {
            // Simple alert for now - you can replace with a proper toast notification
            alert((type === 'success' ? '✅ ' : '❌ ') + message);
        }
        
        function showError(message) {
            const container = document.getElementById('reviewsList');
            container.innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="refreshReviews()">Try Again</button>
                </div>
            `;
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>