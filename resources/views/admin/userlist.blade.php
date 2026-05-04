<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - Admin Users Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
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
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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
        
        /* Category Tabs */
        .category-tabs {
            background: white;
            border-radius: 16px;
            padding: 5px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .category-btn {
            padding: 12px 30px;
            border: none;
            background: transparent;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .category-btn.active {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* User Cards */
        .user-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .user-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .vehicle-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid var(--secondary);
        }
        
        .vehicle-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-booked {
            background: #ffe5e5;
            color: #e74c3c;
        }
        
        .status-available {
            background: #e5f9e5;
            color: #27ae60;
        }
        
        .status-pending {
            background: #fff3e5;
            color: #f39c12;
        }
        
        .status-approved {
            background: #e5f9e5;
            color: #27ae60;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), #1a2530);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Table Styles */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 50px;
            padding: 8px 20px;
            border: 1px solid #ddd;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            padding: 5px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .logo h3 span,
            .sidebar .nav-link span {
                display: none;
            }
            .sidebar .nav-link i {
                margin-right: 0;
            }
            .main-content {
                margin-left: 80px;
            }
        }
        
        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        
        .vehicle-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .btn-action {
            padding: 5px 12px;
            margin: 0 3px;
            border-radius: 8px;
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
                <a class="nav-link active" href="{{route('admin.users')}}">
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
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Users Management</h5>
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
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-primary">{{ $totalCustomers }}</div>
                        <div class="stat-label">Total Customers</div>
                        <i class="fas fa-user-friends text-primary mt-2" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-success">{{ $totalProviders }}</div>
                        <div class="stat-label">Total Providers</div>
                        <i class="fas fa-user-tie text-success mt-2" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-info">{{ $totalVehicles }}</div>
                        <div class="stat-label">Total Vehicles</div>
                        <i class="fas fa-truck text-info mt-2" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value text-warning">{{ $availableVehicles }}</div>
                        <div class="stat-label">Available Vehicles</div>
                        <i class="fas fa-check-circle text-warning mt-2" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs d-flex">
                <button class="category-btn active" onclick="showCategory('customers')">
                    <i class="fas fa-users me-2"></i> Customers
                    <span class="badge bg-secondary ms-2">{{ $totalCustomers }}</span>
                </button>
                <button class="category-btn" onclick="showCategory('providers')">
                    <i class="fas fa-user-tie me-2"></i> Providers
                    <span class="badge bg-secondary ms-2">{{ $totalProviders }}</span>
                </button>
            </div>

            <!-- Customers Section -->
            <div id="customersSection" class="category-section">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Registered Customers</h5>
                            <div>
                                <input type="text" id="customerSearch" class="form-control" placeholder="Search customers..." style="width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>CNIC</th>
                                        <th>Registered On</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td>#{{ $customer->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3498db, #2c3e50); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 10px;">
                                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                </div>
                                                <strong>{{ $customer->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->cnic ?? 'N/A' }}</td>
                                        <td>{{ $customer->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($customer->email_verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewCustomerDetails({{ $customer->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $customer->id }}, 'customer')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Providers Section -->
            <div id="providersSection" class="category-section" style="display: none;">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-user-tie me-2 text-success"></i>Registered Providers & Their Vehicles</h5>
                            <div>
                                <input type="text" id="providerSearch" class="form-control" placeholder="Search providers..." style="width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($providers as $provider)
                        <div class="provider-card mb-4" data-provider-name="{{ strtolower($provider->name) }}" data-provider-email="{{ strtolower($provider->email) }}">
                            <!-- Provider Info Header -->
                            <div class="user-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="user-avatar me-4">
                                            {{ strtoupper(substr($provider->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $provider->name }}</h5>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-envelope me-2"></i>{{ $provider->email }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-id-card me-2"></i>CNIC: {{ $provider->cnic ?? 'N/A' }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-calendar me-2"></i>Registered: {{ $provider->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary p-2 me-2">
                                            <i class="fas fa-truck me-1"></i> {{ $provider->vehicles->count() }} Vehicles
                                        </span>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser({{ $provider->id }}, 'provider')">
                                            <i class="fas fa-trash"></i> Delete Provider
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehicles List -->
                            @if($provider->vehicles->count() > 0)
                            <div class="ms-4 mb-3">
                                <h6 class="mb-3"><i class="fas fa-truck text-primary me-2"></i>Registered Vehicles</h6>
                                <div class="row">
                                    @foreach($provider->vehicles as $vehicle)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="vehicle-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <strong>{{ $vehicle->vehicle_number }}</strong>
                                                @if($vehicle->is_booked == 'yes')
                                                    <span class="vehicle-status status-booked"><i class="fas fa-bookmark me-1"></i>Booked</span>
                                                @else
                                                    <span class="vehicle-status status-available"><i class="fas fa-check-circle me-1"></i>Available</span>
                                                @endif
                                            </div>
                                            <div class="vehicle-info">
                                                <p class="mb-1"><i class="fas fa-tag me-2 text-secondary"></i>Type: <strong>{{ $vehicle->vehicle_type }}</strong></p>
                                                <p class="mb-1"><i class="fas fa-weight-hanging me-2 text-secondary"></i>Capacity: <strong>{{ $vehicle->weight_capacity }} kg</strong></p>
                                                <p class="mb-1"><i class="fas fa-chassis me-2 text-secondary"></i>Chassis: {{ substr($vehicle->chassis_number, -6) }}</p>
                                                <p class="mb-0">
                                                    <i class="fas fa-info-circle me-2 text-secondary"></i>Status: 
                                                    @if($vehicle->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($vehicle->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i> Added: {{ $vehicle->created_at->format('d M Y') }}
                                                </small>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewVehicleDetails({{ $vehicle->id }})">
                                                    <i class="fas fa-info-circle"></i> Details
                                                </button>
                                            </div>

                                            <!-- Booking Status Details -->
                                            @php
                                                $activeBooking = $vehicle->bookings->where('status', 'accept')->first();
                                            @endphp
                                            @if($activeBooking)
                                            <div class="mt-2 pt-2 border-top">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i> Currently Booked
                                                    @if($activeBooking->customer)
                                                        - Customer: {{ $activeBooking->customer->name }}
                                                    @endif
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="alert alert-light ms-4 me-4">
                                <i class="fas fa-info-circle me-2"></i> No vehicles registered yet.
                            </div>
                            @endif
                        </div>
                        @endforeach
                        
                        <div class="mt-3">
                            {{ $providers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>Customer Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="customerDetailBody">
                    <!-- Dynamic content -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Details Modal -->
    <div class="modal fade" id="vehicleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Vehicle Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="vehicleDetailBody">
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Category switching
        function showCategory(category) {
            const customersSection = document.getElementById('customersSection');
            const providersSection = document.getElementById('providersSection');
            const buttons = document.querySelectorAll('.category-btn');
            
            if (category === 'customers') {
                customersSection.style.display = 'block';
                providersSection.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                customersSection.style.display = 'none';
                providersSection.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }
        
        // Search customers
        document.getElementById('customerSearch')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // Search providers
        document.getElementById('providerSearch')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const providerCards = document.querySelectorAll('.provider-card');
            
            providerCards.forEach(card => {
                const providerName = card.getAttribute('data-provider-name') || '';
                const providerEmail = card.getAttribute('data-provider-email') || '';
                if (providerName.includes(searchTerm) || providerEmail.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // View Customer Details
        function viewCustomerDetails(customerId) {
            fetch(`/admin/customer/${customerId}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const customer = data.customer;
                    const modalBody = document.getElementById('customerDetailBody');
                    
                    modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Full Name</div>
                                    <div>${escapeHtml(customer.name)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email Address</div>
                                    <div>${escapeHtml(customer.email)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">CNIC Number</div>
                                    <div>${escapeHtml(customer.cnic || 'N/A')}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Role</div>
                                    <div><span class="badge bg-primary">${escapeHtml(customer.role)}</span></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email Verification</div>
                                    <div>${customer.email_verified ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-warning">Pending</span>'}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Registered On</div>
                                    <div>${new Date(customer.created_at).toLocaleString()}</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mt-3"><i class="fas fa-shopping-cart me-2"></i>Booking History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Vehicle</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${customer.bookings && customer.bookings.length > 0 ? 
                                        customer.bookings.map(booking => `
                                            <tr>
                                                <td>#${booking.id}</td>
                                                <td>${escapeHtml(booking.vehicle?.vehicle_number || 'N/A')}</td>
                                                <td><span class="badge bg-${booking.status === 'complete' ? 'success' : 'warning'}">${booking.status}</span></td>
                                                <td>${new Date(booking.created_at).toLocaleDateString()}</td>
                                            </tr>
                                        `).join('') : 
                                        '<tr><td colspan="4" class="text-center">No bookings found</td></tr>'
                                    }
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    new bootstrap.Modal(document.getElementById('customerDetailsModal')).show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load customer details');
            });
        }
        
        // View Vehicle Details
        function viewVehicleDetails(vehicleId) {
            fetch(`/admin/vehicle/${vehicleId}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const vehicle = data.vehicle;
                    const modalBody = document.getElementById('vehicleDetailBody');
                    
                    modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Vehicle Number</div>
                                    <div><strong>${escapeHtml(vehicle.vehicle_number)}</strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Vehicle Type</div>
                                    <div>${escapeHtml(vehicle.vehicle_type)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Chassis Number</div>
                                    <div>${escapeHtml(vehicle.chassis_number)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Weight Capacity</div>
                                    <div>${vehicle.weight_capacity} kg</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Booking Status</div>
                                    <div>${vehicle.is_booked === 'yes' ? '<span class="badge bg-danger">Booked</span>' : '<span class="badge bg-success">Available</span>'}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Verification Status</div>
                                    <div>${vehicle.status === 'approved' ? '<span class="badge bg-success">Approved</span>' : (vehicle.status === 'pending' ? '<span class="badge bg-warning">Pending</span>' : '<span class="badge bg-danger">Rejected</span>')}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Owner</div>
                                    <div>${escapeHtml(vehicle.user?.name || 'N/A')}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Registered On</div>
                                    <div>${new Date(vehicle.created_at).toLocaleString()}</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mt-3"><i class="fas fa-history me-2"></i>Recent Bookings</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Pickup Location</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${vehicle.bookings && vehicle.bookings.length > 0 ? 
                                        vehicle.bookings.slice(0, 5).map(booking => `
                                            <tr>
                                                <td>#${booking.id}</td>
                                                <td>${escapeHtml(booking.customer?.name || 'N/A')}</td>
                                                <td>${escapeHtml(booking.pickup_location?.substring(0, 30) || 'N/A')}${booking.pickup_location?.length > 30 ? '...' : ''}</td>
                                                <td><span class="badge bg-${booking.status === 'complete' ? 'success' : 'warning'}">${booking.status}</span></td>
                                            </tr>
                                        `).join('') : 
                                        '<tr><td colspan="4" class="text-center">No bookings found</td></tr>'
                                    }
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    new bootstrap.Modal(document.getElementById('vehicleDetailsModal')).show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load vehicle details');
            });
        }
        
        // Delete User
        function deleteUser(userId, userType) {
            if (confirm(`Are you sure you want to delete this ${userType}? This action cannot be undone.`)) {
                fetch(`/admin/user/${userId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
            }
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initialize DataTable for customers
        $(document).ready(function() {
            $('#customersTable').DataTable({
                pageLength: 10,
                ordering: true,
                searching: false, // We'll use custom search
                language: {
                    emptyTable: "No customers found"
                }
            });
        });
    </script>
</body>
</html>