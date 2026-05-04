<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Analytics - TruckLink Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-800: #343a40;
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
            background: #f0f2f5;
        }
        
        /* Topbar */
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .topbar .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid var(--secondary);
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px 24px;
            transition: all 0.3s;
            border: 1px solid var(--gray-200);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border-color: var(--gray-300);
        }
        
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .stat-card .stat-icon i {
            font-size: 24px;
            color: var(--secondary);
        }
        
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 4px;
        }
        
        .stat-card .stat-label {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .stat-card .stat-sub {
            font-size: 12px;
            color: var(--gray-600);
            border-top: 1px solid var(--gray-200);
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Metric Cards */
        .metric-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            border: 1px solid var(--gray-200);
            transition: all 0.3s;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        
        .metric-card i {
            font-size: 32px;
            margin-bottom: 12px;
            color: var(--secondary);
        }
        
        .metric-card .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 5px;
        }
        
        .metric-card .metric-label {
            font-size: 13px;
            color: var(--gray-600);
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            background: white;
            overflow: hidden;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 18px 24px;
            font-weight: 600;
            font-size: 16px;
            color: var(--gray-800);
        }
        
        .card-header i {
            margin-right: 10px;
            color: var(--secondary);
        }
        
        .card-body {
            padding: 20px 24px;
        }
        
        /* Tables */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background: var(--gray-100);
            border-bottom: 2px solid var(--gray-200);
            color: var(--gray-600);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }
        
        .table td {
            padding: 14px 16px;
            vertical-align: middle;
            color: var(--gray-800);
            font-size: 14px;
            border-bottom-color: var(--gray-200);
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--gray-100);
        }
        
        /* Progress Bar */
        .progress {
            height: 6px;
            border-radius: 10px;
            background-color: var(--gray-200);
        }
        
        .progress-bar {
            background-color: var(--secondary);
            border-radius: 10px;
        }
        
        /* Vehicle Item */
        .vehicle-item {
            padding: 14px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .vehicle-item:last-child {
            border-bottom: none;
        }
        
        .vehicle-name {
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .vehicle-number {
            font-size: 12px;
            color: var(--gray-600);
        }
        
        /* Badges */
        .badge-penalty {
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .badge-spending {
            background: #dcfce7;
            color: #16a34a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Text Colors */
        .text-success-custom {
            color: #16a34a !important;
        }
        
        .text-danger-custom {
            color: #dc2626 !important;
        }
        
        /* Buttons */
        .btn-outline-custom {
            border: 1px solid var(--gray-300);
            background: white;
            color: var(--gray-600);
            border-radius: 12px;
            padding: 8px 20px;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .btn-outline-custom:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }
        
        /* Vehicle Spending Table Scroll */
        .vehicle-spending-table {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        /* Insight Cards */
        .insight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 20px;
        }
        
        .insight-card .insight-value {
            font-size: 20px;
            font-weight: 700;
        }
        
        .insight-card .insight-label {
            font-size: 13px;
            opacity: 0.9;
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
        
        @media (max-width: 768px) {
            .stat-card .stat-value {
                font-size: 22px;
            }
        }
        
        /* Chart Containers */
        canvas {
            max-height: 300px;
            width: 100%;
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
                <a class="nav-link" href="{{route('customer.login')}}">
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
            <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-line me-2 text-primary"></i>My Analytics</h5>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=3498db&color=fff" alt="Customer">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ $userName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
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
                    <h4 class="mb-1 fw-bold">Spending & Trip Analytics</h4>
                    <p class="text-muted mb-0">Track your spending, trip history, and vehicle usage insights</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-custom" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Export Report
                    </button>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-value">Rs {{ number_format($totalSpending, 2) }}</div>
                        <div class="stat-label">Total Spending</div>
                        <div class="stat-sub">On {{ $totalTrips }} completed trips</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-value">Rs {{ number_format($totalPenalty, 2) }}</div>
                        <div class="stat-label">Total Penalties Saved</div>
                        <div class="stat-sub">Deducted from your payments</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="stat-value">{{ $totalTrips }}</div>
                        <div class="stat-label">Completed Trips</div>
                        <div class="stat-sub">Total deliveries made</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-value">{{ $totalBookings }}</div>
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-sub">{{ $pendingBookings }} pending • {{ $inProgressBookings }} in progress</div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="metric-card">
                        <i class="fas fa-chart-line"></i>
                        <div class="metric-value">Rs {{ number_format($avgFarePerTrip, 2) }}</div>
                        <div class="metric-label">Average Spending Per Trip</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="metric-card">
                        <i class="fas fa-calculator"></i>
                        <div class="metric-value">Rs {{ number_format($totalEstimatedFare, 2) }}</div>
                        <div class="metric-label">Total Estimated Fare</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="metric-card">
                        <i class="fas fa-truck"></i>
                        <div class="metric-value">{{ $performanceMetrics['unique_vehicles_used'] }}</div>
                        <div class="metric-label">Unique Vehicles Used</div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line"></i> Monthly Spending Trend
                        </div>
                        <div class="card-body">
                            <canvas id="monthlySpendingChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar"></i> Yearly Spending Overview
                        </div>
                        <div class="card-body">
                            <canvas id="yearlySpendingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Insights Row -->
            @if($mostUsedVehicle || $bestValueTrip || $mostExpensiveTrip)
            <div class="row mb-4">
                @if($mostUsedVehicle)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-2x text-primary mb-3"></i>
                            <h6 class="text-muted mb-2">Most Used Vehicle</h6>
                            <div class="fw-bold fs-5">{{ $mostUsedVehicle['vehicle_number'] }}</div>
                            <div class="small text-muted">{{ $mostUsedVehicle['total_trips'] }} trips • Rs {{ number_format($mostUsedVehicle['total_spending'], 2) }} spent</div>
                        </div>
                    </div>
                </div>
                @endif
                @if($bestValueTrip)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-tag fa-2x text-success mb-3"></i>
                            <h6 class="text-muted mb-2">Best Value Trip</h6>
                            <div class="fw-bold fs-5">Rs {{ number_format($bestValueTrip->actual_fare, 2) }}</div>
                            <div class="small text-muted">{{ $bestValueTrip->vehicle->vehicle_number ?? 'N/A' }} • {{ \Illuminate\Support\Str::limit($bestValueTrip->pickup_location, 30) }}</div>
                        </div>
                    </div>
                </div>
                @endif
                @if($mostExpensiveTrip)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-gem fa-2x text-warning mb-3"></i>
                            <h6 class="text-muted mb-2">Most Expensive Trip</h6>
                            <div class="fw-bold fs-5">Rs {{ number_format($mostExpensiveTrip->actual_fare, 2) }}</div>
                            <div class="small text-muted">{{ $mostExpensiveTrip->vehicle->vehicle_number ?? 'N/A' }} • {{ \Illuminate\Support\Str::limit($mostExpensiveTrip->pickup_location, 30) }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Vehicle-wise Spending Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Vehicle-wise Spending Breakdown
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <canvas id="vehicleSpendingChart" style="max-height: 300px;"></canvas>
                        </div>
                        <div class="col-md-7">
                            <div class="vehicle-spending-table">
                                @foreach($vehicleSpending as $vehicle)
                                    @php
                                        $vehiclePercent = $totalSpending > 0 ? ($vehicle['total_spending'] / $totalSpending) * 100 : 0;
                                    @endphp
                                    <div class="vehicle-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="vehicle-name">{{ $vehicle['vehicle_number'] }}</div>
                                                <div class="vehicle-number">{{ $vehicle['vehicle_type'] }} • {{ $vehicle['total_trips'] }} trips</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-success-custom">Rs {{ number_format($vehicle['total_spending'], 2) }}</div>
                                                <small class="text-muted">Avg: Rs {{ number_format($vehicle['avg_fare_per_trip'], 2) }}/trip</small>
                                            </div>
                                        </div>
                                        <div class="progress mt-2">
                                            <div class="progress-bar" style="width: {{ $vehiclePercent }}%"></div>
                                        </div>
                                        @if($vehicle['total_penalty'] > 0)
                                            <div class="mt-2">
                                                <span class="badge-penalty"><i class="fas fa-exclamation-circle me-1"></i> Penalty Saved: Rs {{ number_format($vehicle['total_penalty'], 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @if(empty($vehicleSpending))
                                    <div class="text-center py-4 text-muted">No vehicle usage data available</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Spending Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-truck"></i> Vehicle-wise Spending Details
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Vehicle Type</th>
                                    <th>Total Trips</th>
                                    <th>Estimated Fare</th>
                                    <th>Actual Fare</th>
                                    <th>Penalty Saved</th>
                                    <th>Net Spending</th>
                                    <th>Avg per Trip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicleSpending as $vehicle)
                                    <tr>
                                        <td><strong>{{ $vehicle['vehicle_number'] }}</strong></td>
                                        <td>{{ $vehicle['vehicle_type'] }}</td>
                                        <td>{{ $vehicle['total_trips'] }}</td>
                                        <td>Rs {{ number_format($vehicle['total_estimated_fare'], 2) }}</td>
                                        <td>Rs {{ number_format($vehicle['total_actual_fare'], 2) }}</td>
                                        <td class="text-success-custom">Rs {{ number_format($vehicle['total_penalty'], 2) }}</td>
                                        <td class="text-primary fw-bold">Rs {{ number_format($vehicle['total_spending'], 2) }}</td>
                                        <td>Rs {{ number_format($vehicle['avg_fare_per_trip'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No completed trips found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Monthly Breakdown Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt"></i> Monthly Spending Breakdown
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Trips Completed</th>
                                    <th>Penalty Saved</th>
                                    <th>Total Spending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlySpending as $month)
                                    <tr>
                                        <td><strong>{{ $month['month'] }}</strong></td>
                                        <td>{{ $month['trips'] }}</td>
                                        <td class="text-success-custom">Rs {{ number_format($month['penalty'], 2) }}</td>
                                        <td class="text-primary fw-bold">Rs {{ number_format($month['spending'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No monthly data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Completed Trips -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history"></i> Recent Completed Trips
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Vehicle</th>
                                    <th>Pickup Location</th>
                                    <th>Dropoff Location</th>
                                    <th>Est. Fare</th>
                                    <th>Penalty</th>
                                    <th>Actual Fare</th>
                                    <th>Delivered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td><span class="fw-semibold">#{{ $booking->id }}</span></td>
                                        <td>{{ $booking->vehicle->vehicle_number ?? 'N/A' }} ({!! $booking->vehicle->vehicle_type ?? 'N/A' !!})</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->pickup_location, 30) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->dropoff_location, 30) }}</td>
                                        <td>Rs {{ number_format($booking->estimated_fare, 2) }}</td>
                                        <td class="text-success-custom">Rs {{ number_format($booking->penalty_amount, 2) }}</td>
                                        <td class="text-primary fw-bold">Rs {{ number_format($booking->actual_fare, 2) }}</td>
                                        <td>{{ $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at)->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No completed trips found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Monthly Spending Chart Data
        const monthlyData = @json($monthlySpending);
        const months = monthlyData.map(m => m.month_key);
        const spending = monthlyData.map(m => m.spending);
        const penalties = monthlyData.map(m => m.penalty);

        // Monthly Spending Chart
        const monthlyCtx = document.getElementById('monthlySpendingChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Spending (Rs)',
                        data: spending,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.05)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3498db',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2
                    },
                    {
                        label: 'Penalties Saved (Rs)',
                        data: penalties,
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39, 174, 96, 0.05)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#27ae60',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rs ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Yearly Spending Chart
        const yearlyData = @json($yearlySpending);
        const years = yearlyData.map(y => y.year);
        const yearlySpendingData = yearlyData.map(y => y.spending);

        const yearlyCtx = document.getElementById('yearlySpendingChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Yearly Spending (Rs)',
                        data: yearlySpendingData,
                        backgroundColor: '#3498db',
                        borderRadius: 8,
                        barPercentage: 0.65
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rs ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Vehicle Spending Pie Chart
        const vehicleSpendingData = @json($vehicleSpending);
        const vehicleNames = vehicleSpendingData.map(v => v.vehicle_number);
        const vehicleSpendingValues = vehicleSpendingData.map(v => v.total_spending);

        if (vehicleNames.length > 0) {
            const pieCtx = document.getElementById('vehicleSpendingChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: vehicleNames,
                    datasets: [
                        {
                            data: vehicleSpendingValues,
                            backgroundColor: [
                                '#3498db', '#e74c3c', '#27ae60', '#f39c12', 
                                '#9b59b6', '#1abc9c', '#e67e22', '#2c3e50',
                                '#16a085', '#c0392b', '#2980b9', '#8e44ad'
                            ],
                            borderWidth: 0,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 10,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: Rs ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            const pieContainer = document.getElementById('vehicleSpendingChart');
            if (pieContainer) {
                pieContainer.parentElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-pie fa-3x text-muted mb-3"></i><p class="text-muted">No data available for chart</p></div>';
            }
        }
    </script>
</body>
</html>