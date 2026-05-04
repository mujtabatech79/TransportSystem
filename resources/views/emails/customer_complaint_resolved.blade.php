<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Resolved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #27ae60; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .resolution-box { background: #e8f5e9; border-left: 4px solid #27ae60; padding: 15px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Complaint Resolved</h2>
        </div>
        <div class="content">
            <p>Dear Customer,</p>
            <p>We are pleased to inform you that your complaint regarding booking <strong>#{{ $booking->id ?? 'N/A' }}</strong> has been resolved.</p>
            
            <div class="resolution-box">
                <h3>Resolution Details:</h3>
                <p><strong>Complaint ID:</strong> #{{ $complaint->id }}</p>
                <p><strong>Subject:</strong> {{ $complaint->subject }}</p>
                <p><strong>Admin Response:</strong></p>
                <p>{{ $admin_response }}</p>
                <p><strong>Resolved On:</strong> {{ $complaint->resolved_at ? $complaint->resolved_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}</p>
            </div>
            
            <p>We appreciate your patience and trust in TruckLink. If you have any further concerns, please don't hesitate to contact us.</p>
            
            <p>Thank you for choosing TruckLink!</p>
            
            <p>Best regards,<br>TruckLink Support Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} TruckLink. All rights reserved.</p>
        </div>
    </div>
</body>
</html>