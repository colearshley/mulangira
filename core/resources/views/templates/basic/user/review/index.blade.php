@extends($activeTemplate . 'layouts.master')
@section('content')
    <table class="table cmn--table">
        <thead>
            <tr>
                <th>@lang('Product Name')</th>
                <th>@lang('Image')</th>
                <th>@lang('Price')</th>
                <th>@lang('Rating')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($products as $product)
                <tr id="tr_{{ $product->id }}">
                    <td>{{ __($product->name) }}</td>
                    <td>
                        <img src="{{ $product->imageShow() }}" alt="@lang('image')" class="show-img" width="40px">
                    </td>
                    <td class="text--base">
                        <strong>{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</strong>
                    </td>
                    <td>
                        <div class="ratings">
                            @php
                                echo showProductRatings($product->avg_rate);
                            @endphp
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('user.review.create', [slug($product->name), $product->id]) }}" class="btn btn-sm btn--base @if ($product->reviews->count()) disabled @endif" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('Add Review')">
                            <i class="las la-star-of-david"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="100%" class="justify-content-center text-center text--danger">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ paginateLinks($products) }}
@endsection
