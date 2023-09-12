{!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

<div class="product-price">
    <span>Kalista</span>

    <br>

    <span style = "font-family: Georgia">{!! $product->getTypeInstance()->getPriceHtml() !!}</span>
</div>

{!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}