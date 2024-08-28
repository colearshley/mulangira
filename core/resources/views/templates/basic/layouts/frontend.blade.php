@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.header')
    @yield('content')
    @include($activeTemplate . 'partials.footer')

    <div class="modal fade" id="quickView">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content py-4">
                <span data-bs-dismiss="modal" class="modal-close-btn"><i class="las la-times"></i></span>
                <div class="modal-body" id="productmodalView">

                </div>
            </div>
        </div>
    </div>
@endsection

@if (!$general->maintenance_mode)
    @push('script')
        <script>
            'use strict';
            (function($) {
                // Subscribe Post Method
                $('.newletter-form').on('submit', function(e) {
                    e.preventDefault()
                    var email = $('.subscribe-email').val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('subscribe') }}",
                        method: "POST",
                        data: {
                            email: email
                        },
                        success: function(response) {
                            if (response.success) {
                                $('.subscribe-email').val('')
                                notify('success', response.success);
                            } else {

                                notify('error', response.error.email);
                            }
                        }
                    });
                });



                $(document).on('click', '.add-wishlist', function(e) {
                    e.preventDefault();
                    let product_id = $(this).data('product_id');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('wish.list.add') }}",
                        data: {
                            product_id: product_id
                        },
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                notify('success', response.success);
                                getWishlistCount();
                            } else {
                                notify('error', response.error);
                            }
                        }
                    });
                })

                getWishlistCount();
                getCartCount()

                function getWishlistCount() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('wish.list.count') }}",
                        success: function(response) {
                            var total = Object.keys(response).length;
                            $.each(response, function(indexInArray, value) {
                                $(document).find(`[data-product_id='${value.product_id}']`).closest('.add-wishlist').addClass('active');
                            });
                            $('.show-wishlist-count').text(total);
                        }
                    });
                }

                $(document).on('click', '.add-to-cart', function(e) {
                    e.preventDefault();
                    var product_id = $(this).data('product_id');
                    var quantity = $('.productQuantity').val();
                    if (quantity == undefined) {
                        quantity = 1;
                    }
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        method: "POST",
                        url: "{{ route('cart.list.add') }}",
                        data: {
                            product_id: product_id,
                            quantity: quantity
                        },
                        success: function(response) {
                            if (response.success) {
                                notify('success', response.success);
                                getCartCount();
                            } else {
                                notify('error', response.error);
                            }
                        }
                    });
                })

                function getCartCount() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cart.list.count') }}",
                        success: function(response) {
                            $('.show-cart-count').text(response);
                        }
                    });
                }

                $(document).on('click', '.quickView', function(e) {
                    e.preventDefault();
                    var product_id = parseInt($(this).data('product_id'));
                    $.ajax({
                        type: "get",
                        url: "{{ route('product.quickView') }}",
                        data: {
                            product_id: product_id
                        },
                        success: function(response) {
                            $("#productmodalView").html(response);
                        }
                    });

                });
            })(jQuery);
        </script>
    @endpush
@endif

