@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="products-single-section pt-80 pb-80">
        <div class="container">
            <div class="bg--section p-3 border rounded">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="sync1 owl-carousel owl-theme">
                            <div class="thumbs">
                                <img src="{{ $product->imageShow() }}" alt="image">
                            </div>
                            @foreach ($product->gallery ?? [] as $gallery)
                                <div class="thumbs">
                                    <div class="thumbs">
                                        <img src="{{ getImage(getFilePath('productGallery') . '/' . $gallery, getFileSize('productGallery')) }}" alt="image">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="sync2 owl-carousel owl-theme">
                            <div class="thumbs">
                                <img src="{{ $product->imageShow() }}" alt="image">
                            </div>
                            @foreach ($product->gallery ?? [] as $gallery)
                                <div class="thumbs">
                                    <img src="{{ getImage(getFilePath('productGallery') . '/' . $gallery, getFileSize('productGallery')) }}" alt="image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-7 ps-lg-5">
                        <div class="product-details-content">
                            <div class="repeat--item">
                                <h5 class="title">{{ __($product->name) }}</h5>
                                <div class="ratings-area">
                                    @if ($general->display_stock == Status::YES)
                                        <div class="badge badge--{{ !$product->quantity ? 'danger' : 'success' }} badge-sm me-3">
                                            {{ !$product->quantity ? 'Out of Stock' : 'In Stock' }}
                                        </div>
                                    @endif
                                    <div class="ratings">
                                        @php
                                            echo showProductRatings($product->avg_rate);
                                        @endphp
                                    </div>
                                    <span class="ms-2 me-auto">({{ $product->reviews->count() }})</span>
                                </div>
                            </div>

                            @php
                                $features = $product->features;
                            @endphp

                            <div class="repeat--item border-0">
                                <ul class="lists">
                                    <li>
                                        <span class="name">@lang('Price')</span>
                                        <h5 class="m-0 text--base product-price">
                                            {{ $general->cur_sym }}{{ showAmount(productPrice($product)) }} &nbsp
                                        </h5>

                                        @if ($product->discount || ($product->today_deals == Status::YES))
                                            <h5 class="m-0 text--base product-price"><del class="text--danger"> {{ $general->cur_sym }}{{ showAmount($product->price) }}</del></h5>
                                        @endif
                                    </li>
                                    <li>
                                        <span class="name">@lang('Categories')</span>
                                        <p class="mb-1">{{ $product->category->name }}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="repeat--item">
                                <ul class="lists">
                                    <li>
                                        <span class="name">@lang('Summary')</span>
                                        <p class="summary m-0 ps-5">{{ __($product->summary) }}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="repeat--item">
                                <div class="single-add-cart-area">
                                    <div class="cart-plus-minus">
                                        <div class="cart-decrease qtybutton dec">
                                            <i class="las la-minus"></i>
                                        </div>
                                        <input type="number" class="form-control productQuantity" name="quantity" value="1">
                                        <div class="cart-increase qtybutton inc active">
                                            <i class="las la-plus"></i>
                                        </div>
                                    </div>
                                    @if ($general->display_stock == Status::YES)
                                        <div class="quantity--amount">
                                            (<span class="amount">{{ $product->quantity }}</span>)
                                        </div>
                                    @endif
                                    <a href="#0" class="cmn--btn add-to-cart" data-product_id="{{ $product->id }}">
                                        @lang('Add To Cart')
                                    </a>
                                </div>
                            </div>
                            <div class="repeat--item">
                                <ul class="lists">
                                    <li class="mt-2">
                                        <span class="name">@lang('Share')</span>
                                        <ul class="social-icons">
                                            <li>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" title="@lang('Facebook')">
                                                    <i class="lab la-facebook-f"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com/intent/tweet?text={{ __($product->name) }}%0A{{ url()->current() }}" title="@lang('twitter')">
                                                    <i class="lab la-twitter"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title=my share text&amp;summary=dit is de linkedin summary" title="@lang('Linkedin')">
                                                    <i class="lab la-linkedin-in"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __($product->name) }}&media={{ getImage('assets/images/product/' . $product->image) }}" title="@lang('Pinterest')">
                                                    <i class="lab la-pinterest"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-80">
                <div class="row flex-wrap-reverse gy-5">
                    @if ($topProducts->count() > 0)
                        <div class="col-lg-4 col-xl-3">
                            <div class="filter__widget">
                                <h5 class="filter__widget-title">@lang('Top Products')</h5>
                                <div class="filter__widget-body">
                                    <div class="deal__wrapper">
                                        @foreach ($topProducts as $topProduct)
                                            <a href="{{ route('product.detail', [slug($topProduct->name), $topProduct->id]) }}" class="deal__item">
                                                <div class="deal__item-img">
                                                    <img src="{{ $topProduct->imageShow() }}" alt="banner/products">
                                                </div>
                                                <div class="deal__item-cont">
                                                    <div class="ratings">
                                                        @php
                                                            echo showProductRatings($topProduct->avg_rate);;
                                                        @endphp
                                                    </div>
                                                    <h6 class="price">{{ $general->cur_sym }}{{ showAmount(productPrice($topProduct)) }}</h6>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="@if ($topProducts->count() > 0) col-lg-8 col-xl-9 @else col-lg-12 col-xl-12 @endif">
                        <div class="description-wrapper bg--section">
                            <div class="description__header">
                                <ul class="nav van-tabs nav--tabs">
                                    <li>
                                        <a href="#desc" data-bs-toggle="tab" class="active">@lang('Description')</a>
                                    </li>
                                    @if ($features)
                                        <li>
                                            <a href="#feature" data-bs-toggle="tab">@lang('Specification')</a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="#rating" data-bs-toggle="tab">@lang('Reviews')</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="desc">
                                    <div class="description__body">
                                        <p>@php echo $product->description; @endphp</p>
                                    </div>
                                </div>
                                @if ($features)
                                    <div class="tab-pane fade" id="feature">
                                        <div class="description__body border-0 p-0">
                                            <table class="feature-table table">
                                                <tbody>
                                                    @foreach ($features as $feature)
                                                        <tr>
                                                            <th>{{ $feature['title'] }}</th>
                                                            <td>{{ $feature['description'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <div class="tab-pane fade" id="rating">
                                    <div class="description__body">
                                        <div class="review-area load-reviews">
                                            @forelse ($reviews->take(5) as $review)
                                                @include($activeTemplate.'products.basic_review')
                                            @empty
                                                <div class="review-item">
                                                    <strong class="text--danger">{{ __($emptyMessage) }}</strong>
                                                </div>
                                            @endforelse
                                        </div>

                                        @if(count($reviews) > 5)
                                            <button class="cmn--btn mt-4 loadMoreReviews">@lang('Load More')</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($relatedProduct->count())
                            <div class="mt-5">
                                <h4 class="mb-3">@lang('Related Products')</h4>
                                <div class="row gy-4 justify-content-center">
                                    @foreach ($relatedProduct as $singleProduct)
                                        <div class="col-lg-3 col-xxl-3 col-md-6 col-sm-10">
                                            <div class="product__item">
                                                <div class="product__item-img">
                                                    <a href="{{ route('product.detail', [slug($singleProduct->name), $singleProduct->id]) }}">
                                                        <img src="{{ $singleProduct->imageShow() }}" alt="product">
                                                    </a>
                                                    @php
                                                        if ($singleProduct->discount || $singleProduct->today_deals == Status::YES) {
                                                            echo discountText($singleProduct, $general);
                                                        }
                                                    @endphp
                                                    <div class="product-right-btn">
                                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#quickView" class="quickView" data-product_id="{{ $singleProduct->id }}">
                                                            <i class="las la-expand-arrows-alt"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" data-product_id="{{ $singleProduct->id }}" class="add-wishlist">
                                                            <i class="las la-heart"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="product__item-cont">
                                                    <h6 class="title">
                                                        <a href="{{ route('product.detail', [slug($singleProduct->name), $singleProduct->id]) }}">
                                                            {{ __($singleProduct->name) }}
                                                        </a>
                                                    </h6>
                                                    @if ($general->display_stock == Status::YES)
                                                        <span class="info {{ !$singleProduct->quantity ? 'text--danger' : 'text--success' }}">
                                                            {{ !$singleProduct->quantity ? 'Out of Stock' : 'In Stock' }}
                                                        </span>
                                                    @endif

                                                    <div class="d-flex justify-content-between align-items-center @if (!$general->display_stock) mt-2 @endif">
                                                        <div class="ratings">
                                                            @php
                                                                echo showProductRatings($singleProduct->avg_rate);
                                                            @endphp
                                                        </div>

                                                        <h6 class="m-0 price d-flex flex-wrap gap-1 align-items-center">
                                                            {{ $general->cur_sym }}{{ showAmount(productPrice($singleProduct)) }}
                                                            @if ($singleProduct->discount || ($singleProduct->today_deals == Status::YES))
                                                                <del class="text--danger">{{ $general->cur_sym }}{{ showAmount($singleProduct->price) }}</del>
                                                            @endif
                                                        </h6>
                                                    </div>
                                                    <div class="hover-cont-wrapper">
                                                        <div class="hover-cont-area">
                                                            <a href="#0" class="cmn--btn cart-number-btn add-to-cart" data-product_id="{{ $singleProduct->id }}">
                                                                @lang('Add To Cart')
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use script";

            var showReviews = 5;

            $('.loadMoreReviews').on('click', function(e) {
                e.preventDefault();
                $(this).addClass('btn-disabled').attr("disabled", true);

                var skip = showReviews;

                $.ajax({
                    type: 'get',
                    url: '{{ route('fetch.reviews', $product->id) }}',
                    data: {
                            skip : skip
                        },
                    dataType: "json",

                    success: function (response) {
                        if(response.success){
                            $('.load-reviews').append(response.html);
                            showReviews += 5;
                            $('.loadMoreReviews').removeClass('btn-disabled').attr("disabled", false);
                        }else{
                            notify('error', response.error);
                        }
                    }
                });
            });

            $('.cart-decrease').on('click', function() {
                var quantity = $('input[name="quantity"]').val();
                if (quantity > 0) {
                    TotalPrice();
                } else {
                    $('input[name="quantity"]').val(1);
                    notify('error', 'You have to order a minimum amount of one.');
                }
            });

            $('.cart-increase').on('click', function() {
                var quantity = $('input[name="quantity"]').val();
                if (quantity <= parseInt('{{$product->quantity}}')) {
                    TotalPrice();
                } else {
                    $('input[name="quantity"]').val(1);
                    notify('error', 'You can not order more than the available stock.');
                }
            });

            $('input[name="quantity"]').on('focusout', function() {
                var quantity = $(this).val();
                if ( (quantity > 0) && ( quantity <= parseInt('{{$product->quantity}}') ) ) {
                    TotalPrice();
                } else {
                    $('input[name="quantity"]').val(1);
                    $(".quantity--amount .amount").text(parseInt('{{$product->quantity}}'));
                    TotalPrice()
                    notify('error', 'You have to order a minimum amount of one.');
                }
            });

            function TotalPrice() {
                var quantity = $('input[name="quantity"]').val();
                var productPrice = $('.product-price').text();
                var splitPrice = productPrice.split("{{ $general->cur_sym }}");
                var price = parseFloat(splitPrice[1]);
                var totalPrice = quantity * price;
                $('.total-price').text("{{ $general->cur_sym }}" + totalPrice.toFixed(2));
            }
        })(jQuery);
    </script>
@endpush
