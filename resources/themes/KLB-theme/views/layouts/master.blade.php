

@php
    $velocityHelper = app('Webkul\Velocity\Helpers\Helper');
    $velocityMetaData = $velocityHelper->getVelocityMetaData();

    view()->share('velocityMetaData', $velocityMetaData);
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

    <head>
        <title>@yield('page_title')</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="{{ asset('themes/KLB-theme/assets/css/KLB.css') }}" />
        <link rel="stylesheet" href="{{ asset('themes/KLB-theme/assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('themes/KLB-theme/assets/css/google-font.css') }}" />

        @if (core()->getCurrentLocale()->direction == 'rtl')
            <link href="{{ asset('themes/KLB-theme/assets/css/bootstrap-flipped.css') }}" rel="stylesheet">
        @endif

        @if ($favicon = core()->getCurrentChannel()->favicon_url)
            <link rel="icon" sizes="16x16" href="{{ $favicon }}" />
        @else
            <link rel="icon" sizes="16x16" href="{{ asset('/themes/KLB-theme/assets/images/static/v-icon.png') }}" />
        @endif

        {{-- TODO: This file does not exist! does this still need to be here?
            This currently generates a mime type of text/html because this file
            does not exist. Most JavaScript on this page is combined and minified. --}}
        {{-- <script
            type="text/javascript"
            src="{{ asset('themes/KLB-theme/assets/js/jquery.min.js') }}">
        </script> --}}

        <script
            type="text/javascript"
            baseUrl="{{ url()->to('/') }}"
            src="{{ asset('themes/KLB-theme/assets/js/KLB.js') }}">
        </script>

        {{-- TODO: This file does not exist! does this still need to be here? --}}
        {{-- <script
            type="text/javascript"
            src="{{ asset('themes/KLB-theme/assets/js/jquery.ez-plus.js') }}">
        </script> --}}

        @yield('head')

        @section('seo')
            <meta name="description" content="{{ core()->getCurrentChannel()->description }}"/>
        @show

        @stack('css')

        {!! view_render_event('bagisto.shop.layout.head') !!}

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

    </head>

    <body @if (core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}


        @include('shop::UI.particals')

        <div id="app">
            <product-quick-view v-if="$root.quickView"></product-quick-view>







            <div class="main-container-wrapper">
                <div class="page-header">
                    @section('body-header')
                        @include('shop::layouts.top-nav.index')


                        {!! view_render_event('bagisto.shop.layout.header.before') !!}



                        @include('shop::layouts.header.index')

                        {!! view_render_event('bagisto.shop.layout.header.after') !!}
                    @show

                    @php
                        $velocityContent = app('Webkul\Velocity\Repositories\ContentRepository')->getAllContents();
                    @endphp

                    @section('nav-header')





                            <div>
                                <!-- style ="text-align:center; font-size: 15px; font-family: Georgia;  width: 500px; margin: 0 auto;" -->
                                <navbar-component
                                style="@if(Route::is('shop.home.index')) background-color:transparent; border:none;  text-transform: uppercase; font-weight: bold; @else background-color:#548e9c; font-weight: bold; color:white; @endif; color:white; margin: auto;"


                                >
                                </navbar-component>







                            </div>


                        <!--    <div class="col-12" style="@if(Route::is('shop.home.index')) @else background-color:#548e9c; @endif">
                      <content-header

                            url="{{ url()->to('/') }}"
                            :header-content="{{ json_encode($velocityContent) }}"
                            heading= "{{ __('velocity::app.menu-navbar.text-category') }}"
                            category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 10 }}"
                        ></content-header>

                        <sidebar-component
                            main-sidebar=true
                            id="sidebar-level-0"
                            url="{{ url()->to('/') }}"
                            category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 10 }}"
                            add-class="category-list-container pt10"
                            >
                        </sidebar-component>

                    </div>-->

                    @show
                </div>

                <div class="@if(Route::is('shop.home.index')) home @endif page-content">

                    @yield('top-content')

                    <div class="row col-12 remove-padding-margin">


                        <div class="col-12 no-padding content" id="home-right-bar-container">

                            <div class="container-right row no-margin col-12 no-padding">

                                {!! view_render_event('bagisto.shop.layout.content.before') !!}

                                @yield('content-wrapper')

                                {!! view_render_event('bagisto.shop.layout.content.after') !!}

                            </div>

                        </div>
                    </div>

                    <div class="container">

                        {!! view_render_event('bagisto.shop.layout.full-content.before') !!}

                        @yield('full-content-wrapper')

                        {!! view_render_event('bagisto.shop.layout.full-content.after') !!}

                    </div>
                </div>
            </div>

            <div class="modal-parent" id="loader" style="top: 0" v-show="showPageLoader">
                <overlay-loader :is-open="true"></overlay-loader>
            </div>
        </div>

        <!-- below footer -->
        @section('footer')
            <!-- {!! view_render_event('bagisto.shop.layout.footer.before') !!} -->

                @include('shop::layouts.footer.index')

            <!-- {!! view_render_event('bagisto.shop.layout.footer.after') !!} -->
        @show

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        <div id="alert-container"></div>


        {{-- This is the cart modal which needs to be visible on all pages --}}
        @include('shop::checkout.cart.content',
        [
            'cart' => Cart::getCart()
        ])

        <script type="text/javascript">
            (() => {
                window.showAlert = (messageType, messageLabel, message) => {
                    if (messageType && message !== '') {
                        let alertId = Math.floor(Math.random() * 1000);

                        let html = `<div class="alert ${messageType} alert-dismissible" id="${alertId}">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>${messageLabel ? messageLabel + '!' : ''} </strong> ${message}.
                        </div>`;

                        $('#alert-container').append(html).ready(() => {
                            window.setTimeout(() => {
                                $(`#alert-container #${alertId}`).remove();
                            }, 5000);
                        });
                    }
                }

                let messageType = '';
                let messageLabel = '';

                @if ($message = session('success'))
                    messageType = 'alert-success';
                    messageLabel = "{{ __('velocity::app.shop.general.alert.success') }}";
                @elseif ($message = session('warning'))
                    messageType = 'alert-warning';
                    messageLabel = "{{ __('velocity::app.shop.general.alert.warning') }}";
                @elseif ($message = session('error'))
                    messageType = 'alert-danger';
                    messageLabel = "{{ __('velocity::app.shop.general.alert.error') }}";
                @elseif ($message = session('info'))
                    messageType = 'alert-info';
                    messageLabel = "{{ __('velocity::app.shop.general.alert.info') }}";
                @endif

                if (messageType && '{{ $message }}' !== '') {
                    window.showAlert(messageType, messageLabel, '{{ $message }}');
                }

                window.serverErrors = [];
                @if (isset($errors))
                    @if (count($errors))
                        window.serverErrors = @json($errors->getMessages());
                    @endif
                @endif

                window._translations = @json(app('Webkul\Velocity\Helpers\Helper')->jsonTranslations());
            })();
        </script>

        <script
            type="text/javascript"
            src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}">
        </script>

        @stack('scripts')

        <script>
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>
    </body>
</html>
