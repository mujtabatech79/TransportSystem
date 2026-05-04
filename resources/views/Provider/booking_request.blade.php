{{-- resources/views/Provider/booking_requests.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Booking Requests</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* [Keep all existing CSS styles from your blade file - they remain the same] */
        /* I'm keeping the styles concise but you should keep all your existing styles */
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .loading-spinner-large {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
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
        
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
            background: transparent;
        }
        
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
        
        .content-area {
            padding: 30px;
        }
        
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
        
        .stat-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
        }
        
        .stat-card i {
            font-size: 2.8rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .stat-card .count {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0;
            background: linear-gradient(135deg, var(--dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card .label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .bg-primary-light { background: linear-gradient(135deg, rgba(52, 152, 219, 0.08) 0%, rgba(52, 152, 219, 0.02) 100%); }
        .bg-success-light { background: linear-gradient(135deg, rgba(39, 174, 96, 0.08) 0%, rgba(39, 174, 96, 0.02) 100%); }
        .bg-warning-light { background: linear-gradient(135deg, rgba(243, 156, 18, 0.08) 0%, rgba(243, 156, 18, 0.02) 100%); }
        .bg-danger-light { background: linear-gradient(135deg, rgba(231, 76, 60, 0.08) 0%, rgba(231, 76, 60, 0.02) 100%); }
        .bg-info-light { background: linear-gradient(135deg, rgba(23, 162, 184, 0.08) 0%, rgba(23, 162, 184, 0.02) 100%); }
        
        .request-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background: white;
            margin-bottom: 20px;
            transition: all 0.3s;
            border-left: 4px solid var(--warning);
        }
        
        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .request-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .request-id {
            font-weight: 700;
            color: var(--primary);
        }
        
        .request-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .request-body {
            padding: 20px;
        }
        
        .request-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .customer-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .customer-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        .customer-contact {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .request-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            gap: 10px;
        }
        
        .btn-accept {
            flex: 1;
            background: linear-gradient(135deg, var(--success), #219955);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-reject {
            flex: 1;
            background: linear-gradient(135deg, var(--danger), #c0392b);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-view {
            flex: 1;
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .footer {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            margin-left: 280px;
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
            
            .sidebar .nav-link {
                padding: 15px;
                text-align: center;
                margin: 5px 10px;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content, .footer {
                margin-left: 80px;
            }
            
            .content-area {
                padding: 20px 15px;
            }
            
            .request-info-grid {
                grid-template-columns: 1fr;
            }
            
            .request-footer {
                flex-direction: column;
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
            <a class="nav-link active" href="{{route('booking.requests')}}"><i class="fas fa-bell"></i> <span>Booking Requests</span></a>
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
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Search booking requests...">
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
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Booking Requests</h4>
                    <p class="text-muted mb-0">Review and manage pending booking requests for your vehicles.</p>
                </div>
                <div>
                    <span class="badge bg-warning fs-6"><i class="fas fa-clock me-1"></i> <span id="pendingCount">{{ $bookingRequests->count() }}</span> Pending</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <div class="count" id="statPending">{{ $bookingRequests->count() }}</div>
                            <div class="label">Pending Requests</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-truck"></i>
                            <div class="count">{{ $bookingRequests->groupBy('vehicle_id')->count() }}</div>
                            <div class="label">Vehicles with Requests</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-info-light">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <div class="count">{{ $bookingRequests->groupBy('customer_id')->count() }}</div>
                            <div class="label">Customers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Requests List -->
            <div class="row mt-4">
                <div class="col-12" id="requestsContainer">
                    @if($bookingRequests->isEmpty())
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Pending Requests</h4>
                                <p class="text-muted">You don't have any pending booking requests at the moment.</p>
                                <a href="{{ route('my.vehicle') }}" class="btn btn-primary">
                                    <i class="fas fa-truck me-2"></i> Manage Your Vehicles
                                </a>
                            </div>
                        </div>
                    @else
                        @foreach($bookingRequests as $request)
                        <div class="request-card" id="request-{{ $request->id }}">
                            <div class="request-header">
                                <div>
                                    <span class="request-id">Booking #TL-{{ $request->id }}</span>
                                </div>
                                <div class="request-date">
                                    <i class="far fa-clock me-1"></i> {{ $request->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                            <div class="request-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="customer-info">
                                            <div class="customer-name">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                {{ $request->customer->name ?? 'N/A' }}
                                            </div>
                                            <div class="customer-contact">
                                                <i class="fas fa-phone me-2"></i> {{ $request->customer->mobile ?? $request->customer->phone ?? 'N/A' }} |
                                                <i class="fas fa-envelope ms-2 me-1"></i> {{ $request->customer->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="customer-info">
                                            <div class="customer-name">
                                                <i class="fas fa-truck me-2 text-success"></i>
                                                {{ $request->vehicle->vehicle_type ?? 'N/A' }} - {{ $request->vehicle->vehicle_number ?? 'N/A' }}
                                            </div>
                                            <div class="customer-contact">
                                                <i class="fas fa-weight-hanging me-2"></i> Capacity: {{ $request->vehicle->weight_capacity ?? 'N/A' }} kg |
                                                <i class="fas fa-box ms-2 me-1"></i> {{ $request->vehicle->can_carry ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="request-info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Pickup Location</span>
                                        <span class="info-value"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $request->pickup_location }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Drop Location</span>
                                        <span class="info-value"><i class="fas fa-flag-checkered text-success me-1"></i> {{ $request->dropoff_location }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Booking Date</span>
                                        <span class="info-value"><i class="far fa-calendar me-1"></i> {{ $request->booking_date }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Pickup Time</span>
                                        <span class="info-value"><i class="far fa-clock me-1"></i> {{ $request->pickup_time ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Goods Type</span>
                                        <span class="info-value">{{ $request->goods_type ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Weight</span>
                                        <span class="info-value">{{ $request->goods_weight ?? 'N/A' }} kg</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Distance</span>
                                        <span class="info-value">{{ $request->estimated_distance ?? 'N/A' }} km</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Estimated Fare</span>
                                        <span class="info-value fw-bold text-success">Rs {{ number_format($request->estimated_fare ?? 0) }}</span>
                                    </div>
                                </div>
                                
                                @if($request->special_instructions)
                                <div class="alert alert-info py-2 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Special Instructions:</strong> {{ $request->special_instructions }}
                                </div>
                                @endif
                            </div>
                            <div class="request-footer">
                                <button class="btn-accept" onclick="acceptRequest({{ $request->id }})">
                                    <i class="fas fa-check-circle me-2"></i> Accept
                                </button>
                                <button class="btn-reject" onclick="rejectRequest({{ $request->id }})">
                                    <i class="fas fa-times-circle me-2"></i> Reject
                                </button>
                                <button class="btn-view" onclick="viewRequestDetails({{ $request->id }})">
                                    <i class="fas fa-eye me-2"></i> View Details
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2024 TruckLink: Verified Goods.</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Service Provider Panel v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>System Online</span></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Fast Accept Request (under 2 seconds)
        async function acceptRequest(bookingId) {
            const result = await Swal.fire({
                title: 'Accept Booking?',
                html: `
                    <p>Are you sure you want to accept this booking request?</p>
                    <p class="text-muted">The customer will receive a confirmation email.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Accept',
                cancelButtonText: 'Cancel'
            });
            
            if (result.isConfirmed) {
                // Show quick loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Accepting booking request...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                try {
                    const response = await fetch(`/accept-booking/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Remove the request card immediately
                        const card = document.getElementById(`request-${bookingId}`);
                        if (card) {
                            card.remove();
                        }
                        
                        // Update counts
                        updatePendingCount();
                        
                        Swal.fire({
                            title: 'Accepted!',
                            html: `✅ <strong>Booking accepted successfully!</strong><br><br>Confirmation emails will be sent to both customer and you shortly.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Check if no more requests
                        if (document.querySelectorAll('.request-card').length === 0) {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to accept booking');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        }
        
        // Fast Reject Request with reason
        async function rejectRequest(bookingId) {
            const { value: formValues } = await Swal.fire({
                title: 'Reject Booking Request?',
                html: `
                    <p>Are you sure you want to reject this booking request?</p>
                    <div class="mb-3 text-start">
                        <label for="rejectionReason" class="form-label fw-bold">Reason for rejection:</label>
                        <textarea id="rejectionReason" class="form-control" rows="4" placeholder="Enter reason..."></textarea>
                        <small class="text-muted mt-1">This reason will be emailed to the customer.</small>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const reason = document.getElementById('rejectionReason').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a reason for rejection');
                        return false;
                    }
                    return { reason: reason };
                }
            });
            
            if (formValues) {
                // Show quick loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Rejecting booking request...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                try {
                    const response = await fetch(`/reject-booking/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ rejection_reason: formValues.reason })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Remove the request card immediately
                        const card = document.getElementById(`request-${bookingId}`);
                        if (card) {
                            card.remove();
                        }
                        
                        // Update counts
                        updatePendingCount();
                        
                        Swal.fire({
                            title: 'Rejected!',
                            html: `⚠️ <strong>Booking rejected successfully!</strong><br><br>A notification email will be sent to the customer with the reason.`,
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Check if no more requests
                        if (document.querySelectorAll('.request-card').length === 0) {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to reject booking');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }
        }
        
        // Update pending count in UI
        function updatePendingCount() {
            const remainingCards = document.querySelectorAll('.request-card').length;
            document.getElementById('pendingCount').textContent = remainingCards;
            document.getElementById('statPending').textContent = remainingCards;
            
            // If no more requests, show empty state
            if (remainingCards === 0) {
                const container = document.getElementById('requestsContainer');
                if (container) {
                    container.innerHTML = `
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Pending Requests</h4>
                                <p class="text-muted">You don't have any pending booking requests at the moment.</p>
                                <a href="{{ route('my.vehicle') }}" class="btn btn-primary">
                                    <i class="fas fa-truck me-2"></i> Manage Your Vehicles
                                </a>
                            </div>
                        </div>
                    `;
                }
            }
        }
        
        // View Request Details
        async function viewRequestDetails(bookingId) {
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching booking details',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await fetch(`/booking-details/${bookingId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const booking = data.booking;
                    
                    Swal.fire({
                        title: `Booking #TL-${booking.id}`,
                        html: `
                            <div class="text-start">
                                <h6 class="text-primary mb-3">👤 Customer Information</h6>
                                <p><strong>Name:</strong> ${escapeHtml(booking.customer?.name || 'N/A')}</p>
                                <p><strong>Phone:</strong> ${escapeHtml(booking.customer?.phone || booking.customer?.mobile || 'N/A')}</p>
                                <p><strong>Email:</strong> ${escapeHtml(booking.customer?.email || 'N/A')}</p>
                                
                                <h6 class="text-primary mt-3 mb-3">📍 Trip Information</h6>
                                <p><strong>Pickup:</strong> ${escapeHtml(booking.pickup_location)}</p>
                                <p><strong>Dropoff:</strong> ${escapeHtml(booking.dropoff_location)}</p>
                                <p><strong>Date:</strong> ${escapeHtml(booking.booking_date)}</p>
                                <p><strong>Time:</strong> ${escapeHtml(booking.pickup_time || 'N/A')}</p>
                                
                                <h6 class="text-primary mt-3 mb-3">📦 Goods Information</h6>
                                <p><strong>Type:</strong> ${escapeHtml(booking.goods_type || 'N/A')}</p>
                                <p><strong>Weight:</strong> ${booking.goods_weight || 'N/A'} kg</p>
                                
                                <h6 class="text-primary mt-3 mb-3">💰 Payment Information</h6>
                                <p><strong>Estimated Fare:</strong> Rs ${numberFormat(booking.estimated_fare || 0)}</p>
                                <p><strong>Payment Method:</strong> ${escapeHtml(booking.payment_method || 'N/A')}</p>
                                
                                ${booking.special_instructions ? `
                                <h6 class="text-primary mt-3 mb-3">📝 Special Instructions</h6>
                                <p>${escapeHtml(booking.special_instructions)}</p>
                                ` : ''}
                            </div>
                        `,
                        width: '600px',
                        showCloseButton: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    throw new Error(data.message || 'Failed to fetch details');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            }
        }
        
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.request-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // Helper functions
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function numberFormat(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
</body>
</html>