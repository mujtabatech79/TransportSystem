<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Registration Status - TruckLink</title>
    <style>
        body {
            font-family: 'Arial', 'Segoe UI', sans-serif;
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
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .logo span {
            color: #3498db;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #2c3e50;
        }
        .status-card {
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .status-approved {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
        }
        .status-rejected {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
        }
        .status-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .status-title {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }
        .status-approved .status-title {
            color: #28a745;
        }
        .status-rejected .status-title {
            color: #dc3545;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
        }
        .badge-approved {
            background: #28a745;
            color: white;
        }
        .badge-rejected {
            background: #dc3545;
            color: white;
        }
        .vehicle-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid #e9ecef;
        }
        .vehicle-details h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }
        .detail-table {
            width: 100%;
            margin-top: 15px;
        }
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            width: 140px;
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            flex: 1;
            color: #2c3e50;
            font-weight: 500;
        }
        .detail-value strong {
            color: #3498db;
        }
        .rejection-box {
            background: #fff3f0;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .rejection-box h3 {
            color: #dc3545;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .rejection-box p {
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            color: #721c24;
        }
        .alert-box {
            background: #fff3e0;
            border-left: 4px solid #f39c12;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .alert-box h3 {
            color: #f39c12;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .alert-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        .alert-box li {
            margin: 8px 0;
            color: #856404;
        }
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .info-box h3 {
            color: #3498db;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .info-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        .info-box li {
            margin: 8px 0;
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            margin: 20px 0;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52,152,219,0.3);
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52,152,219,0.4);
        }
        .footer {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
        }
        .support-link {
            color: #3498db;
            text-decoration: none;
        }
        .support-link:hover {
            text-decoration: underline;
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
            .status-title {
                font-size: 24px;
            }
            .status-icon {
                font-size: 48px;
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
            <h1>Vehicle Registration Status</h1>
            <p>Your vehicle registration request has been reviewed</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $user->name }},</strong>
            </div>
            
            @if($status === 'approved')
                <!-- Approved Status Card -->
                <div class="status-card status-approved">
                    <div class="status-icon">✅</div>
                    <div class="status-title">Congratulations!</div>
                    <p style="font-size: 18px; margin: 10px 0;">Your vehicle has been approved and verified!</p>
                    <span class="status-badge badge-approved">
                        <i class="fas fa-check-circle"></i> APPROVED
                    </span>
                </div>
                
                <p style="font-size: 16px;">We are pleased to inform you that your vehicle has been successfully verified and approved by our admin team. Your vehicle is now active and ready to accept bookings from customers!</p>
                
                <!-- Vehicle Details Section -->
                <div class="vehicle-details">
                    <h3>📋 Vehicle Information</h3>
                    <div class="detail-table">
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
                            <div class="detail-value"><span style="color: #28a745; font-weight: 600;">Active ✓</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- What's Next Section -->
                <div class="info-box">
                    <h3>🎉 What happens next?</h3>
                    <ul>
                        <li>Your vehicle will now appear in customer search results</li>
                        <li>You can start receiving booking requests from customers</li>
                        <li>Manage your vehicle availability from your dashboard</li>
                        <li>Track your earnings and performance metrics</li>
                        <li>Update vehicle information anytime (will require re-approval)</li>
                    </ul>
                </div>
                
                <!-- Getting Started Section -->
                <div class="info-box" style="background: #e8f5e9; border-left-color: #28a745;">
                    <h3 style="color: #28a745;">🚀 Getting Started</h3>
                    <ul>
                        <li>Log in to your provider dashboard to manage bookings</li>
                        <li>Set your vehicle availability status</li>
                        <li>Review and respond to booking requests promptly</li>
                        <li>Maintain a high rating by providing excellent service</li>
                    </ul>
                </div>
                
            @else
                <!-- Rejected Status Card -->
                <div class="status-card status-rejected">
                    <div class="status-icon">⚠️</div>
                    <div class="status-title">Status Update</div>
                    <p style="font-size: 18px; margin: 10px 0;">Your vehicle registration requires attention</p>
                    <span class="status-badge badge-rejected">
                        <i class="fas fa-times-circle"></i> REJECTED
                    </span>
                </div>
                
                <p style="font-size: 16px;">We have reviewed your vehicle registration request. Unfortunately, we are unable to approve it at this time.</p>
                
                <!-- Rejection Reason Box -->
                <div class="rejection-box">
                    <h3>❌ Rejection Reason</h3>
                    <p>{{ $rejectionReason ?? 'No specific reason provided. Please contact support for more details.' }}</p>
                </div>
                
                <!-- Vehicle Details Section -->
                <div class="vehicle-details">
                    <h3>📋 Vehicle Information</h3>
                    <div class="detail-table">
                        <div class="detail-row">
                            <div class="detail-label">Vehicle Number:</div>
                            <div class="detail-value">{{ strtoupper($vehicle->vehicle_number) }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Vehicle Type:</div>
                            <div class="detail-value">{{ ucfirst($vehicle->vehicle_type) }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Registration Date:</div>
                            <div class="detail-value">{{ $vehicle->created_at->format('F d, Y') }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value"><span style="color: #dc3545; font-weight: 600;">Rejected ✗</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps Section -->
                <div class="alert-box">
                    <h3>📝 What you can do next:</h3>
                    <ul>
                        <li>Review the rejection reason provided above carefully</li>
                        <li>Correct the issues with your vehicle registration</li>
                        <li>Ensure all documents are clear and valid</li>
                        <li>Submit a new vehicle registration with corrected information</li>
                        <li>Contact our support team if you need clarification</li>
                    </ul>
                </div>
                
                <!-- Tips for Successful Registration -->
                <div class="info-box">
                    <h3>💡 Tips for Successful Registration</h3>
                    <ul>
                        <li>Upload clear, high-quality images of your vehicle and documents</li>
                        <li>Ensure all information matches your official documents</li>
                        <li>Verify that your vehicle number and chassis number are correct</li>
                        <li>Make sure your CNIC is valid and matches your profile</li>
                        <li>Double-check that your contact information is up to date</li>
                    </ul>
                </div>
            @endif
            
            <!-- Dashboard Button -->
            <div style="text-align: center;">
                <a href="{{ $dashboard_url }}" class="button">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            </div>
            
            <!-- Support Message -->
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                <p style="margin: 0; font-size: 14px;">
                    <i class="fas fa-headset"></i> Need help? 
                    <a href="mailto:support@trucklink.com" class="support-link">Contact our support team</a>
                    for assistance.
                </p>
            </div>
            
            <p style="margin-top: 30px;">Thank you for choosing TruckLink for your transportation needs!</p>
            <p style="margin-top: 20px;">Best regards,<br><strong>TruckLink Team</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>© {{ date('Y') }} TruckLink</strong> - Verified Goods Transportation</p>
            <p>This is an automated message from TruckLink. Please do not reply to this email.</p>
            <p>For support, contact: <a href="mailto:support@trucklink.com" class="support-link">support@trucklink.com</a></p>
            <p style="margin-top: 10px; font-size: 11px;">This email contains confidential information about your vehicle registration status.</p>
        </div>
    </div>
</body>
</html>