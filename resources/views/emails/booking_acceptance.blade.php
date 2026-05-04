<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - TruckLink</title>
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
            background: linear-gradient(135deg, #27ae60 0%, #219955 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .logo span { color: #f39c12; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; margin-bottom: 20px; }
        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid #e9ecef;
        }
        .booking-details h3 {
            color: #27ae60;
            margin: 0 0 20px 0;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            display: inline-block;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            width: 140px;
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value { flex: 1; color: #2c3e50; }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #27ae60, #219955);
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
            <h1>Booking Confirmed!</h1>
            <p>Your booking has been accepted by the service provider</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $user->name }},</strong>
            </div>
            
            <p>Great news! Your booking request has been <strong style="color: #27ae60;">ACCEPTED</strong> by the service provider.</p>
            
            <div class="booking-details">
                <h3>📋 Booking Details</h3>
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><strong>#TL-{{ $booking->id }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value">{{ $booking->booking_date->format('F d, Y') }}</div>
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
                    <div class="detail-label">Goods Type:</div>
                    <div class="detail-value">{{ $booking->goods_type ?? 'N/A' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Weight:</div>
                    <div class="detail-value">{{ $booking->goods_weight ?? 'N/A' }} kg</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Estimated Fare:</div>
                    <div class="detail-value"><strong style="color: #27ae60;">Rs {{ number_format($booking->estimated_fare ?? 0) }}</strong></div>
                </div>
            </div>
            
            @if($type === 'customer')
                <div class="booking-details">
                    <h3>🚚 Service Provider Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Provider Name:</div>
                        <div class="detail-value"><strong>{{ $booking->vehicle->user->name ?? 'N/A' }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Vehicle Type:</div>
                        <div class="detail-value">{{ $booking->vehicle->vehicle_type ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Vehicle Number:</div>
                        <div class="detail-value">{{ $booking->vehicle->vehicle_number ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Contact:</div>
                        <div class="detail-value">{{ $booking->vehicle->user->mobile ?? $booking->vehicle->user->email ?? 'N/A' }}</div>
                    </div>
                </div>
            @else
                <div class="booking-details">
                    <h3>👤 Customer Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Customer Name:</div>
                        <div class="detail-value"><strong>{{ $booking->customer->name ?? 'N/A' }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Contact:</div>
                        <div class="detail-value">{{ $booking->customer->email ?? 'N/A' }} | {{ $booking->customer->mobile ?? 'N/A' }}</div>
                    </div>
                </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ $dashboard_url }}" class="button">View Booking Details</a>
            </div>
            
            <p>Thank you for choosing TruckLink!</p>
            <p>Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} TruckLink. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>