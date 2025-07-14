<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        table {
            border-spacing: 0;
            width: 100%;
        }

        td {
            padding: 0;
        }

        /* Email Container */
        .email-container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 40px;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Logo Section */
        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            width: 120px;
        }

        /* Header */
        h1 {
            font-size: 26px;
            color: #333333;
            margin-bottom: 18px;
            font-weight: bold;
        }

        /* Body Text */
        p {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        /* Reset Button */
        .button {
            display: inline-block;
            width: 240px;
            padding: 14px;
            text-align: center;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0, 123, 255, 0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .button:hover {
            background: linear-gradient(45deg, #0056b3, #004494);
            transform: translateY(-3px);
        }

        .button:active {
            transform: translateY(0);
        }

        /* Footer */
        .footer {
            font-size: 14px;
            color: #777777;
            text-align: center;
            margin-top: 30px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td align="center">
                <div class="email-container">

                    <!-- Header Section -->
                    <h1>Password Reset Request</h1>

                    <!-- Body Section -->
                    <p>We received a request to reset your password. Click the button below to proceed:</p>

                    <!-- Reset Link Button -->
                    <a href="{{ $resetLink }}" target="_blank" class="button">Reset Password</a>

                    <!-- Footer Section -->
                    <div class="footer">
                        <p>If you didn’t request this, you can safely ignore this email.</p>
                        <p>© {{ date('Y') }} Toon. All rights reserved.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
