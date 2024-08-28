@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="order-tracking pt-60 pb-60">
        <div class="container">
            <h6 class="text-center mb-4">{{ __($pageTitle) }}</h6>
            <div class="search-tracking">
                <form class="track-search">
                    <div class="input-group">
                        <input type="text" class="form-control form--control" name="order_no" placeholder="@lang('Enter your order number here.')" autocomplete="off">
                        <button type="submit" class="btn btn--base cmn-btn btn--round track-btn">@lang('Track Order')</button>
                    </div>
                </form>
            </div>
            <div id="show_track"></div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        button.btn.btn--base.cmn-btn.btn--round.track-btn {
           padding: 7px 35px;
        }
        @media (max-width:574px) {
            button.btn.btn--base.cmn-btn.btn--round.track-btn {
           padding: 7px 20px;
        }
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.track-btn').on('click', function(e) {
                e.preventDefault()
                let orderNo = $('[name=order_no]').val();
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    url: "{{ route('get.track.order') }}",
                    data: {
                        orderNo: orderNo
                    },
                    method: "POST",
                    success: function(response) {
                        if (response.error) {
                            $('#show_track').html(``)
                            if (response.error.orderNo) {
                                notify('error', response.error.orderNo);
                            } else {
                                notify('error', response.error)
                            }
                        } else {
                            $('#show_track').html(response)
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
