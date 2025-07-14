<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h1, h2, h3 {
            color: #007BFF;
        }
        .order-summary, .product-list, .shipping-info {
            margin-top: 20px;
        }
        .order-summary td, .product-list td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .product-list td {
            padding: 12px 8px;
        }
        .order-summary th, .product-list th {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Order Confirmation</h1>

        <p>Dear {{ $order->user->name }},</p>
        <p>Thank you for your order! We are pleased to confirm that we have received your order <strong>#{{ $order->order_number }}</strong>.</p>

        <h2>Order Details</h2>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('m/d/Y') }}</p>
        <p>
            <h4 style="font-size: 20px; margin-bottom: 10px; padding: 0; font-weight:600">Shipping Address</h4>
            <span  style="font-size: 14px; margin-bottom: 0; padding: 0; display: block; line-height: 20px;">{{ $order->shippingAddress->name ?? null}}</span>
            <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0">{{ $order->shippingAddress->email ?? null}}</span>
            <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->phone_number ?? null }}</span>
            <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->address_line_1 ?? null }}</span>
            <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->address_line_2 ?? null }}</span>
            <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0">{{ $order->shippingAddress->zip_code ?? null }}</span>
        </p>

        <div class="product-list">
            <h3>Product Details</h3>
            <table width="100%">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItem as $item)
                        <tr>
                            <td>{{ optional($item->product->productTranslation)->name ?? 'N/A'}}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ ($item->discounted_price ?: $item->regular_price)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <table width="100%">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>
                        ${{ $order->subtotal }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Shipping:</strong></td>
                    <td>
                        ${{ $order->shipping_cost }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Tax:</strong></td>
                    <td>
                        ${{ $order->tax }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td>
                        ${{ $order->total }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="shipping-info">
            <h3>Shipping Information</h3>
            <p>Your order will be shipped to the address provided. If you have any questions about your order, please feel free to contact us.</p>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us! We appreciate your business.</p>
            <p>Best regards,<br>{{ config('app.name') }}<br><a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
        </div>
    </div>
</body>
</html>
