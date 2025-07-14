<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #007BFF;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h1 {
            font-size: 36px;
            margin: 0 0 10px;
            color: #007BFF;
        }
        .content p {
            font-size: 16px;
            margin: 10px 0;
            color: #555;
        }
        .footer {
            background-color: #f4f4f9;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <strong>Your OTP Code</strong>
        </div>
        <div class="content">
            <h1>{{ $otp }}</h1>
            <p>This OTP is valid for <strong>1 minute</strong>. Please use it to verify your account.</p>
        </div>
        <div class="footer">
            If you didn’t request this code, please ignore this email.
        </div>
    </div>
</body>
</html>
