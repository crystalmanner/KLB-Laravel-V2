@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

<template>
    <div :class="`dropdown ${cartItems.length > 0 ? '' : 'disable-active'}`">
        <cart-btn :item-count="cartItems.length"></cart-btn>

        <!-- this is the mini cart modal -->
        <div
            id="cart-modal-content"
            v-if="cartItems.length > 0"
            class="modal-content cart-modal-content hide"
            style ="position: fixed;top: 10%; left: 15%; height: 80%; width: 70%; overflow-y: scroll ">

            <!--Body-->
            <div class="mini-cart-container">
                <div class="row small-card-container" :key="index" v-for="(item, index) in cartItems">
                    <div class="col-3 product-image-container mr15">
                        <a @click="removeProduct(item.id)">
                            <span class="rango-close"></span>

                        </a>

                        <a class="unset" :href="`${$root.baseUrl}/${item.url_key}`">
                            <div
                                class="product-image"
                                :style="`background-image: url(${item.images.medium_image_url});`">
                            </div>
                        </a>
                    </div>

                    <div class="col-9 no-padding card-body align-vertical-top" style="color:black;">
                        <div class="no-padding">
                            <div class="fs16 text-nowrap fw6" v-html="item.name"></div>

                            <div class="fs18 card-current-price fw6">
                                <quantity-changer></quantity-changer>
                                <span class="card-total-price fw6">
                                    {{ item.base_total }}
                                </span>




                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Body
            <div class="mini-cart-container">
                <div class="col-7">
                    <div class="row" style="text-align:left; ">
                        <span class="col-7" style="color:black;padding:0;margin:0">
                            Items
                        </span>

                        <span class="col-2" style="color:black;padding:0;margin:0">
                            Qty
                        </span>

                        <span class="col-2" style="color:black;padding:0;margin:0">
                            Subtotal
                        </span>

                        <span class="col-1" style="color:black;padding:0;margin:0">
                            remove
                        </span>
                    </div>

                    <div class="row small-card-container" :key="index" v-for="(item, index) in cartItems">

                        <div class="col-7 product-image-container mr15" style="padding:0;margin:0;">
                            <a class="unset" :href="`${$root.baseUrl}/${item.url_key}`">
                                <div
                                    class="product-image"
                                    :style="`background-image: url(${item.images.medium_image_url}); height:50px;`">
                                </div>
                            </a>
                            <div class="fs16 text-nowrap fw6" v-html="item.name"></div>
                        </div>

                        <div class="col-2" style="padding:0">
                                <quantity-changer></quantity-changer>
                        </div>
                        <div class="col-2" style="padding:0;color:black;">
                            <span >
                                {{ item.base_total }}
                            </span>
                        </div>
                        <div class="col-1" style="color:black;">
                            <a @click="removeProduct(item.id)">
                                rm
                            </a>
                        </div>


                    </div>
                </div>
                <div class="col-5"></div>
            </div> -->


            <!--Footer-->
            <div class="modal-footer">
                <h2 class="col-6 text-left fw6">
                    {{ subtotalText }}
                </h2>

                <h2 class="col-6 text-right fw6 no-padding">{{ cartInformation.base_sub_total }}</h2>
            </div>

            <div class="modal-footer">
                <a class="col text-left fs16 link-color remove-decoration" :href="viewCart">{{ cartText }}</a>

                <div class="col text-right no-padding">
                    <a href="#" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">Link</a>
                    <a :href="checkoutUrl">
                        <button
                            type="button"
                            class="theme-btn fs16 fw6">
                            {{ checkoutText }}
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'cartText',
            'viewCart',
            'checkoutUrl',
            'checkoutText',
            'subtotalText',
        ],

        data: function () {
            return {
                cartItems: [],
                cartInformation: [],
            }
        },

        mounted: function () {
            this.getMiniCartDetails();
        },

        watch: {
            '$root.miniCartKey': function () {
                this.getMiniCartDetails();
            }
        },

        methods: {
            getMiniCartDetails: function () {
                this.$http.get(`${this.$root.baseUrl}/mini-cart`)
                .then(response => {
                    if (response.data.status) {
                        this.cartItems = response.data.mini_cart.cart_items;
                        this.cartInformation = response.data.mini_cart.cart_details;
                    }
                })
                .catch(exception => {
                    console.log(this.__('error.something_went_wrong'));
                });
            },

            removeProduct: function (productId) {
                this.$http.delete(`${this.$root.baseUrl}/cart/remove/${productId}`)
                .then(response => {
                    this.cartItems = this.cartItems.filter(item => item.id != productId);

                    window.showAlert(`alert-${response.data.status}`, response.data.label, response.data.message);
                })
                .catch(exception => {
                    console.log(this.__('error.something_went_wrong'));
                });
            },

            toggleMiniCart: function () {
                let modal = $('#cart-modal-content')[0];
                if (modal)
                    modal.classList.toggle('hide');

                let accountModal = $('.account-modal')[0];
                if (accountModal)
                    accountModal.classList.add('hide');

                event.stopPropagation();
            }
        }
    }


</script>
