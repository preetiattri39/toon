<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice # {{ $order->order_number }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .container {
      width: 100%;
      margin: 0 auto;
      padding: 20px;
    }

    .invoice-header img {
      max-width: 100px;
    }

    .invoice-details {
      margin: 20px 0;
    }

    table th,
    table td {
      padding: 12px;
      background: #fff;
      font-size: 14px;
    }

    .total-amount {
      text-align: right;
      margin-top: 20px;
    }

    .shipping-address {
      margin-top: 20px;
    }

    .footer {
      text-align: center;
      margin-top: 40px;
      font-size: 12px;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="invoice-header">
      <img src="{{asset('assets/icons/logo.png')}}" alt="Company Logo">
      <h3 style="font-weight: 700; font-size: 30px; margin-bottom:0; line-height: normal;">Order Summary</h3>
      <p style="font-size: 14px;">
        View your order details and check your order summary here
      </p>
    </div>
    <div style="margin-top:0px; margin-bottom:0px; text-align: right; font-size: 14px;">
      <p class="mb-0" style="margin-bottom: 0">Order ID #: {{ $order->order_number }}</p>
      <p class="mb-0 bold" style="margin-bottom: 0">Invoice Date: {{ $order->created_at->format('m/d/Y') }}</p>
    </div>
  
     <div class="invoice-details" style="margin-top: 0px">
      <table class="" style="width: 100%; border: none; margin-top: 0px">
        <tbody>
                <tr>
                    <td style="background-color: #fff; border-color: #fff; vertical-align: top; padding: 0">
                        <h4 style="font-size: 20px; margin-bottom: 10px; padding: 0; font-weight:600">Shipping Address</h4>
                        <span  style="font-size: 14px; margin-bottom: 0; padding: 0; display: block; line-height: 20px;">{{ $order->shippingAddress->name ?? null}} {{ $order->shippingAddress->last_name ?? null }}</span>
                        <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0">{{ $order->shippingAddress->email ?? null}}</span>
                        <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->phone_number ?? null }}</span>
                        <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->address_line_1 ?? null }}</span>
                        <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0"> {{ $order->shippingAddress->address_line_2 ?? null }}</span>
                        <span style="font-size: 14px; line-height: 20px; padding: 0; display: block; margin-bottom: 0">{{ $order->shippingAddress->zip_code ?? null }}</span>
                    </td>
                    
                </tr>
               
        </tbody>
      </table>
    </div>
    <div class="invoice-details" style="margin-top: 0px">
      <table class="table-bordered" style="width: 100%; margin: 0;">
        <thead>
          <tr>
            <!-- <th>Sr.No</th> -->
            <th style="border: 1px solid #1c1b1b">Item Name</th>
            <th style="border: 1px solid #1c1b1b">Ordered</th>
            <th style="border: 1px solid #1c1b1b">Unit Price</th>
            <th style="border: 1px solid #1c1b1b"> Total</th>
          </tr>
        </thead>
        <tbody>
            @php
                $i=1;
            @endphp
            @foreach ($order->orderItem as $item)
                <tr>
                    <!-- <td>{{$i}}</td> -->
                    <td style="border: 1px solid #1c1b1b">{{ optional($item->product->productTranslation)->name ?? 'N/A'}}</td>
                    <td style="border: 1px solid #1c1b1b">{{ $item->quantity }}</td>
                    <td style="border: 1px solid #1c1b1b">${{ ($item->discounted_price ?: $item->regular_price)}}</td>
                    <td style="border: 1px solid #1c1b1b"> ${{ number_format(($item->discounted_price ?? $item->regular_price) * $item->quantity, 2) }}</td>
                </tr>
                @php
                    ++$i;
                @endphp
            @endforeach
        </tbody>
      </table>
    </div>
    <div class="invoice-details" style="margin-top: 0px">
      <table class="table-bordered" style="width: 100%; margin-top: 0px">
        <tbody>
          <tr>
            <td style="padding: 0"><p style="font-size: 14px; margin: 0"></p></td>
            <td style="padding: 0"><p style="font-size: 14px; margin: 0"></p></td>
            <td style="padding: 0; width:30% ">    
              <p style="font-size: 14px; margin: 0">
               Sub Total:
              </p>
            </td>
            <td style="padding: 0; width:30% ">  
              <p style=" font-size: 14px; margin: 0;text-align: right;">
                ${{ $order->subtotal }}
              </p>
            </td>
          </tr>   
          <tr>
            <td style="padding: 0"></td>
            <td style="padding: 0"></td>
            <td style="padding: 0; width:30% ">  
              <p style="font-size: 14px; margin: 0">
               Shipping Price:
              </p>
            </td>
             <td style="padding: 0; width:30% ">  
              <p style=" font-size: 14px;text-align: right; margin: 0">
                 ${{ $order->shipping_cost }}
              </p>
            </td>
          </tr>   
          <tr>
            <td style="padding: 0"></td>
            <td style="padding: 0"></td>
             <td style="padding: 0; width:30% ">  
              <p style="font-size: 14px; margin: 0">
                <span style="">Tax Price:</span>
              </p>
            </td>
              <td style="padding: 0; width:30% ">  
              <p style=" font-size: 14px; text-align: right; margin: 0">
               ${{ $order->tax }}
              </p>
            </td>
          </tr>   
          <tr>
            <td style="padding: 0"></td>
            <td style="padding: 0"></td>
             <td style="padding: 0; width:30% ">    
              <p style="font-size: 20px; color: green; font-weight: 600; margin: 0">
                <span style="">Total Price:</span>
              </p>
            </td>
            <td style="padding: 0; width:30% ">  
              <p style=" font-size: 20px; color: green; font-weight: 600; text-align: right; margin: 0">
               ${{ $order->total }}
              </p>
            </td>
          </tr>         
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>