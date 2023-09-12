<template>

    <div>
        <span class="col-8">{{ grandTotal }}</span>
        <span class="col-4 text-right fw6" id="grand-total-amount-detail">
            {{ item.base_grand_total}}


        </span>
        <span>Hello</span>

        <span class="col-4 text-right fw6"  id="discount-detail">
            {{ item.base_discount_amount}}

        </span>

        <span>Hello</span>

    </div>









</template>

<script>
export default {
    // https://vuejs.org/v2/guide/components-props.html#Prop-Types
    props: {
        'cartText': String,
        'viewCart': String,
        'checkoutUrl': String,
        'checkoutText': String,
        'subtotalText': String,
        'percent': {
            type: Number,
            default: 0.0
        },
        'grandTotal': Number
    },

    data: function () {
        return {
            cartItems: [],
            cartInformation: []
        }
    },
    watch: {
        '$root.miniCartKey': function () {
            this.getMiniCartDetails();
        }
    },


    mounted: function () {
        this.getMiniCartDetails();

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




        discountedam: function(base_grand_total,base_discount_amount) {
            const dec = (base_grand_total - base_discount_amount) / base_grand_total;
            this.percent = (1 - dec) * 100;
            return percent;
        }

    }
}


</script>
