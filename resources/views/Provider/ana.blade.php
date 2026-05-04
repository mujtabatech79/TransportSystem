<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analytics - TruckLink Vehicle Owner Dashboard</title>
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
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 15px 20px;
            font-weight: 600;
            border-radius: 16px 16px 0 0;
        }
        
        .earning-card {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
        }
        
        .earning-card .stat-value {
            color: white;
        }
        
        .penalty-card {
            background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%);
            color: white;
        }
        
        .trip-card {
            background: linear-gradient(135deg, var(--success) 0%, #1e8449 100%);
            color: white;
        }
        
        .vehicle-card {
            background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%);
            color: white;
        }
        
        .vehicle-earnings-table {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .vehicle-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.2s;
        }
        
        .vehicle-item:hover {
            background: #f8f9fa;
        }
        
        .vehicle-name {
            font-weight: 600;
        }
        
        .vehicle-number {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .badge-penalty {
            background: #ffe0e0;
            color: #e74c3c;
        }
        
        .badge-earning {
            background: #e0ffe0;
            color: #27ae60;
        }
        
        canvas {
            max-height: 300px;
            width: 100%;
        }
        
        .metric-box {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8f9fa;
        }
        
        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .period-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .period-btn {
            padding: 8px 20px;
            border-radius: 25px;
            border: 1px solid #ddd;
            background: white;
            transition: all 0.3s;
        }
        
        .period-btn.active {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
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
        
        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
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
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h5>
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
                    <h4 class="mb-1 fw-bold">Financial & Performance Analytics</h4>
                    <p class="text-muted mb-0">Track your earnings, vehicle performance, and business insights</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Export Report
                    </button>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card earning-card position-relative">
                        <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                        <div class="stat-value">Rs {{ number_format($totalIncome, 2) }}</div>
                        <div class="stat-label">Total Earnings</div>
                        <small>From {{ $totalTrips }} completed trips</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card penalty-card position-relative">
                        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-value">Rs {{ number_format($totalPenalty, 2) }}</div>
                        <div class="stat-label">Total Penalties</div>
                        <small>Deducted from earnings</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card trip-card position-relative">
                        <div class="stat-icon"><i class="fas fa-road"></i></div>
                        <div class="stat-value">{{ $totalTrips }}</div>
                        <div class="stat-label">Completed Trips</div>
                        <small>Total deliveries made</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card vehicle-card position-relative">
                        <div class="stat-icon"><i class="fas fa-truck"></i></div>
                        <div class="stat-value">{{ $performanceMetrics['total_vehicles'] }}</div>
                        <div class="stat-label">Total Vehicles</div>
                        <small>{{ $performanceMetrics['active_vehicles'] }} active</small>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                            <div class="metric-value">Rs {{ number_format($avgFarePerTrip, 2) }}</div>
                            <div class="text-muted">Average Earnings Per Trip</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                            <div class="metric-value">Rs {{ number_format($totalEstimatedFare, 2) }}</div>
                            <div class="text-muted">Total Estimated Fare</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-tachometer-alt fa-2x text-success mb-2"></i>
                            <div class="metric-value">{{ $performanceMetrics['total_vehicles_with_earnings'] }}</div>
                            <div class="text-muted">Vehicles with Earnings</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2 text-primary"></i> Monthly Earnings Trend
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyEarningsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2 text-primary"></i> Yearly Earnings Overview
                        </div>
                        <div class="card-body">
                            <canvas id="yearlyEarningsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Performance Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-truck me-2 text-primary"></i> Vehicle Performance & Earnings
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Vehicle Type</th>
                                    <th>Total Trips</th>
                                    <th>Estimated Fare</th>
                                    <th>Actual Fare</th>
                                    <th>Penalty</th>
                                    <th>Net Earnings</th>
                                    <th>Avg per Trip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicleEarnings as $vehicle)
                                    @php
                                        $progressPercent = $totalIncome > 0 ? ($vehicle['total_earnings'] / $totalIncome) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $vehicle['vehicle_number'] }}</strong>
                                            <div class="progress mt-1" style="width: 100px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $progressPercent }}%"></div>
                                            </div>
                                        </td>
                                        <td>{{ $vehicle['vehicle_type'] }}</td>
                                        <td>{{ $vehicle['total_trips'] }}</td>
                                        <td>Rs {{ number_format($vehicle['total_estimated_fare'], 2) }}</td>
                                        <td>Rs {{ number_format($vehicle['total_actual_fare'], 2) }}</td>
                                        <td class="text-danger">Rs {{ number_format($vehicle['total_penalty'], 2) }}</td>
                                        <td class="text-success fw-bold">Rs {{ number_format($vehicle['total_earnings'], 2) }}</td>
                                        <td>Rs {{ number_format($vehicle['avg_fare_per_trip'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No completed trips found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Vehicle-wise Detailed Breakdown -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2 text-primary"></i> Vehicle-wise Earnings Breakdown
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <canvas id="vehicleEarningsChart"></canvas>
                                </div>
                                <div class="col-md-7">
                                    <div class="vehicle-earnings-table">
                                        @foreach($vehicleEarnings as $vehicle)
                                            @if($vehicle['total_trips'] > 0)
                                                <div class="vehicle-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="vehicle-name">{{ $vehicle['vehicle_number'] }}</div>
                                                            <div class="vehicle-number">{{ $vehicle['vehicle_type'] }} • {{ $vehicle['total_trips'] }} trips</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-success">Rs {{ number_format($vehicle['total_earnings'], 2) }}</div>
                                                            <small class="text-muted">Avg: Rs {{ number_format($vehicle['avg_fare_per_trip'], 2) }}/trip</small>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2">
                                                        @php
                                                            $vehiclePercent = $totalIncome > 0 ? ($vehicle['total_earnings'] / $totalIncome) * 100 : 0;
                                                        @endphp
                                                        <div class="progress-bar bg-primary" style="width: {{ $vehiclePercent }}%"></div>
                                                    </div>
                                                    @if($vehicle['total_penalty'] > 0)
                                                        <div class="mt-1">
                                                            <small class="text-danger"><i class="fas fa-exclamation-circle"></i> Penalty: Rs {{ number_format($vehicle['total_penalty'], 2) }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Breakdown Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i> Monthly Earnings Breakdown
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Trips Completed</th>
                                    <th>Penalty Amount</th>
                                    <th>Total Earnings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyEarnings as $month)
                                    <tr>
                                        <td>{{ $month['month'] }}</td>
                                        <td>{{ $month['trips'] }}</td>
                                        <td class="text-danger">Rs {{ number_format($month['penalty'], 2) }}</td>
                                        <td class="text-success fw-bold">Rs {{ number_format($month['earnings'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No monthly data available</td>
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
                    <i class="fas fa-history me-2 text-primary"></i> Recent Completed Trips
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Vehicle</th>
                                    <th>Customer</th>
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
                                        <td>#{{ $booking->id }}</td>
                                        <td>{{ $booking->vehicle->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->pickup_location, 30) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->dropoff_location, 30) }}</td>
                                        <td>Rs {{ number_format($booking->estimated_fare, 2) }}</td>
                                        <td class="text-danger">Rs {{ number_format($booking->penalty_amount, 2) }}</td>
                                        <td class="text-success fw-bold">Rs {{ number_format($booking->actual_fare, 2) }}</td>
                                        <td>{{ $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at)->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No completed trips found</td>
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
        // Monthly Earnings Chart Data
        const monthlyData = @json($monthlyEarnings);
        const months = monthlyData.map(m => m.month_key);
        const earnings = monthlyData.map(m => m.earnings);
        const penalties = monthlyData.map(m => m.penalty);

        // Monthly Earnings Chart
        const monthlyCtx = document.getElementById('monthlyEarningsChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Earnings (Rs)',
                        data: earnings,
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#27ae60',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Penalties (Rs)',
                        data: penalties,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#e74c3c',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
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
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Yearly Earnings Chart
        const yearlyData = @json($yearlyEarnings);
        const years = yearlyData.map(y => y.year);
        const yearlyEarningsData = yearlyData.map(y => y.earnings);

        const yearlyCtx = document.getElementById('yearlyEarningsChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Yearly Earnings (Rs)',
                        data: yearlyEarningsData,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: '#3498db',
                        borderWidth: 1,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
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
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Vehicle Earnings Pie Chart
        const vehicleEarningsData = @json($vehicleEarnings);
        const vehicleNames = vehicleEarningsData
            .filter(v => v.total_trips > 0)
            .map(v => v.vehicle_number);
        const vehicleEarningsValues = vehicleEarningsData
            .filter(v => v.total_trips > 0)
            .map(v => v.total_earnings);

        const pieCtx = document.getElementById('vehicleEarningsChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: vehicleNames,
                datasets: [
                    {
                        data: vehicleEarningsValues,
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#27ae60', '#f39c12', 
                            '#9b59b6', '#1abc9c', '#e67e22', '#2c3e50',
                            '#16a085', '#c0392b', '#2980b9', '#8e44ad'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
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
                            }
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
    </script>
</body>
</html>