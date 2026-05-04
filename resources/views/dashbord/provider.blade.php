<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Service Provider Dashboard</title>
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
            --info: #17a2b8;
        }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); overflow-x: hidden; }
        .sidebar { background: linear-gradient(180deg, var(--primary) 0%, #1a2530 100%); color: white; height: 100vh; position: fixed; top: 0; left: 0; width: 280px; transition: all 0.3s; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,0.1); border-right: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; background: rgba(255,255,255,0.05); }
        .sidebar .logo h3 { margin: 0; font-weight: 700; font-size: 1.5rem; }
        .sidebar .logo span { color: var(--secondary); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; margin: 8px 15px; border-radius: 12px; transition: all 0.3s ease; font-weight: 500; position: relative; overflow: hidden; }
        .sidebar .nav-link:before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: var(--secondary); transform: translateX(-10px); transition: transform 0.3s ease; }
        .sidebar .nav-link:hover { background: rgba(52, 152, 219, 0.15); color: white; transform: translateX(5px); }
        .sidebar .nav-link:hover:before, .sidebar .nav-link.active:before { transform: translateX(0); }
        .sidebar .nav-link.active { background: rgba(52, 152, 219, 0.2); color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
        .sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }
        .main-content { margin-left: 280px; transition: all 0.3s; min-height: 100vh; }
        .topbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 30px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 999; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .topbar .search-box { position: relative; max-width: 400px; }
        .topbar .search-box input { border-radius: 25px; padding-left: 45px; border: 1px solid rgba(0,0,0,0.1); }
        .topbar .search-box i { position: absolute; left: 20px; top: 12px; color: #6c757d; }
        .topbar .user-info img { width: 45px; height: 45px; border-radius: 50%; margin-right: 12px; border: 3px solid var(--secondary); }
        .content-area { padding: 30px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-bottom: 25px; transition: all 0.3s ease; background: white; overflow: hidden; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        .card-header { background: linear-gradient(135deg, white 0%, #f8f9fa 100%); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 25px; font-weight: 600; font-size: 1.1rem; }
        .stat-card { text-align: center; padding: 30px 20px; position: relative; overflow: hidden; }
        .stat-card:before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--secondary), var(--primary)); }
        .stat-card i { font-size: 2.8rem; margin-bottom: 20px; opacity: 0.9; }
        .stat-card .count { font-size: 2.5rem; font-weight: 700; margin: 15px 0; background: linear-gradient(135deg, var(--dark), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-card .label { color: #6c757d; font-size: 0.95rem; font-weight: 500; }
        .bg-primary-light { background: linear-gradient(135deg, rgba(52, 152, 219, 0.08) 0%, rgba(52, 152, 219, 0.02) 100%); }
        .bg-success-light { background: linear-gradient(135deg, rgba(39, 174, 96, 0.08) 0%, rgba(39, 174, 96, 0.02) 100%); }
        .bg-warning-light { background: linear-gradient(135deg, rgba(243, 156, 18, 0.08) 0%, rgba(243, 156, 18, 0.02) 100%); }
        .bg-danger-light  { background: linear-gradient(135deg, rgba(231, 76, 60, 0.08) 0%, rgba(231, 76, 60, 0.02) 100%); }
        .booking-card { padding: 20px; border-left: 4px solid var(--secondary); margin-bottom: 15px; background-color: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
        .booking-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .booking-card.pending, .booking-card.booked { border-left-color: var(--warning); }
        .booking-card.accepted, .booking-card.completed { border-left-color: var(--success); }
        .booking-card.cancelled { border-left-color: #e74c3c; }
        .badge { padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); border-color: var(--secondary); }
        .btn { border-radius: 10px; font-weight: 500; padding: 10px 20px; transition: all 0.3s ease; border: none; }
        .btn-primary { background: linear-gradient(135deg, var(--secondary), #2980b9); box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4); }
        .footer { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); padding: 20px 30px; border-top: 1px solid rgba(0,0,0,0.05); margin-left: 280px; }
        .chart-container { position: relative; height: 300px; width: 100%; }

        /* Chatbot */
        .typing-indicator { display: flex; gap: 4px; padding: 4px 0; }
        .typing-indicator span { width: 8px; height: 8px; background-color: #6c757d; border-radius: 50%; animation: typing-bounce 1.4s infinite ease-in-out; }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing-bounce { 0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; } 40% { transform: scale(1); opacity: 1; } }
        .suggestion-chip { font-size: 0.75rem; padding: 4px 10px; transition: all 0.2s ease; }
        .suggestion-chip:hover { background-color: #3498db; color: white; border-color: #3498db; }
        .bot-response { line-height: 1.5; }
        .bot-response strong { color: #2c3e50; }
        .btn-blue-border { background: transparent !important; border: 2px solid #e9ecef !important; color: #6c757d !important; transition: all 0.3s ease; overflow: hidden; }
        .btn-blue-border:hover { background: #3498db !important; color: white !important; border-color: #3498db !important; transform: translateY(-2px); }

        /* SmartCard Upload */
        .smartcard-drop-zone { border: 2px dashed #3498db; border-radius: 10px; padding: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: rgba(52, 152, 219, 0.03); }
        .smartcard-drop-zone:hover, .smartcard-drop-zone.drag-over { background: rgba(52, 152, 219, 0.08); border-color: #2980b9; transform: scale(1.01); }
        #smartcardToggleBtn.active { background: #3498db !important; color: white !important; border-color: #3498db !important; }

        /* SmartCard extracted data preview */
        .extracted-field { background: #f8f9ff; border: 1px solid #dee2ff; border-radius: 8px; padding: 8px 12px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
        .extracted-field .field-label { font-size: 0.72rem; color: #6c757d; min-width: 110px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .extracted-field .field-value { font-size: 0.85rem; font-weight: 600; color: #2c3e50; }
        .autofill-banner { background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; border-radius: 10px; padding: 10px 14px; margin-top: 8px; font-size: 0.82rem; }

        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link { padding: 15px; text-align: center; margin: 5px 10px; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }
            .main-content, .footer { margin-left: 80px; }
            .content-area { padding: 20px 15px; }
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
            <a class="nav-link active" href="{{route('provider.login')}}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
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
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Search bookings, customers...">
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User">
                        <span class="ms-2 d-none d-sm-inline fw-semibold">Bilal S.</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{route('user.logout')}}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Service Provider Dashboard</h4>
                    <p class="text-muted mb-0">Manage your vehicles, bookings, and earnings in one place.</p>
                </div>
                <div><span class="badge bg-success fs-6">Verified Provider</span></div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="count">{{ $totalBookings ?? 0 }}</div>
                            <div class="label">Total Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle"></i>
                            <div class="count">{{ $completedBookings ?? '18' }}</div>
                            <div class="label">Completed</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning-light">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <div class="count">{{ $pendingRequest ?? '3' }}</div>
                            <div class="label">Pending</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary-light">
                        <div class="card-body">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="count">{{ $rejectedRequest ?? 0 }}</div>
                            <div class="label">Active Bookings</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Bookings</h5>
                            <a href="{{ route('see.trip') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            @if(isset($bookings) && $bookings->count() > 0)
                                @foreach($bookings as $booking)
                                    <div class="booking-card {{ $booking->status }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6>{{ $booking->pickup_location }} to {{ $booking->dropoff_location }}</h6>
                                                <p class="mb-1">
                                                    Vehicle: {{ $booking->vehicle->vehicle_type ?? 'N/A' }} •
                                                    Customer: {{ $booking->customer->name ?? 'N/A' }}
                                                </p>
                                                <small class="text-muted">
                                                    Booking Date: {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }} •
                                                    Weight: {{ $booking->goods_weight ?? 'N/A' }} kg
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge
                                                    @if($booking->status == 'booked') bg-warning
                                                    @elseif($booking->status == 'completed') bg-success
                                                    @elseif($booking->status == 'cancelled') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No recent bookings found.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header"><h5 class="mb-0">Earnings Overview</h5></div>
                        <div class="card-body">
                            <div class="chart-container"><canvas id="earningsChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- AI Provider Assistant Card -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-robot text-primary me-2"></i>AI Provider Assistant
                            </h5>
                            <button class="btn btn-sm btn-blue-border" id="clearChatBtn">
                                <i class="fas fa-trash-alt me-1"></i> Clear
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <!-- Chat Messages -->
                            <div id="chatMessages" class="p-3" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
                                <!-- Welcome Message -->
                                <div class="d-flex mb-3" id="welcomeMsg">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                                        <i class="fas fa-robot text-white" style="font-size: 14px;"></i>
                                    </div>
                                    <div class="ms-2">
                                        <div class="bg-white rounded-3 p-2 shadow-sm" style="max-width: 85%;">
                                            <small class="text-muted">Provider Assistant</small>
                                            <p class="mb-0 small">
                                                👋 Hello! I'm your <strong>TruckLink Provider AI</strong> assistant.
                                                I can help with your vehicles, bookings, earnings, and complaints.
                                                What would you like to know?
                                            </p>
                                        </div>
                                        <small class="text-muted ms-2" id="welcomeTime"></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Suggestions -->
                            <div class="px-3 pb-2 border-top" id="quickSuggestions">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Pending booking requests">
                                        <i class="fas fa-bell me-1"></i> Pending Requests
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="How to accept a booking?">
                                        <i class="fas fa-check-circle me-1"></i> Accept Booking
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="My total earnings">
                                        <i class="fas fa-money-bill-wave me-1"></i> My Earnings
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="My vehicles status">
                                        <i class="fas fa-truck me-1"></i> My Vehicles
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary suggestion-btn" data-question="Update delivery status">
                                        <i class="fas fa-shipping-fast me-1"></i> Delivery Status
                                    </button>
                                </div>
                            </div>

                            <!-- Input Area -->
                            <div class="p-3 border-top bg-white">
                                <!-- SmartCard Upload Section -->
                                <div id="smartcardUploadArea" class="mb-2" style="display:none;">
                                    <div class="smartcard-drop-zone" id="smartcardDropZone"
                                         onclick="document.getElementById('smartcardFileInput').click()"
                                         ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
                                        <i class="fas fa-id-card text-primary mb-2" style="font-size:1.8rem;"></i>
                                        <p class="mb-1 fw-semibold text-primary" style="font-size:0.85rem;">Upload Smart Card / Registration Book</p>
                                        <p class="text-muted mb-0" style="font-size:0.75rem;">Click or drag & drop — JPG/PNG max 8MB</p>
                                        <input type="file" id="smartcardFileInput" accept="image/*" style="display:none;" onchange="handleSmartcardFile(this.files[0])">
                                    </div>
                                    <div id="smartcardPreview" class="mt-2" style="display:none;">
                                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                            <img id="smartcardThumb" style="width:50px;height:38px;object-fit:cover;border-radius:6px;">
                                            <div class="flex-grow-1">
                                                <small class="fw-semibold d-block" id="smartcardFileName"></small>
                                                <small class="text-muted">AI will extract vehicle details...</small>
                                            </div>
                                            <button class="btn btn-sm btn-success" onclick="uploadSmartcard()" id="detectBtn">
                                                <i class="fas fa-magic me-1"></i> Extract
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="cancelSmartcard()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" id="smartcardToggleBtn"
                                            title="Upload Smart Card for Auto-Fill"
                                            onclick="toggleSmartcardUpload()"
                                            style="border-radius:10px 0 0 10px; border-right:none;">
                                        <i class="fas fa-id-card"></i>
                                    </button>
                                    <input type="text" id="chatInput" class="form-control"
                                           placeholder="Type a message or upload smart card..."
                                           autocomplete="off" style="border-radius:0;">
                                    <button class="btn btn-primary" id="sendChatBtn" style="border-radius:0 10px 10px 0;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1 text-primary"></i>
                                        <strong>Tip:</strong> Upload your smart card to auto-fill the vehicle registration form!
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0"><strong>© 2023 TruckLink: Verified Goods.</strong> All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Service Provider Panel v2.0 • <span class="text-success"><i class="fas fa-circle me-1"></i>System Online</span></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Earnings chart
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Monthly Earnings (Rs)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2, fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
            }
        });
    </script>

    {{-- AI Provider Assistant JavaScript --}}
    <script>
    (function () {
        'use strict';

        const chatMessages   = document.getElementById('chatMessages');
        const chatInput      = document.getElementById('chatInput');
        const sendBtn        = document.getElementById('sendChatBtn');
        const clearChatBtn   = document.getElementById('clearChatBtn');
        const suggestionBtns = document.querySelectorAll('.suggestion-btn');

        let isTyping = false;

        function getCsrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function init() {
            const wtEl = document.getElementById('welcomeTime');
            if (wtEl) wtEl.textContent = getCurrentTime();

            loadHistoryFromServer();

            if (sendBtn)      sendBtn.addEventListener('click', sendMessage);
            if (clearChatBtn) clearChatBtn.addEventListener('click', clearChat);
            if (chatInput) {
                chatInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
                });
            }

            suggestionBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const q = this.getAttribute('data-question');
                    if (q) { chatInput.value = q; sendMessage(); }
                });
            });
        }

        async function loadHistoryFromServer() {
            try {
                const res  = await fetch('{{ route("provider.chatbot.history") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.success && data.history && data.history.length > 0) {
                    const welcomeEl = document.getElementById('welcomeMsg');
                    chatMessages.innerHTML = '';
                    if (welcomeEl) chatMessages.appendChild(welcomeEl);

                    const recent = data.history.slice(-20);
                    recent.forEach(h => {
                        if (h.sender === 'user') addUserMessage(h.message, false);
                        else                      addBotMessage(h.message, [], false);
                    });

                    scrollToBottom();
                }
            } catch (err) {
                console.warn('History load error:', err);
            }
        }

        async function sendMessage() {
            if (isTyping) return;
            const message = chatInput.value.trim();
            if (!message) return;

            chatInput.value = '';
            addUserMessage(message);
            showTypingIndicator();
            isTyping = true;

            try {
                const res  = await fetch('{{ route("provider.chatbot.message") }}', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body:    JSON.stringify({ message })
                });
                const data = await res.json();
                removeTypingIndicator();

                if (data.success) {
                    addBotMessage(data.response, data.suggestions || []);
                } else {
                    addBotMessage(data.response || 'A technical issue occurred. Please try again. 🙏', []);
                }
            } catch (err) {
                console.error('Chat send error:', err);
                removeTypingIndicator();
                addBotMessage('Connection issue. Please check your internet and try again.', []);
            } finally {
                isTyping = false;
                chatInput.focus();
            }
        }

        function addUserMessage(message, scroll = true) {
            const div = document.createElement('div');
            div.className = 'd-flex mb-3 justify-content-end';
            div.innerHTML = `
                <div class="me-2 text-end">
                    <div class="bg-primary text-white rounded-3 p-2 shadow-sm d-inline-block" style="max-width:85%;">
                        <small class="text-white-50 d-block">You</small>
                        <p class="mb-0 small">${escapeHtml(message)}</p>
                    </div>
                    <div><small class="text-muted">${getCurrentTime()}</small></div>
                </div>
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                    <i class="fas fa-user text-white" style="font-size:14px;"></i>
                </div>`;
            chatMessages.appendChild(div);
            if (scroll) scrollToBottom();
        }

        function addBotMessage(response, suggestions = [], scroll = true) {
            const div = document.createElement('div');
            div.className = 'd-flex mb-3';
            div.innerHTML = `
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                    <i class="fas fa-robot text-white" style="font-size:14px;"></i>
                </div>
                <div class="ms-2" style="max-width:90%;">
                    <div class="bg-white rounded-3 p-2 shadow-sm">
                        <small class="text-muted">Provider Assistant</small>
                        <div class="mb-0 small bot-response">${formatResponse(response)}</div>
                    </div>
                    <div><small class="text-muted ms-1">${getCurrentTime()}</small></div>
                    ${renderChips(suggestions)}
                </div>`;
            chatMessages.appendChild(div);
            div.querySelectorAll('.suggestion-chip').forEach(chip => {
                chip.addEventListener('click', function () {
                    const q = this.getAttribute('data-question');
                    if (q) { chatInput.value = q; sendMessage(); }
                });
            });
            if (scroll) scrollToBottom();
        }

        function formatResponse(text) {
            let out = escapeHtml(text);
            out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            out = out.replace(/\n/g, '<br>');
            return out;
        }

        function renderChips(suggestions) {
            if (!suggestions || suggestions.length === 0) return '';
            const chips = suggestions.map(s =>
                `<button class="btn btn-sm btn-outline-secondary suggestion-chip me-1 mt-1"
                         data-question="${escapeHtml(s)}" style="font-size:0.72rem;">
                    <i class="fas fa-comment me-1"></i>${escapeHtml(s)}
                 </button>`
            ).join('');
            return `<div class="mt-1">${chips}</div>`;
        }

        function showTypingIndicator() {
            const div = document.createElement('div');
            div.id = 'typingIndicator';
            div.className = 'd-flex mb-3';
            div.innerHTML = `
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                    <i class="fas fa-robot text-white" style="font-size:14px;"></i>
                </div>
                <div class="ms-2">
                    <div class="bg-white rounded-3 p-2 shadow-sm">
                        <small class="text-muted">Thinking...</small>
                        <div class="typing-indicator mt-1"><span></span><span></span><span></span></div>
                    </div>
                </div>`;
            chatMessages.appendChild(div);
            scrollToBottom();
        }

        function removeTypingIndicator() {
            document.getElementById('typingIndicator')?.remove();
        }

        async function clearChat() {
            if (!confirm('Clear chat history?')) return;
            try {
                await fetch('{{ route("provider.chatbot.clear") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': getCsrf(), 'Content-Type': 'application/json' }
                });
            } catch (err) { console.warn('Clear error:', err); }

            const welcomeEl = document.getElementById('welcomeMsg');
            chatMessages.innerHTML = '';
            if (welcomeEl) chatMessages.appendChild(welcomeEl);

            addBotMessage(
                '✅ Chat history cleared!\n\nHow can I help you?\n\n• Pending requests\n• My vehicles\n• My earnings\n• Delivery status update',
                ['Pending requests', 'My vehicles', 'My earnings']
            );
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = String(text);
            return d.innerHTML;
        }
        function getCurrentTime() {
            return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        function scrollToBottom() { chatMessages.scrollTop = chatMessages.scrollHeight; }

        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();

        // SmartCard Upload
        let smartcardFile = null;

        window.toggleSmartcardUpload = function () {
            const area = document.getElementById('smartcardUploadArea');
            const btn  = document.getElementById('smartcardToggleBtn');
            const isVisible = area.style.display !== 'none';
            area.style.display = isVisible ? 'none' : 'block';
            btn.classList.toggle('active', !isVisible);
            if (!isVisible) {
                addBotMessage(
                    '📸 **Upload Smart Card / Registration Book**\n\nPlease upload a clear photo of your vehicle\'s registration document or smart card.\n\nThe AI will automatically extract:\n• Vehicle Registration Number\n• Chassis Number\n• Vehicle Type\n• Engine Number\n• Make, Model & Year\n\nThis information will be used to auto-fill the vehicle registration form! 🚛',
                    []
                );
            }
        };

        window.handleDragOver = function (e) {
            e.preventDefault();
            document.getElementById('smartcardDropZone').classList.add('drag-over');
        };

        window.handleDrop = function (e) {
            e.preventDefault();
            document.getElementById('smartcardDropZone').classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) handleSmartcardFile(file);
        };

        window.handleSmartcardFile = function (file) {
            if (!file) return;
            if (file.size > 8 * 1024 * 1024) {
                addBotMessage('❌ File size exceeds 8MB. Please use a smaller image.', []);
                return;
            }
            smartcardFile = file;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('smartcardThumb').src = e.target.result;
                document.getElementById('smartcardFileName').textContent = file.name;
                document.getElementById('smartcardPreview').style.display = 'block';
                document.getElementById('smartcardDropZone').style.display = 'none';
            };
            reader.readAsDataURL(file);
        };

        window.cancelSmartcard = function () {
            smartcardFile = null;
            document.getElementById('smartcardPreview').style.display = 'none';
            document.getElementById('smartcardDropZone').style.display = 'block';
            document.getElementById('smartcardFileInput').value = '';
        };

        // ✅ FIXED uploadSmartcard function — NO manual Content-Type header
        window.uploadSmartcard = async function () {
            if (!smartcardFile) return;

            const detectBtn = document.getElementById('detectBtn');
            detectBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Extracting...';
            detectBtn.disabled = true;

            addUserMessage('📷 Uploading smart card for vehicle data extraction...');
            showTypingIndicator();

            const formData = new FormData();
            formData.append('image', smartcardFile);
            formData.append('_token', getCsrf());

            try {
                const res = await fetch('{{ route("provider.chatbot.extract.smartcard") }}', {
                    method: 'POST',
                    // ✅ CRITICAL: Do NOT set Content-Type for FormData
                    // Browser automatically sets it with correct boundary parameter
                    headers: {
                        'X-CSRF-TOKEN': getCsrf(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                removeTypingIndicator();

                // ✅ Check if response is actually JSON before parsing
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    // Server returned HTML (probably error page or redirect)
                    const text = await res.text();
                    console.error('Non-JSON response:', res.status, text.substring(0, 300));
                    addBotMessage(
                        '❌ Server error (' + res.status + '). Please check that you are logged in and try again.',
                        ['Try again', 'Add vehicle manually']
                    );
                    return;
                }

                const data = await res.json();

                if (data.success && data.data) {
                    const d = data.data;
                    const provider = data.provider ? ` (via ${data.provider})` : '';

                    const fields = [];
                    if (d.vehicle_number) fields.push({ label: 'Vehicle Number', value: d.vehicle_number });
                    if (d.chassis_number) fields.push({ label: 'Chassis Number',  value: d.chassis_number });
                    if (d.engine_number)  fields.push({ label: 'Engine Number',   value: d.engine_number });
                    if (d.vehicle_type)   fields.push({ label: 'Vehicle Type',    value: d.vehicle_type });
                    if (d.make)           fields.push({ label: 'Make',            value: d.make });
                    if (d.model)          fields.push({ label: 'Model',           value: d.model });
                    if (d.year)           fields.push({ label: 'Year',            value: d.year });
                    if (d.color)          fields.push({ label: 'Color',           value: d.color });
                    if (d.owner_name)     fields.push({ label: 'Owner Name',      value: d.owner_name });

                    if (fields.length > 0) {
                        const fieldLines = fields.map(f => `• **${f.label}:** ${f.value}`).join('\n');
                        addBotMessage(
                            `✅ **Vehicle Details Extracted!**${provider}\n\n${fieldLines}\n\n🚛 Click below to go to **My Vehicles** — form will be auto-filled!`,
                            ['Go to My Vehicles', 'Register Another Vehicle']
                        );

                        sessionStorage.setItem('extracted_vehicle_data', JSON.stringify(d));

                        // Auto-fill button
                        const linkDiv = document.createElement('div');
                        linkDiv.className = 'd-flex mb-3';
                        linkDiv.innerHTML = `
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:35px;height:35px;">
                                <i class="fas fa-robot text-white" style="font-size:14px;"></i>
                            </div>
                            <div class="ms-2">
                                <div class="bg-white rounded-3 p-2 shadow-sm">
                                    <a href="{{ route('my.vehicle') }}" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-truck me-2"></i>Open My Vehicles — Auto-Fill Ready!
                                    </a>
                                    <p class="mt-2 mb-0 small text-muted">Form will be automatically filled with extracted data.</p>
                                </div>
                            </div>`;
                        chatMessages.appendChild(linkDiv);
                        scrollToBottom();

                        autoFillVehicleFormIfPresent(d);
                    } else {
                        addBotMessage(
                            '⚠️ No details could be extracted from the image.\n\n**Tips for better results:**\n• Ensure good lighting\n• Hold document flat (no wrinkles)\n• Make sure text is in focus\n• Try a closer shot',
                            ['Try again', 'Add vehicle manually']
                        );
                    }
                } else {
                    addBotMessage(
                        `❌ ${data.message || 'Could not process image. Please try a clearer photo.'}`,
                        ['Try again', 'Add vehicle manually']
                    );
                }

            } catch (err) {
                removeTypingIndicator();
                console.error('SmartCard upload error:', err);

                // More specific error messages
                if (err instanceof TypeError && err.message.includes('fetch')) {
                    addBotMessage('❌ Network error — cannot reach the server. Please check your internet connection.', []);
                } else if (err instanceof SyntaxError) {
                    addBotMessage('❌ Server returned an unexpected response. Please try again or contact support.', []);
                } else {
                    addBotMessage('❌ Upload failed: ' + err.message + '. Please try again.', []);
                }
            } finally {
                detectBtn.innerHTML = '<i class="fas fa-magic me-1"></i> Extract';
                detectBtn.disabled = false;
                cancelSmartcard();
                document.getElementById('smartcardUploadArea').style.display = 'none';
                document.getElementById('smartcardToggleBtn').classList.remove('active');
            }
        };

        function autoFillVehicleFormIfPresent(d) {
            const mappings = [
                { value: d.vehicle_number, selectors: ['#vehicle_number', '[name="vehicle_number"]', '#vehicleNumber'] },
                { value: d.chassis_number, selectors: ['#chassis_number', '[name="chassis_number"]', '#chassisNumber'] },
                { value: d.engine_number,  selectors: ['#engine_number',  '[name="engine_number"]',  '#engineNumber'] },
                { value: d.vehicle_type,   selectors: ['#vehicle_type',   '[name="vehicle_type"]',   '#vehicleType',   'select[name="vehicle_type"]'] },
                { value: d.make,           selectors: ['#make',            '[name="make"]',            '#vehicleMake'] },
                { value: d.model,          selectors: ['#model',           '[name="model"]',           '#vehicleModel'] },
                { value: d.year,           selectors: ['#year',            '[name="year"]',            '#manufactureYear', '#manufacture_year'] },
                { value: d.color,          selectors: ['#color',           '[name="color"]',           '#vehicleColor'] },
            ];

            let filled = 0;
            mappings.forEach(({ value, selectors }) => {
                if (!value) return;
                selectors.forEach(sel => {
                    const el = document.querySelector(sel);
                    if (el && el.value === '') {
                        el.value = value;
                        el.dispatchEvent(new Event('input',  { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                        filled++;
                    }
                });
            });

            if (filled > 0) {
                addBotMessage(
                    `✅ **${filled} field(s) auto-filled** in the vehicle registration form on this page!`,
                    []
                );
            }
        }

    })();
    </script>
</body>
</html>