{!! view_render_event('bagisto.shop.products.view.stock.before', ['product' => $product]) !!}

<div class="col-3 product-label-sm">Availability: </div>
<div class="col-9 availability product-label-sm">

    <div
        class="{{! $product->haveSufficientQuantity(1) ? '' : 'active' }} disable-box-shadow">
            @if ( $product->haveSufficientQuantity(1) === true )
                {{ __('shop::app.products.in-stock') }}
                <button style="color:black"><a style="color:white" href="#related_products_section">Buy These Instead</a></button>
            @elseif ( $product->haveSufficientQuantity(1) > 0 )
                {{ __('shop::app.products.available-for-order') }}
            @else
                {{ __('shop::app.products.out-of-stock') }}
                <button style="color:black"><a style="color:white" href="#related_products_section">Buy These Instead</a></button>
            @endif
    </div>
</div>

{!! view_render_event('bagisto.shop.products.view.stock.after', ['product' => $product]) !!}
