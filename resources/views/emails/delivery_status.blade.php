<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Status Update - TruckLink</title>
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
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .logo span { color: #3498db; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; margin-bottom: 20px; }
        .status-card {
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .status-dispatched { background: linear-gradient(135deg, #cfe2ff 0%, #b8d4ff 100%); border: 2px solid #0d6efd; }
        .status-transit { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid #ffc107; }
        .status-delivered { background: linear-gradient(135deg, #d1e7dd 0%, #b8dfc8 100%); border: 2px solid #198754; }
        .status-icon { font-size: 64px; margin-bottom: 15px; }
        .status-title { font-size: 28px; font-weight: 700; margin: 10px 0; }
        .status-dispatched .status-title { color: #0d6efd; }
        .status-transit .status-title { color: #ffc107; }
        .status-delivered .status-title { color: #198754; }
        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .booking-details h3 {
            color: #2c3e50;
            margin: 0 0 20px 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label { width: 140px; font-weight: 600; color: #6c757d; }
        .detail-value { flex: 1; color: #2c3e50; }
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3498db, #2980b9);
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
            <h1>Delivery Status Update</h1>
            <p>Your shipment status has been updated</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $recipient->name }},</strong>
            </div>
            
            @if($status === 'vehicle_dispatched')
                <div class="status-card status-dispatched">
                    <div class="status-icon">🚚</div>
                    <div class="status-title">Vehicle Dispatched!</div>
                    <p style="font-size: 18px; margin: 10px 0;">Your vehicle has been dispatched from the pickup location</p>
                </div>
                <p>Great news! The vehicle has been dispatched and is on its way to pick up your goods.</p>
                
            @elseif($status === 'in_transit')
                <div class="status-card status-transit">
                    <div class="status-icon">🚛</div>
                    <div class="status-title">In Transit!</div>
                    <p style="font-size: 18px; margin: 10px 0;">Your shipment is now in transit to the destination</p>
                </div>
                <p>Your goods are currently in transit and on their way to the delivery location.</p>
                
            @elseif($status === 'delivered')
                <div class="status-card status-delivered">
                    <div class="status-icon">✅</div>
                    <div class="status-title">Delivered Successfully!</div>
                    <p style="font-size: 18px; margin: 10px 0;">Your shipment has been delivered</p>
                </div>
                <p>Congratulations! Your shipment has been successfully delivered to the destination.</p>
            @endif
            
            <div class="booking-details">
                <h3>📋 Booking Details</h3>
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
                    <div class="detail-value">{{ $booking->booking_date ? $booking->booking_date->format('F d, Y') : 'N/A' }}</div>
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
                    <div class="detail-label">Fare:</div>
                    <div class="detail-value"><strong style="color: #28a745;">Rs {{ number_format($booking->estimated_fare ?? 0) }}</strong></div>
                </div>
                
                @if($status === 'vehicle_dispatched' && $booking->dispatched_at)
                <div class="detail-row">
                    <div class="detail-label">Dispatched At:</div>
                    <div class="detail-value">{{ $booking->dispatched_at->format('F d, Y h:i A') }}</div>
                </div>
                @endif
                
                @if($status === 'in_transit' && $booking->in_transit_at)
                <div class="detail-row">
                    <div class="detail-label">In Transit Since:</div>
                    <div class="detail-value">{{ $booking->in_transit_at->format('F d, Y h:i A') }}</div>
                </div>
                @endif
                
                @if($status === 'delivered' && $booking->delivered_at)
                <div class="detail-row">
                    <div class="detail-label">Delivered At:</div>
                    <div class="detail-value">{{ $booking->delivered_at->format('F d, Y h:i A') }}</div>
                </div>
                @endif
            </div>
            
            @if($recipient->role === 'customer')
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
            
            @if($status === 'delivered')
                <div class="info-box">
                    <h3 style="color: #198754; margin: 0 0 10px 0;">✨ Thank You for Choosing TruckLink!</h3>
                    <p>We hope you had a great experience. Please consider leaving a review for the service provider.</p>
                </div>
            @else
                <div class="info-box">
                    <h3 style="color: #3498db; margin: 0 0 10px 0;">📱 Track Your Shipment</h3>
                    <p>You can track your shipment in real-time by logging into your dashboard.</p>
                </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ $dashboard_url }}" class="button">
                    <i class="fas fa-tachometer-alt"></i> Track Your Order
                </a>
            </div>
            
            <p style="margin-top: 30px;">Thank you for choosing TruckLink for your transportation needs!</p>
            <p>Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} TruckLink. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>