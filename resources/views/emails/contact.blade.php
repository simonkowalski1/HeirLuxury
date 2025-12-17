<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fbbf24;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .value {
            color: #111827;
            font-size: 15px;
        }
        .message-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            white-space: pre-wrap;
        }
        .product-info {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .product-info .label {
            color: #92400e;
        }
        .product-info a {
            color: #d97706;
            text-decoration: none;
        }
        .product-info a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HeirLuxury</h1>
        <p style="margin: 10px 0 0; color: #d1d5db; font-size: 14px;">
            @if($productName)
                Product Inquiry
            @else
                Contact Form Submission
            @endif
        </p>
    </div>

    <div class="content">
        @if($productName)
            <div class="product-info">
                <div class="label">Product</div>
                <div class="value">
                    @if($productUrl)
                        <a href="{{ $productUrl }}">{{ $productName }}</a>
                    @else
                        {{ $productName }}
                    @endif
                </div>
            </div>
        @endif

        <div class="field">
            <div class="label">Name</div>
            <div class="value">{{ $firstName }} {{ $lastName }}</div>
        </div>

        <div class="field">
            <div class="label">Email</div>
            <div class="value">
                <a href="mailto:{{ $email }}" style="color: #2563eb;">{{ $email }}</a>
            </div>
        </div>

        @if($phone)
            <div class="field">
                <div class="label">Phone</div>
                <div class="value">
                    <a href="tel:{{ $phone }}" style="color: #2563eb;">{{ $phone }}</a>
                </div>
            </div>
        @endif

        <div class="field">
            <div class="label">Message</div>
            <div class="message-box">{{ $messageContent }}</div>
        </div>

        <div class="footer">
            This message was sent from the HeirLuxury contact form.
        </div>
    </div>
</body>
</html>
