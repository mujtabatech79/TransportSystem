{{-- resources/views/payment/failure.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - TruckLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .failure-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .failure-icon {
            width: 80px;
            height: 80px;
            background: #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .failure-icon i {
            font-size: 40px;
            color: white;
        }
        
        .btn-retry {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(231, 76, 60, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="failure-card">
            <div class="failure-icon">
                <i class="fas fa-times"></i>
            </div>
            
            <h2 class="mb-2">Payment Failed!</h2>
            <p class="text-muted">We couldn't process your payment</p>
            
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $payment->payment_response['error'] ?? 'Transaction could not be completed. Please try again.' }}
            </div>
            
            <div class="mt-4">
                <a href="{{ route('payment.retry', $booking->id) }}" class="btn btn-danger btn-retry">
                    <i class="fas fa-sync-alt me-2"></i> Try Again
                </a>
                <a href="{{ route('customer.login') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-home me-2"></i> Go to Dashboard
                </a>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-credit-card me-1"></i>
                    Need help? Contact our support team.
                </small>
            </div>
        </div>
    </div>
</body>
</html>