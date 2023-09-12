<div class="mini-cart-container pull-right" style= "overflow-y:scroll" >

    <!-- this is the search bar, wishlist button, and shopping cart button -->
    <search-wish-shop
        view-cart="{{ route('shop.checkout.cart.index') }}"
        cart-text="{{ __('shop::app.minicart.view-cart') }}"
        checkout-text="{{ __('shop::app.minicart.checkout') }}"
        checkout-url="{{ route('shop.checkout.onepage.index') }}"
        subtotal-text="{{ __('shop::app.checkout.cart.cart-subtotal') }}">
    </search-wish-shop>

{{-- A copy of the shopping cart for the modal --}}
<!-- this is the mini cart modal -->
    <div
        id="cart-modal-content"
        class="modal-content cart-modal-content hide"
        style ="position: fixed;top: 10%; left: 15%; height: 80%; width: 70%; overflow-y:scroll ">
        <!-- this is a copy of the main shopping cart Vue component -->
        <cart-component></cart-component>
    </div>
    <!-- /#cart-modal-content -->

    {{-- A copy of the shopping cart for the modal --}}
    <!-- this is the mini cart modal -->
    <!--<div
    id="cart-modal-content"
<<<<<<< HEAD
    class="modal-content cart-modal-content "
    style ="position: fixed;top: 10%; left: 15%; height: 80%; width: 70%; ">
=======
    class="modal-content cart-modal-content hide"
    style ="position: fixed;top: 10%; left: 15%; height: 80%; width: 70%; overflow-y:scroll ">
>>>>>>> 804c81081b2c893ebd6d78c7d456fc76638d72df
         this is a copy of the main shopping cart Vue component -->
       <!-- <cart-component></cart-component>
    </div>  -->
</div>
