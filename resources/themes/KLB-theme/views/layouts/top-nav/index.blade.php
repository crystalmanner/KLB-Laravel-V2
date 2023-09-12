<nav id="top" class="@if(Route::is('shop.home.index')) home @endif" style="height:auto;">

    <div class="row" id="sticky-banner">
        <p>Rewards Members Get 30% OFF, Log in to Unlock! Free to Join, Sign Up Now!<button id="sticky-banner-btn" type="button" class="close"><span style="color:white;">&times;</span></button></p>
    </div>

    <div class="row" id="top-banner">
        FREE SHIPPING ON ORDERS OVER $60.
    </div>

    <div class='row'>

        <div class="col-sm-6"></div>

        <div class="col-sm-6">
            @include('velocity::layouts.top-nav.login-section')
        </div>

    </div>
</nav>
