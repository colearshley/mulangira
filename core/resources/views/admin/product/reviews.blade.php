@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm ">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Reviews')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $review->user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                                </div>
                                                <span class="name"><a href="{{ route('admin.users.detail', $review->user->id) }}">{{ $review->user->username }}</a></span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ strLimit($review->review_comment, 40) }}
                                            <button type="button" data-review="{{ $review->review_comment }}" class="icon-btn btn--info btn-sm reviewBtn"><i class="las la-eye"></i></button>
                                        </td>
                                        <td>
                                            {{ getAmount($review->stars)}} @lang('stars')
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--danger  confirmationBtn" data-action="{{ route('admin.product.review.remove', $review->id) }}" data-question="@lang('Are you sure to remove this review?')">
                                                <i class="la la-trash"></i> @lang('Remove')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Review')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted review-detail"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.reviewBtn').on('click', function() {
                let modal = $('#reviewModal');
                let review = $(this).data('review');
                modal.find('.review-detail').text(review);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
