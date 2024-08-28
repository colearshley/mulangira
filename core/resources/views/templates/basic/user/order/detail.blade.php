@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="col-xxl-12 col-lg-12">
        <div class="dashboard-wrapper">
            <div class="row g-3 mb-5">
                <h6>@lang('Order Details')</h6>
                <div class="col-md-6">
                    <div class="deposit-preview">
                        <div class="deposit-content w-100">
                            <ul>
                                <li>
                                    @lang('Order No')
                                    <span>{{$order->order_no}}</span>
                                </li>
                                <li>
                                    @lang('Total Price')
                                    <span>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                                </li>
                                <li>
                                    @lang('Payment Type')
                                    @if ($order->payment_type == Status::PAYMENT_ONLINE)
                                        <span>{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                                    @else
                                        <span>@lang('Cash on delivery')</span>
                                    @endif
                                </li>
                                @if (@$order->deposit->trx)
                                    <li>
                                        @lang('Payment Trx')
                                        <span>{{ @$order->deposit->trx }}</span>
                                    </li>
                                @endif
                                <li>
                                    @lang('Order Date')
                                    <span>{{ showDateTime($order->created_at) }}</span>
                                </li>
                                <li>
                                    @lang('Order Status')
                                    @php
                                        echo $order->ordersBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="deposit-preview ">
                        <div class="deposit-content w-100">
                            <ul>
                                <li>
                                    @lang('Shipping Area')
                                    <span>{{ __(@$order->shipping->name) }}</span>
                                </li>
                                @if ($order->discount * 1)
                                    <li>
                                        @lang('Coupon')
                                        <span>{{ __(@$order->coupon->name) }}</span>
                                    </li>
                                @endif
                                @php
                                    $address = json_decode($order->address);
                                @endphp
                                <li>
                                    @lang('Delivery Address')
                                    <span>
                                        {{ __($address->address) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('Country & State')
                                    <span>
                                        {{ __($address->country) }} @lang('&') {{ __($address->state) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('City & Zip')
                                    <span>
                                        {{ __($address->city) }} @lang('&') {{ __($address->zip) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('Payment Status')
                                    @php
                                        echo $order->paymentBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table cmn--table">
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
                                <a href="{{ route('product.detail', [slug($detail->product->name), $detail->product->id]) }}" class="text--base">
                                    {{ __(strLimit(@$detail->product->name, 15)) }}
                                </a>

                                @if ($order->order_status == Status::ORDER_DELIVERED)
                                    @if ($detail->product->file)
                                        (<span>
                                            <a href="{{ route('download', [$detail->product->id, $detail->product->file]) }}" class="mr-3 text--primary">
                                                <i class="las la-download"></i> @lang('Download File')
                                            </a>
                                        </span>)
                                    @elseif ($detail->product->link)
                                        (<span>
                                            <a href="{{ $detail->product->link }}" target="_blank" class="mr-3 text--primary">
                                                <i class="las la-external-link-alt"></i> @lang('Visit URL')
                                            </a>
                                        </span>)
                                    @endif
                                @endif
                            </td>
                            <td>
                                <strong>{{ $detail->quantity }}</strong>
                            </td>
                            <td class="text--base">
                                <strong>{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</strong>
                            </td>
                            <td>
                                <strong>{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text--danger text-center">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="total-wrapper">
                <div class="d-flex flex-wrap justify-content-between">
                    <strong>@lang('Subtotal :')</strong><strong> {{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</strong></strong>
                </div>
                <div class="d-flex flex-wrap justify-content-between">
                    <strong>@lang('Shipping Charge :')</strong><strong> {{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</strong>
                </div>
                @if ($order->discount * 1)
                    <div class="d-flex flex-wrap justify-content-between">
                        <strong>@lang('Discount :')</strong><strong> {{ showAmount($order->discount) }} {{ __($general->cur_text) }}</strong>
                    </div>
                @endif
                <div class="d-flex flex-wrap justify-content-between border-0">
                    <strong>@lang('Total :')</strong><strong> {{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .total-wrapper {
            max-width: 300px;
            margin-left: auto;
            margin-top: 15px;
            font-size: 14px;
            margin-right: 20px;
        }

        @media (max-width:575px) {
            .total-wrapper {
                margin-right: 0;
            }
        }

        .total-wrapper>div {
            padding: 6px 0;
            border-bottom: 1px dashed #ddd;
        }
    </style>
@endpush
