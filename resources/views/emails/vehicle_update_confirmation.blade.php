<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Update Confirmation</title>
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
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .vehicle-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #f39c12;
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
        .alert {
            background: #fff3e0;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            font-weight: 600;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vehicle Update Confirmation</h1>
            <p>Your vehicle information has been updated</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            
            <p>Your vehicle information has been successfully updated and is now pending admin approval.</p>
            
            <div class="vehicle-details">
                <h3>Updated Vehicle Details:</h3>
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
            </div>
            
            <div class="alert">
                <strong>⚠️ Note:</strong> Your vehicle is now under review by our admin team. The status will be set to "pending" until approved. You will receive another email once your vehicle is approved.
            </div>
            
            <p>If you have any questions, please contact our support team.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('my.vehicle') }}" class="button">View My Vehicles</a>
            </div>
            
            <p style="margin-top: 30px;">Thank you for using TruckLink!</p>
            <p>Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} TruckLink. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>