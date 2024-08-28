@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row g-3 g-xxl-4">
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="las la-list"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('All Orders')</p>
                    <h4 class="title">{{ $order['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="las la-spinner"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('Pending Orders')</p>
                    <h4 class="title">{{ $order['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="lar la-check-square"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('Order Confirmed')</p>
                    <h4 class="title">{{ $order['confirmed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="las la-truck"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('Shipping Orders')</p>
                    <h4 class="title">{{ $order['shipped'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="lar la-check-circle"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('Order Delivered')</p>
                    <h4 class="title">{{ $order['delivered'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="las la-times-circle"></i>
                </div>
                <div class="dashboard-card__content">
                    <p>@lang('Cancelled Orders')</p>
                    <h4 class="title">{{ $order['cancelled'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <h6 class="mt-4 mb-1">@lang('Latest Orders')</h6>
    <table class="table cmn--table">
        <thead>
            <tr>
                <th>@lang('Order No')</th>
                <th>@lang('Payment Type')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Status')</th>
                <th>@lang('Time')</th>
                <th>@lang('More')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->order_no }}</td>
                    <td>
                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                            @lang('Online Payment')
                        @else
                            @lang('Cash On Delivery')
                        @endif
                    </td>
                    <td class="text--base">
                        <strong>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong>
                    </td>
                    <td> @php echo $order->ordersBadge; @endphp
                    </td>
                    <td>
                        <div>
                            {{ showDateTime($order->created_at) }}
                        </div>
                    </td>
                    <td>
                        <div>
                            <a href="{{ route('user.order.detail', $order->id) }}" class="btn btn-sm btn--base">
                                <i class="las la-desktop"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>

    </table>
@endsection
