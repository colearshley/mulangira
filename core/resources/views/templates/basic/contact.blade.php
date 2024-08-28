@php
    $contactContent = getContent('contact_us.content', true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="contact-section pt-80 bg-white">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="contact-info">
                        <div class="contact-info__icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info__content">
                            <h6 class="title">@lang('Phone Number')</h6>
                            <a href="tel:{{ @$contactContent->data_values->contact_number }}" class="text--base">{{ @$contactContent->data_values->contact_number}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="contact-info">
                        <div class="contact-info__icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info__content">
                            <h6 class="title">@lang('Contact Email')</h6>
                            <a href="mailto:{{ @$contactContent->data_values->contact_email }}" class="text--base">{{@$contactContent->data_values->contact_email}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="contact-info">
                        <div class="contact-info__icon">
                            <i class="las la-map-marker"></i>
                        </div>
                        <div class="contact-info__content">
                            <h6 class="title">@lang('Main Address')</h6>
                            <span class="text--base">{{ __(@$contactContent->data_values->address) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4 gy-sm-5 align-items-center pt-5">
                <div class="col-lg-6">
                    <div class="contact-wrapper">
                        <h4 class="title">{{ __(@$contactContent->data_values->title) }}</h4>
                        <p class="text--base mb-4">{{ __(@$contactContent->data_values->subtitle) }}</p>
                        <form method="post" action="" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">@lang('Name')</label>
                                    <input name="name" type="text" class="form-control form--control form--control-4" value="{{ old('name',@$user->fullname) }}" @if($user) readonly @endif required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">@lang('Email')</label>
                                    <input name="email" type="email" class="form-control form--control form--control-4" value="{{  old('email',@$user->email) }}" @if($user) readonly @endif required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Subject')</label>
                                <input name="subject" type="text" class="form-control form--control form--control-4" value="{{ old('subject') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Message')</label>
                                <textarea name="message" wrap="off" class="form-control form--control form--control-4" required>{{ old('message') }}</textarea>
                            </div>
                            <x-captcha class="form--control-4"/>
                            <div class="form-group">
                                <button type="submit" class="btn btn--base  h-50 w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-thumb ps-lg-4">
                        <img src="{{ getImage('assets/images/frontend/contact_us/' . @$contactContent->data_values->image, '480x430') }}" alt="contact">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
