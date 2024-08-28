@php
    $content = getContent('login.content', true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account-section pt-60 bg-white">
        <div class="container">
            <div class="account-wrapper">
                <div class="row gy-5 align-items-center">
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="account-thumb rtl">
                            <img src="{{ getImage('assets/images/frontend/login/' . @$content->data_values->image, '640x650') }}" alt="thumb">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="account-right ps-lg-5">
                            <div class="account-header mb-4">
                                <h5 class="title mb-1">{{ __(@$content->data_values->heading) }}</h5>
                                <p class="mb-0 fs--14px">{{ __(@$content->data_values->subheading) }}</p>
                            </div>
                            <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="form-group">
                                    <label for="email" class="form--label">@lang('Username or Email')</label>
                                    <input type="text" name="username" value="{{ old('username') }}" class="form-control form--control" required>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex flex-wrap justify-content-between mb-2">
                                        <label for="password" class="form--label mb-0">@lang('Password')</label>
                                        <a class="text--base text-decoration-underline forgot-pass" href="{{ route('user.password.request') }}">
                                            @lang('Forgot your password?')
                                        </a>
                                    </div>
                                    <input id="password" type="password" class="form-control form--control" name="password" required>
                                </div>

                                <x-captcha/>

                                <div class="form-group form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        @lang('Remember Me')
                                    </label>
                                </div>

                                <div class="form-group">
                                    <button type="submit" id="recaptcha" class="btn btn--base w-100 h-50">
                                        @lang('Login')
                                    </button>
                                </div>
                                <p class="mb-0">@lang('Don\'t have any account?') <a href="{{ route('user.register') }}" class="text--base text-decoration-underline">@lang('Register')</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
