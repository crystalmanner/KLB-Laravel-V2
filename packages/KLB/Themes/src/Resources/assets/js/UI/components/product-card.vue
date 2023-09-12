<template>
    <div class="col-12 lg-card-container list-card product-card row" v-if="list">
        <div class="product-image">
            <a :title="product.name" :href="`${baseUrl}/${product.slug}`">
                <img
                    :src="product.image || product.product_image"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" />

                <!-- <product-quick-view-btn :quick-view-details="product" v-if="!isMobile()"></product-quick-view-btn> -->
            </a>
        </div>

        <div class="product-information">
            <div>
                <div class="product-name">
                    <a :href="`${baseUrl}/${product.slug}`" :title="product.name" class="unset">
                        <span style= "font-family: Georgia" class="fs16">{{ product.name }}</span>
                    </a>
                </div>

                <div class="sticker discount_badge_style" v-if="product.new">
                    <!-- {{ product.new }} -->
                    -20%
                </div>

                <div class="product-price" style= "font-family: Georgia" v-html="product.priceHTML"></div>

                <div class="product-rating" v-if="product.totalReviews && product.totalReviews > 0">
                    <star-ratings :ratings="product.avgRating"></star-ratings>
                    <span>{{ __('products.reviews-count', {'totalReviews': product.totalReviews}) }}</span>
                </div>

                <div class="product-rating" v-else>
                    <span class="fs14" v-text="product.firstReviewText"></span>
                </div>

                <vnode-injector :nodes="getDynamicHTML(product.addToCartHtml)"></vnode-injector>
            </div>
        </div>
    </div>

    <!-- START HERE -->
    <div class="card grid-card product-card-new" v-else>
        <a :href="`${baseUrl}/${product.slug}`" :title="product.name" class="product-image-container">
            <img
                loading="lazy"
                :alt="product.name"
                :src="product.image || product.product_image"
                :data-src="product.image || product.product_image"
                class="card-img-top lzy_img"
                :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" />
                <!-- :src="`${$root.baseUrl}/vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png`" /> -->

            <!-- <product-quick-view-btn :quick-view-details="product"></product-quick-view-btn> -->
        </a>

        <div class="card-body">
            <div class="col-12" style="display: flex; justify-content: center;">
                <a
                    class="unset"
                    :title="product.name"
                    :href="`${baseUrl}/${product.slug}`">

                    <span class="fs16" style= "font-family: Georgia" >{{ product.name | truncate }}</span>
                </a>
            </div>

            <div class="sticker discount_badge_style" v-if="product.new">
                <!-- {{ product.new }} -->
                -20%
            </div>

            <div class="fs16" style="display: flex; justify-content: center; font-family: Georgia;" v-html="product.priceHTML"></div>

            <!-- <div
                class="product-rating col-12 no-padding"
                v-if="product.totalReviews && product.totalReviews > 0">

                <star-ratings :ratings="product.avgRating"></star-ratings>
                <a class="fs14 align-top unset active-hover" :href="`${$root.baseUrl}/reviews/${product.slug}`">
                    {{ __('products.reviews-count', {'totalReviews': product.totalReviews}) }}
                </a>
            </div> -->

            <!-- <div class="product-rating col-12 no-padding" v-else>
                <span class="fs14" v-text="product.firstReviewText"></span>
            </div> -->

            <vnode-injector :nodes="getDynamicHTML(product.addToCartHtml)"></vnode-injector>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: [
            'list',
            'product',
        ],

        data: function () {
            return {
                'addToCart': 0,
                'addToCartHtml': '',
            }
        },

        methods: {
            'isMobile': function () {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
</script>