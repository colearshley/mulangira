@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="top-brands-section pt-60 pb-120">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-12">
                    <div class="section__header">
                        <h5 class="title">@lang('All Brands')</h5>
                    </div>
                    <div class="row g-3">
                        @foreach ($brands as $brand)
                            <div class="col-sm-6 col-md-6 col-lg-3">
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
            {{ paginateLinks($brands) }}
        </div>
    </section>
@endsection
