@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row">
                <h5 class="text-center">{{ __($pageTitle) }}</h5>
                <div class="col-md-12 mt-5">
                    @php
                        echo $policy->data_values->details;
                    @endphp

                </div>
            </div>
        </div>
    </section>
@endsection
