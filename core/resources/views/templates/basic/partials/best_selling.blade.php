@php
    $products = App\Models\Product::available()->where('sale_count', '!=', 0)->orderBy('sale_count', 'desc')->latest()->take(6)->get();
@endphp
<section class="best-selling-section pt-60 pb-60">
    <div class="mx-4">
        <div class="section__header">
            <h5 class="title">@lang('Best Selling Products')</h5>
            <div class="view-all">
                <a href="{{ route('products.best.selling') }}" class="view--all">@lang('Show All')</a>
            </div>
        </div>
        <div class="row g-3 justify-content-center">
            @if ($products->count() > 0)
                @include($activeTemplate . 'products.display_products')
            @endif
        </div>
    </div>
</section>
