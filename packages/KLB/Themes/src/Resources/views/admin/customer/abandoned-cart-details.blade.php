@extends('admin::layouts.content')

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <!-- <h1>{{ __('themes::app.customer.abandoned-cart.title') }}</h1> -->
                <h1>Cart Details</h1>
            </div>
        </div>

        <div class="page-content">
        <!-- edit render function with addtional parameter of id -->
            @inject('abandonedCartDetails','KLB\Themes\Datagrids\AbandonedCartDetailsDatagrid')
            {!! $abandonedCartDetails->renderWithId($id) !!}
        </div>
    </div>
    
@endsection