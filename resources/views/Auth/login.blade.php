<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckLink - Join Our Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --admin: #9b59b6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Navigation Bar */
        .navbar {
            background-color: var(--primary);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }


        /* Sign In Button Border - Paste this in style section */
.navbar .btn-outline-light {
    border: 2px solid rgba(255, 255, 255, 0.8) !important;
    border-radius: 8px !important;
    padding: 8px 20px !important;
    font-weight: 500 !important;
}
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white !important;
        }
        
        /* Main Container */
        .main-container {
            min-height: calc(100vh - 140px); /* Account for navbar and footer */
            display: flex;
            flex-direction: column;
        }
        
        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            width: 100%;
        }
        
        .auth-container {
            width: 100%;
            max-width: 1300px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            min-height: 700px;
            max-height: 85vh;
        }
        
        /* Left Side - Fixed/Static */
        .auth-left {
            flex: 1;
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(44, 62, 80, 0.7);
            z-index: 1;
        }
        
        .auth-left > * {
            position: relative;
            z-index: 2;
        }
        
        /* Right Side - Scrollable */
        .auth-right {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            max-height: 100%;
        }
        
        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 2.5rem;
            margin-right: 15px;
            color: var(--secondary);
        }
        
        .logo h1 {
            font-weight: 700;
            font-size: 2.2rem;
            margin: 0;
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        .auth-left-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .auth-left-content p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin-bottom: 40px;
        }
        
        .features-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .features-list i {
            color: var(--secondary);
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .stats {
            margin-top: 30px;
        }
        
        .stats h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--secondary);
        }
        
        .stats p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 0;
        }
        
        /* Auth Tabs */
        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .auth-tab {
            padding: 15px 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        
        .auth-tab.active {
            color: var(--secondary);
            border-bottom: 3px solid var(--secondary);
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .form-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            border-color: var(--secondary);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 12px 30px;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, var(--admin), #8e44ad);
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3);
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(155, 89, 182, 0.4);
        }
        
        .btn-block {
            width: 100%;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #6c757d;
        }
        
        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .social-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .social-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .btn-google {
            color: #DB4437;
        }
        
        .btn-facebook {
            color: #4267B2;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
        }
        
        .auth-footer a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .user-type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .user-type-btn {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-type-btn.active {
            border-color: var(--secondary);
            background: rgba(52, 152, 219, 0.05);
        }
        
        .user-type-btn.admin.active {
            border-color: var(--admin);
            background: rgba(155, 89, 182, 0.05);
        }
        
        .user-type-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .user-type-btn.customer i {
            color: var(--secondary);
        }
        
        .user-type-btn.provider i {
            color: var(--success);
        }
        
        .user-type-btn.admin i {
            color: var(--admin);
        }
        
        .user-type-btn h5 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .user-type-btn p {
            font-size: 0.85rem;
            color: #6c757d;
            margin: 0;
        }
        
        .admin-access-code {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: rgba(155, 89, 182, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--admin);
        }
        
        .admin-access-code p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--admin);
        }
        
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--secondary);
        }
        
        /* Error message styling */
        .error {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        }
        
        /* Success message styling */
        .success {
            color: #27ae60;
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        }
        
        /* Alert styling */
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        /* Footer Styling */
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer h5 {
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer h5:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--secondary);
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.6);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .auth-container {
                flex-direction: column;
                max-width: 600px;
                max-height: none;
            }
            
            .auth-left {
                padding: 40px 30px;
                background-attachment: scroll;
            }
            
            .auth-right {
                padding: 40px 30px;
                overflow-y: visible;
            }
            
            .auth-left-content h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .auth-tabs {
                flex-direction: column;
            }
            
            .auth-tab {
                text-align: center;
            }
            
            .social-login {
                flex-direction: column;
            }
            
            .user-type-selector {
                flex-direction: column;
            }
            
            .auth-left-content h2 {
                font-size: 1.8rem;
            }
            
            .stats h3 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .auth-left {
                padding: 30px 20px;
            }
            
            .auth-right {
                padding: 30px 20px;
            }
            
            .auth-left-content h2 {
                font-size: 1.6rem;
            }
            
            .logo h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar from Landing Page -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-truck-moving me-2"></i>TruckLink
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light" href="#login-form" onclick="switchToLogin()">Sign In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-container">
        <div class="auth-wrapper">
            <div class="auth-container">
                <!-- Left Side - Static Branding & Info -->
                <div class="auth-left">
                    <div class="logo">
                        <i class="fas fa-truck-moving"></i>
                        <h1>Truck<span>Link</span></h1>
                    </div>
                    
                    <div class="auth-left-content">
                        <h2>Join Pakistan's Premier Logistics Platform</h2>
                        <p>Connect directly with verified vehicle owners for fast, secure, and transparent goods transportation services.</p>
                        
                        <ul class="features-list">
                            <li>
                                <i class="fas fa-shield-alt"></i>
                                <span>Verified Service Providers & Customers</span>
                            </li>
                            <li>
                                <i class="fas fa-calculator"></i>
                                <span>Automated Fare Calculation</span>
                            </li>
                            <li>
                                <i class="fas fa-map-marked-alt"></i>
                                <span>Real-Time Shipment Tracking</span>
                            </li>
                            <li>
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Flexible Payment Options</span>
                            </li>
                            <li>
                                <i class="fas fa-robot"></i>
                                <span>AI-Powered Assistance</span>
                            </li>
                        </ul>
                        
                        <div class="stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3>500+</h3>
                                    <p>Verified Vehicles</p>
                                </div>
                                <div class="col-4">
                                    <h3>2,000+</h3>
                                    <p>Successful Deliveries</p>
                                </div>
                                <div class="col-4">
                                    <h3>15+</h3>
                                    <p>Cities Served</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Scrollable Forms -->
                <div class="auth-right">
                    <div class="auth-tabs">
                        <div class="auth-tab active" data-tab="register">Register</div>
                        <div class="auth-tab" data-tab="login">Login</div>
                    </div>
                    
                    <!-- Registration Form -->
                    <div class="auth-form active" id="register-form">
                        <h2 class="form-title">Create Your Account</h2>
                        <p class="form-subtitle">Join thousands of users on Pakistan's leading logistics platform</p>
                        
                        <!-- Display errors if any -->
                        <div id="registerErrors" class="alert alert-danger d-none">
                            <ul class="mb-0" id="registerErrorsList"></ul>
                        </div>
                        
                        <!-- Display success message if any -->
                        <div id="registerSuccess" class="alert alert-success d-none"></div>
                        
                        <!-- Display Laravel validation errors -->
                        @if($errors->any() && old('_token') && request()->routeIs('user.register'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(session('success') && session('form_type') == 'register')
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="user-type-selector">
                            <div class="user-type-btn customer active" data-type="customer">
                                <i class="fas fa-user"></i>
                                <h5>Customer</h5>
                                <p>I want to transport goods</p>
                            </div>
                            <div class="user-type-btn provider" data-type="provider">
                                <i class="fas fa-truck"></i>
                                <h5>Service Provider</h5>
                                <p>I own a vehicle</p>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('user.register') }}" id="registrationForm">
                            @csrf
                            <input type="hidden" name="role" id="selectedRole" value="customer">
                            
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-user-circle"></i> Personal Information</h4>
                                <div class="form-group">
                                    <label class="form-label" for="name">Full Name</label>
                                    <div class="input-with-icon">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" value="{{ (session('form_type') == 'register' && $errors->any()) ? old('name') : '' }}" required>
                                        <i class="fas fa-user input-icon"></i>
                                    </div>
                                    <div class="error" id="nameError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="email">Email Address</label>
                                    <div class="input-with-icon">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="{{ (session('form_type') == 'register' && $errors->any()) ? old('email') : '' }}" required>
                                        <i class="fas fa-envelope input-icon"></i>
                                    </div>
                                    <div class="error" id="emailError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="cnic">CNIC Number</label>
                                    <div class="input-with-icon">
                                        <input type="text" class="form-control" id="cnic" name="cnic" placeholder="XXXXX-XXXXXXX-X" value="{{ (session('form_type') == 'register' && $errors->any()) ? old('cnic') : '' }}" required>
                                        <i class="fas fa-id-card input-icon"></i>
                                    </div>
                                    <div class="error" id="cnicError"></div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4 class="section-title"><i class="fas fa-lock"></i> Account Security</h4>
                                <div class="form-group">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-with-icon">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                                        <i class="fas fa-lock input-icon"></i>
                                    </div>
                                    <div class="error" id="passwordError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                                    <div class="input-with-icon">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                                        <i class="fas fa-lock input-icon"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required {{ (session('form_type') == 'register' && $errors->any() && old('terms')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block mt-4" id="registerBtn">Create Account</button>
                        </form>
                        
                        <div class="divider">
                            <span>Or register with</span>
                        </div>
                        
                        <div class="social-login">
                            <button class="social-btn btn-google" type="button">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button class="social-btn btn-facebook" type="button">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                        </div>
                        
                        <div class="auth-footer">
                            Already have an account? <a href="#" class="switch-to-login">Login here</a>
                        </div>
                    </div>
                    
                    <!-- Login Form -->
                    <div class="auth-form" id="login-form">
                        <h2 class="form-title">Welcome Back</h2>
                        <p class="form-subtitle">Sign in to your TruckLink account</p>
                        
                        <!-- Display errors if any -->
                        <div id="loginErrors" class="alert alert-danger d-none">
                            <ul class="mb-0" id="loginErrorsList"></ul>
                        </div>
                        
                        <div id="loginError" class="alert alert-danger d-none"></div>
                        
                        <!-- Display Laravel validation errors -->
                        @if($errors->any() && old('_token') && request()->routeIs('user.login'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(session('error') && session('form_type') == 'login')
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('user.login') }}" id="loginForm">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="loginEmail">Email Address</label>
                                <div class="input-with-icon">
                                    <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email" value="{{ (session('form_type') == 'login' && $errors->any()) ? old('email') : '' }}" required>
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                <div class="error" id="loginEmailError"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="loginPassword">Password</label>
                                <div class="input-with-icon">
                                    <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                                    <i class="fas fa-lock input-icon"></i>
                                </div>
                                <div class="error" id="loginPasswordError"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" {{ (session('form_type') == 'login' && $errors->any() && old('remember')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rememberMe">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-decoration-none">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block" id="loginBtn">Sign In</button>
                        </form>
                        
                        <div class="divider">
                            <span>Or login with</span>
                        </div>
                        
                        <div class="social-login">
                            <button class="social-btn btn-google" type="button">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button class="social-btn btn-facebook" type="button">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                        </div>
                        
                        <div class="auth-footer">
                            Don't have an account? <a href="#" class="switch-to-register">Register here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer from Landing Page -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-truck-moving me-2"></i>TruckLink</h5>
                    <p>Pakistan's premier digital logistics platform connecting customers directly with verified vehicle owners for secure and transparent goods transportation.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Services</h5>
                    <ul class="footer-links">
                        <li><a href="#">Goods Transportation</a></li>
                        <li><a href="#">Vehicle Rental</a></li>
                        <li><a href="#">Corporate Logistics</a></li>
                        <li><a href="#">Fleet Management</a></li>
                        <li><a href="#">Route Optimization</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 id="contact">Contact Us</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Karachi, Pakistan</li>
                        <li><i class="fas fa-phone me-2"></i> +92 300 1234567</li>
                        <li><i class="fas fa-envelope me-2"></i> info@trucklink.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 TruckLink: Verified Goods. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Tab Switching
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.auth-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all forms
                document.querySelectorAll('.auth-form').forEach(form => {
                    form.classList.remove('active');
                });
                
                // Show corresponding form
                const tabId = this.getAttribute('data-tab');
                document.getElementById(`${tabId}-form`).classList.add('active');
                
                // Clear all form fields when switching tabs
                clearFormFields();
            });
        });
        
        // User Type Selection
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.user-type-btn').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get selected user type
                const userType = this.getAttribute('data-type');
                const selectedRole = document.getElementById('selectedRole');
                
                // Update the hidden role field
                selectedRole.value = userType;
            });
        });
        
        // Switch between login and register
        document.querySelectorAll('.switch-to-login').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                clearFormFields();
                document.querySelector('.auth-tab[data-tab="login"]').click();
            });
        });
        
        document.querySelectorAll('.switch-to-register').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                clearFormFields();
                document.querySelector('.auth-tab[data-tab="register"]').click();
            });
        });
        
        // Function for navbar sign in button
        function switchToLogin() {
            clearFormFields();
            document.querySelector('.auth-tab[data-tab="login"]').click();
        }
        
        // Function to clear all form fields
        function clearFormFields() {
            // Clear registration form fields
            document.getElementById('registrationForm').reset();
            document.getElementById('selectedRole').value = 'customer';
            
            // Reset user type selector
            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.user-type-btn.customer').classList.add('active');
            
            // Clear login form fields
            document.getElementById('loginForm').reset();
            
            // Clear error messages
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('registerErrors').classList.add('d-none');
            document.getElementById('registerSuccess').classList.add('d-none');
            document.getElementById('loginErrors').classList.add('d-none');
            document.getElementById('loginError').classList.add('d-none');
            
            // Clear Laravel alert messages
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-danger') || alert.classList.contains('alert-success')) {
                    alert.remove();
                }
            });
        }
        
        // Auto-switch to appropriate tab based on Laravel errors
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any() && old('_token'))
                @if(request()->routeIs('user.register'))
                    document.querySelector('.auth-tab[data-tab="register"]').click();
                @elseif(request()->routeIs('user.login'))
                    document.querySelector('.auth-tab[data-tab="login"]').click();
                @endif
            @endif
            
            @if(session('success') && session('form_type') == 'register')
                document.querySelector('.auth-tab[data-tab="register"]').click();
                // Auto-clear form fields after successful registration
                setTimeout(() => {
                    clearFormFields();
                }, 100);
            @endif
            
            @if(session('error') && session('form_type') == 'login')
                document.querySelector('.auth-tab[data-tab="login"]').click();
            @endif
            
            // Clear form fields if coming from a fresh page load (not from validation errors)
            @if(!$errors->any() && !session('success') && !session('error'))
                clearFormFields();
            @endif
        });
        
        // Client-side validation for registration
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('registerErrors').classList.add('d-none');
            document.getElementById('registerSuccess').classList.add('d-none');
            
            let hasErrors = false;
            const errors = [];
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const cnic = document.getElementById('cnic').value;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;
            
            if (!name || name.length < 3) {
                document.getElementById('nameError').textContent = 'Please enter a valid name (min 3 characters)';
                hasErrors = true;
                errors.push('Please enter a valid name');
            }
            
            if (!email || !email.includes('@')) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address';
                hasErrors = true;
                errors.push('Please enter a valid email address');
            }
            
            if (!cnic || cnic.length < 13) {
                document.getElementById('cnicError').textContent = 'Please enter a valid CNIC number (min 13 characters)';
                hasErrors = true;
                errors.push('Please enter a valid CNIC number');
            }
            
            if (!password || password.length < 8) {
                document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long';
                hasErrors = true;
                errors.push('Password must be at least 8 characters long');
            }
            
            if (password !== passwordConfirm) {
                document.getElementById('passwordError').textContent = 'Passwords do not match';
                hasErrors = true;
                errors.push('Passwords do not match');
            }
            
            if (!terms) {
                document.getElementById('passwordError').textContent = 'Please agree to the Terms and Conditions';
                hasErrors = true;
                errors.push('Please agree to the Terms of Service and Privacy Policy');
            }
            
            if (hasErrors) {
                e.preventDefault();
                // Show errors in alert box
                const errorsList = document.getElementById('registerErrorsList');
                errorsList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorsList.appendChild(li);
                });
                document.getElementById('registerErrors').classList.remove('d-none');
            }
        });
        
        // Client-side validation for login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('loginErrors').classList.add('d-none');
            document.getElementById('loginError').classList.add('d-none');
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            let hasErrors = false;
            const errors = [];
            
            if (!email || !email.includes('@')) {
                document.getElementById('loginEmailError').textContent = 'Please enter a valid email address';
                hasErrors = true;
                errors.push('Please enter a valid email address');
            }
            
            if (!password || password.length < 1) {
                document.getElementById('loginPasswordError').textContent = 'Please enter your password';
                hasErrors = true;
                errors.push('Please enter your password');
            }
            
            if (hasErrors) {
                e.preventDefault();
                // Show errors in alert box
                const errorsList = document.getElementById('loginErrorsList');
                errorsList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorsList.appendChild(li);
                });
                document.getElementById('loginErrors').classList.remove('d-none');
            }
        });
        
        // Social login buttons (demo functionality)
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Social login functionality would be implemented here. For now, please use the form above.');
            });
        });
        
        // Clear form fields when clicking on any tab
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Small delay to ensure the form is visible before clearing
                setTimeout(() => {
                    clearFormFields();
                }, 50);
            });
        });
        
        // Clear form when page is loaded fresh
        window.addEventListener('load', function() {
            // Check if this is a fresh load (not a redirect with validation errors)
            if (!window.performance.getEntriesByType("navigation")[0].type === "reload") {
                clearFormFields();
            }
        });
    </script>
</body>
</html>