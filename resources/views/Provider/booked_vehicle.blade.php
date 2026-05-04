<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Active Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --info: #17a2b8;
            --danger: #dc3545;
        }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); overflow-x: hidden; }
        .sidebar { background: linear-gradient(180deg, var(--primary) 0%, #1a2530 100%); color: white; height: 100vh; position: fixed; top: 0; left: 0; width: 280px; transition: all 0.3s; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,0.1); border-right: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; background: rgba(255,255,255,0.05); }
        .sidebar .logo h3 { margin: 0; font-weight: 700; font-size: 1.5rem; }
        .sidebar .logo span { color: var(--secondary); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; margin: 8px 15px; border-radius: 12px; transition: all 0.3s ease; font-weight: 500; position: relative; overflow: hidden; }
        .sidebar .nav-link:before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: var(--secondary); transform: translateX(-10px); transition: transform 0.3s ease; }
        .sidebar .nav-link:hover { background: rgba(52,152,219,0.15); color: white; transform: translateX(5px); }
        .sidebar .nav-link:hover:before { transform: translateX(0); }
        .sidebar .nav-link.active { background: rgba(52,152,219,0.2); color: white; box-shadow: 0 4px 15px rgba(52,152,219,0.3); }
        .sidebar .nav-link.active:before { transform: translateX(0); }
        .sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }
        .main-content { margin-left: 280px; transition: all 0.3s; min-height: 100vh; }
        .topbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 30px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 999; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .topbar .search-box { position: relative; max-width: 400px; }
        .topbar .search-box input { border-radius: 25px; padding-left: 45px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); transition: all 0.3s ease; }
        .topbar .search-box input:focus { box-shadow: 0 0 0 3px rgba(52,152,219,0.1); border-color: var(--secondary); }
        .topbar .search-box i { position: absolute; left: 20px; top: 12px; color: #6c757d; }
        .topbar .user-info img { width: 45px; height: 45px; border-radius: 50%; margin-right: 12px; border: 3px solid var(--secondary); box-shadow: 0 2px 10px rgba(52,152,219,0.3); }
        .content-area { padding: 30px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-bottom: 25px; transition: all 0.3s ease; background: white; overflow: hidden; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        .card-header { background: linear-gradient(135deg, white 0%, #f8f9fa 100%); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 25px; font-weight: 600; font-size: 1.1rem; }
        .stat-card { text-align: center; padding: 30px 20px; position: relative; overflow: hidden; }
        .stat-card:before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--secondary), var(--primary)); }
        .stat-card i { font-size: 2.8rem; margin-bottom: 20px; opacity: 0.9; }
        .stat-card .count { font-size: 2.5rem; font-weight: 700; margin: 15px 0; background: linear-gradient(135deg, var(--dark), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-card .label { color: #6c757d; font-size: 0.95rem; font-weight: 500; }
        .bg-primary-light { background: linear-gradient(135deg, rgba(52,152,219,0.08) 0%, rgba(52,152,219,0.02) 100%); }
        .bg-success-light { background: linear-gradient(135deg, rgba(39,174,96,0.08) 0%, rgba(39,174,96,0.02) 100%); }
        .bg-warning-light { background: linear-gradient(135deg, rgba(243,156,18,0.08) 0%, rgba(243,156,18,0.02) 100%); }
        .bg-info-light { background: linear-gradient(135deg, rgba(23,162,184,0.08) 0%, rgba(23,162,184,0.02) 100%); }
        .booking-card { border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; margin-bottom: 20px; transition: all 0.3s; border-left: 4px solid transparent; }
        .booking-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .booking-card.order_confirmed { border-left-color: var(--info); }
        .booking-card.vehicle_dispatched { border-left-color: var(--primary); }
        .booking-card.in_transit { border-left-color: var(--warning); }
        .booking-card.delivered { border-left-color: var(--success); }
        .booking-image { height: 180px; background-size: cover; background-position: center; position: relative; }
        .booking-badge { position: absolute; top: 15px; right: 15px; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
        .booking-badge.order_confirmed { background: var(--info); color: white; }
        .booking-badge.vehicle_dispatched { background: var(--primary); color: white; }
        .booking-badge.in_transit { background: var(--warning); color: white; }
        .booking-badge.delivered { background: var(--success); color: white; }
        .booking-info { padding: 20px; }
        .booking-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .booking-details { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .detail-item { display: flex; flex-direction: column; }
        .detail-label { font-size: 0.8rem; color: #6c757d; font-weight: 500; }
        .detail-value { font-weight: 600; font-size: 0.9rem; }
        .status-badge { font-size: 0.8rem; padding: 3px 8px; border-radius: 12px; font-weight: 500; }
        .status-badge.order_confirmed { background: #cff4fc; color: #055160; }
        .status-badge.vehicle_dispatched { background: #cfe2ff; color: #052c65; }
        .status-badge.in_transit { background: #fff3cd; color: #664d03; }
        .status-badge.delivered { background: #d1e7dd; color: #0a3622; }
        .delivery-actions { display: flex; gap: 8px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.05); }
        .btn-delivery { flex: 1; border: none; padding: 8px 12px; border-radius: 8px; font-weight: 500; font-size: 0.85rem; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 5px; cursor: pointer; }
        .btn-delivery.dispatched { background: linear-gradient(135deg, var(--primary), #2980b9); color: white; }
        .btn-delivery.transit { background: linear-gradient(135deg, var(--warning), #d68910); color: white; }
        .btn-delivery.delivered { background: linear-gradient(135deg, var(--success), #219955); color: white; }
        .btn-delivery:disabled { opacity: 0.5; cursor: not-allowed; }
        .action-buttons { display: flex; gap: 10px; margin-top: 10px; }
        .action-buttons .btn { flex: 1; height: 42px; display: flex; align-items: center; justify-content: center; }
        .btn-detail { background: transparent; border: 2px solid var(--secondary); color: var(--secondary); transition: all 0.3s ease; }
        .btn-detail:hover { background: var(--secondary); color: white; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(52,152,219,0.3); }
        .btn-contract { background: linear-gradient(135deg, var(--success), #219955); color: white; border: none; }
        .btn-contract:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(39,174,96,0.3); }
        .btn-filter { background: transparent; border: 2px solid var(--secondary); color: var(--secondary); transition: all 0.3s ease; }
        .btn-filter:hover { background: var(--secondary); color: white; }
        .footer { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); padding: 20px 30px; border-top: 1px solid rgba(0,0,0,0.05); margin-left: 280px; }

        /* ===== LIVE LOCATION STYLES ===== */
        .live-location-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed rgba(0,0,0,0.1);
        }
        .btn-share-location {
            width: 100%;
            padding: 9px 14px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }
        .btn-share-location.start {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 3px 10px rgba(39,174,96,0.35);
        }
        .btn-share-location.start:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(39,174,96,0.45);
        }
        .btn-share-location.stop {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 3px 10px rgba(231,76,60,0.35);
        }
        .btn-share-location.stop:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(231,76,60,0.45);
        }
        .location-sharing-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            margin-top: 6px;
            padding: 5px 10px;
            border-radius: 20px;
            background: rgba(39,174,96,0.1);
            color: #27ae60;
            font-weight: 600;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #27ae60;
            animation: pulseDot 1.4s infinite;
            flex-shrink: 0;
        }
        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.6; }
        }

        /* Detail Modal */
        .detail-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 1100; opacity: 0; visibility: hidden; transition: all 0.3s ease; overflow-y: auto; padding: 20px 0; }
        .detail-modal.active { opacity: 1; visibility: visible; }
        .detail-modal-content { background-color: white; border-radius: 16px; width: 90%; max-width: 800px; max-height: 90vh; box-shadow: 0 15px 40px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s ease; position: relative; overflow: hidden; }
        .detail-modal.active .detail-modal-content { transform: translateY(0); }
        .detail-modal-header { padding: 20px 25px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10; border-radius: 16px 16px 0 0; background: linear-gradient(135deg, white 0%, #f8f9fa 100%); }
        .detail-modal-title { font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--primary); }
        .detail-modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark); transition: all 0.3s ease; }
        .detail-modal-close:hover { color: var(--accent); transform: scale(1.1); }
        .detail-modal-body { padding: 25px; overflow-y: auto; max-height: calc(90vh - 100px); }
        .detail-section { margin-bottom: 25px; }
        .detail-section-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 15px; color: var(--primary); border-bottom: 2px solid var(--secondary); padding-bottom: 8px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .detail-item-large { display: flex; flex-direction: column; padding: 15px; background-color: #f8f9fa; border-radius: 10px; transition: all 0.3s ease; }
        .detail-item-large:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .detail-label-large { font-size: 0.9rem; color: #6c757d; font-weight: 500; margin-bottom: 5px; }
        .detail-value-large { font-weight: 600; font-size: 1rem; color: var(--dark); }
        .timeline-item { display: flex; align-items: center; margin-bottom: 15px; position: relative; }
        .timeline-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
        .timeline-icon.completed { background: var(--success); color: white; }
        .timeline-icon.pending { background: #e9ecef; color: #6c757d; }
        .timeline-content { flex: 1; }
        .timeline-title { font-weight: 600; margin-bottom: 2px; }
        .timeline-time { font-size: 0.8rem; color: #6c757d; }
        .detail-modal-footer { padding: 20px 25px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: flex-end; gap: 10px; position: sticky; bottom: 0; background: white; z-index: 10; border-radius: 0 0 16px 16px; }

        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link { padding: 15px; text-align: center; margin: 5px 10px; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }
            .main-content, .footer { margin-left: 80px; }
            .content-area { padding: 20px 15px; }
            .booking-details { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .delivery-actions { flex-direction: column; }
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
            <a class="nav-link " href="{{route('booking.requests')}}"><i class="fas fa-bell"></i> <span>Booking Requests</span></a>
            <a class="nav-link active" href="{{route('see.trip')}}"><i class="fas fa-clipboard-list"></i> <span>Active Bookings</span></a>
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



<!-- 
     <a class="nav-link" href="{{route('booking.requests')}}">
                <i class="fas fa-bell"></i> <span>Booking Requests</span>
                @php
                    $providerId = session('user_id');
                    $vehicleIds = \App\Models\Vehicle::where('user_id', $providerId)->pluck('id');
                    $pendingCount = \App\Models\Booking::whereIn('vehicle_id', $vehicleIds)->where('status', 'request')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-2">{{ $pendingCount }}</span>
                @endif
            </a> -->
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Search bookings, customers...">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">{{ session('user_name', 'Provider') }}</span>
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

        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Active Bookings</h4>
                    <p class="text-muted mb-0">Manage and track your confirmed bookings.</p>
                </div>
                <span class="badge bg-success fs-6">Verified Provider</span>
            </div>

            <!-- Stats -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-clipboard-list text-primary"></i>
                            <div class="count" id="totalBookings">{{ $totalBookings ?? 0 }}</div>
                            <div class="label">Total Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-truck text-warning"></i>
                            <div class="count" id="pendingDeliveries">{{ $pendingDeliveries ?? 0 }}</div>
                            <div class="label">In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle text-success"></i>
                            <div class="count" id="completedBookings">{{ $completedBookings ?? 0 }}</div>
                            <div class="label">Delivered</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-info-light">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave text-info"></i>
                            <div class="count">Rs {{ number_format($totalEarnings ?? 0) }}</div>
                            <div class="label">Earnings</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mt-4">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Bookings</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Delivery Status</label>
                            <select class="form-select" id="deliveryStatusFilter">
                                <option value="">All Status</option>
                                <option value="order_confirmed">Order Confirmed</option>
                                <option value="vehicle_dispatched">Vehicle Dispatched</option>
                                <option value="in_transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicleTypeFilter">
                                <option value="">All Types</option>
                                <option value="truck">Truck</option>
                                <option value="van">Van</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sortBy">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="fare">Fare (High to Low)</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-filter" onclick="applyFilters()"><i class="fas fa-filter me-2"></i> Apply Filters</button>
                            <button class="btn btn-filter ms-2" onclick="resetFilters()"><i class="fas fa-redo me-2"></i> Reset</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Grid -->
            <div class="row mt-4" id="bookingsGrid">
                @if($bookings->isEmpty())
                    <div class="col-12">
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Active Bookings</h4>
                                <p class="text-muted">You don't have any confirmed bookings yet.</p>
                                <a href="{{ route('booking.requests') }}" class="btn btn-primary"><i class="fas fa-bell me-2"></i> View Pending Requests</a>
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($bookings as $booking)
                    <div class="col-xl-4 col-lg-6 col-md-6 booking-item"
                        data-id="{{ $booking->id }}"
                        data-status="{{ $booking->delivery_status }}"
                        data-type="{{ strtolower($booking->vehicle->vehicle_type ?? '') }}"
                        data-fare="{{ $booking->estimated_fare ?? 0 }}">

                        <div class="booking-card {{ $booking->delivery_status }}" id="booking-{{ $booking->id }}">
                            <div class="booking-image" style="background-image: url('{{ $booking->vehicle->vehicle_image ? asset('uploads/vehicles/' . $booking->vehicle->vehicle_image) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500' }}')">
                                <div class="booking-badge {{ $booking->delivery_status }}">{{ $booking->delivery_status_text }}</div>
                            </div>

                            <div class="booking-info">
                                <div class="booking-title">
                                    <span>#TL-{{ $booking->id }}</span>
                                    <span class="status-badge {{ $booking->delivery_status }}">{{ $booking->delivery_status_text }}</span>
                                </div>

                                <div class="booking-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Customer</span>
                                        <span class="detail-value">{{ $booking->customer->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Vehicle</span>
                                        <span class="detail-value">{{ $booking->vehicle->vehicle_type ?? 'N/A' }} - {{ $booking->vehicle->vehicle_number ?? '' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Pickup</span>
                                        <span class="detail-value">{{ Str::limit($booking->pickup_location, 20) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Drop</span>
                                        <span class="detail-value">{{ Str::limit($booking->dropoff_location, 20) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Date</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Fare</span>
                                        <span class="detail-value fw-bold text-success">Rs {{ number_format($booking->estimated_fare ?? 0) }}</span>
                                    </div>
                                </div>

                                <!-- Delivery Action Buttons -->
                                <div class="delivery-actions">
                                    @php $currentStatus = $booking->delivery_status; @endphp
                                    <button class="btn-delivery dispatched"
                                        onclick="updateDeliveryStatus({{ $booking->id }}, 'vehicle_dispatched')"
                                        {{ $currentStatus !== 'order_confirmed' ? 'disabled' : '' }}>
                                        <i class="fas fa-truck"></i> Dispatch
                                    </button>
                                    <button class="btn-delivery transit"
                                        onclick="updateDeliveryStatus({{ $booking->id }}, 'in_transit')"
                                        {{ $currentStatus !== 'vehicle_dispatched' ? 'disabled' : '' }}>
                                        <i class="fas fa-route"></i> Transit
                                    </button>
                                    <button class="btn-delivery delivered"
                                        onclick="updateDeliveryStatus({{ $booking->id }}, 'delivered')"
                                        {{ $currentStatus !== 'in_transit' ? 'disabled' : '' }}>
                                        <i class="fas fa-check-circle"></i> Deliver
                                    </button>
                                </div>

                                {{-- ===== LIVE LOCATION SECTION — HAMESHA DIKHEGA (kisi bhi status pe) ===== --}}
                                <div class="live-location-section" id="live-location-section-{{ $booking->id }}">
                                    @if($booking->is_sharing_location)
                                        <button class="btn-share-location stop"
                                            id="loc-btn-{{ $booking->id }}"
                                            onclick="stopLocationSharing({{ $booking->id }})">
                                            <i class="fas fa-stop-circle"></i>
                                            Stop Sharing Location
                                        </button>
                                        <div class="location-sharing-status" id="sharing-status-{{ $booking->id }}">
                                            <div class="pulse-dot"></div>
                                            Live location is ON — customer can see you
                                        </div>
                                    @else
                                        <button class="btn-share-location start"
                                            id="loc-btn-{{ $booking->id }}"
                                            onclick="startLocationSharing({{ $booking->id }})">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Share Live Location
                                        </button>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <button class="btn btn-detail" onclick="viewBookingDetails({{ $booking->id }})">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                    <button class="btn btn-contract" onclick="contactCustomer({{ $booking->id }})">
                                        <i class="fas fa-phone me-1"></i> Contact
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6"><p class="mb-0"><strong>© 2024 TruckLink: Verified Goods.</strong> All rights reserved.</p></div>
                <div class="col-md-6 text-end"><p class="mb-0 text-muted">Service Provider Panel v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>System Online</span></p></div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3 class="detail-modal-title">Booking Details</h3>
                <button class="detail-modal-close" id="closeDetailModal">&times;</button>
            </div>
            <div class="detail-modal-body" id="detailModalBody"></div>
            <div class="detail-modal-footer">
                <button class="btn btn-secondary" id="closeModalBtn">Close</button>
                <button class="btn btn-primary" id="contactBtn"><i class="fas fa-phone me-2"></i> Contact Customer</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ===== BOOKING DATA FROM PHP =====
        let bookingData = {
            @foreach($bookings as $booking)
            {{ $booking->id }}: {
                id: {{ $booking->id }},
                customerName: "{{ addslashes($booking->customer->name ?? 'N/A') }}",
                customerPhone: "{{ addslashes($booking->customer->mobile ?? $booking->customer->phone ?? 'N/A') }}",
                customerEmail: "{{ addslashes($booking->customer->email ?? 'N/A') }}",
                pickupLocation: "{{ addslashes($booking->pickup_location ?? 'N/A') }}",
                dropoffLocation: "{{ addslashes($booking->dropoff_location ?? 'N/A') }}",
                vehicleType: "{{ addslashes($booking->vehicle->vehicle_type ?? 'N/A') }}",
                vehicleNumber: "{{ addslashes($booking->vehicle->vehicle_number ?? 'N/A') }}",
                goodsType: "{{ addslashes($booking->goods_type ?? 'N/A') }}",
                goodsWeight: "{{ $booking->goods_weight ?? 'N/A' }}",
                bookingDate: "{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}",
                pickupTime: "{{ $booking->pickup_time ?? 'N/A' }}",
                deliveryStatus: "{{ $booking->delivery_status }}",
                deliveryStatusText: "{{ $booking->delivery_status_text }}",
                fare: "{{ number_format($booking->estimated_fare ?? 0) }}",
                actualFare: "{{ number_format($booking->actual_fare ?? 0) }}",
                penaltyAmount: "{{ number_format($booking->penalty_amount ?? 0) }}",
                distance: "{{ $booking->estimated_distance ?? 'N/A' }} km",
                specialInstructions: "{{ addslashes($booking->special_instructions ?? 'No instructions') }}",
                paymentMethod: "{{ $booking->payment_method ?? 'N/A' }}",
                paymentStatus: "{{ $booking->payment_status ?? 'N/A' }}",
                acceptedAt: "{{ $booking->accepted_at ? \Carbon\Carbon::parse($booking->accepted_at)->format('d M Y h:i A') : 'N/A' }}",
                dispatchedAt: "{{ $booking->dispatched_at ? \Carbon\Carbon::parse($booking->dispatched_at)->format('d M Y h:i A') : 'N/A' }}",
                inTransitAt: "{{ $booking->in_transit_at ? \Carbon\Carbon::parse($booking->in_transit_at)->format('d M Y h:i A') : 'N/A' }}",
                deliveredAt: "{{ $booking->delivered_at ? \Carbon\Carbon::parse($booking->delivered_at)->format('d M Y h:i A') : 'N/A' }}",
                isSharingLocation: {{ $booking->is_sharing_location ? 'true' : 'false' }}
            },
            @endforeach
        };

        // ===== LIVE LOCATION VARIABLES =====
        let locationIntervals = {};

        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', function () {
            setupEventListeners();

            // Agar koi booking already sharing thi toh resume interval
            Object.keys(bookingData).forEach(bookingId => {
                const bData = bookingData[bookingId];
                if (bData.isSharingLocation) {
                    startLocationInterval(parseInt(bookingId));
                }
            });
        });

        function setupEventListeners() {
            document.getElementById('closeDetailModal').addEventListener('click', () => document.getElementById('detailModal').classList.remove('active'));
            document.getElementById('closeModalBtn').addEventListener('click', () => document.getElementById('detailModal').classList.remove('active'));
            document.getElementById('contactBtn').addEventListener('click', () => {
                Swal.fire({ title: 'Contact Customer', text: 'Contact feature will be available soon!', icon: 'info', confirmButtonColor: '#3498db' });
            });
            document.getElementById('detailModal').addEventListener('click', function (e) {
                if (e.target === this) this.classList.remove('active');
            });
            document.getElementById('searchInput').addEventListener('keyup', function () {
                const s = this.value.toLowerCase();
                document.querySelectorAll('.booking-item').forEach(item => {
                    item.style.display = item.textContent.toLowerCase().includes(s) ? '' : 'none';
                });
            });
        }

        // ===================================================
        //              LIVE LOCATION FUNCTIONS
        // ===================================================

        /**
         * Location sharing shuru karo
         */
        function startLocationSharing(bookingId) {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Geolocation is not supported by your browser.', 'error');
                return;
            }

            Swal.fire({
                title: 'Starting Location Sharing...',
                html: '<div class="text-center"><div class="spinner-border text-success mb-2"></div><p>Getting your current location...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            // maximumAge: 0 — cached location use mat karo, fresh location lo
            // enableHighAccuracy: true — GPS use karo (mobile pe)
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Pehle server ko start karo
                    fetch(`/booking/${bookingId}/start-location-sharing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            // Turant pehla location bhejna
                            sendLocationToServer(bookingId, lat, lng);

                            // UI update
                            updateLocationButton(bookingId, true);

                            // Interval shuru (har 60 seconds)
                            startLocationInterval(bookingId);

                            Swal.fire({
                                title: 'Location Sharing Started!',
                                html: `<div class="text-center">
                                    <p>Customer can now see your live location.</p>
                                    <small class="text-muted">📍 Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}</small><br>
                                    <small class="text-muted">Updates every 1 minute automatically.</small>
                                </div>`,
                                icon: 'success',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to start sharing.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Server error. Please try again.', 'error');
                    });
                },
                (err) => {
                    let errMsg = 'Location access denied.';
                    switch(err.code) {
                        case err.PERMISSION_DENIED:
                            errMsg = 'Location permission denied. Please allow location access in browser settings.';
                            break;
                        case err.POSITION_UNAVAILABLE:
                            errMsg = 'Location information is unavailable. Please check your GPS/network.';
                            break;
                        case err.TIMEOUT:
                            errMsg = 'Location request timed out. Please try again.';
                            break;
                    }
                    Swal.fire('Location Error', errMsg, 'warning');
                    console.error('Geolocation error:', err.message);
                },
                {
                    enableHighAccuracy: true,  // GPS force — mobile pe accurate location
                    timeout: 15000,            // 15 second wait
                    maximumAge: 0              // cached location bilkul mat use karo
                }
            );
        }

        /**
         * Location sharing band karo
         */
        function stopLocationSharing(bookingId) {
            Swal.fire({
                title: 'Stop Sharing?',
                text: 'Customer will no longer see your live location.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Stop',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    // Pehle interval clear karo
                    clearLocationInterval(bookingId);

                    fetch(`/booking/${bookingId}/stop-location-sharing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            updateLocationButton(bookingId, false);
                            Swal.fire({
                                title: 'Stopped',
                                text: 'Location sharing has been stopped.',
                                icon: 'info',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to stop sharing.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Server error. Please try again.', 'error');
                    });
                }
            });
        }

        /**
         * Location interval shuru karo (har 60 seconds auto-update)
         */
        function startLocationInterval(bookingId) {
            clearLocationInterval(bookingId); // pehle clear

            locationIntervals[bookingId] = setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        sendLocationToServer(bookingId, pos.coords.latitude, pos.coords.longitude);
                        console.log(`Auto location update #${bookingId}: ${pos.coords.latitude}, ${pos.coords.longitude}`);
                    },
                    (err) => console.error('Auto location error:', err.message),
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0  // cached location mat use karo
                    }
                );
            }, 60000); // 60 seconds
        }

        /**
         * Location interval band karo
         */
        function clearLocationInterval(bookingId) {
            if (locationIntervals[bookingId]) {
                clearInterval(locationIntervals[bookingId]);
                delete locationIntervals[bookingId];
            }
        }

        /**
         * Location server pe bhejna
         */
        function sendLocationToServer(bookingId, lat, lng) {
            fetch(`/booking/${bookingId}/update-live-location`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ lat: lat, lng: lng })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    console.log(`✅ Location sent for booking #${bookingId}: ${lat}, ${lng}`);
                } else {
                    console.warn(`⚠️ Location update refused for #${bookingId}:`, data.message);
                    // Server ne refuse kiya toh interval band karo
                    clearLocationInterval(bookingId);
                }
            })
            .catch(err => console.error('Send location error:', err));
        }

        /**
         * Location button UI update karna
         */
        function updateLocationButton(bookingId, isSharingNow) {
            const section = document.getElementById(`live-location-section-${bookingId}`);
            if (!section) return;

            if (isSharingNow) {
                section.innerHTML = `
                    <button class="btn-share-location stop"
                        id="loc-btn-${bookingId}"
                        onclick="stopLocationSharing(${bookingId})">
                        <i class="fas fa-stop-circle"></i>
                        Stop Sharing Location
                    </button>
                    <div class="location-sharing-status" id="sharing-status-${bookingId}">
                        <div class="pulse-dot"></div>
                        Live location is ON — customer can see you
                    </div>
                `;
            } else {
                section.innerHTML = `
                    <button class="btn-share-location start"
                        id="loc-btn-${bookingId}"
                        onclick="startLocationSharing(${bookingId})">
                        <i class="fas fa-map-marker-alt"></i>
                        Share Live Location
                    </button>
                `;
            }
        }

        // ===================================================
        //           DELIVERY STATUS FUNCTIONS
        // ===================================================

        function updateDeliveryStatus(bookingId, status) {
            let title, text, confirmButtonColor, statusText;

            switch (status) {
                case 'vehicle_dispatched':
                    title = 'Dispatch Vehicle?';
                    text = 'Are you sure you want to mark this vehicle as dispatched? The customer will receive an email notification.';
                    confirmButtonColor = '#3498db';
                    statusText = 'Dispatched';
                    break;
                case 'in_transit':
                    title = 'Mark as In Transit?';
                    text = 'Confirm that the vehicle is now in transit to destination.';
                    confirmButtonColor = '#f39c12';
                    statusText = 'In Transit';
                    break;
                case 'delivered':
                    title = 'Mark as Delivered?';
                    text = 'Confirm that the goods have been delivered successfully.';
                    confirmButtonColor = '#27ae60';
                    statusText = 'Delivered';
                    break;
                default: return;
            }

            Swal.fire({
                title, text, icon: 'question',
                showCancelButton: true,
                confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating Status...',
                        html: '<div class="text-center"><div class="spinner-border text-primary mb-3"></div><p>Please wait...</p></div>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`/update-delivery-status/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const currentDateTime = getCurrentDateTime();

                            if (status === 'vehicle_dispatched') {
                                bookingData[bookingId].dispatchedAt = currentDateTime;
                                bookingData[bookingId].deliveryStatus = 'vehicle_dispatched';
                                bookingData[bookingId].deliveryStatusText = 'Vehicle Dispatched';
                                // Live location section already visible hai — kuch karne ki zaroorat nahi

                            } else if (status === 'in_transit') {
                                bookingData[bookingId].inTransitAt = currentDateTime;
                                bookingData[bookingId].deliveryStatus = 'in_transit';
                                bookingData[bookingId].deliveryStatusText = 'In Transit';
                                // Location sharing chal sakti hai in_transit pe bhi — band mat karo

                            } else if (status === 'delivered') {
                                bookingData[bookingId].deliveredAt = currentDateTime;
                                bookingData[bookingId].deliveryStatus = 'delivered';
                                bookingData[bookingId].deliveryStatusText = 'Delivered';
                                // Delivered hone ke baad sharing band karo
                                clearLocationInterval(bookingId);
                            }

                            if (status === 'delivered') {
                                let penaltyHtml = '';
                                if (data.penalty && data.penalty.penalty > 0) {
                                    penaltyHtml = `<br><br><div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>${data.penalty.message}</strong>
                                        <hr class="my-2">
                                        <div class="small">Delay: ${data.penalty.delay_hours} hr | Penalty: Rs ${data.penalty.amount.toLocaleString()} | Final: Rs ${data.penalty.actual_fare.toLocaleString()}</div>
                                    </div>`;
                                } else if (data.penalty) {
                                    penaltyHtml = `<br><br><div class="alert alert-success mt-2"><i class="fas fa-check-circle me-2"></i>${data.penalty.message}</div>`;
                                }

                                Swal.fire({
                                    title: 'Delivered!',
                                    html: `✅ <strong>${data.message}</strong><br><br>Booking completed!${penaltyHtml}`,
                                    icon: 'success'
                                }).then(() => removeBookingCard(bookingId));
                            } else {
                                Swal.fire({
                                    title: 'Success!',
                                    html: `✅ Status updated to <strong>${statusText}</strong>`,
                                    icon: 'success',
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => updateButtonStates(bookingId, status));
                            }
                        } else {
                            Swal.fire({ title: 'Error!', text: data.message, icon: 'error' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ title: 'Error!', text: 'An error occurred. Please try again.', icon: 'error' });
                    });
                }
            });
        }

        function removeBookingCard(bookingId) {
            clearLocationInterval(bookingId);
            const cardContainer = document.querySelector(`.booking-item[data-id="${bookingId}"]`);
            if (cardContainer) {
                cardContainer.style.transition = 'all 0.3s ease';
                cardContainer.style.opacity = '0';
                cardContainer.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    cardContainer.remove();
                    updateStatsAfterDelivery();
                    if (document.querySelectorAll('.booking-item').length === 0) {
                        document.getElementById('bookingsGrid').innerHTML = `
                            <div class="col-12"><div class="card text-center py-5"><div class="card-body">
                                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Active Bookings</h4>
                                <p class="text-muted">All bookings have been delivered.</p>
                                <a href="{{ route('booking.requests') }}" class="btn btn-primary"><i class="fas fa-bell me-2"></i> View Pending Requests</a>
                            </div></div></div>`;
                    }
                }, 300);
            }
        }

        function updateStatsAfterDelivery() {
            const totalEl = document.getElementById('totalBookings');
            const pendingEl = document.getElementById('pendingDeliveries');
            const completedEl = document.getElementById('completedBookings');
            if (totalEl) totalEl.textContent = Math.max(0, (parseInt(totalEl.textContent) || 0) - 1);
            if (pendingEl) pendingEl.textContent = Math.max(0, (parseInt(pendingEl.textContent) || 0) - 1);
            if (completedEl) completedEl.textContent = (parseInt(completedEl.textContent) || 0) + 1;
        }

        function updateButtonStates(bookingId, newStatus) {
            const bookingCard = document.getElementById(`booking-${bookingId}`);
            if (!bookingCard) return;

            const badge = bookingCard.querySelector('.booking-badge');
            const statusBadge = bookingCard.querySelector('.status-badge');
            const dispatchBtn = bookingCard.querySelector('.btn-delivery.dispatched');
            const transitBtn = bookingCard.querySelector('.btn-delivery.transit');
            const deliverBtn = bookingCard.querySelector('.btn-delivery.delivered');

            bookingCard.classList.remove('order_confirmed', 'vehicle_dispatched', 'in_transit', 'delivered');

            if (newStatus === 'vehicle_dispatched') {
                bookingCard.classList.add('vehicle_dispatched');
                badge.className = 'booking-badge vehicle_dispatched';
                badge.textContent = 'Vehicle Dispatched';
                statusBadge.className = 'status-badge vehicle_dispatched';
                statusBadge.textContent = 'Vehicle Dispatched';
                dispatchBtn.disabled = true;
                transitBtn.disabled = false;
                deliverBtn.disabled = true;
            } else if (newStatus === 'in_transit') {
                bookingCard.classList.add('in_transit');
                badge.className = 'booking-badge in_transit';
                badge.textContent = 'In Transit';
                statusBadge.className = 'status-badge in_transit';
                statusBadge.textContent = 'In Transit';
                dispatchBtn.disabled = true;
                transitBtn.disabled = true;
                deliverBtn.disabled = false;
            }
        }

        // ===================================================
        //              BOOKING DETAILS MODAL
        // ===================================================

        function viewBookingDetails(bookingId) {
            const booking = bookingData[bookingId];
            if (!booking) { Swal.fire('Error', 'Booking details not found', 'error'); return; }

            const penaltyInfo = (booking.penaltyAmount && parseFloat(booking.penaltyAmount) > 0) ?
                `<div class="alert alert-warning mt-3"><i class="fas fa-exclamation-triangle me-2"></i><strong>Penalty Applied:</strong> Rs ${booking.penaltyAmount}<br><strong>Final Fare:</strong> Rs ${booking.actualFare}</div>` : '';

            const timelineHtml = `
                <div class="timeline-item">
                    <div class="timeline-icon ${booking.acceptedAt !== 'N/A' ? 'completed' : 'pending'}"><i class="fas fa-check"></i></div>
                    <div class="timeline-content"><div class="timeline-title">Order Confirmed</div><div class="timeline-time">${booking.acceptedAt}</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon ${booking.dispatchedAt !== 'N/A' ? 'completed' : 'pending'}"><i class="fas fa-truck"></i></div>
                    <div class="timeline-content"><div class="timeline-title">Vehicle Dispatched</div><div class="timeline-time">${booking.dispatchedAt}</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon ${booking.inTransitAt !== 'N/A' ? 'completed' : 'pending'}"><i class="fas fa-route"></i></div>
                    <div class="timeline-content"><div class="timeline-title">In Transit</div><div class="timeline-time">${booking.inTransitAt}</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon ${booking.deliveredAt !== 'N/A' ? 'completed' : 'pending'}"><i class="fas fa-check-circle"></i></div>
                    <div class="timeline-content"><div class="timeline-title">Delivered</div><div class="timeline-time">${booking.deliveredAt}</div></div>
                </div>
            `;

            document.getElementById('detailModalBody').innerHTML = `
                <div class="detail-section">
                    <h4 class="detail-section-title">Booking Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large"><span class="detail-label-large">Booking ID</span><span class="detail-value-large">TL-${booking.id}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Status</span><span class="detail-value-large"><span class="badge ${getStatusBadgeClass(booking.deliveryStatus)}">${booking.deliveryStatusText}</span></span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Booking Date</span><span class="detail-value-large">${booking.bookingDate}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Pickup Time</span><span class="detail-value-large">${booking.pickupTime}</span></div>
                    </div>
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Delivery Timeline</h4>
                    <div class="delivery-timeline">${timelineHtml}</div>
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Trip Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large"><span class="detail-label-large">Pickup</span><span class="detail-value-large">${escapeHtml(booking.pickupLocation)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Drop</span><span class="detail-value-large">${escapeHtml(booking.dropoffLocation)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Distance</span><span class="detail-value-large">${booking.distance}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Fare</span><span class="detail-value-large text-success fw-bold">Rs ${booking.fare}</span></div>
                    </div>
                    ${penaltyInfo}
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Customer Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large"><span class="detail-label-large">Name</span><span class="detail-value-large">${escapeHtml(booking.customerName)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Phone</span><span class="detail-value-large">${escapeHtml(booking.customerPhone)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Email</span><span class="detail-value-large">${escapeHtml(booking.customerEmail)}</span></div>
                    </div>
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Vehicle & Goods</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large"><span class="detail-label-large">Vehicle</span><span class="detail-value-large">${escapeHtml(booking.vehicleType)} - ${escapeHtml(booking.vehicleNumber)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Goods Type</span><span class="detail-value-large">${escapeHtml(booking.goodsType)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Weight</span><span class="detail-value-large">${booking.goodsWeight} kg</span></div>
                    </div>
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Payment</h4>
                    <div class="detail-grid">
                        <div class="detail-item-large"><span class="detail-label-large">Method</span><span class="detail-value-large">${escapeHtml(booking.paymentMethod)}</span></div>
                        <div class="detail-item-large"><span class="detail-label-large">Status</span><span class="detail-value-large">${escapeHtml(booking.paymentStatus)}</span></div>
                    </div>
                </div>
                <div class="detail-section">
                    <h4 class="detail-section-title">Special Instructions</h4>
                    <div class="detail-item-large"><span class="detail-value-large">${escapeHtml(booking.specialInstructions)}</span></div>
                </div>
            `;

            document.getElementById('detailModal').classList.add('active');
        }

        function contactCustomer(bookingId) {
            const booking = bookingData[bookingId];
            if (booking) {
                Swal.fire({
                    title: 'Contact Customer',
                    html: `<div class="text-start"><p><strong>Name:</strong> ${escapeHtml(booking.customerName)}</p><p><strong>Phone:</strong> ${escapeHtml(booking.customerPhone)}</p><p><strong>Email:</strong> ${escapeHtml(booking.customerEmail)}</p></div>`,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            }
        }

        // ===================================================
        //              FILTER FUNCTIONS
        // ===================================================

        function applyFilters() {
            const deliveryStatus = document.getElementById('deliveryStatusFilter').value;
            const vehicleType = document.getElementById('vehicleTypeFilter').value.toLowerCase();
            const sortBy = document.getElementById('sortBy').value;
            let items = Array.from(document.querySelectorAll('.booking-item'));
            if (deliveryStatus) items = items.filter(item => item.dataset.status === deliveryStatus);
            if (vehicleType) items = items.filter(item => item.dataset.type === vehicleType);
            if (sortBy === 'fare') items.sort((a, b) => parseFloat(b.dataset.fare) - parseFloat(a.dataset.fare));
            else if (sortBy === 'newest') items.sort((a, b) => parseInt(b.dataset.id) - parseInt(a.dataset.id));
            else if (sortBy === 'oldest') items.sort((a, b) => parseInt(a.dataset.id) - parseInt(b.dataset.id));
            document.querySelectorAll('.booking-item').forEach(item => item.style.display = 'none');
            items.forEach(item => item.style.display = '');
            if (items.length === 0) Swal.fire({ title: 'No Results', text: 'No bookings match your filters', icon: 'info', timer: 1500, showConfirmButton: false });
        }

        function resetFilters() {
            document.getElementById('deliveryStatusFilter').value = '';
            document.getElementById('vehicleTypeFilter').value = '';
            document.getElementById('sortBy').value = 'newest';
            document.querySelectorAll('.booking-item').forEach(item => item.style.display = '');
            Swal.fire({ title: 'Filters Reset', text: 'All filters cleared', icon: 'success', timer: 1500, showConfirmButton: false });
        }

        // ===================================================
        //              UTILITY FUNCTIONS
        // ===================================================

        function getCurrentDateTime() {
            const now = new Date();
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const day = now.getDate(), month = months[now.getMonth()], year = now.getFullYear();
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${day} ${month} ${year} ${hours}:${minutes} ${ampm}`;
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'order_confirmed': 'bg-info',
                'vehicle_dispatched': 'bg-primary',
                'in_transit': 'bg-warning',
                'delivered': 'bg-success'
            };
            return classes[status] || 'bg-secondary';
        }

        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[m]);
        }
    </script>
</body>
</html>