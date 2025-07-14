@extends('admin.layouts.master')
@section('title', 'order details')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="">

        <div class="status">
            <h2 class="f-20 bold title-main">Order Summary</h2>
            
        </div>
        <div class="card p-3">
        <div class="invoice-header d-flex justify-content-between flex-sm-row flex-column py-3">
            <div class="pb-sm-0 pb-3">
                <h3 style="" class="heading-primary d-sm-block d-inline pr-sm-0 pr-2 mb-2">Order Status</h3>
                <select name="update-order-status" id="update-order-status" class="status-bar light-gray fs-14" data-order-id="{{$order->id}}">
                    <option value="pending" {{isset($order->status) && $order->status=='pending' ? 'selected' : ''}}>Pending</option>
                    <option value="confirmed" {{isset($order->status) && $order->status=='confirmed' ? 'selected' : ''}}>Confirmed</option>
                    <option value="shipped" {{isset($order->status) && $order->status=='shipped' ? 'selected' : ''}}>Shipped</option>
                    <option value="completed" {{isset($order->status) && $order->status=='completed' ? 'selected' : ''}}>Completed</option>
                </select>
            </div>
            <!-- <p style="font-size: 14px;">
                View your order details and check your order summary here
            </p> -->
            <div class="text-sm-end text-start" style="margin-top:0px; margin-bottom:0px;   font-size: 14px;">
                <p class="mb-0" style="margin-bottom: 0">Order ID #: {{ $order->order_number }}</p>
                <p class="mb-0 bold" style="margin-bottom: 0">Invoice Date: {{ $order->created_at->format('m/d/Y') }}</p>
            </div>
        </div>
       
    
        <div class="invoice-details" style="margin-top: 0px">
        <table class="" style="width: 100%; border: none; margin-top: 0px">
            <tbody>
                    <tr>
                        <td style="border-color: #fff; vertical-align: top; padding: 10px 0">
                            <h3 class="heading-primary">Shipping Address</h4>
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
        <div class="invoice-details pdf-data" style="margin-top: 0px; overflow-x: scroll;">
        <table class="table-bordered" style="width: 100%; margin: 0;">
            <thead>
            <tr>
                <!-- <th>Sr.No</th> -->
                <th style="border: 1px solid #3c3c3c" class="p-2">Item Name</th>
                <th style="border: 1px solid #3c3c3c"class="p-2">Ordered</th>
                <th style="border: 1px solid #3c3c3c"class="p-2">Unit Price</th>
                <th style="border: 1px solid #3c3c3c" class="p-2"> Total</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp
                @foreach ($order->orderItem as $item)
                    <tr>
                        <!-- <td>{{$i}}</td> -->
                        <td style="border: 1px solid #3c3c3c" >{{ optional($item->product->productTranslation)->name ?? 'N/A'}}</td>
                        <td style="border: 1px solid #3c3c3c">{{ $item->quantity }}</td>
                        <td style="border: 1px solid #3c3c3c">${{ ($item->discounted_price ?: $item->regular_price)}}</td>
                        <td style="border: 1px solid #3c3c3c"> ${{ number_format(($item->discounted_price ?? $item->regular_price) * $item->quantity, 2) }}</td>
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
                    <td style="padding: 0"><p style="font-size: 14px; margin: 0"></p></td>
                    <td style="padding: 0; width:20% ">    
                    <p style="font-size: 14px; margin: 0" class="pt-3 pe-3">
                    Sub Total:
                    </p>
                    </td>
                    <td style="padding: 0; width:20% ">  
                    <p style=" font-size: 14px; margin: 0;text-align: right;" class="pt-3 pe-3">
                        ${{ $order->subtotal }}
                    </p>
                    </td>
                </tr>   
                <tr>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td class="w-20" style="padding: 0; ">  
                    <p style="font-size: 14px; margin: 0" >
                    Shipping Price:
                    </p>
                    </td>
                    <td class="w-20" style="padding: 0; ">  
                    <p style=" font-size: 14px;text-align: right; margin: 0" >
                        ${{ $order->shipping_cost }}
                    </p>
                    </td>
                </tr>   
                <tr>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td class="w-20" style="padding: 0;">  
                    <p style="font-size: 14px; margin: 0" >
                        <span style="">Tax Price:</span>
                    </p>
                    </td>
                    <td class="w-20" style="padding: 0; ">  
                    <p style=" font-size: 14px; text-align: right; margin: 0">
                    ${{ $order->tax }}
                    </p>
                    </td>
                </tr>   
                <tr>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td style="padding: 0"></td>
                    <td class="w-20" style="padding: 0;  ">    
                    <p class="total-price pb-3 pe-3">
                        <span style="">Total Price:</span>
                    </p>
                    </td>
                    <td class="w-20" style="padding: 0;  ">  
                    <p class="total-price text-right pb-3 pe-3">
                    ${{ $order->total }}
                    </p>
                    </td>
                </tr>         
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

    $(document).on('change','select#update-order-status',function(){ 

        var order_status = $(this).val();
        var order_id = $(this).attr("data-order-id");

        $.ajax({
            method: 'GET',
            url: '{{route("order-status")}}',
            data: {
                _token: "{{ csrf_token() }}", 
                order_status:order_status,
                order_id:order_id
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('An error occurred:', error); // Log the error for debugging
            }
        });
    })

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection