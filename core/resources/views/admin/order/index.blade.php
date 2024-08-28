@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order No')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_no }}</td>
                                        <td>
                                            <div>
                                                <a href="{{ route('admin.users.detail', $order->user->id) }}"> {{ $order->user->username }}</a>
                                                <br>
                                                {{ $order->user->email }}
                                            </div>
                                        </td>
                                        <td>{{ showAmount($order->total) }}{{__($general->cur_text)}}</td>
                                        <td>{{ showDateTime($order->create_at) }}</td>
                                        <td> @php echo $order->ordersBadge; @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i>
                                                    @lang('Details')
                                                </a>

                                                @if($order->order_status == Status::ORDER_PENDING || $order->order_status == Status::ORDER_CONFIRMED ||$order->order_status == Status::ORDER_SHIPPED)
                                                    <button class="btn btn-sm orderStatusModal
                                                        @if ($order->order_status == Status::ORDER_PENDING) btn-outline--warning
                                                        @elseif($order->order_status == Status::ORDER_CONFIRMED) btn-outline--success
                                                        @elseif($order->order_status == Status::ORDER_SHIPPED) btn-outline--info
                                                        @endif"
                                                        data-url="{{ route('admin.orders.status', $order->id) }}"
                                                        data-order_status={{ $order->order_status }}>

                                                        @if ($order->order_status == Status::ORDER_PENDING)
                                                            <i class="lar la-check-circle"></i>@lang('Confirm')
                                                        @elseif($order->order_status == Status::ORDER_CONFIRMED)
                                                            <i class="lar la-check-circle"></i>@lang('Shipped')
                                                        @elseif($order->order_status == Status::ORDER_SHIPPED)
                                                            <i class="las la-truck"></i>@lang('Delivered')
                                                        @endif
                                                    </button>
                                                @endif

                                                @if ($order->order_status == Status::ORDER_PENDING)
                                                    <button class="btn btn-sm btn-outline--danger cancelOrderModal" data-url="{{ route('admin.orders.status', $order->id) }}">
                                                        <i class="lar la-times-circle"></i>
                                                        @lang('Cancel')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="orderStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="modal-detail"></p>
                        <input type="hidden" name="order_status">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by Order No.." />
@endpush

@push('style')
    <style>
        .disabled-order {
            background-color: #6952bd4d;
            cursor: no-drop;
        }

        .disabled-order:hover {
            background-color: #6952bd4d;
        }

        .disabled-cancel-order {
            background-color: #ea545575;
            cursor: no-drop;
        }

        .disabled-cancel-order:hover {
            background-color: #ea545575;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.orderStatusModal').on('click', function() {
                var modal = $('#orderStatusModal');
                var url = $(this).data('url');
                var orderStatus = $(this).data('order_status');
                if (orderStatus == 0) {
                    status = 1;
                    modal.find('.modal-detail').text(`@lang('Are you sure to confirm this order?')`);
                } else if (orderStatus == 1) {
                    status = 2;
                    modal.find('.modal-detail').text(`@lang('Are you sure to confirm this shipped?')`);

                } else if (orderStatus == 2) {
                    modal.find('.modal-detail').text(`@lang('Are you sure to confirm this Delivey?')`);
                    status = 3;
                }
                modal.find('form').attr('action', url);
                modal.find('[name=order_status]').val(status);
                modal.modal('show');
            });

            $('.cancelOrderModal').on('click', function() {
                var modal = $('#orderStatusModal');
                var url = $(this).data('url');
                var orderStatus = 9;
                modal.find('form').attr('action', url);
                modal.find('[name=order_status]').val(orderStatus);
                modal.find('.modal-detail').text(`@lang('Are you sure to cancel this order?')`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
