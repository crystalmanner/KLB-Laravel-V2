{!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}
    <login-header></login-header>
{!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}

<script type="text/x-template" id="login-header-template">
    <div class="dropdown">
        <div id="account" class="@if(Route::is('shop.home.index')) home @endif">
            <br>


            <div class="welcome-content pull-right" >
                <div>
                    <!-- create a gift card route HERE -->
                    <a style="font-size:12px;" href="{{ route('customer.session.index') }}">
                        Gift Card
                    </a>
                </div>
                <div class="space">
                    |
                </div>
                @guest('customer')
                    <div>
                        <a style="font-size:12px;" id="customer_register_link" href="{{ route('customer.register.index') }}">
                            Register
                        </a>
                    </div>
                    <div class="space">
                        |
                    </div>
                    <div>
                        <a style="font-size:12px;" id="customer_login_link" href="{{ route('customer.session.index') }}">
                            {{ __('shop::app.header.sign-in') }}
                        </a>
                    </div>
                @endguest

                @auth('customer')
                    <div>
                        <a style="font-size:12px;" id="customer_logout_link" href="{{ route('customer.session.destroy') }}">
                            {{ __('shop::app.header.logout') }}
                        </a>
                    </div>
                    <div class="space" >
                        |
                    </div>
                    <div>
                        <a style="font-size:12px;" href="{{ route('customer.profile.index') }}">
                            My Account
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </div>
</script>

@push('scripts')
    <script type="text/javascript">

        Vue.component('login-header', {
            template: '#login-header-template',

            methods: {
                togglePopup: function (event) {
                    let accountModal = this.$el.querySelector('.account-modal');
                    let modal = $('#cart-modal-content')[0];

                    if (modal)
                        modal.classList.add('hide');

                    accountModal.classList.toggle('hide');

                    event.stopPropagation();
                }
            }
        })

    </script>
@endpush

