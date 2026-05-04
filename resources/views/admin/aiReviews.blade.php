<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckLink - AI Reviews</title>
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
            --positive: #27ae60;
            --negative: #e74c3c;
            --neutral: #f39c12;
        }

        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); overflow-x: hidden; }

        /* ── Sidebar ── */
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

        /* ── Main ── */
        .main-content { margin-left: 280px; transition: all 0.3s; min-height: 100vh; }
        .topbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 30px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 999; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .topbar .user-info img { width: 45px; height: 45px; border-radius: 50%; margin-right: 12px; border: 3px solid var(--secondary); }
        .content-area { padding: 30px; }

        /* ── Cards ── */
        .card { border: none; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); margin-bottom: 25px; transition: all 0.3s ease; background: white; overflow: hidden; }
        .card-header { background: linear-gradient(135deg, white 0%, #f8f9fa 100%); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 25px; font-weight: 600; font-size: 1.1rem; }

        /* ── Stat Cards ── */
        .stat-card { text-align: center; padding: 20px; background: white; border-radius: 16px; transition: all 0.3s; height: 100%; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-label { font-size: 0.85rem; color: #6c757d; margin-top: 5px; }

        /* ── AI Category Tabs ── */
        .ai-category-tabs { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .cat-tab { padding: 12px 24px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; border: 2px solid transparent; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
        .cat-tab:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.15); }
        .cat-tab.all        { background: #f0f4ff; border-color: var(--secondary); color: var(--secondary); }
        .cat-tab.all.active { background: var(--secondary); color: white; }
        .cat-tab.positive        { background: #eafaf1; border-color: var(--positive); color: var(--positive); }
        .cat-tab.positive.active { background: var(--positive); color: white; }
        .cat-tab.negative        { background: #fdf2f2; border-color: var(--negative); color: var(--negative); }
        .cat-tab.negative.active { background: var(--negative); color: white; }
        .cat-tab.neutral        { background: #fffbeb; border-color: var(--neutral); color: var(--neutral); }
        .cat-tab.neutral.active { background: var(--neutral); color: white; }
        .cat-badge { background: rgba(0,0,0,0.12); border-radius: 20px; padding: 2px 10px; font-size: 0.78rem; font-weight: 700; }

        /* ── Category Sections ── */
        .category-section { display: none; }
        .category-section.active { display: block; }

        /* ── Review Card ── */
        .review-card { border: 1px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 20px; transition: all 0.3s; background: white; position: relative; }
        .review-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .review-card.positive-card { border-left: 4px solid var(--positive); }
        .review-card.negative-card { border-left: 4px solid var(--negative); }
        .review-card.neutral-card  { border-left: 4px solid var(--neutral); }
        .review-header { display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; margin-bottom: 15px; }
        .reviewer-info { display: flex; align-items: center; gap: 15px; }
        .reviewer-avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--secondary), var(--primary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; }
        .ai-label { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .ai-label.positive { background: #eafaf1; color: var(--positive); }
        .ai-label.negative { background: #fdf2f2; color: var(--negative); }
        .ai-label.neutral  { background: #fffbeb; color: var(--neutral); }
        .rating-stars { color: #f39c12; font-size: 1rem; letter-spacing: 2px; }
        .review-text { color: #4a5568; line-height: 1.6; margin: 15px 0; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px; }
        .info-item { background: #f8f9fa; padding: 10px 14px; border-radius: 10px; border-left: 3px solid var(--secondary); }
        .info-label { font-size: 0.72rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-weight: 600; margin-top: 3px; font-size: 0.9rem; }

        /* ── Filters ── */
        .filter-btn { border: 2px solid #e9ecef; background: transparent; color: #6c757d; padding: 8px 20px; border-radius: 30px; margin: 0 5px; transition: all 0.3s ease; font-weight: 500; }
        .filter-btn:hover, .filter-btn.active { border-color: var(--secondary); background: var(--secondary); color: white; transform: translateY(-2px); }
        .rating-filter-btn { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; margin: 3px; }

        /* ── Search ── */
        .search-bar { background: white; border-radius: 50px; padding: 5px 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; }
        .search-bar input { border: none; padding: 10px 0; outline: none; width: 250px; font-family: 'Poppins', sans-serif; }
        .search-bar button { background: transparent; border: none; color: var(--secondary); }

        /* ── Chart ── */
        .chart-container { max-width: 300px; margin: 0 auto; }

        /* ── AI Badge ── */
        .ai-powered-badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 6px 16px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

        /* ── Category Summary Cards ── */
        .cat-summary { border-radius: 16px; padding: 20px; text-align: center; }
        .cat-summary.positive-summary { background: linear-gradient(135deg, #eafaf1, #d5f5e3); border: 1px solid #a9dfbf; }
        .cat-summary.negative-summary { background: linear-gradient(135deg, #fdf2f2, #fadbd8); border: 1px solid #f1948a; }
        .cat-summary.neutral-summary  { background: linear-gradient(135deg, #fffbeb, #fef9e7); border: 1px solid #f9e79f; }
        .cat-summary .cat-count { font-size: 2.2rem; font-weight: 700; }
        .cat-summary .cat-name  { font-size: 0.85rem; font-weight: 600; }
        .cat-summary.positive-summary .cat-count { color: var(--positive); }
        .cat-summary.negative-summary .cat-count { color: var(--negative); }
        .cat-summary.neutral-summary .cat-count  { color: var(--neutral); }

        /* ── Modal ── */
        .modal-content { border-radius: 20px; border: none; }
        .modal-header { background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%); color: white; border-radius: 20px 20px 0 0; }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .info-card { background: #f8f9fa; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
        .info-card h6 { margin-bottom: 10px; color: var(--primary); }

        /* ── Loader ── */
        #loadingIndicator { display: none; text-align: center; padding: 40px; }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar .logo h3 span, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }
            .main-content { margin-left: 80px; }
            .content-area { padding: 20px 15px; }
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
                <a class="nav-link" href="{{route('admin.login')}}">
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
                <a class="nav-link" href="{{route('admin.ratings-reviews')}}">
                    <i class="fas fa-star"></i> <span>Ratings & Reviews</span>
                </a>
                
                <a class="nav-link active" href="{{ route('admin.ai-reviews') }}"><i class="fas fa-robot"></i><span>AI Reviews</span></a>
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
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0"><i class="fas fa-robot me-2 text-primary"></i>AI-Powered Review Analysis</h5>
            <span class="ai-powered-badge"><i class="fas fa-brain"></i> Gemini AI</span>
            <span id="aiStatusBadge" style="display:none;" class="badge bg-warning">Using Rule-Based Fallback</span>
        </div>
        <div class="user-info d-flex align-items-center">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin" style="width:45px;height:45px;border-radius:50%;border:3px solid var(--secondary);">
                    <span class="ms-2 d-none d-sm-inline fw-semibold">Admin</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-area">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">AI Review Categories</h4>
                <p class="text-muted mb-0">AI automatically classifies reviews into Positive, Negative, and Neutral</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="search-bar">
                    <button><i class="fas fa-search"></i></button>
                    <input type="text" id="searchInput" placeholder="Search reviews..." onkeyup="debounceSearch()">
                </div>
                <button class="btn btn-outline-secondary rounded-pill" onclick="refreshReviews()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-primary rounded-pill" onclick="reAnalyzeReviews()" title="Force AI Re-analysis">
                    <i class="fas fa-brain me-1"></i>Re-analyze
                </button>
            </div>
        </div>

        <!-- Top Stats -->
        <div class="row mb-4">
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value text-primary" id="totalReviews">0</div>
                    <div class="stat-label">Total Reviews</div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value text-warning" id="avgRating">0</div>
                    <div class="stat-label">Avg Rating</div>
                    <div class="rating-stars mt-1" id="avgRatingStars"></div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value text-success" id="approvedReviews">0</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value text-warning" id="pendingReviews">0</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value" style="color:var(--positive);" id="positiveCount">0</div>
                    <div class="stat-label">Positive</div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value" style="color:var(--negative);" id="negativeCount">0</div>
                    <div class="stat-label">Negative</div>
                </div>
            </div>
        </div>

        <!-- AI Category Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="cat-summary positive-summary" onclick="switchCategory('positive')" style="cursor:pointer;">
                    <div class="cat-count" id="summaryPositive">0</div>
                    <div class="cat-name text-success"><i class="fas fa-thumbs-up me-1"></i> Positive Reviews</div>
                    <small class="text-muted">Happy customers, praise & compliments</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="cat-summary negative-summary" onclick="switchCategory('negative')" style="cursor:pointer;">
                    <div class="cat-count" id="summaryNegative">0</div>
                    <div class="cat-name text-danger"><i class="fas fa-thumbs-down me-1"></i> Negative Reviews</div>
                    <small class="text-muted">Complaints, bad experiences, issues</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="cat-summary neutral-summary" onclick="switchCategory('neutral')" style="cursor:pointer;">
                    <div class="cat-count" id="summaryNeutral">0</div>
                    <div class="cat-name" style="color:var(--neutral);"><i class="fas fa-minus-circle me-1"></i> Neutral Reviews</div>
                    <small class="text-muted">Average, mixed or factual feedback</small>
                </div>
            </div>
        </div>

        <!-- Rating Distribution + Breakdown -->
        <div class="row mb-4">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Rating Distribution</h5></div>
                    <div class="card-body"><div class="chart-container"><canvas id="ratingChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Rating Breakdown</h5></div>
                    <div class="card-body" id="ratingBreakdown"></div>
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
                            <button class="filter-btn rating-filter-btn" data-rating="5" onclick="setRatingFilter(5)">★★★★★</button>
                            <button class="filter-btn rating-filter-btn" data-rating="4" onclick="setRatingFilter(4)">★★★★☆</button>
                            <button class="filter-btn rating-filter-btn" data-rating="3" onclick="setRatingFilter(3)">★★★☆☆</button>
                            <button class="filter-btn rating-filter-btn" data-rating="2" onclick="setRatingFilter(2)">★★☆☆☆</button>
                            <button class="filter-btn rating-filter-btn" data-rating="1" onclick="setRatingFilter(1)">★☆☆☆☆</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-check-circle me-1"></i>Status Filter</label>
                        <div>
                            <button class="filter-btn active" data-status-filter="all" onclick="setStatusFilter('all')">All</button>
                            <button class="filter-btn" data-status-filter="approved" onclick="setStatusFilter('approved')">Approved</button>
                            <button class="filter-btn" data-status-filter="pending" onclick="setStatusFilter('pending')">Pending</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-sort-amount-down me-1"></i>Sort By</label>
                        <select class="form-select rounded-pill" id="sortBy" onchange="sortReviews()">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="highest">Highest Rating</option>
                            <option value="lowest">Lowest Rating</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Category Tabs + Reviews List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="ai-category-tabs mb-0">
                    <button class="cat-tab all active" data-cat="all" onclick="switchCategory('all')">
                        <i class="fas fa-list"></i> All <span class="cat-badge" id="tabCountAll">0</span>
                    </button>
                    <button class="cat-tab positive" data-cat="positive" onclick="switchCategory('positive')">
                        <i class="fas fa-smile"></i> Positive <span class="cat-badge" id="tabCountPositive">0</span>
                    </button>
                    <button class="cat-tab negative" data-cat="negative" onclick="switchCategory('negative')">
                        <i class="fas fa-frown"></i> Negative <span class="cat-badge" id="tabCountNegative">0</span>
                    </button>
                    <button class="cat-tab neutral" data-cat="neutral" onclick="switchCategory('neutral')">
                        <i class="fas fa-meh"></i> Neutral <span class="cat-badge" id="tabCountNeutral">0</span>
                    </button>
                </div>
                <span class="text-muted" id="reviewsCount">Showing 0 reviews</span>
            </div>
            <div class="card-body">
                <div id="loadingIndicator">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">AI is analyzing reviews...</p>
                </div>
                <div id="reviewsList"></div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted" id="paginationInfo">Page 1 of 1</div>
                    <nav><ul class="pagination mb-0" id="paginationControls"></ul></nav>
                </div>
            </div>
        </div>

    </div><!-- /content-area -->
</div><!-- /main-content -->

<!-- Review Details Modal -->
<div class="modal fade" id="reviewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i>Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewDetailBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // ── State ─────────────────────────────────────────────────
    let currentPage         = 1;
    let lastPage            = 1;
    let currentCategory     = 'all';
    let currentRatingFilter = 'all';
    let currentStatusFilter = 'all';
    let currentSearchTerm   = '';
    let currentSort         = 'newest';
    let allReviews          = [];
    let ratingChart         = null;
    let searchTimer         = null;

    // Counts per category (for tab badges)
    let categoryCounts = { all: 0, positive: 0, negative: 0, neutral: 0 };

    // ── Init ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', loadReviews);

    // ── Load Reviews ──────────────────────────────────────────
    function loadReviews() {
        document.getElementById('loadingIndicator').style.display = 'block';
        document.getElementById('reviewsList').innerHTML = '';

        let url = `/admin/ai-reviews/data?page=${currentPage}&per_page=10`
            + `&category=${currentCategory}`
            + `&rating=${currentRatingFilter}`
            + `&status=${currentStatusFilter}`
            + `&sort=${currentSort}`;
        if (currentSearchTerm) url += `&search=${encodeURIComponent(currentSearchTerm)}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                document.getElementById('loadingIndicator').style.display = 'none';
                if (data.success) {
                    allReviews = data.reviews || [];
                    updateStats(data.stats);
                    renderReviews(allReviews);
                    updatePagination(data.current_page, data.last_page, data.total);
                    updateRatingChart(data.stats);
                    updateRatingBreakdown(data.stats);
                    // Show AI fallback notice if needed
                    document.getElementById('aiStatusBadge').style.display = data.ai_used ? 'none' : 'inline-flex';
                } else {
                    showError('Failed to load reviews');
                }
            })
            .catch(err => {
                document.getElementById('loadingIndicator').style.display = 'none';
                console.error(err);
                showError('Error loading reviews');
            });
    }

    // ── Update Stats ──────────────────────────────────────────
    function updateStats(stats) {
        if (!stats) return;
        document.getElementById('totalReviews').textContent   = stats.total           || 0;
        document.getElementById('avgRating').textContent      = stats.avg_rating      || 0;
        document.getElementById('approvedReviews').textContent= stats.approved        || 0;
        document.getElementById('pendingReviews').textContent = stats.pending_approval|| 0;
        document.getElementById('positiveCount').textContent  = stats.positive_count  || 0;
        document.getElementById('negativeCount').textContent  = stats.negative_count  || 0;

        // Summary cards
        document.getElementById('summaryPositive').textContent = stats.positive_count || 0;
        document.getElementById('summaryNegative').textContent = stats.negative_count || 0;
        document.getElementById('summaryNeutral').textContent  = stats.neutral_count  || 0;

        // Tab badges (load all-category counts separately for tab badges)
        document.getElementById('tabCountAll').textContent      = stats.total           || 0;
        document.getElementById('tabCountPositive').textContent = stats.positive_count  || 0;
        document.getElementById('tabCountNegative').textContent = stats.negative_count  || 0;
        document.getElementById('tabCountNeutral').textContent  = stats.neutral_count   || 0;

        document.getElementById('avgRatingStars').innerHTML = generateStarRating(stats.avg_rating || 0);
    }

    // ── Generate Stars ────────────────────────────────────────
    function generateStarRating(rating) {
        const full = Math.floor(rating);
        const half = (rating - full) >= 0.5;
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= full) stars += '<i class="fas fa-star"></i>';
            else if (i === full + 1 && half) stars += '<i class="fas fa-star-half-alt"></i>';
            else stars += '<i class="far fa-star"></i>';
        }
        return stars;
    }

    // ── Rating Chart ──────────────────────────────────────────
    function updateRatingChart(stats) {
        const ctx = document.getElementById('ratingChart').getContext('2d');
        if (ratingChart) ratingChart.destroy();
        ratingChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['5 Star','4 Star','3 Star','2 Star','1 Star'],
                datasets: [{ data: [stats.five_star||0, stats.four_star||0, stats.three_star||0, stats.two_star||0, stats.one_star||0], backgroundColor: ['#27ae60','#3498db','#f39c12','#e67e22','#e74c3c'], borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // ── Rating Breakdown ──────────────────────────────────────
    function updateRatingBreakdown(stats) {
        const total = stats.total || 1;
        const rows = [
            { label: '5 Star', count: stats.five_star||0, cls: 'bg-success' },
            { label: '4 Star', count: stats.four_star||0, cls: 'bg-primary' },
            { label: '3 Star', count: stats.three_star||0, cls: 'bg-warning' },
            { label: '2 Star', count: stats.two_star||0, cls: '' },
            { label: '1 Star', count: stats.one_star||0, cls: 'bg-danger' },
        ];
        document.getElementById('ratingBreakdown').innerHTML = rows.map(r => `
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>${r.label}</span>
                    <span>${r.count} (${Math.round(r.count/total*100)}%)</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar ${r.cls}" style="width:${r.count/total*100}%;${r.cls===''?'background:#e67e22;':''}"></div>
                </div>
            </div>`).join('');
    }

    // ── Render Reviews ────────────────────────────────────────
    function renderReviews(reviews) {
        const container = document.getElementById('reviewsList');
        if (!reviews || reviews.length === 0) {
            container.innerHTML = `<div class="text-center py-5"><i class="fas fa-star fa-3x text-muted mb-3"></i><p class="text-muted">No reviews found in this category</p></div>`;
            document.getElementById('reviewsCount').textContent = 'Showing 0 reviews';
            return;
        }

        const catIcons = { positive: '😊', negative: '😞', neutral: '😐' };
        const catColors = { positive: 'positive', negative: 'negative', neutral: 'neutral' };

        let html = '';
        reviews.forEach(review => {
            const customerName  = review.customer?.name       || 'N/A';
            const customerEmail = review.customer?.email      || 'N/A';
            const providerName  = review.provider?.name       || 'N/A';
            const vehicleNumber = review.booking?.vehicle?.vehicle_number || 'N/A';
            const vehicleType   = review.booking?.vehicle?.vehicle_type   || 'N/A';
            const bookingId     = review.booking_id;
            const reviewText    = review.review || 'No review text provided';
            const rating        = review.rating;
            const createdAt     = new Date(review.created_at).toLocaleDateString();
            const isApproved    = review.is_approved;
            const cat           = review.ai_category || 'neutral';

            html += `
            <div class="review-card ${cat}-card">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">${escapeHtml(customerName.charAt(0).toUpperCase())}</div>
                        <div>
                            <h6 class="mb-0">${escapeHtml(customerName)}</h6>
                            <small class="text-muted">${escapeHtml(customerEmail)}</small>
                            <div class="mt-1">
                                <span class="ai-label ${catColors[cat]}">${catIcons[cat]} ${cat.charAt(0).toUpperCase()+cat.slice(1)}</span>
                                ${isApproved ? '<span class="badge bg-success ms-1" style="font-size:.7rem;">Approved</span>' : '<span class="badge bg-warning ms-1" style="font-size:.7rem;">Pending</span>'}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="rating-stars mb-1">${generateStarRating(rating)}</div>
                        <small class="text-muted">${createdAt}</small>
                    </div>
                </div>
                <div class="review-text">"${escapeHtml(reviewText)}"</div>
                <div class="info-grid">
                    <div class="info-item"><div class="info-label"><i class="fas fa-hashtag"></i> Booking</div><div class="info-value">#${bookingId}</div></div>
                    <div class="info-item"><div class="info-label"><i class="fas fa-truck"></i> Vehicle</div><div class="info-value">${escapeHtml(vehicleNumber)} (${escapeHtml(vehicleType)})</div></div>
                    <div class="info-item"><div class="info-label"><i class="fas fa-user-tie"></i> Provider</div><div class="info-value">${escapeHtml(providerName)}</div></div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-sm btn-info" onclick="viewReviewDetails(${review.id})"><i class="fas fa-eye me-1"></i>View Details</button>
                    ${!isApproved
                        ? `<button class="btn btn-sm btn-success" onclick="toggleReviewStatus(${review.id})"><i class="fas fa-check-circle me-1"></i>Approve</button>`
                        : `<button class="btn btn-sm btn-warning" onclick="toggleReviewStatus(${review.id})"><i class="fas fa-eye-slash me-1"></i>Hide</button>`
                    }
                    <button class="btn btn-sm btn-danger" onclick="deleteReview(${review.id})"><i class="fas fa-trash me-1"></i>Delete</button>
                </div>
            </div>`;
        });

        container.innerHTML = html;
        document.getElementById('reviewsCount').textContent = `Showing ${reviews.length} reviews`;
    }

    // ── View Review Details ───────────────────────────────────
    function viewReviewDetails(reviewId) {
        const review = allReviews.find(r => r.id === reviewId);
        if (!review) return;
        const cat = review.ai_category || 'neutral';
        const catIcons = { positive: '😊 Positive', negative: '😞 Negative', neutral: '😐 Neutral' };

        document.getElementById('reviewDetailBody').innerHTML = `
            <div class="info-card mb-3">
                <h6><i class="fas fa-robot text-primary me-2"></i>AI Classification</h6>
                <span class="ai-label ${cat}" style="font-size:1rem;">${catIcons[cat]}</span>
                <span class="ms-3"><strong>Rating:</strong> ${generateStarRating(review.rating)} (${review.rating}/5)</span>
            </div>
            <div class="info-card mb-3">
                <h6><i class="fas fa-star text-warning me-2"></i>Review Info</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong> ${new Date(review.created_at).toLocaleString()}<br>
                        <strong>Status:</strong> <span class="badge ${review.is_approved ? 'bg-success' : 'bg-warning'}">${review.is_approved ? 'Approved' : 'Pending'}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Booking ID:</strong> #${review.booking_id}<br>
                        <strong>Review ID:</strong> #${review.id}
                    </div>
                </div>
            </div>
            <div class="info-card mb-3">
                <h6><i class="fas fa-user me-2"></i>Customer</h6>
                <table class="table table-borderless mb-0">
                    <tr><td width="130"><strong>Name:</strong></td><td>${escapeHtml(review.customer?.name||'N/A')}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${escapeHtml(review.customer?.email||'N/A')}</td></tr>
                    <tr><td><strong>CNIC:</strong></td><td>${escapeHtml(review.customer?.cnic||'N/A')}</td></tr>
                </table>
            </div>
            <div class="info-card mb-3">
                <h6><i class="fas fa-user-tie me-2"></i>Vehicle Owner</h6>
                <table class="table table-borderless mb-0">
                    <tr><td width="130"><strong>Name:</strong></td><td>${escapeHtml(review.provider?.name||'N/A')}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${escapeHtml(review.provider?.email||'N/A')}</td></tr>
                </table>
            </div>
            <div class="info-card mb-3">
                <h6><i class="fas fa-truck me-2"></i>Vehicle</h6>
                <table class="table table-borderless mb-0">
                    <tr><td width="130"><strong>Number:</strong></td><td>${escapeHtml(review.booking?.vehicle?.vehicle_number||'N/A')}</td></tr>
                    <tr><td><strong>Type:</strong></td><td>${escapeHtml(review.booking?.vehicle?.vehicle_type||'N/A')}</td></tr>
                    <tr><td><strong>Capacity:</strong></td><td>${review.booking?.vehicle?.weight_capacity||0} kg</td></tr>
                </table>
            </div>
            <div class="info-card mb-3">
                <h6><i class="fas fa-route me-2"></i>Booking</h6>
                <table class="table table-borderless mb-0">
                    <tr><td width="130"><strong>Pickup:</strong></td><td>${escapeHtml(review.booking?.pickup_location||'N/A')}</td></tr>
                    <tr><td><strong>Dropoff:</strong></td><td>${escapeHtml(review.booking?.dropoff_location||'N/A')}</td></tr>
                    <tr><td><strong>Goods:</strong></td><td>${escapeHtml(review.booking?.goods_type||'N/A')} (${review.booking?.goods_weight||0} kg)</td></tr>
                    <tr><td><strong>Est. Fare:</strong></td><td>Rs ${review.booking?.estimated_fare||0}</td></tr>
                    <tr><td><strong>Actual Fare:</strong></td><td>Rs ${review.booking?.actual_fare||0}</td></tr>
                    <tr><td><strong>Payment:</strong></td><td><span class="badge ${review.booking?.payment_status==='paid'?'bg-success':'bg-warning'}">${review.booking?.payment_status||'pending'}</span></td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-info">${review.booking?.status||'N/A'}</span></td></tr>
                </table>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-comment me-2"></i>Review Text</h6>
                <p class="mb-0">"${escapeHtml(review.review||'No review text provided')}"</p>
            </div>`;

        new bootstrap.Modal(document.getElementById('reviewDetailsModal')).show();
    }

    // ── Toggle Status ─────────────────────────────────────────
    function toggleReviewStatus(reviewId) {
        fetch(`/admin/reviews/${reviewId}/toggle-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            if (data.success) { showToast(data.message, 'success'); loadReviews(); }
            else showToast(data.message || 'Failed', 'error');
        }).catch(() => showToast('Error updating status', 'error'));
    }

    // ── Delete Review ─────────────────────────────────────────
    function deleteReview(reviewId) {
        if (confirm('Are you sure you want to delete this review?')) {
            fetch(`/admin/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.success) { showToast(data.message, 'success'); loadReviews(); }
                else showToast(data.message || 'Failed', 'error');
            }).catch(() => showToast('Error deleting review', 'error'));
        }
    }

    // ── Re-Analyze ────────────────────────────────────────────
    function reAnalyzeReviews() {
        fetch('/admin/ai-reviews/re-analyze', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            if (data.success) { showToast('Re-analysis triggered! Reloading...', 'success'); setTimeout(loadReviews, 1000); }
        }).catch(() => showToast('Error triggering re-analysis', 'error'));
    }

    // ── Category Switching ────────────────────────────────────
    function switchCategory(cat) {
        currentCategory = cat;
        currentPage = 1;
        document.querySelectorAll('.cat-tab').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-cat') === cat) btn.classList.add('active');
        });
        loadReviews();
    }

    // ── Filters ───────────────────────────────────────────────
    function setRatingFilter(rating) {
        currentRatingFilter = rating; currentPage = 1;
        document.querySelectorAll('.rating-filter-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-rating') == rating);
        });
        loadReviews();
    }

    function setStatusFilter(status) {
        currentStatusFilter = status; currentPage = 1;
        document.querySelectorAll('[data-status-filter]').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-status-filter') == status);
        });
        loadReviews();
    }

    function sortReviews()  { currentSort = document.getElementById('sortBy').value; currentPage = 1; loadReviews(); }
    function refreshReviews() { currentPage = 1; loadReviews(); }

    // ── Search with debounce ──────────────────────────────────
    function debounceSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentSearchTerm = document.getElementById('searchInput').value;
            currentPage = 1;
            loadReviews();
        }, 400);
    }

    // ── Pagination ────────────────────────────────────────────
    function updatePagination(current, last, total) {
        currentPage = current; lastPage = last;
        document.getElementById('paginationInfo').textContent = `Page ${current} of ${last} (${total} total)`;

        let html = `<li class="page-item ${current<=1?'disabled':''}"><a class="page-link" href="#" onclick="changePage(${current-1});return false;">Prev</a></li>`;
        let start = Math.max(1, current-2), end = Math.min(last, current+2);
        if (start > 1) { html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1);return false;">1</a></li>`; if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`; }
        for (let i = start; i <= end; i++) { html += `<li class="page-item ${i===current?'active':''}"><a class="page-link" href="#" onclick="changePage(${i});return false;">${i}</a></li>`; }
        if (end < last) { if (end < last-1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`; html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${last});return false;">${last}</a></li>`; }
        html += `<li class="page-item ${current>=last?'disabled':''}"><a class="page-link" href="#" onclick="changePage(${current+1});return false;">Next</a></li>`;
        document.getElementById('paginationControls').innerHTML = html;
    }

    function changePage(page) { if (page < 1 || page > lastPage) return; currentPage = page; loadReviews(); }

    // ── Helpers ───────────────────────────────────────────────
    function showToast(message, type = 'success') { alert((type === 'success' ? '✅ ' : '❌ ') + message); }
    function showError(message) {
        document.getElementById('reviewsList').innerHTML = `<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>${message}</p><button class="btn btn-primary" onclick="refreshReviews()">Try Again</button></div>`;
    }
    function escapeHtml(text) { if (!text) return ''; const d = document.createElement('div'); d.textContent = text; return d.innerHTML; }
</script>
</body>
</html>