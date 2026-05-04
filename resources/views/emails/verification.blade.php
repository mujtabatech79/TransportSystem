<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    
    <p>Thank you for registering with TruckLink!</p>
    
    <p>Please click the link below to verify your email address:</p>
    
    <p>
        <a href="{{ $verificationUrl }}" style="
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        ">
            Verify Email Address
        </a>
    </p>
    
    <p>Or copy and paste this link in your browser:</p>
    <p>{{ $verificationUrl }}</p>
    
    <p>If you did not create an account, no further action is required.</p>
    
    <p>Best regards,<br>TruckLink Team</p>
</body>
</html>