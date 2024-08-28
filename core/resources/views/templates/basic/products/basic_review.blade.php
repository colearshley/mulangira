<div class="review-item">
    <div class="thumb">
        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$review->user->image, getFileSize('userProfile')) }}" alt="review">
    </div>
    <div class="content">
        <div class="entry-meta">
            <h6 class="posted-on">
                <span class="text--base">{{ __(@$review->user->username) }}</span>
                <span>@lang('Posted on') {{ showDateTime($review->create_at) }}</span>
            </h6>
            <div class="ratings">
                @php
                    echo showProductRatings($review->stars);
                @endphp
            </div>
        </div>
        <div class="entry-content">
            <p>{{ __($review->review_comment) }}</p>
        </div>
    </div>
</div>

@push('style')
    <style>
        .review-item .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush


