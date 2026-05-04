<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Update - TruckLink</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo { font-size: 32px; font-weight: bold; margin-bottom: 10px; }
        .logo span { color: #f39c12; }
        .content { padding: 30px; }
        .rejection-box {
            background: #fff3f0;
            border-left: 4px solid #e74c3c;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .rejection-box p { margin: 0; color: #721c24; }
        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label { width: 130px; font-weight: 600; color: #6c757d; }
        .detail-value { flex: 1; }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        @media (max-width: 600px) {
            .detail-row { flex-direction: column; }
            .detail-label { width: 100%; margin-bottom: 5px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Truck<span>Link</span></div>
            <h1>Booking Request Update</h1>
            <p>Your booking request status has been updated</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $user->name }},</strong>
            </div>
            
            <p>Your booking request has been <strong style="color: #e74c3c;">REJECTED</strong> by the service provider.</p>
            
            <div class="rejection-box">
                <strong>❌ Rejection Reason:</strong>
                <p>{{ $rejectionReason ?? 'No specific reason provided. Please contact support for more details.' }}</p>
            </div>
            
            <div class="booking-details">
                <h3>📋 Booking Information</h3>
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><strong>#TL-{{ $booking->id }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Pickup Location:</div>
                    <div class="detail-value">{{ $booking->pickup_location }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Drop Location:</div>
                    <div class="detail-value">{{ $booking->dropoff_location }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value">{{ $booking->booking_date->format('F d, Y') }}</div>
                </div>
            </div>
            
            @if($user->role === 'customer')
                <div class="alert-box" style="background: #fff3e0; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <strong>📝 What you can do:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>Try booking a different vehicle</li>
                        <li>Contact support for assistance</li>
                        <li>Review your booking details and try again</li>
                    </ul>
                </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ $dashboard_url }}" class="button">View All Bookings</a>
            </div>
            
            <p>Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} TruckLink. All rights reserved.</p>
        </div>
    </div>
</body>
</html>