<style>
    .camera-icon {
        background-image: url("{{ asset('/vendor/webkul/ui/assets/images/Camera.svg') }}");
    }
</style>

<!-- Cart Section -->
<script type="text/x-template" id="cart-btn-template">

    {{-- this is the mini-cart button in the upper right-hand corner --}}
    <button
        type="button"
        id="mini-cart"
        @click="toggleMiniCart"
        :class="`btn btn-link disable-box-shadow ${itemCount == 0 ? 'cursor-not-allowed' : ''}`">

        <div class="@if(Route::is('shop.home.index')) home @endif mini-cart-content" >

            <svg style="height:27px; width:27px" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 30 30">
                <g>
                    <g>
                        <path d="M20,6V5c0-2.761-2.239-5-5-5s-5,2.239-5,5v1H4v24h22V6H20z M12,5c0-1.657,1.343-3,3-3s3,1.343,3,3v1h-6V5z M24,28H6V8h4v3    h2V8h6v3h2V8h4V28z"></path>
                    </g>
                </g>
            </svg>

            <span class="badge" v-text="itemCount" v-if="itemCount != 0"></span>
        </div>

    </button>

</script>

<script type="text/x-template" id="close-btn-template">

    <button type="button" class="close disable-box-shadow">
        <span class="white-text fs20" @click="togglePopup">×</span>
    </button>

</script>

<!-- quantity changer -->
<script type="text/x-template" id="quantity-changer-template">
    <div :class="`quantity control-group ${errors.has(controlName) ? 'has-error' : ''}`">
        <label class="required ">{{ __('shop::app.products.quantity') }}</label>
        <button type="button" class="decrease" @click="decreaseQty()">-</button>

        <input
            :value="qty"
            class="control"
            :name="controlName"
            :v-validate="validations"
            data-vv-as="&quot;{{ __('shop::app.products.quantity') }}&quot;"
            readonly />

        <button type="button" class="increase" @click="increaseQty()">+</button>

        <span class="control-error" v-if="errors.has(controlName)">@{{ errors.first(controlName) }}</span>
    </div>
</script>

@include('velocity::UI.header')

<!-- logo section -->
<script type="text/x-template" id="logo-template">
    <a
        :class="`left ${addClass}`"
        href="{{ route('shop.home.index') }}">

        @if (Route::is('shop.home.index'))
            <img class="logo" src="{{ asset('themes/KLB-theme/assets/images/Kalista_LOGO_White.png') }}" />
        @else
            <img class="logo" src="{{ asset('themes/KLB-theme/assets/images/Kalista_LOGO_Black.png') }}" />
        @endif
    </a>
</script>

<!-- Search Bar Section -->
<script type="text/x-template" id="searchbar-template">
    <div class="row" style="height:65px">
        <div class="@if(Route::is('shop.home.index')) home @endif col-lg-12 searchbar-klb">
            <form
                method="GET"
                role="search"
                action="{{ route('velocity.search.index') }}"
            >
                            <input
                                required
                                name="term"
                                type="search"
                                placeholder="{{ __('velocity::app.header.search-text') }}"
                                :value="searchedQuery.term ? searchedQuery.term.split('+').join(' ') : ''" />

                            <button class="btn" type="submit" >
                                <!-- <svg style="height:16px; width:16px;" data-icon="search" viewBox="0 0 512 512" width="100%" height="100%">
                                    <path d="M495,466.2L377.2,348.4c29.2-35.6,46.8-81.2,46.8-130.9C424,103.5,331.5,11,217.5,11C103.4,11,11,103.5,11,217.5   S103.4,424,217.5,424c49.7,0,95.2-17.5,130.8-46.7L466.1,495c8,8,20.9,8,28.9,0C503,487.1,503,474.1,495,466.2z M217.5,382.9   C126.2,382.9,52,308.7,52,217.5S126.2,52,217.5,52C308.7,52,383,126.3,383,217.5S308.7,382.9,217.5,382.9z"></path>
                                </svg> -->
                                <svg style="height:16px; width:16px;" data-icon="search" viewBox="0 0 512 512" width="100%" height="100%">
                                    <path d="M495,466.2L377.2,348.4c29.2-35.6,46.8-81.2,46.8-130.9C424,103.5,331.5,11,217.5,11C103.4,11,11,103.5,11,217.5   S103.4,424,217.5,424c49.7,0,95.2-17.5,130.8-46.7L466.1,495c8,8,20.9,8,28.9,0C503,487.1,503,474.1,495,466.2z M217.5,382.9   C126.2,382.9,52,308.7,52,217.5S126.2,52,217.5,52C308.7,52,383,126.3,383,217.5S308.7,382.9,217.5,382.9z"></path>
                                </svg>
                            </button>

            </form>

            {!! view_render_event('bagisto.shop.layout.header.wishlist.before') !!}
            <!-- Wishlist Sectin -->
                <a class="wishlist-btn unset" :href="`${isCustomer ? '{{ route('customer.wishlist.index') }}' : '{{ route('velocity.product.guest-wishlist') }}'}`">
                    <svg style="height:27px;width:27px;" aria-hidden="true" data-prefix="fal" data-icon="heart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-heart fa-w-16 fa-2x"><path fill="currentColor" d="M462.3 62.7c-54.5-46.4-136-38.7-186.6 13.5L256 96.6l-19.7-20.3C195.5 34.1 113.2 8.7 49.7 62.7c-62.8 53.6-66.1 149.8-9.9 207.8l193.5 199.8c6.2 6.4 14.4 9.7 22.6 9.7 8.2 0 16.4-3.2 22.6-9.7L472 270.5c56.4-58 53.1-154.2-9.7-207.8zm-13.1 185.6L256.4 448.1 62.8 248.3c-38.4-39.6-46.4-115.1 7.7-161.2 54.8-46.8 119.2-12.9 142.8 11.5l42.7 44.1 42.7-44.1c23.2-24 88.2-58 142.8-11.5 54 46 46.1 121.5 7.7 161.2z" class=""></path></svg>
                    <div class="badge-container" v-if="wishlistCount > 0">
                        <span class="badge" v-text="wishlistCount"></span>
                    </div>
                    <!-- <span>{{ __('shop::app.layouts.wishlist') }}</span> -->
                </a>
            {!! view_render_event('bagisto.shop.layout.header.wishlist.after') !!}

            {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}
                @include('shop::checkout.cart.mini-cart')
            {!! view_render_event('bagisto.shop.layout.header.cart-item.after') !!}

            @php
                $showCompare = core()->getConfigData('general.content.shop.compare_option') == "1" ? true : false
            @endphp

            {!! view_render_event('bagisto.shop.layout.header.compare.before') !!}
                @if ($showCompare)
                    <!-- <a
                        class="compare-btn unset"
                        @auth('customer')
                            href="{{ route('velocity.customer.product.compare') }}"
                        @endauth

                        @guest('customer')
                            href="{{ route('velocity.product.compare') }}"
                        @endguest
                        >

                        <i class="material-icons">compare_arrows</i>
                        <div class="badge-container" v-if="compareCount > 0">
                            <span class="badge" v-text="compareCount"></span>
                        </div>
                        <span>{{ __('velocity::app.customer.compare.text') }}</span>
                    </a> -->
                @endif
            {!! view_render_event('bagisto.shop.layout.header.compare.after') !!}


        </div>
    </div>
</script>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
<!-- Image Search -->
<!-- <script type="text/x-template" id="image-search-component-template">
    <div class="d-inline-block">
        <label class="image-search-container" for="image-search-container">
            <i class="icon camera-icon"></i>

            <input
                type="file"
                class="d-none"
                ref="image_search_input"
                id="image-search-container"
                v-on:change="uploadImage()" />

            <img
                class="d-none"
                id="uploaded-image-url"
                :src="uploadedImageUrl" />
        </label>
    </div>
</script> -->

<script type="text/javascript">
    (() => {
        Vue.component('cart-btn', {
            template: '#cart-btn-template',

            props: ['itemCount'],

            methods: {
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
        });

        Vue.component('close-btn', {
            template: '#close-btn-template',

            methods: {
                togglePopup: function () {
                    $('#cart-modal-content').hide();
                }
            }
        });

        Vue.component('quantity-changer', {
            template: '#quantity-changer-template',
            inject: ['$validator'],
            props: {
                controlName: {
                    type: String,
                    default: 'quantity'
                },

                quantity: {
                    type: [Number, String],
                    default: 1
                },

                minQuantity: {
                    type: [Number, String],
                    default: 1
                },

                validations: {
                    type: String,
                    default: 'required|numeric|min_value:1'
                }
            },

            data: function() {
                return {
                    qty: this.quantity
                }
            },

            watch: {
                quantity: function (val) {
                    this.qty = val;

                    this.$emit('onQtyUpdated', this.qty);
                }
            },

            methods: {
                // This updates the quantity for the Buy Now button on a
                // single product page
                buyNowQty: function() {
                    var quantity = $('form.buy-now-form input[name="quantity"]');

                    if ( quantity.length === 1 ) {
                        quantity.val( this.qty );
                    }
                },
                decreaseQty: function() {
                    if (this.qty > this.minQuantity)
                        this.qty = parseInt(this.qty) - 1;

                    this.$emit('onQtyUpdated', this.qty);

                    this.buyNowQty();
                },

                increaseQty: function() {
                    this.qty = parseInt(this.qty) + 1;

                    this.$emit('onQtyUpdated', this.qty);

                    this.buyNowQty();
                }
            }
        });

        Vue.component('logo-component', {
            template: '#logo-template',
            props: ['addClass'],
        });

        Vue.component('searchbar-component', {
            template: '#searchbar-template',
            data: function () {
                return {
                    compareCount: 0,
                    wishlistCount: 0,
                    searchedQuery: [],
                    isCustomer: '{{ auth()->guard('customer')->user() ? "true" : "false" }}' == "true",
                }
            },

            watch: {
                '$root.headerItemsCount': function () {
                    this.updateHeaderItemsCount();
                }
            },

            created: function () {
                let searchedItem = window.location.search.replace("?", "");
                searchedItem = searchedItem.split('&');

                let updatedSearchedCollection = {};

                searchedItem.forEach(item => {
                    let splitedItem = item.split('=');
                    updatedSearchedCollection[splitedItem[0]] = decodeURI(splitedItem[1]);
                });

                if (updatedSearchedCollection['image-search'] == 1) {
                    updatedSearchedCollection.term = '';
                }

                this.searchedQuery = updatedSearchedCollection;

                this.updateHeaderItemsCount();
            },

            methods: {
                'focusInput': function (event) {
                    $(event.target.parentElement.parentElement).find('input').focus();
                },

                'updateHeaderItemsCount': function () {
                    if (! this.isCustomer) {
                        let comparedItems = this.getStorageValue('compared_product');
                        let wishlistedItems = this.getStorageValue('wishlist_product');

                        if (wishlistedItems) {
                            this.wishlistCount = wishlistedItems.length;
                        }

                        if (comparedItems) {
                            this.compareCount = comparedItems.length;
                        }
                    } else {
                        this.$http.get(`${this.$root.baseUrl}/items-count`)
                            .then(response => {
                                this.compareCount = response.data.compareProductsCount;
                                this.wishlistCount = response.data.wishlistedProductsCount;
                            })
                            .catch(exception => {
                                console.log(this.__('error.something_went_wrong'));
                            });
                    }
                }
            }
        });

        // Vue.component('image-search-component', {
        //     template: '#image-search-component-template',
        //     data: function() {
        //         return {
        //             uploadedImageUrl: ''
        //         }
        //     },

        //     methods: {
        //         uploadImage: function() {
        //             var imageInput = this.$refs.image_search_input;

        //             if (imageInput.files && imageInput.files[0]) {
        //                 if (imageInput.files[0].type.includes('image/')) {
        //                     this.$root.showLoader();

        //                     var formData = new FormData();

        //                     formData.append('image', imageInput.files[0]);

        //                     axios.post(
        //                         "{{ route('shop.image.search.upload') }}",
        //                         formData,
        //                         {
        //                             headers: {
        //                                 'Content-Type': 'multipart/form-data'
        //                             }
        //                         }
        //                     ).then(response => {
        //                         var net;
        //                         var self = this;
        //                         this.uploadedImageUrl = response.data;


        //                         async function app() {
        //                             var analysedResult = [];

        //                             var queryString = '';

        //                             net = await mobilenet.load();

        //                             const imgElement = document.getElementById('uploaded-image-url');

        //                             try {
        //                                 const result = await net.classify(imgElement);

        //                                 result.forEach(function(value) {
        //                                     queryString = value.className.split(',');

        //                                     if (queryString.length > 1) {
        //                                         analysedResult = analysedResult.concat(queryString)
        //                                     } else {
        //                                         analysedResult.push(queryString[0])
        //                                     }
        //                                 });
        //                             } catch (error) {
        //                                 self.$root.hideLoader();

        //                                 window.showAlert(
        //                                     `alert-danger`,
        //                                     this.__('shop.general.alert.error'),
        //                                     "{{ __('shop::app.common.error') }}"
        //                                 );
        //                             }

        //                             localStorage.searchedImageUrl = self.uploadedImageUrl;

        //                             queryString = localStorage.searched_terms = analysedResult.join('_');

        //                             self.$root.hideLoader();

        //                             window.location.href = "{{ route('shop.search.index') }}" + '?term=' + queryString + '&image-search=1';
        //                         }

        //                         app();
        //                     }).catch(() => {
        //                         this.$root.hideLoader();

        //                         window.showAlert(
        //                             `alert-danger`,
        //                             this.__('shop.general.alert.error'),
        //                             "{{ __('shop::app.common.error') }}"
        //                         );
        //                     });
        //                 } else {
        //                     imageInput.value = '';

        //                     alert('Only images (.jpeg, .jpg, .png, ..) are allowed.');
        //                 }
        //             }
        //         }
        //     }
        // });
    })()
</script>
