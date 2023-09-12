import Vue from 'vue';
import accounting from 'accounting';
import VueCarousel from 'vue-carousel';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/index.css';
import de from 'vee-validate/dist/locale/de';
import ar from 'vee-validate/dist/locale/ar';
import VeeValidate, { Validator } from 'vee-validate';
import axios from 'axios';
import Embed from 'v-video-embed'
window.axios = axios;
window.VeeValidate = VeeValidate;
window.jQuery = window.$ = require("jquery");
window.BootstrapSass = require("bootstrap-sass");
Vue.use(Embed);
Vue.use(VueToast);
Vue.use(VueCarousel);
Vue.use(BootstrapSass);

Vue.prototype.$http = axios;

Vue.use(VeeValidate, {
    dictionary: {
        ar: ar,
        de: de,
    }
});

Vue.filter('currency', function (value, argument) {
    return accounting.formatMoney(value, argument);
});

window.Vue = Vue;
window.Carousel = VueCarousel;

// UI components
Vue.component("vue-slider", require("vue-slider-component"));
Vue.component('mini-cart', require('./UI/components/mini-cart').default);
Vue.component('modal-component', require('./UI/components/modal'));
Vue.component("add-to-cart", require("./UI/components/add-to-cart").default);
Vue.component('star-ratings', require('./UI/components/star-rating'));
Vue.component('quantity-btn', require('./UI/components/quantity-btn'));
Vue.component("product-card", require("./UI/components/product-card").default);
Vue.component("wishlist-component", require("./UI/components/wishlist").default);
Vue.component('carousel-component', require('./UI/components/carousel').default);
Vue.component('child-sidebar', require('./UI/components/child-sidebar').default);
Vue.component('card-list-header', require('./UI/components/card-header').default);
Vue.component('magnify-image', require('./UI/components/image-magnifier').default);
Vue.component('compare-component', require('./UI/components/product-compare').default);
Vue.component("shimmer-component", require("./UI/components/shimmer-component").default);
Vue.component('responsive-sidebar', require('./UI/components/responsive-sidebar').default);
Vue.component('sidebar-component', require('./UI/components/sidebar').default);
Vue.component('product-quick-view', require('./UI/components/product-quick-view'));
Vue.component('product-quick-view-btn', require('./UI/components/product-quick-view-btn').default);

// might need to change these
Vue.component('navbar-component', require('./UI/components/navbar').default);
Vue.component('percentage-component', require('./UI/components/percentage').default);
// Buy Now button
// Vue.component("buy-now", require("./UI/components/buy-now").default);
// Search bar, wishlist button, shopping cart button
Vue.component("search-wish-shop", require("./UI/components/search-wish-shop").default);

Vue.component('mini-cart2', require('./UI/components/mini-cart2').default);
Vue.component('movie', require('./UI/components/movie').default);
Vue.component('stylingComp', require('./UI/components/stylingComp').default);




window.eventBus = new Vue();

jQuery(function () {
    // define a mixin object
    Vue.mixin(require('./UI/components/trans'));

    Vue.mixin({
        data: function () {
            return {
                'imageObserver': null,
                'navContainer': false,
                'headerItemsCount': 0,
                'sharedRootCategories': [],
                'responsiveSidebarTemplate': '',
                'responsiveSidebarKey': Math.random(),
                'baseUrl': document.querySelector("script[src$='KLB.js']").getAttribute('baseUrl')
            }
        },

        methods: {
            redirect: function (route) {
                route ? window.location.href = route : '';
            },

            debounceToggleSidebar: function (id, {target}, type) {
                // setTimeout(() => {
                    this.toggleSidebar(id, target, type);
                // }, 500);
            },

            toggleSidebar: function (id, {target}, type) {
                if (
                    Array.from(target.classList)[0] == "main-category"
                    || Array.from(target.parentElement.classList)[0] == "main-category"
                ) {
                    let sidebar = $(`#sidebar-level-${id}`);
                    if (sidebar && sidebar.length > 0) {
                        if (type == "mouseover") {
                            this.show(sidebar);
                        } else if (type == "mouseout") {
                            this.hide(sidebar);
                        }
                    }
                } else if (
                    Array.from(target.classList)[0]     == "category"
                    || Array.from(target.classList)[0]  == "category-icon"
                    || Array.from(target.classList)[0]  == "category-title"
                    || Array.from(target.classList)[0]  == "category-content"
                    || Array.from(target.classList)[0]  == "rango-arrow-right"
                ) {
                    let parentItem = target.closest('li');

                    if (target.id || parentItem.id.match('category-')) {
                        let subCategories = $(`#${target.id ? target.id : parentItem.id} .sub-categories`);

                        if (subCategories && subCategories.length > 0) {
                            let subCategories1 = Array.from(subCategories)[0];
                            subCategories1 = $(subCategories1);

                            if (type == "mouseover") {
                                this.show(subCategories1);

                                let sidebarChild = subCategories1.find('.sidebar');
                                this.show(sidebarChild);
                            } else if (type == "mouseout") {
                                this.hide(subCategories1);
                            }
                        } else {
                            if (type == "mouseout") {
                                let sidebar = $(`#${id}`);
                                sidebar.hide();
                            }
                        }
                    }
                }
            },

            show: function (element) {
                element.show();
                element.mouseleave(({target}) => {
                    $(target.closest('.sidebar')).hide();
                });
            },

            hide: function (element) {
                element.hide();
            },

            toggleButtonDisability ({event, actionType}) {
                let button = event.target.querySelector('button[type=submit]');

                button ? button.disabled = actionType : '';
            },

            onSubmit: function (event) {
                this.toggleButtonDisability({event, actionType: true});

                if(typeof tinyMCE !== 'undefined')
                    tinyMCE.triggerSave();

                this.$validator.validateAll().then(result => {
                    if (result) {
                        event.target.submit();
                    } else {
                        this.toggleButtonDisability({event, actionType: false});

                        eventBus.$emit('onFormError')
                    }
                });
            },

            isMobile: function () {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i|/mobi/i.test(navigator.userAgent)) {
                    if (this.isMaxWidthCrossInLandScape()) {
                        return false;
                    }
                    return true
                } else {
                    return false
                }
            },

            isMaxWidthCrossInLandScape: function() {
                return window.innerWidth > 900;
            },

            getDynamicHTML: function (input) {
                var _staticRenderFns;
                const { render, staticRenderFns } = Vue.compile(input);

                if (this.$options.staticRenderFns.length > 0) {
                    _staticRenderFns = this.$options.staticRenderFns;
                } else {
                    _staticRenderFns = this.$options.staticRenderFns = staticRenderFns;
                }

                try {
                    var output = render.call(this, this.$createElement);
                } catch (exception) {
                    console.log(this.__('error.something_went_wrong'));
                }

                this.$options.staticRenderFns = _staticRenderFns;

                return output;
            },

            getStorageValue: function (key) {
                let value = window.localStorage.getItem(key);

                if (value) {
                    value = JSON.parse(value);
                }

                return value;
            },

            setStorageValue: function (key, value) {
                window.localStorage.setItem(key, JSON.stringify(value));

                return true;
            },
        }
    });

    new Vue({
        el: "#app",
        VueToast,

        data: function () {
            return {
                modalIds: {},
                miniCartKey: 0,
                quickView: false,
                productDetails: [],
                showPageLoader: false,
            }
        },

        created: function () {
            setTimeout(() => {
                document.body.classList.remove("modal-open");
            }, 0);

            window.addEventListener('click', () => {
                let modals = document.getElementsByClassName('sensitive-modal');

                Array.from(modals).forEach(modal => {
                    modal.classList.add('hide');
                });
            });
        },

        mounted: function () {
            setTimeout(() => {
                this.addServerErrors();
            }, 0);

            document.body.style.display = "block";
            this.$validator.localize(document.documentElement.lang);

            this.loadCategories();
            this.addIntersectionObserver();
        },

        methods: {
            onSubmit: function (event) {
                this.toggleButtonDisability({event, actionType: true});

                if(typeof tinyMCE !== 'undefined')
                    tinyMCE.triggerSave();

                this.$validator.validateAll().then(result => {
                    if (result) {
                        event.target.submit();
                    } else {
                        this.toggleButtonDisability({event, actionType: false});

                        eventBus.$emit('onFormError')
                    }
                });
            },

            toggleButtonDisable (value) {
                var buttons = document.getElementsByTagName("button");

                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].disabled = value;
                }
            },

            addServerErrors: function (scope = null) {
                for (var key in serverErrors) {
                    var inputNames = [];
                    key.split('.').forEach(function(chunk, index) {
                        if(index) {
                            inputNames.push('[' + chunk + ']')
                        } else {
                            inputNames.push(chunk)
                        }
                    })

                    var inputName = inputNames.join('');

                    const field = this.$validator.fields.find({
                        name: inputName,
                        scope: scope
                    });

                    if (field) {
                        this.$validator.errors.add({
                            id: field.id,
                            field: inputName,
                            msg: serverErrors[key][0],
                            scope: scope
                        });
                    }
                }
            },

            addFlashMessages: function () {
                if (window.flashMessages.alertMessage)
                    window.alert(window.flashMessages.alertMessage);
            },

            showModal: function (id) {
                this.$set(this.modalIds, id, true);
            },

            loadCategories: function () {
                this.$http.get(`${this.baseUrl}/categories`)
                .then(response => {
                    this.sharedRootCategories = response.data.categories;
                    $(`<style type='text/css'> .sub-categories{ min-height:${response.data.categories.length * 30}px;} </style>`).appendTo("head");
                })
                .catch(error => {
                    console.log('failed to load categories');
                })
            },

            addIntersectionObserver: function () {
                this.imageObserver = new IntersectionObserver((entries, imgObserver) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target
                            lazyImage.src = lazyImage.dataset.src
                        }
                    })
                });
            },

            showLoader: function () {
                $('#loader').show();
                $('.overlay-loader').show();

                document.body.classList.add("modal-open");
            },

            hideLoader: function () {
                $('#loader').hide();
                $('.overlay-loader').hide();

                document.body.classList.remove("modal-open");
            }
        }
    });

    // Hides the sticky banner if it exists
    $( '#sticky-banner-btn' ).on( 'click', function( event ) {
        $( '#sticky-banner' ).hide();
    });

    // Handle buy now buttons
    $('button[name^=buy_now_button_]').on( 'click', function( event ) {
        // https://stackoverflow.com/a/991371/1620794
        console.log('buy_now button clicked');
        var form = event.target.form;
        // set is_buy_now to 1
        $( form ).find( 'input[name="is_buy_now"]' ).val( '1' );
        form.querySelector( 'input[name="is_buy_now"]' ).value = '1';
        form.submit();
    });

    // Is related to this:
    // packages/KLB/Themes/src/Resources/views/shop/home/kalista-edit.blade.php
    fetch('https://edit.kalista-beauty.com/shopify-recent-articles')
    .then(response => response.json())
    .then(data => {

      if(data.length){
        var related_article =
          `<div class="container-fluid">
				<div class="widget-title">
					<h3 class="box-title">
						<span class="title">
                          <span>
                              Read more beauty tips on the Kalista Edit
                          </span>
						</span>
  					</h3>
  				</div>`
				for(var i=0 ; i<data.length ;i++){

                var article =
				`<div class="grid-item col5 col-lg-3 col-md-4 col-6" style="max-width:20%; display: inline-block; vertical-align: top">
                	<div class="inner product-item" data-product-id="">
                    	<div class="inner-top">
                        	<div class="product-top" style="text-align: center;">
                            	<div class="product-image">

                                    <a href="${data[i].link}" class="product-grid-image"
                                    data-collections-related="/collections/?view=related" target="_blank">
                                    <img alt=""
                                    class="lazyautosizes lazyloaded"
                                    data-widths="[180, 360, 540, 720, 900, 1080, 1296, 1512, 1728, 2048]"
                                    data-aspectratio="1.3333333333333333" data-sizes="auto" sizes="210px"
                                    src="${data[i].image}">
  									</a>

	                            </div>
                            </div>

                            <div class="product-bottom">
                                <a class="product-title" style="text-align: center;color: #232323;display: block;margin-bottom: 14px;font-size: 12px;line-height: 22px;font-weight: 500;"href="${data[i].link}" target="_blank">
                                    <span>${data[i].title}</span>
                                </a>
                            </div>

  						</div>
                    </div>
				</div>`

                related_article += article;
                }

        var kalistEdit = document.getElementById( 'kalist-edit' );
        if ( kalistEdit !== null ) {
            kalistEdit.innerHTML = related_article;
        }
      }

  	});

    // for compilation of html coming from server
    Vue.component('vnode-injector', {
        functional: true,
        props: ['nodes'],
        render(h, {props}) {
            return props.nodes;
        }
    });
});
