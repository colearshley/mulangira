@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerElement = getContent('banner.element');
    @endphp
    <section class="banner-section my-4">
        <div class="container-fluid">
            <div class="banner__wrapper">
                <div class="banner__wrapper-category d-none d-lg-block">
                    <div class="banner__wrapper-category-inner">
                        <h6 class="banner__wrapper-category-inner-header">@lang('Categories')</h6>
                        @include($activeTemplate . 'partials.navbar')
                    </div>
                </div>
                <div class="banner__wrapper-content">
                    <div class="banner-slider owl-theme owl-carousel">
                        @foreach ($bannerElement as $banner)
                            <div class="banner__wrapper-content-inner">
                                <a href="{{ $banner->data_values->url }}">
                                    <img src="{{ getImage('assets/images/frontend/banner/' . $banner->data_values->image, '1290x480') }}" alt="banner">
                                </a>
                            </div>
                           
                        @endforeach
                    </div>
                </div>
                
                <div class="banner__wrapper-products">
                    <div class="banner__wrapper-products-inner">
                        <h6 class="banner__wrapper-products-inner-header">@lang('Today\'s Deal')</h6>
                        <div class="banner__wrapper-products-inner-body">
                            <div class="product-max-xl-slider">
                                @forelse ($todayDealProducts as $product)
                                    @php
                                        $price = productPrice($product);
                                    @endphp
                                    <a href="{{ route('product.detail', [slug($product->name), $product->id]) }}" class="deal__item">
                                        <div class="deal__item-img">
                                            <img src="{{ $product->imageShow() }}">
                                        </div>
                                        <div class="deal__item-cont">
                                            <h6 class="price text--base">{{ $general->cur_sym }}{{ showAmount($price) }}</h6>
                                            <del class="old-price">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                                        </div>
                                    </a>
                                @empty
                                    <div class="deal__item">
                                        <div class="deal__item-cont">
                                            <h6 class="price text--base">@lang('No deal found yet')</h6>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="ttl -m -fs20 -df -i-ctr -phm -j-ctr -mh-48px homepg-heading" style="background-color:#E7BBC2;">SWIPE FOR MORE  →</div>
    </section>

    @include($activeTemplate . 'partials.hot_deal')
    @include($activeTemplate . 'partials.featured_product')
    @include($activeTemplate . 'partials.best_selling')
    @include($activeTemplate . 'partials.category_brands')
@endsection
