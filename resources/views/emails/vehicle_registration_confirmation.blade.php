<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Registration Confirmation</title>
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
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .vehicle-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .vehicle-details h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            width: 140px;
            color: #6c757d;
        }
        .detail-value {
            flex: 1;
            color: #2c3e50;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #f39c12;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .alert {
            background: #e8f5e9;
            border-left: 4px solid #27ae60;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert.warning {
            background: #fff3e0;
            border-left-color: #f39c12;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            font-weight: 600;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.3);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
        }
        .logo span {
            color: #3498db;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                Truck<span>Link</span>
            </div>
            <h1>Vehicle Registration Confirmation</h1>
            <p>Thank you for registering your vehicle with TruckLink</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            
            <p>We have received your vehicle registration request. Here are the details of your submitted vehicle:</p>
            
            <div class="vehicle-details">
                <h3>Vehicle Information</h3>
                <div class="detail-row">
                    <div class="detail-label">Vehicle Number:</div>
                    <div class="detail-value"><strong>{{ strtoupper($vehicle->vehicle_number) }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Vehicle Type:</div>
                    <div class="detail-value">{{ ucfirst($vehicle->vehicle_type) }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Chassis Number:</div>
                    <div class="detail-value">{{ $vehicle->chassis_number }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Weight Capacity:</div>
                    <div class="detail-value">{{ $vehicle->weight_capacity }} kg</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Can Carry:</div>
                    <div class="detail-value">{{ $vehicle->can_carry }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge">Pending Approval</span>
                    </div>
                </div>
            </div>
            
            <div class="alert warning">
                <strong>⚠️ Important:</strong> Your vehicle is currently under review by our admin team. 
                You will receive another email once your vehicle is approved.
            </div>
            
            <div class="alert">
                <strong>✅ What happens next?</strong><br>
                1. Admin will review your vehicle documents (usually within 24-48 hours)<br>
                2. You'll receive an email confirmation when approved<br>
                3. Once approved, your vehicle will appear in search results<br>
                4. You can start receiving booking requests
            </div>
            
            <p>If you have any questions or need to make changes to your vehicle information, please contact our support team.</p>
            
            <div style="text-align: center;">
                <a href="{{ $approval_url }}" class="button">Go to Dashboard</a>
            </div>
            
            <p style="margin-top: 30px;">Thank you for choosing TruckLink!</p>
            <p>Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} TruckLink. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p>For support, contact: {{ $support_email }}</p>
        </div>
    </div>
</body>
</html>