{{-- resources/views/customer/booking.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckLink - Book Vehicle</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.15);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
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
        }
        
        .topbar .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid var(--secondary);
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
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, white 0%, #f8f9fa 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            font-weight: 600;
        }
        
        /* Vehicle Summary */
        .vehicle-summary-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 12px 12px 0 0;
        }
        
        .vehicle-summary-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--success);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .vehicle-summary-info {
            padding: 20px;
        }
        
        /* Map Container */
        .map-container {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 2px solid #dee2e6;
            position: relative;
        }
        
        #map {
            height: 100%;
            width: 100%;
            z-index: 1;
        }
        
        .map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.95);
            padding: 15px 30px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
        }
        
        .map-loading.show {
            display: block;
        }
        
        /* Directions Panel */
        .directions-panel {
            position: absolute;
            top: 60px;
            right: 10px;
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            padding: 15px;
            display: none;
        }
        
        .directions-panel.show {
            display: block;
        }
        
        .directions-panel h6 {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 8px;
            position: sticky;
            top: 0;
            background: white;
            z-index: 2;
        }
        
        .direction-step {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .direction-step:hover {
            background: #f0f7ff;
        }
        
        .direction-step:last-child {
            border-bottom: none;
        }
        
        .direction-icon {
            width: 30px;
            height: 30px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .direction-content {
            flex-grow: 1;
        }
        
        .direction-instruction {
            color: #333;
            line-height: 1.4;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .direction-distance {
            color: var(--secondary);
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .toggle-directions-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-weight: 500;
            color: var(--dark);
        }
        
        .toggle-directions-btn:hover {
            background: var(--secondary);
            color: white;
        }
        
        .toggle-directions-btn i {
            margin-right: 5px;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            border-color: var(--secondary);
        }
        
        /* Fare Card */
        .fare-card {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .fare-amount {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .distance-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        /* Suggestions Box */
        .suggestions-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            display: none;
            margin-top: 2px;
        }
        
        .suggestions-box:not(:empty) {
            display: block;
        }
        
        .suggestion-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .suggestion-item:hover {
            background: #f0f7ff;
        }
        
        .suggestion-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Footer */
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
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
            
            .main-content, .footer {
                margin-left: 80px;
            }
            
            .directions-panel {
                width: 280px;
            }
        }

        .route-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .location-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 2000;
            min-width: 320px;
        }

        .location-dialog h5 {
            margin-bottom: 20px;
            color: var(--dark);
        }

        .location-dialog .btn {
            padding: 12px;
            font-weight: 500;
        }

        .loading-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px 30px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 2000;
        }
        
        /* Blue route line styles */
        .blue-route {
            filter: drop-shadow(0 0 5px rgba(52, 152, 219, 0.5));
        }
        
        .leaflet-interactive {
            filter: drop-shadow(0 0 3px rgba(52, 152, 219, 0.3));
        }
        
        /* Custom scrollbar for directions panel */
        .directions-panel::-webkit-scrollbar {
            width: 6px;
        }
        
        .directions-panel::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .directions-panel::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }
        
        .directions-panel::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }
        
        /* Route Selection Cards */
        .route-selector {
            margin: 20px 0;
        }

        .route-cards {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: thin;
        }

        .route-card {
            flex: 0 0 280px;
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
        }

        .route-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .route-card.selected {
            border-color: var(--secondary);
            background: #f0f7ff;
        }

        .route-card.fastest {
            border-left: 4px solid #27ae60;
        }

        .route-card.economical {
            border-left: 4px solid #f39c12;
        }

        .route-badge {
            position: absolute;
            top: -10px;
            right: 10px;
            background: var(--secondary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .route-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .route-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .route-toll-badge {
            background: #ffeaa7;
            color: #d63031;
            padding: 4px 8px;
            border-radius: 16px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .route-details {
            margin: 12px 0;
        }

        .route-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .route-metric i {
            width: 20px;
            color: var(--secondary);
        }

        .route-metric span:last-child {
            font-weight: 600;
            color: var(--dark);
        }

        .route-fare {
            text-align: center;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px dashed #dee2e6;
        }

        .route-fare-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--success);
        }

        .route-fare-note {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .route-summary {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .route-selector::-webkit-scrollbar {
            height: 6px;
        }

        .route-selector::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .route-selector::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }

        /* Comparison Table */
        .route-comparison {
            margin-top: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
        }

        .comparison-table {
            width: 100%;
            font-size: 0.9rem;
        }

        .comparison-table th {
            font-weight: 600;
            color: var(--dark);
            padding: 8px;
            text-align: left;
        }

        .comparison-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .comparison-table tr:last-child td {
            border-bottom: none;
        }

        .comparison-table .selected-route {
            background: rgba(52, 152, 219, 0.1);
            font-weight: 500;
        }
        
        .toll-info {
            font-size: 0.85rem;
            margin-top: 5px;
            color: #e67e22;
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
                <a class="nav-link active" href="{{route('customer.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
               <a class="nav-link" href="{{route('all.vehicle')}}">
                    <i class="fas fa-search"></i> <span>All vehicle</span>
                </a>
                <a class="nav-link active" href="{{route('find.vehicle')}}">
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
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">Book Vehicle</h4>
                <p class="text-muted mb-0">Fill in the details to book your vehicle</p>
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ session('user_name') ?? 'Ahmed R.' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($vehicle))
            <!-- Vehicle Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Selected Vehicle</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="vehicle-summary-image" style="background-image: url('{{ $vehicle->vehicle_image ? asset('storage/'.$vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}')">
                                <div class="vehicle-summary-badge">
                                    @if($vehicle->is_booked == 'no')
                                        Available
                                    @else
                                        Booked
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="vehicle-summary-info">
                                <h5>{{ $vehicle->vehicle_type }} - {{ $vehicle->vehicle_number }}</h5>
                                <div class="row mt-3">
                                    <div class="col-4">
                                        <small class="text-muted">Driver</small>
                                        <p class="mb-0">{{ $vehicle->user->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Capacity</small>
                                        <p class="mb-0">{{ $vehicle->weight_capacity ?? 'N/A' }} kg</p>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Rate/km</small>
                                        <p class="mb-0">Rs {{ $vehicle->rate_per_km ?? '35' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Booking Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('trip.submit') }}" method="POST" id="bookingForm">
                        @csrf
                        @if(isset($vehicle))
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        <input type="hidden" id="vehicle_type" value="{{ $vehicle->vehicle_type ?? 'truck' }}">
                        <input type="hidden" id="rate_per_km" value="{{ $vehicle->rate_per_km ?? '35' }}">
                        @else
                        <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
                        <input type="hidden" id="vehicle_type" value="truck">
                        <input type="hidden" id="rate_per_km" value="35">
                        @endif
                        
                        <!-- Hidden fields -->
                        <input type="hidden" id="pickup_lat" name="pickup_lat">
                        <input type="hidden" id="pickup_lng" name="pickup_lng">
                        <input type="hidden" id="destination_lat" name="destination_lat">
                        <input type="hidden" id="destination_lng" name="destination_lng">
                        <input type="hidden" id="estimated_distance" name="estimated_distance">
                        <input type="hidden" id="estimated_fare" name="estimated_fare">
                        <input type="hidden" id="estimated_duration" name="estimated_duration">
                        <input type="hidden" id="route_polyline" name="route_polyline">
                        <input type="hidden" id="route_directions" name="route_directions">
                        <input type="hidden" id="selected_route_data" name="selected_route_data">
                        
                        <div class="row g-3">
                            <!-- Date Field -->
                            <div class="col-md-6">
                                <label class="form-label">Pickup Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="trip_date" name="trip_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                            
                            <!-- Time Field -->
                            <div class="col-md-6">
                                <label class="form-label">Pickup Time</label>
                                <input type="time" class="form-control" id="pickup_time" name="pickup_time" value="09:00">
                            </div>
                            
                            <!-- Pickup Location -->
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Pickup Location <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                    <input type="text" class="form-control" id="pickup_location" name="pickup_location" placeholder="Enter pickup address" required autocomplete="off">
                                    <button class="btn btn-outline-primary" type="button" onclick="getCurrentLocation('pickup')" title="Use my current location">
                                        <i class="fas fa-location-arrow"></i>
                                    </button>
                                </div>
                                <div id="pickup-suggestions" class="suggestions-box"></div>
                            </div>
                            
                            <!-- Destination -->
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Destination <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-success"></i></span>
                                    <input type="text" class="form-control" id="drop_location" name="drop_location" placeholder="Enter destination address" required autocomplete="off">
                                    <button class="btn btn-outline-primary" type="button" onclick="getCurrentLocation('destination')" title="Use my current location">
                                        <i class="fas fa-location-arrow"></i>
                                    </button>
                                </div>
                                <div id="destination-suggestions" class="suggestions-box"></div>
                            </div>
                            
                            <!-- Goods Details -->
                            <div class="col-md-6">
                                <label class="form-label">Type of Goods</label>
                                <select class="form-select" id="goods_type" name="goods_type">
                                    <option value="">Select goods type</option>
                                    <option value="furniture">Furniture</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="textiles">Textiles</option>
                                    <option value="machinery">Machinery</option>
                                    <option value="food">Food Items</option>
                                    <option value="construction">Construction Materials</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Goods Weight (kg)</label>
                                <input type="number" step="0.01" class="form-control" id="goods_weight" name="goods_weight" placeholder="Enter weight">
                            </div>
                            
                            <!-- Special Instructions -->
                            <div class="col-12">
                                <label class="form-label">Special Instructions</label>
                                <textarea class="form-control" id="special_instructions" name="special_instructions" rows="2" placeholder="Any special instructions..."></textarea>
                            </div>
                            
                            <!-- Map -->
                            <div class="col-12">
                                <label class="form-label">Route Preview <small class="text-muted">(Click on map to set locations)</small></label>
                                <div class="map-container">
                                    <div id="map"></div>
                                    <button class="toggle-directions-btn" onclick="toggleDirectionsPanel()">
                                        <i class="fas fa-list"></i> <span id="directionsBtnText">Show Directions</span>
                                    </button>
                                    <div id="directionsPanel" class="directions-panel">
                                        <h6>
                                            <i class="fas fa-turn-down me-2"></i>Turn-by-Turn Directions
                                            <button class="btn-close float-end" onclick="toggleDirectionsPanel()"></button>
                                        </h6>
                                        <div id="directionsList">
                                            <p class="text-muted text-center">Select pickup and destination to see directions</p>
                                        </div>
                                    </div>
                                    <div class="map-loading" id="mapLoading">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Calculating routes...
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Multiple Route Selection -->
                            <div class="col-12 mt-3">
                                <div class="route-selector">
                                    <label class="form-label fw-bold mb-3">
                                        <i class="fas fa-route me-2"></i>Select Your Preferred Route
                                    </label>
                                    
                                    <!-- Route Cards -->
                                    <div id="routeCards" class="route-cards">
                                        <!-- Routes will be populated here dynamically -->
                                        <div class="text-center text-muted w-100 py-4">
                                            <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                                            <p>Select pickup and destination to see available routes</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Route Comparison Table (hidden initially) -->
                                    <div id="routeComparison" class="route-comparison" style="display: none;">
                                        <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Route Comparison</h6>
                                        <table class="comparison-table">
                                            <thead>
                                                <tr>
                                                    <th>Route</th>
                                                    <th>Distance</th>
                                                    <th>Duration</th>
                                                    <th>Tolls</th>
                                                    <th>Fare</th>
                                                </tr>
                                            </thead>
                                            <tbody id="comparisonBody">
                                                <!-- Comparison rows will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fare Card -->
                            <div class="col-md-12">
                                <div class="fare-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                                            <div class="distance-badge">
                                                <i class="fas fa-road me-1"></i>
                                                <span id="distance_display">-- km</span>
                                            </div>
                                            <div class="mt-2">
                                                <i class="fas fa-clock me-1"></i>
                                                <span id="duration_display">-- min</span>
                                            </div>
                                        </div>
                                        <div class="col-md-8 text-center text-md-end">
                                            <div class="small">Estimated Fare</div>
                                            <div class="fare-amount" id="fare_display">--</div>
                                            <div class="small" id="fare_note"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select payment method</option>
                                    <option value="jazzcash">JazzCash</option>
                                    <option value="easypaisa">Easypaisa</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>
                            </div>
                            
                            <!-- Terms -->
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms_agreed" checked>
                                    <label class="form-check-label" for="terms_agreed">
                                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms</a>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('find.vehicle') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                    <i class="fas fa-check-circle me-1"></i> Submit Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2024 TruckLink</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Customer Panel</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Standard terms and conditions apply...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Configuration
        const ORS_API_KEY = '{{ env("ORS_API_KEY") }}';
        
        // Global variables
        let map;
        let pickupMarker = null;
        let destinationMarker = null;
        let currentPolyline = null;
        let debounceTimer;
        let routeCalculationInProgress = false;
        let directionsVisible = false;
        
        // Multiple routes variables
        let availableRoutes = [];
        let selectedRouteId = 0;
        
        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            setupEventListeners();
            setDefaultDateTime();
            validateRequiredFields();
            
            // Check if we have saved coordinates in session
            const savedPickup = sessionStorage.getItem('pickup_location');
            const savedDrop = sessionStorage.getItem('drop_location');
            
            if (savedPickup) {
                const data = JSON.parse(savedPickup);
                document.getElementById('pickup_location').value = data.address;
                document.getElementById('pickup_lat').value = data.lat;
                document.getElementById('pickup_lng').value = data.lng;
                updateMarker('pickup', data.lat, data.lng, data.address);
            }
            
            if (savedDrop) {
                const data = JSON.parse(savedDrop);
                document.getElementById('drop_location').value = data.address;
                document.getElementById('destination_lat').value = data.lat;
                document.getElementById('destination_lng').value = data.lng;
                updateMarker('destination', data.lat, data.lng, data.address);
            }
            
            if (savedPickup && savedDrop) {
                setTimeout(() => calculateRoute(), 500);
            }
        });
        
        function initMap() {
            // Default center (Pakistan/Kashmir region)
            map = L.map('map').setView([34.0, 74.5], 8);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            // Add click event
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                showLocationDialog(lat, lng);
            });
        }
        
        function showLocationDialog(lat, lng) {
            // Remove any existing dialog
            const existingDialog = document.querySelector('.location-dialog');
            if (existingDialog) {
                existingDialog.remove();
            }
            
            // Create custom dialog
            const dialog = document.createElement('div');
            dialog.className = 'location-dialog';
            dialog.innerHTML = `
                <h5 class="text-center"><i class="fas fa-map-marker-alt me-2"></i>Select Location Type</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-danger" onclick="setMapLocation(${lat}, ${lng}, 'pickup')">
                        <i class="fas fa-map-marker-alt me-2"></i> Set as Pickup Location
                    </button>
                    <button class="btn btn-success" onclick="setMapLocation(${lat}, ${lng}, 'destination')">
                        <i class="fas fa-map-marker-alt me-2"></i> Set as Destination
                    </button>
                    <button class="btn btn-secondary" onclick="this.closest('.location-dialog').remove()">
                        Cancel
                    </button>
                </div>
            `;
            
            document.body.appendChild(dialog);
        }
        
        // Make function global
        window.setMapLocation = function(lat, lng, type) {
            const dialog = document.querySelector('.location-dialog');
            if (dialog) {
                dialog.remove();
            }
            reverseGeocode(lat, lng, type);
        };
        
        // Reverse geocoding with timeout
        function reverseGeocode(lat, lng, type) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-overlay';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Getting address...';
            document.body.appendChild(loadingDiv);
            
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                signal: controller.signal
            })
            .then(response => response.json())
            .then(data => {
                clearTimeout(timeoutId);
                loadingDiv.remove();
                
                const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                if (type === 'pickup') {
                    document.getElementById('pickup_location').value = address;
                    document.getElementById('pickup_lat').value = lat;
                    document.getElementById('pickup_lng').value = lng;
                    updateMarker('pickup', lat, lng, address);
                    
                    // Save to session
                    sessionStorage.setItem('pickup_location', JSON.stringify({
                        lat: lat,
                        lng: lng,
                        address: address
                    }));
                } else {
                    document.getElementById('drop_location').value = address;
                    document.getElementById('destination_lat').value = lat;
                    document.getElementById('destination_lng').value = lng;
                    updateMarker('destination', lat, lng, address);
                    
                    // Save to session
                    sessionStorage.setItem('drop_location', JSON.stringify({
                        lat: lat,
                        lng: lng,
                        address: address
                    }));
                }
                
                calculateRoute();
                validateRequiredFields();
            })
            .catch(error => {
                clearTimeout(timeoutId);
                loadingDiv.remove();
                
                if (error.name === 'AbortError') {
                    // Fallback if geocoding takes too long
                    const address = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    
                    if (type === 'pickup') {
                        document.getElementById('pickup_location').value = address;
                        document.getElementById('pickup_lat').value = lat;
                        document.getElementById('pickup_lng').value = lng;
                        updateMarker('pickup', lat, lng, address);
                    } else {
                        document.getElementById('drop_location').value = address;
                        document.getElementById('destination_lat').value = lat;
                        document.getElementById('destination_lng').value = lng;
                        updateMarker('destination', lat, lng, address);
                    }
                    
                    calculateRoute();
                    validateRequiredFields();
                }
            });
        }
        
        // Setup event listeners
        function setupEventListeners() {
            // Date validation
            document.getElementById('trip_date').min = new Date().toISOString().split('T')[0];
            
            // Location search with debounce
            document.getElementById('pickup_location').addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchLocation(this.value, 'pickup'), 500);
            });
            
            document.getElementById('drop_location').addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchLocation(this.value, 'destination'), 500);
            });
            
            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#pickup_location') && !e.target.closest('#pickup-suggestions')) {
                    document.getElementById('pickup-suggestions').innerHTML = '';
                }
                if (!e.target.closest('#drop_location') && !e.target.closest('#destination-suggestions')) {
                    document.getElementById('destination-suggestions').innerHTML = '';
                }
            });
            
            // Suggestion click - using event delegation
            document.getElementById('pickup-suggestions').addEventListener('click', function(e) {
                const item = e.target.closest('.suggestion-item');
                if (item) {
                    e.preventDefault();
                    const lat = parseFloat(item.dataset.lat);
                    const lng = parseFloat(item.dataset.lng);
                    const displayName = item.dataset.display;
                    
                    document.getElementById('pickup_location').value = displayName;
                    document.getElementById('pickup_lat').value = lat;
                    document.getElementById('pickup_lng').value = lng;
                    
                    updateMarker('pickup', lat, lng, displayName);
                    document.getElementById('pickup-suggestions').innerHTML = '';
                    
                    // Save to session
                    sessionStorage.setItem('pickup_location', JSON.stringify({
                        lat: lat,
                        lng: lng,
                        address: displayName
                    }));
                    
                    calculateRoute();
                    validateRequiredFields();
                }
            });
            
            document.getElementById('destination-suggestions').addEventListener('click', function(e) {
                const item = e.target.closest('.suggestion-item');
                if (item) {
                    e.preventDefault();
                    const lat = parseFloat(item.dataset.lat);
                    const lng = parseFloat(item.dataset.lng);
                    const displayName = item.dataset.display;
                    
                    document.getElementById('drop_location').value = displayName;
                    document.getElementById('destination_lat').value = lat;
                    document.getElementById('destination_lng').value = lng;
                    
                    updateMarker('destination', lat, lng, displayName);
                    document.getElementById('destination-suggestions').innerHTML = '';
                    
                    // Save to session
                    sessionStorage.setItem('drop_location', JSON.stringify({
                        lat: lat,
                        lng: lng,
                        address: displayName
                    }));
                    
                    calculateRoute();
                    validateRequiredFields();
                }
            });
            
            // Payment method change
            document.getElementById('payment_method').addEventListener('change', validateRequiredFields);
            
            // Input blur events to trigger validation
            document.getElementById('pickup_location').addEventListener('blur', validateRequiredFields);
            document.getElementById('drop_location').addEventListener('blur', validateRequiredFields);
            document.getElementById('trip_date').addEventListener('change', validateRequiredFields);
        }
        
        // Search location with timeout
        function searchLocation(query, type) {
            if (query.length < 3) {
                document.getElementById(type === 'pickup' ? 'pickup-suggestions' : 'destination-suggestions').innerHTML = '';
                return;
            }
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=pk&addressdetails=1`, {
                signal: controller.signal
            })
            .then(response => response.json())
            .then(data => {
                clearTimeout(timeoutId);
                let html = '';
                data.forEach(item => {
                    html += `<div class="suggestion-item" data-type="${type}" data-lat="${item.lat}" data-lng="${item.lon}" data-display="${item.display_name}">
                        <i class="fas fa-map-marker-alt me-2 text-${type === 'pickup' ? 'danger' : 'success'}"></i> 
                        ${item.display_name.substring(0, 60)}...
                    </div>`;
                });
                document.getElementById(type === 'pickup' ? 'pickup-suggestions' : 'destination-suggestions').innerHTML = html;
            })
            .catch(error => {
                clearTimeout(timeoutId);
                // Silently fail for suggestions
            });
        }
        
        // Update marker
        function updateMarker(type, lat, lng, address) {
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${type === 'pickup' ? '#dc3545' : '#28a745'}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [20, 20]
            });
            
            if (type === 'pickup') {
                if (pickupMarker) map.removeLayer(pickupMarker);
                pickupMarker = L.marker([lat, lng], { icon }).addTo(map).bindPopup(`<b>Pickup</b><br>${address.substring(0, 50)}...`);
            } else {
                if (destinationMarker) map.removeLayer(destinationMarker);
                destinationMarker = L.marker([lat, lng], { icon }).addTo(map).bindPopup(`<b>Destination</b><br>${address.substring(0, 50)}...`);
            }
            
            // Fit bounds
            if (pickupMarker && destinationMarker) {
                const bounds = L.latLngBounds([pickupMarker.getLatLng(), destinationMarker.getLatLng()]);
                map.fitBounds(bounds, { padding: [50, 50] });
            } else if (pickupMarker) {
                map.setView([lat, lng], 13);
            } else if (destinationMarker) {
                map.setView([lat, lng], 13);
            }
        }
        
        // Toggle directions panel
        window.toggleDirectionsPanel = function() {
            const panel = document.getElementById('directionsPanel');
            const btnText = document.getElementById('directionsBtnText');
            directionsVisible = !directionsVisible;
            
            if (directionsVisible) {
                panel.classList.add('show');
                btnText.textContent = 'Hide Directions';
            } else {
                panel.classList.remove('show');
                btnText.textContent = 'Show Directions';
            }
        };
        
        // Format instruction text
        function formatInstruction(instruction) {
            if (!instruction) return 'Continue';
            
            // Clean up instruction text
            let text = instruction.replace(/<[^>]*>/g, ''); // Remove HTML tags
            text = text.charAt(0).toUpperCase() + text.slice(1); // Capitalize first letter
            
            return text;
        }
        
        // Get icon for instruction
        function getInstructionIcon(instruction) {
            const text = (instruction || '').toLowerCase();
            
            if (text.includes('left')) return 'fa-arrow-left';
            if (text.includes('right')) return 'fa-arrow-right';
            if (text.includes('straight') || text.includes('continue')) return 'fa-arrow-up';
            if (text.includes('destination') || text.includes('arrive')) return 'fa-flag-checkered';
            if (text.includes('start') || text.includes('head')) return 'fa-play';
            if (text.includes('roundabout') || text.includes('circle')) return 'fa-circle';
            if (text.includes('merge')) return 'fa-code-branch';
            if (text.includes('exit')) return 'fa-sign-out-alt';
            if (text.includes('ferry')) return 'fa-ship';
            if (text.includes('toll')) return 'fa-toll';
            
            return 'fa-arrow-right';
        }
        
        // Display turn-by-turn directions
        function displayDirections(directions) {
            const directionsList = document.getElementById('directionsList');
            const directionsPanel = document.getElementById('directionsPanel');
            const btnText = document.getElementById('directionsBtnText');
            
            if (!directions || directions.length === 0) {
                directionsList.innerHTML = '<p class="text-muted text-center">No turn-by-turn directions available</p>';
                return;
            }
            
            let html = '';
            let totalDistance = 0;
            
            directions.forEach((step, index) => {
                // Convert distance from meters to km if needed
                const distanceInMeters = step.distance || 0;
                const distanceInKm = (distanceInMeters / 1000).toFixed(2);
                totalDistance += distanceInMeters;
                
                // Get instruction
                const instruction = formatInstruction(step.instruction || step.name || 'Continue');
                const icon = getInstructionIcon(instruction);
                
                html += `
                    <div class="direction-step">
                        <span class="direction-icon"><i class="fas ${icon}"></i></span>
                        <div class="direction-content">
                            <div class="direction-instruction">${instruction}</div>
                            ${distanceInKm > 0 ? `<div class="direction-distance">${distanceInKm} km</div>` : ''}
                        </div>
                    </div>
                `;
            });
            
            directionsList.innerHTML = html;
            
            // Auto show directions panel when we have directions
            if (!directionsVisible) {
                directionsPanel.classList.add('show');
                directionsVisible = true;
                btnText.textContent = 'Hide Directions';
            }
        }
        
        // Calculate route - UPDATED for multiple routes
        function calculateRoute() {
            const pickupLat = document.getElementById('pickup_lat').value;
            const pickupLng = document.getElementById('pickup_lng').value;
            const destLat = document.getElementById('destination_lat').value;
            const destLng = document.getElementById('destination_lng').value;
            const vehicleType = document.getElementById('vehicle_type')?.value || 'truck';
            
            if (!pickupLat || !pickupLng || !destLat || !destLng) {
                if (currentPolyline) {
                    map.removeLayer(currentPolyline);
                    currentPolyline = null;
                }
                document.getElementById('routeCards').innerHTML = '<div class="text-center text-muted w-100 py-4"><i class="fas fa-map-marked-alt fa-2x mb-2"></i><p>Select pickup and destination to see available routes</p></div>';
                document.getElementById('routeComparison').style.display = 'none';
                return;
            }
            
            // Clear previous timeout
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            
            // Debounce route calculation
            debounceTimer = setTimeout(() => {
                if (routeCalculationInProgress) return;
                
                routeCalculationInProgress = true;
                document.getElementById('mapLoading').classList.add('show');
                
                // Call server-side route calculation
                fetch('{{ route("calculate.route") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pickup_lat: pickupLat,
                        pickup_lng: pickupLng,
                        dropoff_lat: destLat,
                        dropoff_lng: destLng,
                        vehicle_type: vehicleType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    routeCalculationInProgress = false;
                    document.getElementById('mapLoading').classList.remove('show');
                    
                    if (data.success && data.routes && data.routes.length > 0) {
                        availableRoutes = data.routes;
                        
                        // Display route cards
                        displayRouteCards(availableRoutes);
                        
                        // Select the first route by default
                        selectRoute(0);
                        
                        // Show comparison table
                        displayRouteComparison(availableRoutes);
                    } else {
                        // Fallback to OSRM or straight line
                        calculateRouteFallback(pickupLat, pickupLng, destLat, destLng);
                    }
                })
                .catch(error => {
                    routeCalculationInProgress = false;
                    document.getElementById('mapLoading').classList.remove('show');
                    console.error('Route calculation error:', error);
                    
                    // Fallback
                    calculateRouteFallback(pickupLat, pickupLng, destLat, destLng);
                });
            }, 500);
        }
        
        // Fallback route calculation (OSRM) - FIXED with geometry
        function calculateRouteFallback(pickupLat, pickupLng, destLat, destLng) {
            // Try OSRM first
            fetch(`https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${destLng},${destLat}?overview=full&geometries=geojson&steps=true&alternatives=3`)
                .then(response => response.json())
                .then(data => {
                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        const routes = [];
                        
                        data.routes.forEach((route, index) => {
                            const distanceKm = (route.distance / 1000).toFixed(2);
                            const durationMinutes = Math.round(route.duration / 60);
                            const fare = calculateFare(distanceKm);
                            
                            routes.push({
                                id: index,
                                name: index === 0 ? 'Fastest Route' : (index === 1 ? 'Alternative Route' : 'Scenic Route'),
                                distance: parseFloat(distanceKm),
                                distance_text: distanceKm + ' km',
                                duration: durationMinutes,
                                duration_text: formatDuration(durationMinutes),
                                fare: fare,
                                fare_text: 'Rs ' + fare.toLocaleString(),
                                geometry: route.geometry,
                                directions: extractDirections(route),
                                has_tolls: index === 0,
                                toll_cost: index === 0 ? Math.round(distanceKm * 4) : 0,
                                summary: index === 0 ? 'Via main highways' : (index === 1 ? 'Avoiding tolls' : 'Scenic route')
                            });
                        });
                        
                        availableRoutes = routes;
                        displayRouteCards(routes);
                        selectRoute(0);
                        displayRouteComparison(routes);
                    } else {
                        // Ultimate fallback to generated routes
                        generateFallbackRoutes(pickupLat, pickupLng, destLat, destLng);
                    }
                })
                .catch(() => {
                    generateFallbackRoutes(pickupLat, pickupLng, destLat, destLng);
                });
        }
        
        // Generate fallback routes - FIXED with geometry
        function generateFallbackRoutes(lat1, lng1, lat2, lng2) {
            // Calculate direct distance using Haversine formula
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const directDistance = R * c;
            
            // Create approximate geometries for each route
            const geometry1 = {
                type: 'LineString',
                coordinates: [
                    [lng1, lat1],
                    [(lng1 + lng2) / 2 + 0.1, (lat1 + lat2) / 2 + 0.05],
                    [lng2, lat2]
                ]
            };
            
            const geometry2 = {
                type: 'LineString',
                coordinates: [
                    [lng1, lat1],
                    [lng1 + 0.3, lat1 - 0.2],
                    [lng2 - 0.3, lat2 + 0.2],
                    [lng2, lat2]
                ]
            };
            
            const geometry3 = {
                type: 'LineString',
                coordinates: [
                    [lng1, lat1],
                    [(lng1 + lng2) / 2, (lat1 + lat2) / 2 + 0.1],
                    [lng2, lat2]
                ]
            };
            
            const routes = [
                {
                    id: 0,
                    name: 'Fastest (Motorway)',
                    distance: parseFloat((directDistance * 1.1).toFixed(2)),
                    duration: Math.round((directDistance * 1.1 / 60) * 60),
                    has_tolls: true,
                    toll_cost: Math.round(directDistance * 4),
                    summary: 'Via M-1/M-2 Motorway - Fastest route with tolls',
                    geometry: geometry1,
                    directions: [
                        { instruction: 'Start from pickup location', distance: 0, duration: 0 },
                        { instruction: 'Head towards motorway entrance', distance: 3000, duration: 180 },
                        { instruction: 'Merge onto M-1/M-2 Motorway', distance: 50000, duration: 1800 },
                        { instruction: 'Continue on motorway', distance: directDistance * 500, duration: directDistance * 20 },
                        { instruction: 'Take exit towards destination', distance: 5000, duration: 300 },
                        { instruction: 'Arrive at destination', distance: 0, duration: 0 }
                    ]
                },
                {
                    id: 1,
                    name: 'Economic (No Tolls)',
                    distance: parseFloat((directDistance * 1.4).toFixed(2)),
                    duration: Math.round((directDistance * 1.4 / 40) * 60),
                    has_tolls: false,
                    toll_cost: 0,
                    summary: 'Via GT Road - Avoid tolls, longer but economical',
                    geometry: geometry2,
                    directions: [
                        { instruction: 'Start from pickup location', distance: 0, duration: 0 },
                        { instruction: 'Head towards GT Road', distance: 2000, duration: 120 },
                        { instruction: 'Continue on GT Road', distance: 60000, duration: 3600 },
                        { instruction: 'Pass through local markets', distance: 20000, duration: 1800 },
                        { instruction: 'Turn towards destination', distance: 15000, duration: 900 },
                        { instruction: 'Arrive at destination', distance: 0, duration: 0 }
                    ]
                },
                {
                    id: 2,
                    name: 'Balanced Route',
                    distance: parseFloat((directDistance * 1.2).toFixed(2)),
                    duration: Math.round((directDistance * 1.2 / 50) * 60),
                    has_tolls: false,
                    toll_cost: 0,
                    summary: 'Mix of highways and local roads',
                    geometry: geometry3,
                    directions: [
                        { instruction: 'Start from pickup location', distance: 0, duration: 0 },
                        { instruction: 'Take main highway', distance: 40000, duration: 1800 },
                        { instruction: 'Continue on highway', distance: 60000, duration: 2700 },
                        { instruction: 'Exit towards city center', distance: 15000, duration: 900 },
                        { instruction: 'Arrive at destination', distance: 0, duration: 0 }
                    ]
                }
            ];
            
            routes.forEach(route => {
                const fare = calculateFare(route.distance);
                route.fare = fare;
                route.fare_text = 'Rs ' + fare.toLocaleString();
                route.distance_text = route.distance + ' km';
                route.duration_text = formatDuration(route.duration);
            });
            
            availableRoutes = routes;
            displayRouteCards(routes);
            selectRoute(0);
            displayRouteComparison(routes);
        }
        
        // Calculate fare
        function calculateFare(distance) {
            const ratePerKm = parseFloat(document.getElementById('rate_per_km')?.value || 35);
            return Math.round(distance * ratePerKm * 1.16);
        }
        
        // Format duration
        function formatDuration(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            if (hours > 0) {
                return hours + ' hr ' + mins + ' min';
            }
            return mins + ' min';
        }
        
        // Extract directions from route
        function extractDirections(route) {
            const directions = [];
            if (route.legs && route.legs[0] && route.legs[0].steps) {
                route.legs[0].steps.forEach(step => {
                    let instruction = step.maneuver?.type || 'Continue';
                    if (step.maneuver?.modifier) {
                        instruction = step.maneuver.modifier + ' ' + instruction;
                    }
                    if (step.name && step.name !== '-') {
                        instruction += ' onto ' + step.name;
                    }
                    directions.push({
                        instruction: instruction,
                        distance: step.distance,
                        duration: step.duration
                    });
                });
            }
            return directions;
        }
        
        // Display route cards
        function displayRouteCards(routes) {
            const container = document.getElementById('routeCards');
            let html = '';
            
            routes.forEach((route, index) => {
                const isFastest = index === 0;
                const isEconomical = route.fare === Math.min(...routes.map(r => r.fare));
                const cardClass = isFastest ? 'fastest' : (isEconomical ? 'economical' : '');
                
                html += `
                    <div class="route-card ${cardClass} ${index === selectedRouteId ? 'selected' : ''}" 
                         onclick="selectRoute(${index})"
                         data-route-id="${index}">
                        ${index === 0 ? '<div class="route-badge">Fastest</div>' : ''}
                        <div class="route-header">
                            <span class="route-name">${route.name}</span>
                            ${route.has_tolls ? '<span class="route-toll-badge"><i class="fas fa-toll me-1"></i>Toll</span>' : ''}
                        </div>
                        <div class="route-details">
                            <div class="route-metric">
                                <span><i class="fas fa-road me-1"></i> Distance</span>
                                <span>${route.distance_text}</span>
                            </div>
                            <div class="route-metric">
                                <span><i class="fas fa-clock me-1"></i> Duration</span>
                                <span>${route.duration_text}</span>
                            </div>
                            ${route.has_tolls ? `
                            <div class="route-metric">
                                <span><i class="fas fa-money-bill-wave me-1 text-warning"></i> Toll</span>
                                <span>Rs ${route.toll_cost?.toLocaleString() || '0'}</span>
                            </div>
                            ` : ''}
                        </div>
                        <div class="route-fare">
                            <div class="route-fare-amount">${route.fare_text}</div>
                            <div class="route-fare-note">Including taxes</div>
                        </div>
                        <div class="route-summary" title="${route.summary || ''}">
                            <i class="fas fa-info-circle me-1"></i> ${route.summary || 'Standard route'}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('routeComparison').style.display = 'block';
        }
        
        // Select a route - Make it globally accessible - FIXED with duration storage
        window.selectRoute = function(routeId) {
            selectedRouteId = routeId;
            const route = availableRoutes[routeId];
            
            if (!route) return;
            
            // Update card selection
            document.querySelectorAll('.route-card').forEach(card => {
                card.classList.remove('selected');
            });
            const selectedCard = document.querySelector(`.route-card[data-route-id="${routeId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
            // Update comparison table highlight
            updateComparisonHighlight(routeId);
            
            // Update map with selected route
            if (route.geometry) {
                drawRoute(route.geometry);
                // Limit geometry size if too large
                let geometryToSave = route.geometry;
                if (route.geometry.coordinates && route.geometry.coordinates.length > 100) {
                    // Sample the coordinates to reduce size
                    const step = Math.floor(route.geometry.coordinates.length / 50);
                    const sampledCoords = [];
                    for (let i = 0; i < route.geometry.coordinates.length; i += step) {
                        sampledCoords.push(route.geometry.coordinates[i]);
                    }
                    // Ensure last coordinate is included
                    if (sampledCoords[sampledCoords.length - 1] !== route.geometry.coordinates[route.geometry.coordinates.length - 1]) {
                        sampledCoords.push(route.geometry.coordinates[route.geometry.coordinates.length - 1]);
                    }
                    geometryToSave = {
                        type: 'LineString',
                        coordinates: sampledCoords
                    };
                }
                document.getElementById('route_polyline').value = JSON.stringify(geometryToSave);
            } else {
                // Draw approximate route if no geometry
                drawApproximateRoute(routeId);
                // Create approximate geometry for fallback
                if (pickupMarker && destinationMarker) {
                    const pickup = pickupMarker.getLatLng();
                    const dest = destinationMarker.getLatLng();
                    const approxGeometry = {
                        type: 'LineString',
                        coordinates: [
                            [pickup.lng, pickup.lat],
                            [dest.lng, dest.lat]
                        ]
                    };
                    document.getElementById('route_polyline').value = JSON.stringify(approxGeometry);
                }
            }
            
            // Update fare, distance, and duration display
            document.getElementById('distance_display').textContent = route.distance_text;
            document.getElementById('duration_display').textContent = route.duration_text;
            document.getElementById('fare_display').textContent = route.fare_text;
            
            // Update hidden fields - INCLUDING DURATION
            document.getElementById('estimated_distance').value = route.distance;
            document.getElementById('estimated_fare').value = route.fare;
            document.getElementById('estimated_duration').value = route.duration; // ✅ FIXED: Duration now stored
            
            // Limit directions data
            if (route.directions && route.directions.length > 0) {
                // Only store first 20 directions at most
                const limitedDirections = route.directions.slice(0, 20);
                document.getElementById('route_directions').value = JSON.stringify(limitedDirections);
            }
            
            // Save limited selected route data
            const selectedRouteData = {
                id: routeId,
                name: route.name,
                has_tolls: route.has_tolls,
                toll_cost: route.toll_cost,
                distance: route.distance,
                duration: route.duration, // ✅ Include duration
                fare: route.fare
            };
            
            document.getElementById('selected_route_data').value = JSON.stringify(selectedRouteData);
            
            // Save limited route options
            if (availableRoutes.length > 0) {
                const limitedRoutes = availableRoutes.map(r => ({
                    id: r.id,
                    name: r.name,
                    distance: r.distance,
                    duration: r.duration, // ✅ Include duration
                    fare: r.fare,
                    has_tolls: r.has_tolls,
                    toll_cost: r.toll_cost,
                    summary: r.summary
                }));
                
                let routeOptionsInput = document.getElementById('route_options');
                if (!routeOptionsInput) {
                    routeOptionsInput = document.createElement('input');
                    routeOptionsInput.type = 'hidden';
                    routeOptionsInput.name = 'route_options';
                    routeOptionsInput.id = 'route_options';
                    document.getElementById('bookingForm').appendChild(routeOptionsInput);
                }
                routeOptionsInput.value = JSON.stringify(limitedRoutes);
            }
            
            // Display directions for selected route
            if (route.directions && route.directions.length > 0) {
                displayDirections(route.directions.slice(0, 20));
            } else {
                // Create simple directions
                const simpleDirections = [
                    {
                        instruction: `Start from ${document.getElementById('pickup_location').value || 'pickup location'}`,
                        distance: 0
                    },
                    {
                        instruction: `Continue via ${route.name}`,
                        distance: route.distance * 1000
                    },
                    {
                        instruction: `Arrive at ${document.getElementById('drop_location').value || 'destination'}`,
                        distance: 0
                    }
                ];
                displayDirections(simpleDirections);
                document.getElementById('route_directions').value = JSON.stringify(simpleDirections);
            }
            
            // Add toll info to fare note
            if (route.has_tolls) {
                document.getElementById('fare_note').innerHTML = `Includes toll charges <span class="badge bg-warning text-dark">Rs ${route.toll_cost?.toLocaleString() || '0'}</span>`;
            } else {
                document.getElementById('fare_note').innerHTML = 'No toll charges';
            }
            
            validateRequiredFields();
        };
        
        // Draw approximate route based on route type
        function drawApproximateRoute(routeId) {
            if (!pickupMarker || !destinationMarker) return;
            
            const pickup = pickupMarker.getLatLng();
            const dest = destinationMarker.getLatLng();
            
            if (currentPolyline) {
                map.removeLayer(currentPolyline);
            }
            
            let points = [];
            const colors = ['#3498db', '#27ae60', '#e67e22'];
            
            if (routeId === 0) {
                // Fastest - more direct
                points = [
                    [pickup.lat, pickup.lng],
                    [(pickup.lat + dest.lat) / 2 + 0.05, (pickup.lng + dest.lng) / 2 - 0.05],
                    [dest.lat, dest.lng]
                ];
            } else if (routeId === 1) {
                // Economic - avoid tolls (curvier)
                points = [
                    [pickup.lat, pickup.lng],
                    [pickup.lat - 0.2, pickup.lng + 0.3],
                    [dest.lat + 0.2, dest.lng - 0.3],
                    [dest.lat, dest.lng]
                ];
            } else {
                // Balanced
                points = [
                    [pickup.lat, pickup.lng],
                    [(pickup.lat + dest.lat) / 2 + 0.15, (pickup.lng + dest.lng) / 2],
                    [dest.lat, dest.lng]
                ];
            }
            
            currentPolyline = L.polyline(points, {
                color: colors[routeId % colors.length],
                weight: 5,
                opacity: 0.8,
                dashArray: routeId === 1 ? '10, 10' : null
            }).addTo(map);
            
            map.fitBounds(currentPolyline.getBounds(), { padding: [50, 50] });
        }
        
        // Display route comparison table
        function displayRouteComparison(routes) {
            const tbody = document.getElementById('comparisonBody');
            let html = '';
            
            routes.forEach((route, index) => {
                const isSelected = index === selectedRouteId;
                const rowClass = isSelected ? 'selected-route' : '';
                
                html += `
                    <tr class="${rowClass}" onclick="selectRoute(${index})" style="cursor: pointer;">
                        <td>
                            <strong>${route.name}</strong>
                            ${index === 0 ? '<span class="badge bg-success ms-2">Fastest</span>' : ''}
                            ${route.fare === Math.min(...routes.map(r => r.fare)) ? '<span class="badge bg-warning ms-2">Economical</span>' : ''}
                        </td>
                        <td>${route.distance_text}</td>
                        <td>${route.duration_text}</td>
                        <td>
                            ${route.has_tolls ? 
                                '<span class="text-danger"><i class="fas fa-toll me-1"></i>Yes</span>' : 
                                '<span class="text-success"><i class="fas fa-check me-1"></i>No</span>'}
                        </td>
                        <td><strong class="text-success">${route.fare_text}</strong></td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Update comparison highlight
        function updateComparisonHighlight(routeId) {
            const rows = document.querySelectorAll('#comparisonBody tr');
            rows.forEach((row, index) => {
                if (index === routeId) {
                    row.classList.add('selected-route');
                } else {
                    row.classList.remove('selected-route');
                }
            });
        }
        
        // Draw route with blue color
        function drawRoute(geometry) {
            if (currentPolyline) {
                map.removeLayer(currentPolyline);
            }
            
            let latLngs = [];
            
            // Handle GeoJSON format
            if (geometry && geometry.coordinates) {
                latLngs = geometry.coordinates.map(coord => [coord[1], coord[0]]);
            } else if (Array.isArray(geometry)) {
                latLngs = geometry.map(coord => [coord[1], coord[0]]);
            } else {
                console.error('Unknown geometry format');
                return;
            }
            
            // Use different colors for different routes
            const colors = ['#3498db', '#27ae60', '#e67e22'];
            const color = colors[selectedRouteId % colors.length];
            
            currentPolyline = L.polyline(latLngs, {
                color: color,
                weight: 6,
                opacity: 0.9,
                smoothFactor: 1,
                lineCap: 'round',
                lineJoin: 'round'
            }).addTo(map);
            
            // Fit map to show the entire route
            map.fitBounds(currentPolyline.getBounds(), { padding: [50, 50] });
        }
        
        // Get current location - Make it globally accessible
        window.getCurrentLocation = function(type) {
            if (navigator.geolocation) {
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };
                
                // Show loading
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'loading-overlay';
                loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Getting your location...';
                document.body.appendChild(loadingDiv);
                
                navigator.geolocation.getCurrentPosition(
                    position => {
                        loadingDiv.remove();
                        reverseGeocode(position.coords.latitude, position.coords.longitude, type);
                    },
                    error => {
                        loadingDiv.remove();
                        let message = 'Location access denied';
                        if (error.code === error.TIMEOUT) {
                            message = 'Location request timed out';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            message = 'Location information unavailable';
                        }
                        alert(message);
                    },
                    options
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        };
        
        // Set default date/time
        function setDefaultDateTime() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('trip_date').value = tomorrow.toISOString().split('T')[0];
        }
        
        // Validate fields
        function validateRequiredFields() {
            const tripDate = document.getElementById('trip_date').value;
            const pickupLoc = document.getElementById('pickup_location').value;
            const dropLoc = document.getElementById('drop_location').value;
            const pickupLat = document.getElementById('pickup_lat').value;
            const destLat = document.getElementById('destination_lat').value;
            const paymentMethod = document.getElementById('payment_method').value;
            const submitBtn = document.getElementById('submitBtn');
            
            // Also check if we have routes selected
            const hasSelectedRoute = availableRoutes.length > 0 && selectedRouteId !== undefined;
            
            submitBtn.disabled = !(tripDate && pickupLoc && dropLoc && pickupLat && destLat && paymentMethod && hasSelectedRoute);
        }
        
        // Form submit validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const dist = document.getElementById('estimated_distance').value;
            const fare = document.getElementById('estimated_fare').value;
            const duration = document.getElementById('estimated_duration').value;
            
            if (!dist || !fare || !duration) {
                e.preventDefault();
                alert('Please select a route first (Distance, Fare, and Duration are required)');
                return;
            }
            
            if (!document.getElementById('terms_agreed').checked) {
                e.preventDefault();
                alert('Please agree to the terms and conditions');
                return;
            }
            
            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
        });
    </script>
</body>
</html>