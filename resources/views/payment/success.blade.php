{{-- resources/views/payment/success.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Successful - TruckLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1%, transparent 1%);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes moveBackground {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }
        
        /* Floating Particles */
        .particle {
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .success-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 0;
            text-align: center;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 300% 100%;
            animation: gradientShift 3s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header Section */
        .card-header-section {
            padding: 40px 40px 20px 40px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        }
        
        .sandbox-badge {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(133, 100, 4, 0.2);
            letter-spacing: 0.5px;
        }
        
        .sandbox-badge i {
            font-size: 0.85rem;
        }
        
        .success-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px -5px rgba(39, 174, 96, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 25px -5px rgba(39, 174, 96, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 35px -5px rgba(39, 174, 96, 0.5);
            }
        }
        
        .success-icon i {
            font-size: 45px;
            color: white;
        }
        
        .card-header-section h2 {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .card-header-section p {
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        /* Payment Details Section */
        .payment-details-section {
            padding: 0 40px;
        }
        
        .section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: #667eea;
        }
        
        .payment-details {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-label i {
            width: 20px;
            color: #667eea;
            font-size: 0.9rem;
        }
        
        .detail-value {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }
        
        .detail-value.amount {
            font-size: 1.1rem;
            color: #27ae60;
            font-weight: 700;
        }
        
        /* Alert Message */
        .alert-custom {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: none;
            border-radius: 16px;
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #155724;
            font-size: 0.9rem;
        }
        
        .alert-custom i {
            font-size: 1.2rem;
        }
        
        /* Buttons Section */
        .buttons-section {
            padding: 0 40px 30px 40px;
        }
        
        .btn-custom {
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: #6c757d;
            border: 2px solid #e0e0e0;
        }
        
        .btn-secondary-custom:hover {
            background: #f8f9fa;
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }
        
        /* Footer Section */
        .card-footer-section {
            padding: 20px 40px;
            background: #f8f9fa;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .card-footer-section small {
            font-size: 0.75rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .success-card {
                margin: 20px;
            }
            
            .card-header-section,
            .payment-details-section,
            .buttons-section,
            .card-footer-section {
                padding-left: 25px;
                padding-right: 25px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Loading Animation for Print */
        @media print {
            body::before,
            .particle {
                display: none;
            }
            
            .success-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .btn-custom {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Particles -->
    <div class="particle" style="width: 3px; height: 3px; top: 10%; left: 20%; animation-duration: 8s;"></div>
    <div class="particle" style="width: 4px; height: 4px; top: 70%; left: 85%; animation-duration: 6s;"></div>
    <div class="particle" style="width: 2px; height: 2px; top: 40%; left: 10%; animation-duration: 10s;"></div>
    <div class="particle" style="width: 5px; height: 5px; top: 85%; left: 25%; animation-duration: 7s;"></div>
    <div class="particle" style="width: 3px; height: 3px; top: 15%; left: 75%; animation-duration: 9s;"></div>

    <div class="container">
        <div class="success-card">
            <!-- Header Section -->
            <div class="card-header-section">
                <div class="sandbox-badge">
                    <i class="fas fa-flask"></i> 
                    <span>SANDBOX MODE - Demo Payment</span>
                </div>
                
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                
                <h2>Payment Successful!</h2>
                <p>Your booking has been confirmed</p>
            </div>
            
            <!-- Payment Details Section -->
            <div class="payment-details-section">
                <div class="section-title">
                    <i class="fas fa-receipt"></i>
                    <span>PAYMENT DETAILS</span>
                </div>
                
                <div class="payment-details">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-hashtag"></i>
                            <span>Booking ID</span>
                        </div>
                        <div class="detail-value">#{{ $booking->id }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-qrcode"></i>
                            <span>Transaction ID</span>
                        </div>
                        <div class="detail-value">{{ $payment->transaction_id ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Amount Paid</span>
                        </div>
                        <div class="detail-value amount">Rs {{ number_format($payment->amount) }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment Method</span>
                        </div>
                        <div class="detail-value">{{ ucfirst($payment->payment_method) }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Payment Date</span>
                        </div>
                        <div class="detail-value">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    </div>
                    
                    @if($payment->card_number_masked)
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-id-card"></i>
                            <span>Card Used</span>
                        </div>
                        <div class="detail-value">{{ $payment->card_number_masked }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Alert Section -->
            <div class="payment-details-section">
                <div class="alert-custom">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <span>Your booking request has been sent to the provider. You will receive a confirmation soon.</span>
                </div>
            </div>
            
            <!-- Buttons Section -->
            <div class="buttons-section">
                <a href="{{ route('customer.login') }}" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Go to Dashboard</span>
                </a>
                <a href="{{ route('customer.bookings') }}" class="btn btn-secondary-custom btn-custom">
                    <i class="fas fa-list"></i>
                    <span>View My Bookings</span>
                </a>
            </div>
            
            <!-- Footer Section -->
            <div class="card-footer-section">
                <small>
                    <i class="fas fa-envelope"></i>
                    A confirmation email has been sent to your registered email address.
                </small>
            </div>
        </div>
    </div>
</body>
</html>