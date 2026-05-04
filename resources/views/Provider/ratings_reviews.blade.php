<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - My Ratings & Reviews</title>
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
        
        /* Vehicle Card */
        .vehicle-card {
            border: 1px solid #e9ecef;
            border-radius: 16px;
            margin-bottom: 25px;
            background: white;
            transition: all 0.3s;
            overflow: hidden;
        }
        
        .vehicle-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .vehicle-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            padding: 20px 25px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .vehicle-header:hover {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
        }
        
        .vehicle-header .vehicle-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .vehicle-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .vehicle-stats .stat {
            background: rgba(255,255,255,0.15);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .toggle-icon {
            transition: transform 0.3s;
        }
        
        .toggle-icon.rotated {
            transform: rotate(180deg);
        }
        
        .vehicle-body {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
        }
        
        .vehicle-body.show {
            max-height: 2000px;
            transition: max-height 0.6s ease-in;
        }
        
        .vehicle-body-inner {
            padding: 25px;
        }
        
        /* Review Card inside vehicle */
        .review-item {
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .review-item:last-child {
            border-bottom: none;
        }
        
        .review-item:hover {
            background: #f8f9fa;
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
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .review-text {
            color: #4a5568;
            line-height: 1.6;
            margin: 15px 0;
            font-style: italic;
        }
        
        .booking-info {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
            margin-top: 10px;
            font-size: 0.85rem;
        }
        
        .booking-info i {
            width: 25px;
            color: var(--secondary);
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
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        /* Search Bar */
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
        
        /* Rating Breakdown */
        .rating-breakdown-item {
            margin-bottom: 15px;
        }
        
        .rating-label {
            width: 60px;
            font-size: 0.85rem;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            flex: 1;
        }
        
        .rating-count {
            width: 40px;
            text-align: right;
            font-size: 0.85rem;
            color: #6c757d;
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
        
        .badge-rating {
            background: #f39c12;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
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
            <a class="nav-link " href="{{route('messages.conversations')}}"><i class="fas fa-comments"></i> <span>Messages</span></a>
            <a class="nav-link active" href="{{route('provider.ratings-reviews')}}"><i class="fas fa-star"></i> <span>Ratings & Reviews</span></a>
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
            <h5 class="mb-0"><i class="fas fa-star me-2"></i>My Ratings & Reviews</h5>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($providerName ?? 'Provider') }}&background=3498db&color=fff" alt="Provider">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ $providerName ?? 'Provider' }}</span>
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
                    <p class="text-muted mb-0">See what customers are saying about your vehicles</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="search-bar">
                        <i class="fas fa-search text-muted"></i>
                        <input type="text" id="searchInput" placeholder="Search reviews or customers..." onkeyup="filterReviews()">
                    </div>
                    <button class="btn btn-blue-border" onclick="location.reload()">
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
                        <div class="stat-label">Overall Rating</div>
                        <div class="rating-stars" id="avgRatingStars"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-success" id="totalVehicles">0</div>
                        <div class="stat-label">Total Vehicles</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-info" id="fiveStarCount">0</div>
                        <div class="stat-label">5-Star Reviews</div>
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
                            <div class="chart-container" style="max-width: 300px; margin: 0 auto;">
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
                        <div class="card-body" id="ratingBreakdown">
                            <!-- Dynamic content -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Row -->
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
                            <label class="form-label fw-semibold"><i class="fas fa-sort-amount-down me-1"></i>Sort By</label>
                            <select class="form-select" id="sortBy" onchange="sortReviews()">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="highest">Highest Rating</option>
                                <option value="lowest">Lowest Rating</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-truck me-1"></i>Vehicle Filter</label>
                            <select class="form-select" id="vehicleFilter" onchange="filterByVehicle()">
                                <option value="all">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} ({{ $vehicle->vehicle_type }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicles with Reviews -->
            <div id="vehiclesContainer">
                @php
                    $totalAllReviews = 0;
                    $totalAllRatings = 0;
                    $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                @endphp
                
                @foreach($vehicles as $vehicle)
                    @php
                        $vehicleReviews = $reviewsByVehicle[$vehicle->id] ?? collect();
                        $reviewCount = $vehicleReviews->count();
                        $totalAllReviews += $reviewCount;
                        $avgVehicleRating = $reviewCount > 0 ? $vehicleReviews->avg('rating') : 0;
                        $totalAllRatings += $vehicleReviews->sum('rating');
                        
                        // Count ratings for this vehicle
                        foreach($vehicleReviews as $review) {
                            if(isset($ratingCounts[$review->rating])) {
                                $ratingCounts[$review->rating]++;
                            }
                        }
                    @endphp
                    
                    <div class="vehicle-card" data-vehicle-id="{{ $vehicle->id }}" data-vehicle-number="{{ $vehicle->vehicle_number }}">
                        <div class="vehicle-header" onclick="toggleVehicle(this)">
                            <div class="vehicle-title">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-truck me-2"></i>
                                        {{ $vehicle->vehicle_number }}
                                        <small class="text-light-50 ms-2">({{ $vehicle->vehicle_type }})</small>
                                    </h5>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="vehicle-stats">
                                        <span class="stat">
                                            <i class="fas fa-star me-1"></i>
                                            {{ number_format($avgVehicleRating, 1) }}
                                        </span>
                                        <span class="stat">
                                            <i class="fas fa-comment me-1"></i>
                                            {{ $reviewCount }} Reviews
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down toggle-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="vehicle-body">
                            <div class="vehicle-body-inner">
                                @if($reviewCount > 0)
                                    @foreach($vehicleReviews as $review)
                                        <div class="review-item" data-rating="{{ $review->rating }}" data-review-text="{{ strtolower($review->review ?? '') }}" data-customer-name="{{ strtolower($review->customer->name ?? '') }}">
                                            <div class="review-header">
                                                <div class="reviewer-info">
                                                    <div class="reviewer-avatar">
                                                        {{ strtoupper(substr($review->customer->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $review->customer->name ?? 'Anonymous' }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>{{ $review->customer->email ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="rating-stars mb-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ $review->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="review-text">
                                                <p class="mb-0">"{{ $review->review ?? 'No review text provided.' }}"</p>
                                            </div>
                                            
                                            <div class="booking-info">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <i class="fas fa-hashtag"></i> Booking ID: #{{ $review->booking_id }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <i class="fas fa-route"></i> 
                                                        {{ $review->booking->pickup_location ?? 'N/A' }} → {{ $review->booking->dropoff_location ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <i class="fas fa-calendar-week"></i> 
                                                        Booking Date: {{ $review->booking->booking_date ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <i class="fas fa-money-bill-wave"></i> 
                                                        Fare: Rs {{ number_format($review->booking->actual_fare ?? $review->booking->estimated_fare ?? 0, 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-star"></i>
                                        <h5>No Reviews Yet</h5>
                                        <p class="text-muted">This vehicle hasn't received any reviews yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($vehicles->isEmpty())
                    <div class="card">
                        <div class="empty-state">
                            <i class="fas fa-truck"></i>
                            <h5>No Vehicles Registered</h5>
                            <p class="text-muted">You haven't registered any vehicles yet.</p>
                            <a href="{{route('my.vehicle')}}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Vehicle
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global variables
        let currentRatingFilter = 'all';
        let currentSort = 'newest';
        let currentVehicleFilter = 'all';
        let currentSearchTerm = '';
        let ratingChart = null;
        
        // Review data passed from backend
        const vehiclesData = @json($vehicles);
        const reviewsByVehicleData = @json($reviewsByVehicle);
        
        // Stats from backend
        const totalReviews = {{ $totalReviews ?? 0 }};
        const averageRating = {{ $averageRating ?? 0 }};
        const totalVehicles = {{ $vehicles->count() }};
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();
            updateRatingChart();
            updateRatingBreakdown();
        });
        
        function updateStats() {
            document.getElementById('totalReviews').textContent = totalReviews;
            document.getElementById('avgRating').textContent = averageRating.toFixed(1);
            document.getElementById('totalVehicles').textContent = totalVehicles;
            
            // Count 5-star reviews
            let fiveStarCount = 0;
            for (const vehicleId in reviewsByVehicleData) {
                fiveStarCount += reviewsByVehicleData[vehicleId].filter(r => r.rating === 5).length;
            }
            document.getElementById('fiveStarCount').textContent = fiveStarCount;
            
            // Update average rating stars
            const avgRating = averageRating;
            const fullStars = Math.floor(avgRating);
            const hasHalfStar = (avgRating - fullStars) >= 0.5;
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    starsHtml += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            document.getElementById('avgRatingStars').innerHTML = starsHtml;
        }
        
        function updateRatingChart() {
            // Calculate rating distribution
            let ratingCounts = {1: 0, 2: 0, 3: 0, 4: 0, 5: 0};
            
            for (const vehicleId in reviewsByVehicleData) {
                reviewsByVehicleData[vehicleId].forEach(review => {
                    if (ratingCounts[review.rating]) {
                        ratingCounts[review.rating]++;
                    }
                });
            }
            
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
                            ratingCounts[5] || 0,
                            ratingCounts[4] || 0,
                            ratingCounts[3] || 0,
                            ratingCounts[2] || 0,
                            ratingCounts[1] || 0
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
        
        function updateRatingBreakdown() {
            // Calculate rating distribution
            let ratingCounts = {1: 0, 2: 0, 3: 0, 4: 0, 5: 0};
            let total = 0;
            
            for (const vehicleId in reviewsByVehicleData) {
                reviewsByVehicleData[vehicleId].forEach(review => {
                    if (ratingCounts[review.rating]) {
                        ratingCounts[review.rating]++;
                        total++;
                    }
                });
            }
            
            if (total === 0) {
                document.getElementById('ratingBreakdown').innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No ratings available yet</p>
                    </div>
                `;
                return;
            }
            
            const stars = [5, 4, 3, 2, 1];
            const starColors = {
                5: 'success',
                4: 'primary',
                3: 'warning',
                2: 'orange',
                1: 'danger'
            };
            
            let html = '';
            stars.forEach(star => {
                const count = ratingCounts[star] || 0;
                const percentage = (count / total) * 100;
                const color = starColors[star];
                html += `
                    <div class="rating-breakdown-item d-flex align-items-center gap-2">
                        <div class="rating-label">
                            <i class="fas fa-star text-${color}"></i> ${star} Star
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-${color}" style="width: ${percentage}%"></div>
                        </div>
                        <div class="rating-count">${count}</div>
                    </div>
                `;
            });
            
            document.getElementById('ratingBreakdown').innerHTML = html;
        }
        
        function toggleVehicle(element) {
            const vehicleCard = element.closest('.vehicle-card');
            const vehicleBody = vehicleCard.querySelector('.vehicle-body');
            const toggleIcon = vehicleCard.querySelector('.toggle-icon');
            
            vehicleBody.classList.toggle('show');
            toggleIcon.classList.toggle('rotated');
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
            
            applyFilters();
        }
        
        function sortReviews() {
            currentSort = document.getElementById('sortBy').value;
            applyFilters();
        }
        
        function filterByVehicle() {
            currentVehicleFilter = document.getElementById('vehicleFilter').value;
            applyFilters();
        }
        
        function filterReviews() {
            currentSearchTerm = document.getElementById('searchInput').value.toLowerCase();
            applyFilters();
        }
        
        function applyFilters() {
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            
            vehicleCards.forEach(card => {
                const vehicleId = card.getAttribute('data-vehicle-id');
                const reviewItems = card.querySelectorAll('.review-item');
                
                // Check vehicle filter
                let showVehicle = true;
                if (currentVehicleFilter !== 'all' && currentVehicleFilter != vehicleId) {
                    showVehicle = false;
                }
                
                if (!showVehicle) {
                    card.style.display = 'none';
                    return;
                }
                
                let visibleReviews = 0;
                let reviewsArray = [];
                
                reviewItems.forEach(item => {
                    const rating = parseInt(item.getAttribute('data-rating'));
                    const reviewText = item.getAttribute('data-review-text') || '';
                    const customerName = item.getAttribute('data-customer-name') || '';
                    
                    let show = true;
                    
                    // Rating filter
                    if (currentRatingFilter !== 'all' && rating !== parseInt(currentRatingFilter)) {
                        show = false;
                    }
                    
                    // Search filter
                    if (currentSearchTerm && !reviewText.includes(currentSearchTerm) && !customerName.includes(currentSearchTerm)) {
                        show = false;
                    }
                    
                    if (show) {
                        visibleReviews++;
                        reviewsArray.push(item);
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Sort reviews if needed
                if (currentSort !== 'newest' && reviewsArray.length > 0) {
                    const container = card.querySelector('.vehicle-body-inner');
                    const parent = container;
                    
                    reviewsArray.sort((a, b) => {
                        const dateA = new Date(a.querySelector('.text-muted small')?.innerText || '');
                        const dateB = new Date(b.querySelector('.text-muted small')?.innerText || '');
                        const ratingA = parseInt(a.getAttribute('data-rating'));
                        const ratingB = parseInt(b.getAttribute('data-rating'));
                        
                        switch(currentSort) {
                            case 'oldest':
                                return dateA - dateB;
                            case 'highest':
                                return ratingB - ratingA;
                            case 'lowest':
                                return ratingA - ratingB;
                            case 'newest':
                            default:
                                return dateB - dateA;
                        }
                    });
                    
                    // Reorder DOM elements
                    reviewsArray.forEach(item => {
                        parent.appendChild(item);
                        item.style.display = 'block';
                    });
                } else if (currentSort === 'newest') {
                    reviewsArray.forEach(item => {
                        item.style.display = 'block';
                    });
                }
                
                // Show/hide vehicle based on visible reviews
                if (visibleReviews === 0) {
                    card.style.display = 'none';
                } else {
                    card.style.display = 'block';
                    
                    // Update vehicle header stats based on filtered reviews
                    let totalRating = 0;
                    reviewsArray.forEach(item => {
                        totalRating += parseInt(item.getAttribute('data-rating'));
                    });
                    const avgFilteredRating = visibleReviews > 0 ? (totalRating / visibleReviews).toFixed(1) : 0;
                    
                    const statsSpan = card.querySelector('.vehicle-stats');
                    if (statsSpan) {
                        statsSpan.innerHTML = `
                            <span class="stat">
                                <i class="fas fa-star me-1"></i>
                                ${avgFilteredRating}
                            </span>
                            <span class="stat">
                                <i class="fas fa-comment me-1"></i>
                                ${visibleReviews} Reviews
                            </span>
                        `;
                    }
                }
            });
        }
        
        function refreshPage() {
            location.reload();
        }
    </script>
</body>
</html>