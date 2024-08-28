@php
    $contactContent = getContent('contact_us.content', true);
    $footerElement  = getContent('footer.element');
    $footerContent  = getContent('footer.content', true);
    $socialElement  = getContent('social_icon.element');
    $policyPages    = getContent('policy_pages.element');
    $services       = getContent('service.element');
    $categories     = App\Models\Category::active()->with('subcategories', function($subcategories) {
                        $subcategories->active();
                    })->latest()->limit(6)->get();
@endphp

<div class="tos-links-section pt-60 pb-60 bg-white">
    <div class="container">
        <div class="tos-links row gy-4">
            @foreach ($services as $service)
                <div class="col-lg-3 col-md-6">
                    <a href="javascript:void(0)">
                        <div class="icon">
                            <img src="{{ getImage('assets/images/frontend/service/' . $service->data_values->image, '50x50') }}" alt="icon">
                        </div>
                        <div class="content">
                            <span class="subtitle">{{ __($service->data_values->title) }}</span>
                            <p>{{ __($service->data_values->short_detail) }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<footer>
    @if ($categories->count())
        <div class="footer-top">
            <div class="container">
                <div class="footer__wrapper">
                    @foreach ($categories as $category)
                        <div class="footer__widget">
                            <h6 class="title">{{ __(strLimit($category->name, 15)) }}</h6>
                            <ul>
                                @foreach ($category->subcategories->take(4) as $subcategory)
                                    <li>
                                        <a href="{{ route('subcategory.products', [slug($subcategory->name), $subcategory->id]) }}">
                                            {{ __($subcategory->name) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="footer-bottom">
            <div class="footer__wrapper">

                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Payment Methods')</h6>
                    <div class="d-flex flex-wrap">
                        @foreach (@$footerElement as $footer)
                            <div class="pay-img">
                                <img src="{{ getImage('assets/images/frontend/footer/' . @$footer->data_values->image, '70x40') }}" alt="payment">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Subscribe Newsletter')</h6>
                    <p class="mb-4 text-white">{{ __(@$footerContent->data_values->subscribe_title) }}</p>
                    <form class="newletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control subscribe-email" placeholder="@lang('Enter Your Email')" required>
                            <button type="submit" class="cmn--btn subscribe-btn"><i class="las la-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Find Us')</h6>
                    <p class="mb-4 text-white"><i class="las la-map-marker-alt"></i>{{ __(@$contactContent->data_values->address) }}</p>
                    <ul class="social-icons justify-content-start">
                        @foreach ($socialElement as $social)
                            <li>
                                <a href="{{ @$social->data_values->url }}">
                                    @php echo $social->data_values->icon; @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="bg--dark">
        <div class="container">
            <div class="copyright-area justify-content-beetween">
                <div class="copyright">
                    @lang('Copyright') &copy; {{ date('Y') }} @lang('Copyright ©.... Mulangila Clothes And Shoe Traders')
                </div>
                <div class="policy-page">
                    @foreach ($policyPages as $policy)
                        <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" class="text-white">{{ __(@$policy->data_values->title) }}{{ $loop->last ? '' : ',' }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
