<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title> {{ $general->siteName(__($pageTitle)) }}</title>
        @include('partials.seo')
        <!-- Bootstrap CSS -->
        <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />

        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/animate.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lightbox.min.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/owl.min.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">

        @stack('style-lib')

        @stack('style')
        <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}">

    </head>

    <body class="overflow-hidden">
        <div class="preloader">
            <div class="loader-bg">
                <div class="loader-inner">
                    <span></span>
                </div>
            </div>
        </div>

        @yield('app')

        <div class="overlay"></div>
        @php $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first(); @endphp

        @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
            <!-- cookies dark version start -->
            <div class="cookies-card text-center hide">
                <div class="cookies-card__icon bg--base">
                    <i class="las la-cookie-bite"></i>
                </div>
                <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" target="_blank" class="text--base">@lang('learn more')</a></p>
                <div class="cookies-card__btn mt-4">
                    <button type="button" class="btn btn--base w-100 policy">@lang('Allow')</button>
                </div>
            </div>
            <!-- cookies dark version end -->
        @endif

        <button type="button" class="scrollToTop"><i class="las la-angle-double-up"></i></button>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset($activeTemplateTrue . 'js/jquery-ui.min.js') }}"></script>
        <script src="{{ asset($activeTemplateTrue . 'js/rafcounter.min.js') }}"></script>
        <script src="{{ asset($activeTemplateTrue . 'js/lightbox.min.js') }}"></script>
        <script src="{{ asset($activeTemplateTrue . 'js/owl.min.js') }}"></script>
        <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
        <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>

        <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

        @stack('script-lib')
        @stack('script')

        @include('partials.plugins')
        @include('partials.notify')

        <script>
            (function($) {
                "use strict";
                $(".langSel").on("change", function() {
                    window.location.href = "{{ route('home') }}/change/" + $(this).val();
                });

                $('.policy').on('click', function() {
                    $.get('{{ route('cookie.accept') }}', function(response) {
                        $('.cookies-card').addClass('d-none');
                    });
                });

                setTimeout(function() {
                    $('.cookies-card').removeClass('hide')
                }, 2000);

                $.each($('input, select, textarea'), function(i, element) {
                    var elementType = $(element);
                    if (elementType.attr('type') != 'checkbox') {
                        if (element.hasAttribute('required')) {
                            $(element).closest('.form-group').find('label').addClass('required');
                        }
                    }

                });

                Array.from(document.querySelectorAll('table')).forEach(table => {
                    let heading = table.querySelectorAll('thead tr th');
                    Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                        Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                            (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                        });
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
