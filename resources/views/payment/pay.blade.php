{{-- resources/views/payment/pay.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Payment - TruckLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --purple: #667eea;
            --purple-dark: #764ba2;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 1%, transparent 1%);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        /* Floating Particles */
        .particle {
            position: absolute;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            pointer-events: none;
            animation: float 8s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
            50% { opacity: 0.5; }
        }
        
        .payment-container {
            max-width: 550px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .payment-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            overflow: hidden;
            transition: transform 0.3s ease;
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header Section */
        .payment-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .payment-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .payment-header i {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 60px;
            margin-bottom: 15px;
        }
        
        .payment-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .payment-header p {
            margin: 8px 0 0;
            opacity: 0.85;
            font-size: 0.9rem;
        }
        
        /* Booking Summary */
        .booking-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 25px;
            border-bottom: 1px solid #eef2f6;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .summary-item {
            background: white;
            padding: 12px;
            border-radius: 14px;
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
        }
        
        .summary-item:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .summary-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .summary-label i {
            font-size: 0.75rem;
        }
        
        .summary-value {
            font-weight: 700;
            color: #2c3e50;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Amount Display */
        .amount-display {
            text-align: center;
            padding: 25px;
            background: linear-gradient(135deg, #e8f4f8 0%, #d1ecf1 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .amount-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #6c757d;
        }
        
        .amount {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        
        .amount small {
            font-size: 1rem;
            color: #6c757d;
            -webkit-text-fill-color: #6c757d;
        }
        
        /* Payment Body */
        .payment-body {
            padding: 30px;
        }
        
        /* Payment Methods Grid */
        .methods-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }
        
        .method-btn {
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 15px 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .method-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .method-btn:hover::before {
            left: 100%;
        }
        
        .method-btn:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .method-btn.active {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }
        
        .method-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }
        
        .method-icon img {
            height: 32px;
            object-fit: contain;
        }
        
        .method-name {
            font-weight: 600;
            font-size: 0.85rem;
            margin: 0;
            color: #2c3e50;
        }
        
        /* Card Details Form */
        .card-details {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            display: none;
            border: 1px solid #eef2f6;
            animation: slideDown 0.3s ease;
        }
        
        .card-details.show {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-details h6 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Test Cards Section */
        .test-cards {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 16px;
            padding: 18px;
            margin-top: 25px;
            font-size: 0.8rem;
            border: 1px solid rgba(133, 100, 4, 0.2);
        }
        
        .test-cards i {
            color: #856404;
        }
        
        .test-cards strong {
            color: #856404;
        }
        
        .test-cards code {
            background: rgba(255,255,255,0.8);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #856404;
        }
        
        /* Pay Button */
        .btn-pay {
            background: linear-gradient(135deg, #27ae60 0%, #219a52 100%);
            border: none;
            padding: 16px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            margin-top: 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-pay::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-pay:hover::before {
            left: 100%;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.4);
        }
        
        .btn-pay:disabled {
            opacity: 0.7;
            transform: none;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }
        
        .loading-content {
            background: white;
            padding: 40px 50px;
            border-radius: 32px;
            text-align: center;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        .loading-content .spinner-border {
            width: 4rem;
            height: 4rem;
        }
        
        .sandbox-badge {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
            border: 1px solid rgba(133, 100, 4, 0.2);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .payment-container {
                max-width: 100%;
            }
            
            .payment-body {
                padding: 20px;
            }
            
            .methods-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .method-btn {
                padding: 12px 8px;
            }
            
            .method-icon {
                font-size: 1.5rem;
            }
            
            .method-icon img {
                height: 25px;
            }
            
            .amount {
                font-size: 2rem;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
        
        /* Print Styles */
        @media print {
            body::before,
            .particle,
            .btn-pay,
            .test-cards,
            .sandbox-badge,
            .loading-overlay {
                display: none;
            }
            
            .payment-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Particles -->
    <div class="particle" style="width: 4px; height: 4px; top: 15%; left: 10%; animation-duration: 7s;"></div>
    <div class="particle" style="width: 6px; height: 6px; top: 75%; left: 85%; animation-duration: 9s;"></div>
    <div class="particle" style="width: 3px; height: 3px; top: 45%; left: 15%; animation-duration: 6s;"></div>
    <div class="particle" style="width: 5px; height: 5px; top: 85%; left: 20%; animation-duration: 8s;"></div>
    <div class="particle" style="width: 3px; height: 3px; top: 25%; left: 90%; animation-duration: 10s;"></div>

    <div class="container">
        <div class="payment-container">
            <div class="payment-card">
                <div class="payment-header">
                    <i class="fas fa-lock fa-2x"></i>
                    <h3>Secure Payment</h3>
                    <p>Complete your booking payment securely</p>
                </div>
                
                <div class="booking-summary">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-label">
                                <i class="fas fa-hashtag"></i>
                                <span>BOOKING ID</span>
                            </div>
                            <p class="summary-value">#{{ $booking->id }}</p>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">
                                <i class="fas fa-truck"></i>
                                <span>VEHICLE</span>
                            </div>
                            <p class="summary-value">{{ $booking->vehicle->vehicle_number ?? 'N/A' }}</p>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>PICKUP</span>
                            </div>
                            <p class="summary-value">{{ Str::limit($booking->pickup_location, 25) }}</p>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">
                                <i class="fas fa-flag-checkered"></i>
                                <span>DROPOFF</span>
                            </div>
                            <p class="summary-value">{{ Str::limit($booking->dropoff_location, 25) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="amount-display">
                    <div class="amount-label">Total Amount to Pay</div>
                    <div class="amount">Rs {{ number_format($payment->amount) }}<small>.00</small></div>
                    <small class="text-muted">Including all taxes & fees</small>
                </div>
                
                <div class="payment-body">
                    <div class="sandbox-badge">
                        <i class="fas fa-flask"></i>
                        <span>SANDBOX MODE - Testing Only</span>
                    </div>
                    
                    <label class="form-label mb-3 d-block">Select Payment Method</label>
                    
                    <div class="methods-grid">
                        <div class="method-btn" data-method="card" onclick="selectMethod('card')">
                            <div class="method-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="method-name">Credit/Debit Card</div>
                        </div>
                        <div class="method-btn" data-method="jazzcash" onclick="selectMethod('jazzcash')">
                            <div class="method-icon">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/JazzCash_Logo.svg/120px-JazzCash_Logo.svg.png" alt="JazzCash" style="height: 30px;">
                            </div>
                            <div class="method-name">JazzCash</div>
                        </div>
                        <div class="method-btn" data-method="easypaisa" onclick="selectMethod('easypaisa')">
                            <div class="method-icon">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Easypaisa_logo.svg/120px-Easypaisa_logo.svg.png" alt="Easypaisa" style="height: 30px;">
                            </div>
                            <div class="method-name">Easypaisa</div>
                        </div>
                        <div class="method-btn" data-method="cod" onclick="selectMethod('cod')">
                            <div class="method-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="method-name">Cash on Delivery</div>
                        </div>
                    </div>
                    
                    <!-- Card Payment Form -->
                    <div id="cardForm" class="card-details">
                        <h6><i class="fas fa-credit-card me-2"></i>Card Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="card_number" placeholder="4111 1111 1111 1111" maxlength="19">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Expiry Month</label>
                                <input type="text" class="form-control" id="expiry_month" placeholder="MM" maxlength="2">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Expiry Year</label>
                                <input type="text" class="form-control" id="expiry_year" placeholder="YYYY" maxlength="4">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">CVV</label>
                            <input type="password" class="form-control" id="cvv" placeholder="123" maxlength="3">
                        </div>
                    </div>
                    
                    <!-- JazzCash Form -->
                    <div id="jazzcashForm" class="card-details">
                        <h6><i class="fas fa-mobile-alt me-2"></i>JazzCash Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Mobile Number (Registered with JazzCash)</label>
                            <input type="tel" class="form-control" id="jazzcash_phone" placeholder="03XXXXXXXXX">
                            <small class="text-muted mt-1 d-block">You will receive an OTP for verification</small>
                        </div>
                    </div>
                    
                    <!-- Easypaisa Form -->
                    <div id="easypaisaForm" class="card-details">
                        <h6><i class="fas fa-mobile-alt me-2"></i>Easypaisa Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Mobile Number (Registered with Easypaisa)</label>
                            <input type="tel" class="form-control" id="easypaisa_phone" placeholder="03XXXXXXXXX">
                        </div>
                    </div>
                    
                    <!-- COD Form -->
                    <div id="codForm" class="card-details">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You will pay in cash when the vehicle arrives at your pickup location.
                        </div>
                    </div>
                    
                    <!-- Sandbox Test Cards Info -->
                    <div class="test-cards">
                        <i class="fas fa-flask me-2"></i>
                        <strong>Sandbox Test Cards:</strong><br>
                        <span class="text-muted">✅ Success:</span> <code>4111 1111 1111 1111</code> (CVV: 123, Exp: 12/25)<br>
                        <span class="text-muted">✅ Success:</span> <code>5555 5555 5555 4444</code> (CVV: 123, Exp: 12/25)<br>
                        <span class="text-muted">❌ Failure:</span> <code>4000 0000 0000 0002</code> (Card declined)<br>
                        <span class="text-muted">⚠️ Insufficient:</span> <code>4012 8888 8888 1881</code> (Insufficient balance)
                    </div>
                    
                    <button class="btn btn-pay text-white" onclick="processPayment()" id="payBtn">
                        <i class="fas fa-lock me-2"></i> Pay Rs {{ number_format($payment->amount) }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner-border text-success mb-4" role="status" style="width: 3.5rem; height: 3.5rem;"></div>
            <h5 class="fw-bold">Processing Payment...</h5>
            <p class="text-muted mb-0">Please do not close this window</p>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-spinner fa-spin me-1"></i> Verifying transaction
                </small>
            </div>
        </div>
    </div>
    
    <script>
        let selectedMethod = 'card';
        
        function selectMethod(method) {
            selectedMethod = method;
            
            // Update active state
            document.querySelectorAll('.method-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-method="${method}"]`).classList.add('active');
            
            // Hide all forms
            document.getElementById('cardForm').classList.remove('show');
            document.getElementById('jazzcashForm').classList.remove('show');
            document.getElementById('easypaisaForm').classList.remove('show');
            document.getElementById('codForm').classList.remove('show');
            
            // Show selected form
            document.getElementById(`${method}Form`).classList.add('show');
        }
        
        // Format card number with spaces
        document.getElementById('card_number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value.substring(0, 19);
        });
        
        // Auto-focus expiry fields
        document.getElementById('expiry_month')?.addEventListener('input', function(e) {
            if (e.target.value.length === 2) {
                document.getElementById('expiry_year').focus();
            }
        });
        
        function processPayment() {
            let paymentData = {
                payment_method: selectedMethod,
                _token: '{{ csrf_token() }}'
            };
            
            // Validate based on method
            if (selectedMethod === 'card') {
                const cardNumber = document.getElementById('card_number')?.value.replace(/\s/g, '');
                const expiryMonth = document.getElementById('expiry_month')?.value;
                const expiryYear = document.getElementById('expiry_year')?.value;
                const cvv = document.getElementById('cvv')?.value;
                
                if (!cardNumber || cardNumber.length < 16) {
                    alert('Please enter a valid card number');
                    return;
                }
                if (!expiryMonth || expiryMonth.length !== 2) {
                    alert('Please enter expiry month (MM)');
                    return;
                }
                if (!expiryYear || expiryYear.length !== 4) {
                    alert('Please enter expiry year (YYYY)');
                    return;
                }
                if (!cvv || cvv.length !== 3) {
                    alert('Please enter valid CVV');
                    return;
                }
                
                paymentData.card_number = cardNumber;
                paymentData.expiry_month = expiryMonth;
                paymentData.expiry_year = expiryYear;
                paymentData.cvv = cvv;
                
            } else if (selectedMethod === 'jazzcash') {
                const phone = document.getElementById('jazzcash_phone')?.value;
                if (!phone || phone.length !== 11 || !phone.startsWith('03')) {
                    alert('Please enter a valid JazzCash mobile number (03XXXXXXXXX)');
                    return;
                }
                paymentData.phone_number = phone;
                
            } else if (selectedMethod === 'easypaisa') {
                const phone = document.getElementById('easypaisa_phone')?.value;
                if (!phone || phone.length !== 11 || !phone.startsWith('03')) {
                    alert('Please enter a valid Easypaisa mobile number (03XXXXXXXXX)');
                    return;
                }
                paymentData.phone_number = phone;
            }
            
            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            const payBtn = document.getElementById('payBtn');
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            
            // Process payment
            fetch('{{ route("payment.process", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    alert('Payment Failed: ' + data.message);
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<i class="fas fa-lock me-2"></i> Pay Rs {{ number_format($payment->amount) }}';
                }
            })
            .catch(error => {
                document.getElementById('loadingOverlay').style.display = 'none';
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-lock me-2"></i> Pay Rs {{ number_format($payment->amount) }}';
            });
        }
        
        // Set default card for testing
        document.getElementById('card_number') && (document.getElementById('card_number').value = '4111 1111 1111 1111');
        document.getElementById('expiry_month') && (document.getElementById('expiry_month').value = '12');
        document.getElementById('expiry_year') && (document.getElementById('expiry_year').value = '2025');
        document.getElementById('cvv') && (document.getElementById('cvv').value = '123');
        
        // Initialize default active method
        selectMethod('card');
    </script>
</body>
</html>