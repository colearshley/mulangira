@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.product.store', $product->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">@lang('Product Information')</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>@lang('Name')</label>
                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            </div>

                            <div class="form-group col-md-3">
                                <label>@lang('Brands')</label>
                                <select class="form-control" name="brand_id" required>
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected(@$product->brand_id == $brand->id)>{{ __($brand->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Category')</label>
                                <select name="category_id" class="form-control" required>
                                    <option selected disabled>@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(@$product->category_id == $category->id) data-subcategories="{{ $category->subcategories }}">
                                            {{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Subcategory')</label>
                                <select name="subcategory_id" class="form-control" required>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Product SKU')</label>
                                <input type="text" name="product_sku" class="form-control" value="{{ $product->product_sku }}" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Stock Quantity')</label>
                                <input type="number" name="quantity" min="0" class="form-control" value="{{ $product->quantity }}" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Price')</label>
                                <div class="input-group">
                                    <input type="number" name="price" step="any" min="0" class="form-control" value="{{ old('price', getAmount(@$product->price)) }}" required />
                                    <span class="input-group-text"> {{ __($general->cur_text) }} </span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Discount') </label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="discount" min="0" value="{{ getAmount($product->discount) }}">
                                    <select name="discount_type" class="input-group-text">
                                        <option value="1" @selected($product->discount_type == 1)>{{ __($general->cur_text) }}</option>
                                        <option value="2" @selected($product->discount_type == 1)>@lang('%')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">@lang('Make Product Digital')</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Is Digital')</label>
                                <select name="digital_item" class="form-control" required>
                                    <option value="0" @selected($product->digital_item == 0)>@lang('No')</option>
                                    <option value="1" @selected($product->digital_item == 1)>@lang('Yes')</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 typeDiv">
                                @if ($product->digital_item)
                                    <label>@lang('Select Type')</label>
                                    <select name="file_type" class="form-control" required>
                                        <option value="1" @selected($product->file_type == 1)>@lang('File')</option>
                                        <option value="2" @selected($product->file_type == 2)>@lang('Link')</option>
                                    </select>
                                @endif
                            </div>
                            <div class="form-group col-md-4 fileLinkDiv">
                                @if ($product->digital_item)
                                    @if ($product->file_type == 1 && $product->file)
                                        <label class="required">@lang('Upload File')</label>
                                        @if ($product->digital_item)
                                            <a href="{{ route('download', [$product->id, $product->file]) }}" class="mr-3 text--primary">
                                                <i class="las la-file"></i> @lang('Download File')
                                            </a>
                                        @endif

                                        <div class="file-upload-wrapper" data-text="@lang('Upload Your File')">
                                            <input type="file" name="file" id="inputAttachments" accept=".pdf, .docx, .txt, .zip, .xlx, .csv, .ai, .psd, .pptx" class="file-upload-field">
                                        </div>
                                        <small class="mt-2">
                                            @lang('Supported files'): @lang('.pdf'), @lang('.docx'), @lang('.txt'), @lang('.zip'), @lang('.xlx'), @lang('.csv'), @lang('.ai'), @lang('.psd'), @lang('.pptx')
                                        </small>
                                    @elseif ($product->file_type == 2 && $product->link)
                                        <label class="required">@lang('Link')</label>
                                        <input type="url" name="link" class="form-control" value="{{ $product->link }}" required />
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        @lang('Product Details')
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>@lang('Summary')</label>
                            <textarea name="summary" class="form-control" cols="2" rows="5" required>{{ $product->summary }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea rows="5" class="form-control nicEdit" name="description">{{ $product->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card border--primary mt-3">
                        <div class="card-header bg--primary d-flex justify-content-between">
                            <h5 class="text-white">@lang('Product Specificaiton')</h5>
                            <button type="button" class="btn btn-sm btn-outline-light float-end addFeatureData"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                        </div>
                        <div class="card-body">
                            <div class="row addedFeature">
                                @if ($product->features)
                                    @foreach ($product->features as $freature)
                                        @php $featureIndex = $loop->index; @endphp
                                        <div class="col-md-12 service-data">
                                            <div class="row gy-3 ">
                                                <div class="col-md-6">
                                                    <input name="features[{{ $loop->iteration }}][title]" class="form-control" type="text" value="{{ $freature['title'] }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" name="features[{{ $loop->iteration }}][description]" value="{{ $freature['description'] }}">
                                                        <button type="button" class="input-group-text btn btn--danger removeServiceBtn">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">@lang('Image Section')</div>
                    <div class="card-body">
                        <div class="image-uploader-wrapper">
                            <div class="profile-uploader">
                                <label class="form-group">@lang('Main Image') :</label>
                                <div class="payment-method-item">
                                    <div class="payment-method-header d-flex flex-wrap">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url('{{ $product->imageShow() }}')"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg">
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="gallery-uploader">
                                <label class="form-label">@lang('Gallery Image') :</label>
                                <div class="input-field">
                                    <div class="input-images"></div>
                                    <small class="form-text text-muted">
                                        <i class="las la-info-circle"></i> @lang('You can only upload a maximum of 6 images')</label>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Update')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/admin/css/image-uploader.min.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let oldSubcategory = "{{ $product->subcategory_id }}";
            let featured = 2;

            @if ($product->features)
                let extra = "{{ $featureIndex }}";
                featured  = parseInt(featured) + parseInt(extra);
            @endif

            @if (!empty($galleries))
                let preloaded = @json($galleries);
                console.log(preloaded);
            @endif

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'gallery',
                preloadedInputName: 'olddd',
                maxFiles: 6
            });

            $(document).on('input', 'input[name="gallery[]"]', function() {
                var fileUpload = $("input[type='file']");
                if (parseInt(fileUpload.get(0).files.length) > 6) {
                    $('#errorModal').modal('show');
                }
            });

            $('[name=category_id]').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = `<option  disabled selected>@lang('Select one')</option>`;

                $.each(subcategories, function(id, subcat) {
                    html += `<option  value="${subcat.id}" ${oldSubcategory == subcat.id ?'selected':''}>${subcat.name}</option>`
                });

                $('[name=subcategory_id]').html(html);
            }).change();

            let linkHtml = `<label class="required">@lang('Link')</label>
                            <input type="url" name="link" class="form-control" required />`;

            $('[name=digital_item]').on('change', function() {
                let value = $(this).val();
                let html;

                if (value == 1) {
                    html = `<label class="required">@lang('Select Type')</label>
                            <select name="file_type" class="form-control" required>
                                <option value="1">@lang('File')</option>
                                <option value="2" selected>@lang('Link')</option>
                            </select>`;
                    $('.fileLinkDiv').html(linkHtml);
                } else {
                    html = ``;
                    $('.fileLinkDiv').empty();
                }

                $('.typeDiv').html(html);
            });

            $(document).on('change', '[name=file_type]', function() {
                let value = $(this).val();
                let html;

                if (value == 1) {
                    html = `<label class="required">@lang('Upload File')</label>
                            <div class="file-upload-wrapper" data-text="@lang('Upload Your File')">
                                <input type="file" name="file" id="inputAttachments" accept=".pdf, .docx, .txt, .zip, .xlx, .csv, .ai, .psd, .pptx" class="file-upload-field" required/>
                            </div>
                            <small class="mt-2">
                                @lang('Supported files'): @lang('.pdf'), @lang('.docx'), @lang('.txt'), @lang('.zip'), @lang('.xlx'), @lang('.csv'), @lang('.ai'), @lang('.psd'), @lang('.pptx')
                            </small>`;
                } else {
                    html = linkHtml;
                }

                $('.fileLinkDiv').html(html);
            });

            $('.addFeatureData').on('click', function() {
                let html = ` <div class="col-md-12 service-data">
                            <div class="row gy-3 ">
                                 <div class="col-md-6">
                                    <input name="features[${featured}][title]" class="form-control" type="text" required placeholder="@lang('Title')">
                                </div>
                                 <div class="col-md-6">
                                    <div class="input-group mb-3">

                                        <input type="text" class="form-control" name="features[${featured}][description]" required placeholder="@lang('Description')">
                                        <button type="button" class="input-group-text btn btn--danger removeServiceBtn">
                                        <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>

                         </div>
                    </div>`;
                $('.addedFeature').append(html);
                featured += 1;
            });

            $(document).on('click', '.removeServiceBtn', function() {
                $(this).closest('.service-data').remove();
            });

        })(jQuery);
    </script>
@endpush
