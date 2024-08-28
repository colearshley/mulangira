@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="col-xxl-12 col-lg-12">
        <div class="dashboard-wrapper">
            <table class="table cmn--table">
                <thead>
                    <tr>
                        <th scope="col">@lang('Order No')</th>
                        <th scope="col">@lang('Payment Type')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Order Status')</th>
                        <th scope="col">@lang('More')</th>
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
                            <td>
                                @php
                                    echo $order->ordersBadge;
                                @endphp

                                @if (@$order->deposit->admin_feedback != null)
                                    <span class="badge badge--info status-info detailBtn" data-admin_feedback="{{ __(@$order->deposit->admin_feedback) }}"><i class="fa fa-info"></i></span>
                                @endif
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
            {{ paginateLinks($orders) }}
        </div>
    </div>
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg--base">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="payment-detail"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .status-info {
            min-width: 20px !important;
            cursor: pointer;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.payment-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
