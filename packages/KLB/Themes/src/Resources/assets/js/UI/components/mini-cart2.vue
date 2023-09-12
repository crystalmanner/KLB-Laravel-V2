@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

<template>
    <div >

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
                <a  :href="viewCart">

                    <button  >Edit Cart</button>


                </a>

                <div class="col text-right no-padding">
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
            this.$http.get(`${this.$root.baseUrl}/mini-cart2`)
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
        }
    }
}

</script>

