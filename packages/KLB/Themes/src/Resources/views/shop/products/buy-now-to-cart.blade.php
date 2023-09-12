{!! view_render_event('bagisto.shop.products.buy_now_to_cart.before', ['product' => $product]) !!}

{{-- This occurs when this blade template is already called from within a </form> element --}}
@if (isset($form) && !$form)
<button
        type="button"
        name="buy_now_button_{{ $product->product_id }}"
        id="buy_now_button_{{ $product->product_id }}"
        value="buy_now_button_{{ $product->product_id }}"
        data-product-id="{{ $product->product_id }}"
        {{ ! $product->isSaleable() ? 'disabled' : '' }}
        class="btn-buy-now {{ $buyNowBtnClass ?? '' }}">

        <!-- the black shopping card icon on the button -->
        @if (!isset($showCartIcon) || $showCartIcon))
            <i class="material-icons text-down-3">shop</i>
        @endif

        {{-- <span class="fs14 fw6 text-uppercase text-up-4"> --}}
            {{-- The double-underscore function is explained here:
                https://laravel.com/docs/7.x/localization#retrieving-translation-strings
            It's used for retrieving localized versions of strings so that "Buy Now" will
            appear appropriately in the customer's language. Thankfully, Bagisto already
            includes this support. --}}
            {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.buy-now') }}
        {{-- </span> --}}
    </button>
@else
<!-- direct form embedded into the page; works well! the only issue now is to
    somehow update the quantity input value for the form below when the quantity
    is updated while viewing a single product:
    packages/KLB/Themes/src/Resources/views/shop/products/view.blade.php
-->
<form
    method="POST"
    {{-- The routes are listed in packages/Webkul/Shop/src/Http/routes.php
        The original route was named cart.add, which redirects to shop.checkout.cart.index --}}
    action="{{ route('cart.buy_now', $product->product_id) }}"
    name="buy_now_button_{{ $product->product_id }}"
    id="buy_now_button_{{ $product->product_id }}"
    value="buy_now_button_{{ $product->product_id }}"
    class="buy-now-form">

    <!-- the token necessary for submitting a form -->
    @csrf

    {{-- This value is required in order to redirect to /checkout/onepage;
        the add() function in packages/Webkul/Shop/src/Http/Controllers/CartController.php
        looks for this value in the request and redirects if present --}}
    <input type="hidden" name="is_buy_now" value="1">
    {{-- The ID of the current product --}}
    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
    {{-- Since this button buys a product directly, this value determines the
        quantity being purchased; ideally, if the quantity is adjusted, this
        value should be changed dynamically via javascript --}}
    <input type="hidden" name="quantity" value="1">
    <button
        type="submit"
        name="buy-now-button-{{ $product->product_id }}"
        id="buy-now-button-{{ $product->product_id }}"
        {{ ! $product->isSaleable() ? 'disabled' : '' }}
        class="btn-buy-now {{ $buyNowBtnClass ?? '' }}">

        <!-- the black shopping card icon on the button -->
        @if (!isset($showCartIcon) || $showCartIcon))
            <i class="material-icons text-down-3">shop</i>
        @endif

        {{-- <span class="fs14 fw6 text-uppercase text-up-4"> --}}
            {{-- The double-underscore function is explained here:
                https://laravel.com/docs/7.x/localization#retrieving-translation-strings
            It's used for retrieving localized versions of strings so that "Buy Now" will
            appear appropriately in the customer's language. Thankfully, Bagisto already
            includes this support. --}}
            {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.buy-now') }}
        {{-- </span> --}}
    </button>
</form>
@endif

{{-- Vue component for buy now;
    packages/KLB/Themes/src/Resources/assets/js/app.js lists the Vue components
    packages/KLB/Themes/src/Resources/assets/js/UI/components/buy-now.vue is the
    Vue component file for the buy-now button below. This is currently unused
    until I can figure out how to respect the quantity specified.
      --}}
{{-- <buy-now
form="true"
csrf-token='{{ csrf_token() }}'
product-flat-id="{{ $product->id }}"
product-id="{{ $product->product_id }}"
reload-page="{{ $reloadPage ?? false }}"
move-to-cart="{{ $moveToCart ?? false }}"
add-class-to-btn="{{ $addToCartBtnClass ?? '' }}"
is-enable={{ ! $product->isSaleable() ? 'false' : 'true' }}
show-cart-icon={{ !(isset($showCartIcon) && !$showCartIcon) }}
btn-text="{{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.buy-now') }}">
</buy-now> --}}


{!! view_render_event('bagisto.shop.products.buy_now_to_cart.after', ['product' => $product]) !!}
