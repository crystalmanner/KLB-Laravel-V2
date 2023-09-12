{!! view_render_event('bagisto.shop.products.add_to_cart.before', ['product' => $product]) !!}

    <div class="mx-0 no-padding">
        {{-- What is this for? --}}
         {{-- @if (isset($showCompare) && $showCompare)
            <compare-component
                @auth('customer')
                    customer="true"
                @endif

                @guest('customer')
                    customer="false"
                @endif

                slug="{{ $product->url_key }}"
                product-id="{{ $product->id }}"
                add-tooltip="{{ __('velocity::app.customer.compare.add-tooltip') }}">
            </compare-component>
        @endif --}}

        {{-- This includes the buy now button before the heart wishlist  --}}
        @if (isset($buyNow) && $buyNow)
            @include('shop::products.buy-now-to-cart', [
                'product'           => $product,
            ])
        @endif

        {{-- This includes the heart wishlist button --}}
        @if (! (isset($showWishlist) && !$showWishlist))
            @include('shop::products.wishlist', [
                'addClass' => $addWishlistClass ?? ''
            ])
        @endif

        {{-- This is the div that contains the Add to Cart button on a product page --}}
        <div class="add-to-cart-btn pl0">
            @if (isset($form) && !$form)
                <!-- add-to-cart - isset($form) -->
                <button
                    type="submit"
                    name="add_to_cart_{{ $product->product_id }}"
                    {{-- id="add_to_cart_{{ $product->product_id }}" --}}
                    {{ ! $product->isSaleable() ? 'disabled' : '' }}
                    class="theme-btn {{ $addToCartBtnClass ?? '' }}">

                    @if (! (isset($showCartIcon) && !$showCartIcon))
                        <i class="material-icons text-down-3">shopping_cart</i>
                    @endif

                    {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') :  __('shop::app.products.add-to-cart') }}
                </button>
            @elseif(isset($addToCartForm) && !$addToCartForm)
                <form
                    method="POST"
                    action="{{ route('cart.add', $product->product_id) }}">

                    @csrf

                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button
                        type="submit"
                        name="add_to_cart_{{ $product->product_id }}"
                        {{-- id="add_to_cart_{{ $product->product_id }}" --}}
                        {{ ! $product->isSaleable() ? 'disabled' : '' }}
                        class="btn btn-add-to-cart {{ $addToCartBtnClass ?? '' }}">

                        @if (! (isset($showCartIcon) && !$showCartIcon))
                            <i class="material-icons text-down-3">shopping_cart</i>
                        @endif

                        <span class="fs14 fw6 text-uppercase text-up-4">
                            {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart') }}
                        </span>
                    </button>
                </form>
            @else
                <!-- Vue component for add-to-cart -->
                <add-to-cart
                    form="true"
                    csrf-token='{{ csrf_token() }}'
                    product-flat-id="{{ $product->id }}"
                    product-id="{{ $product->product_id }}"
                    reload-page="{{ $reloadPage ?? true }}"
                    move-to-cart="{{ $moveToCart ?? true }}"
                    add-class-to-btn="{{ $addToCartBtnClass ?? '' }}"
                    is-enable={{ ! $product->isSaleable() ? 'false' : 'true' }}
                    show-cart-icon={{ !(isset($showCartIcon) && !$showCartIcon) }}
                    btn-text="{{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart') }}">
                </add-to-cart>
            @endif
        </div>
    </div>

{!! view_render_event('bagisto.shop.products.add_to_cart.after', ['product' => $product]) !!}
