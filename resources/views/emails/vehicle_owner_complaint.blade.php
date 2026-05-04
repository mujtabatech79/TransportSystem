<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .complaint-box { background: #fff3f0; border-left: 4px solid #e74c3c; padding: 15px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>TruckLink - Complaint Notification</h2>
        </div>
        <div class="content">
            <p>Dear Vehicle Owner,</p>
            <p>A complaint has been filed against your vehicle regarding booking <strong>#{{ $booking->id ?? 'N/A' }}</strong>.</p>
            
            <div class="complaint-box">
                <h3>Complaint Details:</h3>
                <p><strong>Complaint ID:</strong> #{{ $complaint->id }}</p>
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $complaint->complaint_type)) }}</p>
                <p><strong>Subject:</strong> {{ $complaint->subject }}</p>
                <p><strong>Description:</strong></p>
                <p>{{ $complaint->description }}</p>
                <p><strong>Filed By:</strong> {{ $customer->name ?? 'Customer' }}</p>
                <p><strong>Filed On:</strong> {{ $complaint->created_at->format('F d, Y h:i A') }}</p>
            </div>
            
            <p>Please review this complaint. The admin will investigate and provide a resolution.</p>
            
            <p>If you have any questions, please contact support.</p>
            
            <p>Thank you,<br>TruckLink Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} TruckLink. All rights reserved.</p>
            <p>This is an automated notification, please do not reply.</p>
        </div>
    </div>
</body>
</html>