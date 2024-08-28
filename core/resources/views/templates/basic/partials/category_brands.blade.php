@php
    $brands     = App\Models\Brand::active()->featured()->latest()->take(6)->get();
    $categories = App\Models\Category::active()->featured()->latest()->limit(6)->get();
@endphp

<section class="top-brands-section pt-60 pb-120">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-6">
                <div class="section__header">
                    <h5 class="title">@lang('Top Categories')</h5>
                    <div class="view-all">
                        <a href="{{ route('category.all') }}" class="view--all">@lang('Show All')</a>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($categories as $category)
                        <div class="col-sm-6">
                            <a class="brand__item" href="{{ route('category.products', [slug($category->name), $category->id]) }}">
                                <div class="brand__item-img">
                                    <img src="{{ $category->imageShow() }}">
                                </div>
                                <div class="brand__item-cont">
                                    <span>{{ __($category->name) }}</span>
                                    <span><i class="las la-angle-right"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section__header">
                    <h5 class="title">@lang('Top Brands')</h5>
                    <div class="view-all">
                        <a href="{{ route('brand.all') }}" class="view--all">@lang('Show All')</a>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($brands as $brand)
                        <div class="col-sm-6">
                            <a class="brand__item" href="{{ route('brand.products', [slug($brand->name), $brand->id]) }}">
                                <div class="brand__item-img">
                                    <img src="{{ $brand->imageShow() }}" alt="products">
                                </div>
                                <div class="brand__item-cont">
                                    <span>{{ __($brand->name) }}</span>
                                    <span><i class="las la-angle-right"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
