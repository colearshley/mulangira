@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-wrapper">
        <form class="profile-edit-form row register" action="" method="post" enctype="multipart/form-data">
            @csrf
            <div class="profile-user mb-xl-0">
                <label class="btn btn--base profile-img-upload" for="profile-image"><i class="las la-pen"></i></label>
                <div class="thumb">
                    <img class="profile-user-path" src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="user">
                    <input type="file" name="image" class="form-control form--control" accept=".png, .jpg, .jpeg" id="profile-image" hidden>
                </div>

                <div class="remove-image">
                    <i class="las la-times"></i>
                </div>
            </div>
            <div class="profile-form-area">
                @csrf
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('First Name')</label>
                        <input type="text" class="form-control form--control" name="firstname" value="{{ $user->firstname }}" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Last Name')</label>
                        <input type="text" class="form-control form--control" name="lastname" value="{{ $user->lastname }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('E-mail Address')</label>
                        <input class="form-control form--control" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Mobile Number')</label>
                        <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Address')</label>
                        <input type="text" class="form-control form--control" name="address" value="{{ @$user->address->address }}">
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('State')</label>
                        <input type="text" class="form-control form--control" name="state" value="{{ @$user->address->state }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('Zip Code')</label>
                        <input type="text" class="form-control form--control" name="zip" value="{{ @$user->address->zip }}">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('City')</label>
                        <input type="text" class="form-control form--control" name="city" value="{{ @$user->address->city }}">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('Country')</label>
                        <input class="form-control form--control" value="{{ @$user->address->country }}" disabled>
                    </div>

                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn--base w-100 h-50">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('style')
    <style>
        .profile-img-upload {
            width: 40px;
            height: 40px;
            display: flex;
            position: absolute !important;
            bottom: 0 !important;
            right: 50px !important;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            padding: 10px !important;
        }
        .profile-wrapper .profile-user {
            text-align: center;
            width: 100%;
            max-width: 250px;
            height: 160px !important;
            position: relative;
            margin: 0 auto 40px;
        }
        .profile-wrapper .profile-user .thumb {
            width: 160px !important;
            height: 160px !important;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            position: relative;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            var prevImg = $('.profile-user .thumb').html();

            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = $('.profile-user').find('.profile-user-path');
                        preview.attr('src', `${e.target.result}`);
                        preview.fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#profile-image").on('change', function() {
                proPicURL(this);
            });
        })(jQuery);
    </script>
@endpush
