<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="card-title ">@lang('Order detail of') {{ $order->order_no }}</h5>
        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline--primary " target="_blank">
            <i class="las la-print"></i>
            @lang('Print Invoice')
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order No')
                        <span class="fw-bold">{{ $order->order_no }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Price')
                        <span class="fw-bold">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment')
                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                            <span class="fw-bold">{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                        @else
                            <span class="fw-bold">@lang('Cash on delivery')</span>
                        @endif
                    </li>
                    @if (@$order->deposit->trx)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payment Trx')
                            <span class="fw-bold">{{ @$order->deposit->trx }}</span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order Date')
                        <span class="fw-bold">{{ showDateTime($order->created_at) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order Status')
                        @php
                            echo $order->ordersBadge;
                        @endphp
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Shipping Area')
                        <span class="fw-bold">{{ __(@$order->shipping->name) }}</span>
                    </li>
                    @if ($order->discount != 0)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Coupon')
                            <span class="fw-bold">{{ __(@$order->coupon->name) }}</span>
                        </li>
                    @endif
                    @php
                        $address = json_decode($order->address);
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Delivery Address')
                        <span class="fw-bold">
                            {{ __($address->address) }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Country / State')
                        <span class="fw-bold">
                            {{ __($address->country) }} / {{ __($address->state) }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('City / Zip')
                        <span class="fw-bold">
                            {{ __($address->city) }} / {{ __($address->zip) }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment Status')
                        @php
                            echo $order->paymentBadge;
                        @endphp
                    </li>
                </ul>
            </div>
        </div>

        <div class="table-responsive--md  table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th>@lang('Product Name')</th>
                        <th>@lang('Quantity')</th>
                        <th>@lang('Price')</th>
                        <th>@lang('Subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderDetail as $detail)
                        <tr>
                            <td>
                                <a href="{{ route('admin.product.edit', $detail->product->id) }}">
                                    {{ __(strLimit($detail->product->name, 20)) }}
                                </a>

                                @if ($detail->product->file)
                                    (<a href="{{ route('download', [$detail->product->id, $detail->product->file]) }}" class="mr-3 text--primary">
                                        <i class="las la-arrow-circle-down"></i>@lang('Download File')
                                    </a>)
                                @elseif ($detail->product->link)
                                    (<span>
                                        <a href="{{ $detail->product->link }}" target="_blank" class="mr-3 text--primary">
                                            <i class="las la-external-link-alt"></i> @lang('Visit URL')
                                        </a>
                                    </span>)
                                @endif
                            </td>
                            <td>
                                <strong>{{ $detail->quantity }}</strong>
                            </td>

                            <td>
                                <strong>{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</strong>
                            </td>

                            <td>
                                <strong>{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td><span>@lang('Subtotal :')</span><strong> {{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><span>@lang('Shipping Charge :')</span><strong> {{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><span>@lang('Discount :')</span><strong> {{ showAmount($order->discount) }} {{ __($general->cur_text) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><span>@lang('Total :')</span><strong> {{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('style')
    <style>
       span {
            text-align: right;
       }
    </style>
@endpush
