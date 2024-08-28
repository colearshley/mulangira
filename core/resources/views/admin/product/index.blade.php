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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Product SKU')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Stock Quantity')</th>
                                    <th>@lang('status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ $product->imageShow() }}"" alt="@lang('image')">
                                                </div>
                                                <span class="name">{{ __($product->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->product_sku }}</td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($product->price) }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>
                                            @php
                                                echo $product->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.product.reviews', $product->id) }}" class="btn btn-sm btn-outline--info">
                                                    <i class="las la-comment-dots"></i> @lang('Reviews')
                                                </a>

                                                <button class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown">
                                                    <i class="las la-ellipsis-v"></i> @lang('Action')
                                                </button>

                                                <div class="dropdown-menu p-0">
                                                    <a href="{{ route('admin.product.edit', $product->id) }}" class="dropdown-item categoryEdit">
                                                        <i class="las la-pen"></i> @lang('Edit')
                                                    </a>
                                                    @if (!$product->status)
                                                        <button class="dropdown-item text--primary confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to enable this product?')">
                                                            <i class="la la-eye"></i> @lang('Enable')
                                                        </button>
                                                    @else
                                                        <button class="dropdown-item text--danger confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to disable this product?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @endif
                                                    @if (!$product->featured_product)
                                                        <button class=" dropdown-item text--info confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Are you sure to featured this product?')">
                                                            <i class="las la-check-circle"></i> @lang('Featured')
                                                        </button>
                                                    @else
                                                        <button class=" dropdown-item text--warning confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Are you sure to not featured this ?')">
                                                            <i class="las la-times-circle"></i> @lang('Unfeatured')
                                                        </button>
                                                    @endif

                                                    @if (!$product->hot_deals)
                                                        <button class="dropdown-item text--info confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Are you sure to enable hot deal this product?')">
                                                            <i class="la la-calendar-times"></i> @lang('Hot Deal Enable')
                                                        </button>
                                                    @else
                                                        <button class="dropdown-item text--warning confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Are you sure to disable hot deal this product?')">
                                                            <i class="la la-check-square"></i> @lang('Hot Deal Disable')
                                                        </button>
                                                    @endif

                                                    @if (!$product->today_deals)
                                                        <button class="dropdown-item text--info confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Are you sure to enable today deals this product?')">
                                                            <i class="la la-times-circle"></i> @lang('Today Deal Enable')
                                                        </button>
                                                    @else
                                                        <button class="dropdown-item text--warning confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Are you sure to enable today deals this product?')">
                                                            <i class="la la-clock"></i> @lang('Today Deal Disable')
                                                        </button>
                                                    @endif
                                                </div>
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
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($products) }}
                    </div>
                @endif
           </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />

    <a href="{{ route('admin.product.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i> @lang('Add New')
    </a>
@endpush


@push('style')
<style>
.h-45 {
    line-height: 28.5px !important;
}
</style>
@endpush
