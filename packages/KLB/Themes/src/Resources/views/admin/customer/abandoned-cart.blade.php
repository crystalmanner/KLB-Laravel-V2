@extends('admin::layouts.content')

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <!-- <h1>{{ __('themes::app.customer.abandoned-cart.title') }}</h1> -->
                <h1>Abandoned Carts</h1>
            </div>
        </div>

        <div class="page-content">
            @inject('abandonedCart','KLB\Themes\Datagrids\AbandonedCartDatagrid')
            {!! $abandonedCart->render() !!}
        </div>
    </div>
    
@endsection