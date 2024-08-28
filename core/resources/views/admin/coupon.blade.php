@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Min Order Amount')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Start Date')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->name }}</td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($coupon->min_order) }}</td>
                                        <td>
                                            {{ showAmount($coupon->discount) }}{{ $coupon->discount_type == 1 ? __($general->cur_text) : '%' }}
                                        </td>
                                        <td>
                                            <span>{{ showDateTime($coupon->start_date) }}</span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($coupon->end_date)->diffInDays(\Carbon\Carbon::now()->format('Y-m-d')) }}
                                            {{ $coupon->end_date >= now()->format('Y-m-d') ? trans('Days Left') : trans('Days ago expired') }}
                                        </td>
                                        <td> @php echo $coupon->statusBadge; @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $coupon }}" data-modal_title="@lang('Edit Coupon')">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>

                                                @if (!$coupon->status)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.coupon.status', $coupon->id) }}" data-question="@lang('Are you sure to enable this coupon?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger  confirmationBtn" data-action="{{ route('admin.coupon.status', $coupon->id) }}" data-question="@lang('Are you sure to disable this coupon?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($coupons->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($coupons) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.coupon.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Discount') </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="discount" required>
                                        <select name="discount_type" class="input-group-text">
                                            <option value="1" @selected(old('discount_type') == 1)>{{ __($general->cur_text) }}</option>
                                            <option value="2" @selected(old('discount_type') == 2)>@lang('%')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Minimum Order') </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="min_order" value="{{ old('min_order') }}" required>
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label> @lang('Start Date')</label>
                                    <input type="text" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' placeholder="@lang('Select date')" name="start_date" autocomplete="off" value="{{ old('start_date') }}" required>
                                </div>
                                <div class="form-group">
                                    <label> @lang('End Date')</label>
                                    <input type="text" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' placeholder="@lang('Select date')" name="end_date" autocomplete="off" value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by name..." />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Coupon')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('style')
    <style>
        .datepickers-container {
            z-index: 99999999;
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.datepicker-here').on('input keydown keypress keyup', function () {
                return false;
            });

            $('.datepicker-here').datepicker({
                minDate: new Date(),
            });
        })(jQuery);
    </script>
@endpush
