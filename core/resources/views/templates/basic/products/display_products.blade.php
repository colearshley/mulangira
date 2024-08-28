@forelse($products as $product)
    @php
        $price = productPrice($product);
    @endphp

         <div class="col-xl-2 col-md-3 col-sm-4">
        <div class="product__item">
            <div class="product__item-img">
                <a href="{{ route('product.detail', [slug($product->name), $product->id]) }}">
                    <img src="{{ $product->imageShow() }}">
                </a>
                @php
                    if ($product->discount != 0 || $product->today_deals == 1) {
                        $discount = discountText($product, $general);
                        echo $discount;
                    }
                @endphp
                <div class="product-right-btn">
                    <a href="#0" data-bs-toggle="modal" data-bs-target="#quickView" class="quickView" data-product_id="{{ $product->id }}">
                        <i class="las la-expand-arrows-alt"></i>
                    </a>
                    <a href="#0" data-product_id="{{ $product->id }}" class="add-wishlist">
                        <i class="las la-heart"></i>
                    </a>
                </div>
            </div>
            <div class="product__item-cont">
                <h6 class="title">
                    <a href="{{ route('product.detail', [slug($product->name), $product->id]) }}">
                        {{ __($product->name) }}
                    </a>
                </h6>
                @if ($general->display_stock == Status::ENABLE)
                    <span class="info {{ $product->quantity == 0 ? 'text--danger' : 'text--success' }}">
                        {{ $product->quantity == 0 ? 'Out of Stock' : 'In Stock' }}
                    </span>
                @endif
                <div class="d-flex justify-content-between align-items-center @if (!$general->display_stock) mt-2 @endif">
                    <div class="ratings">
                        @php
                            $star = showProductRatings($product->avg_rate);
                            echo $star;
                        @endphp
                    </div>
                    <h6 class="m-0 price">
                        {{ $general->cur_sym }}{{ showAmount($price) }}
                        @if ($product->discount != 0)
                            <del class="text--danger">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                        @elseif($product->today_deals == 1)
                            <del class="text--danger">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                        @endif
                    </h6>
                </div>
                <div class="hover-cont-wrapper">
                    <div class="hover-cont-area">
                        <button class="cmn--btn cart-number-btn add-to-cart" data-product_id="{{ $product->id }}">
                            @lang('Add To Cart')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-xl-12 col-md-12 col-sm-12 text-center">
        <strong class="text--danger">{{ __($emptyMessage) }}</strong>
    </div>
@endforelse
