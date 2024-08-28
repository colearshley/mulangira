@php $products = App\Models\Product::available()->featured()->latest()->with('reviews')->take(12)->get(); @endphp
<section class="latest-products-section pt-60 pb-60">
    <div class="mx-4">
        <div class="section__header">
            <h5 class="title">@lang('Featured Products')</h5>
            <div class="view-all">
                <a href="{{ route('products.featured') }}" class="view--all">@lang('Show All')</a>
            </div>
        </div>
        <div class="row g-3 justify-content-center">
            @if ($products->count() > 0)
                @include($activeTemplate . 'products.display_products')
            @endif
        </div>
    </div>
</section>

