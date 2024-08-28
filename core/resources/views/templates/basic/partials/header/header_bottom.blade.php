<div class="header-bottom bg--section py-2">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo me-lg-4 me-auto">
                <a href="{{ route('home') }}"><h2 style="font-size: 23px; white-space: nowrap;">Mulangila Clothes & Shoe Traders</h2>

                    <!--<img src="{{ getImage('assets/images/logoIcon/logo.png') }}" alt="">-->
                </a>
            </div>
            <form action="{{ route('products') }}" method="GET" class="search-form d-none d-lg-block">
                <div class="input-group search--group">
                    <input type="text" class="form-control" name="search" placeholder="@lang('Search here')" value="{{ request()->search ?? null }}">
                    <button class="cmn--btn" type="submit">@lang('Search') </button>
                </div>
            </form>
            <div class="cart-wrapper d-flex flex-wrap  me-4 me-lg-0">
                <a href="{{ route('wish.list.product') }}" class="cart--btn">
                    <i class="far fa-heart"></i>
                    <span class="qty show-wishlist-count">0</span>
                </a>
                <a href="{{ route('cart.list.product') }}" class="cart--btn">
                    <i class="fas fa-cart-arrow-down"></i>
                    <span class="qty show-cart-count">0</span>
                </a>
            </div>
            <div class="header-bar d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</div>
