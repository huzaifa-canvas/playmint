<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f7;
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            margin: auto;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo h1 {
            color: #7367F0;
            font-size: 28px;
            margin: 0;
        }
        h2 {
            color: #333;
            font-size: 20px;
        }
        p {
            color: #555;
            font-size: 15px;
            line-height: 1.6;
        }
        .purpose {
            display: inline-block;
            background-color: #f0edff;
            color: #7367F0;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .otp-box {
            text-align: center;
            margin: 25px 0;
        }
        .otp {
            display: inline-block;
            font-size: 36px;
            font-weight: bold;
            color: #7367F0;
            letter-spacing: 8px;
            padding: 15px 30px;
            background-color: #f8f7ff;
            border: 2px dashed #7367F0;
            border-radius: 10px;
        }
        .warning {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            color: #856404;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>{{ config('app.name', 'PlayMint') }}</h1>
        </div>

        <h2>Hello {{ $user->name ?? 'User' }},</h2>

        <span class="purpose">{{ $purpose ?? 'Verification' }}</span>

        <p>Your One-Time Password (OTP) is:</p>

        <div class="otp-box">
            <div class="otp">{{ $otp }}</div>
        </div>

        <p>This OTP is valid for the next <strong>10 minutes</strong>. Please do not share it with anyone.</p>

        <div class="warning">
            ⚠️ If you did not request this code, please ignore this email or contact support immediately.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'PlayMint') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
