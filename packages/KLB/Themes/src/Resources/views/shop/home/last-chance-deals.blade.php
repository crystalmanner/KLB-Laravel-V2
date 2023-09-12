
@php
    $count = $velocityMetaData ? $velocityMetaData->new_products_count : 10;
    $direction = core()->getCurrentLocale()->direction == 'rtl' ? 'rtl' : 'ltr';
@endphp

<last-chance-deals></last-chance-deals>

@push('scripts')
    <script type="text/x-template" id="last-chance-deals-template">
        <div class="new-products container-fluid">
            <shimmer-component v-if="isLoading && !isMobileView"></shimmer-component>

            <template v-else-if="newProducts.length > 0">
                <div class="widget-title">
                    <h3 class="box-title">
                        <span class="title">
                            <span>LAST CHANCE DEALS</span>
                        </span>
                    </h3>
                </div>

                {!! view_render_event('bagisto.shop.last-chance-deals.before') !!}

                    @if ($showRecentlyViewed)
                        @push('css')
                            <style>
                                .recently-viewed {
                                    padding-right: 0px;
                                }
                            </style>
                        @endpush

                        <div class="row {{ $direction }}">
                            <div class="col-9 no-padding carousel-products vc-full-screen with-recent-viewed" v-if="!isMobileView">
                                <carousel-component
                                    slides-per-page="5"
                                    navigation-enabled="show"
                                    pagination-enabled="hide"
                                    id="last-chance-deals-carousel"
                                    locale-direction="{{ $direction }}"
                                    :slides-count="newProducts.length">

                                    <slide
                                        :key="index"
                                        :slot="`slide-${index}`"
                                        v-for="(product, index) in newProducts">
                                        <product-card
                                            :list="list"
                                            :product="product">
                                        </product-card>
                                    </slide>
                                </carousel-component>
                            </div>


                            <div class="col-12 no-padding carousel-products vc-small-screen" v-else>
                                <carousel-component
                                    slides-per-page="2"
                                    navigation-enabled="hide"
                                    pagination-enabled="hide"
                                    id="last-chance-deals-carousel"
                                    locale-direction="{{ $direction }}"
                                    :slides-count="newProducts.length">

                                    <slide
                                        :key="index"
                                        :slot="`slide-${index}`"
                                        v-for="(product, index) in newProducts">
                                        <product-card
                                            :list="list"
                                            :product="product">
                                        </product-card>
                                    </slide>
                                </carousel-component>
                            </div>

                            @include ('shop::products.list.recently-viewed', [
                                'quantity'          => 3,
                                'addClass'          => 'col-lg-3 col-md-12',
                            ])


                        </div>

                    @else
                        <div class="carousel-products vc-full-screen {{ $direction }}" v-if="!isMobileView">
                            <carousel-component
                                slides-per-page="5"
                                navigation-enabled="hide"
                                pagination-enabled="hide"
                                id="last-chance-deals-carousel"
                                locale-direction="{{ $direction }}"
                                :slides-count="newProducts.length">

                                <slide
                                    :key="index"
                                    :slot="`slide-${index}`"
                                    v-for="(product, index) in newProducts">
                                    <product-card
                                        :list="list"
                                        :product="product">
                                    </product-card>
                                </slide>
                            </carousel-component>
                        </div>

                        <div class="carousel-products vc-small-screen {{ $direction }}" v-else>
                            <carousel-component
                                slides-per-page="2"
                                navigation-enabled="hide"
                                pagination-enabled="hide"
                                id="last-chance-deals-carousel"
                                locale-direction="{{ $direction }}"
                                :slides-count="newProducts.length">

                                <slide
                                    :key="index"
                                    :slot="`slide-${index}`"
                                    v-for="(product, index) in newProducts">
                                    <product-card
                                        :list="list"
                                        :product="product">
                                    </product-card>
                                </slide>
                            </carousel-component>
                        </div>
                    @endif
                    <div class="row justify-center">
                        <button class="homestyle-btn">SHOP BEAUTY DEALS</button>
                    </div>
                {!! view_render_event('bagisto.shop.last-chance-deals.after') !!}

            </template>

            @if ($count==0)
                <template>
                        @if ($showRecentlyViewed)
                            @push('css')
                                <style>
                                    .recently-viewed {
                                        padding-right: 0px;
                                    }
                                </style>
                            @endpush

                            <div class="row {{ $direction }}">
                                <div class="col-9 no-padding carousel-products vc-full-screen with-recent-viewed" v-if="!isMobileView"></div>

                                @include ('shop::products.list.recently-viewed', [
                                    'quantity'          => 3,
                                    'addClass'          => 'col-lg-3 col-md-12',
                                ])
                            </div>
                        @endif
                </template>
            @endif
        </div>
    </script>

    <script type="text/javascript">
        (() => {
            Vue.component('last-chance-deals', {
                'template': '#last-chance-deals-template',
                data: function () {
                    return {
                        'list': false,
                        'isLoading': true,
                        'newProducts': [],
                        'isMobileView': this.$root.isMobile(),
                    }
                },

                mounted: function () {
                    //change here
                    this.getNewProducts();
                },

                methods: {
                    //change here
                    'getNewProducts': function () {
                        this.$http.get(`${this.baseUrl}/category-details?category-slug=clearance&count={{ $count }}`)
                        .then(response => {
                             var count = '{{$count}}';
                            if (response.data.status && count != 0){
                                this.newProducts = response.data.products;
                            }else{
                                this.newProducts = 0;
                            }

                            this.isLoading = false;
                        })
                        .catch(error => {
                            this.isLoading = false;
                            console.log(this.__('error.something_went_wrong'));
                        })
                    }
                }
            })
        })()
    </script>
@endpush