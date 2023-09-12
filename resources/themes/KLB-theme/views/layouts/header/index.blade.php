<header class="@if(Route::is('shop.home.index')) home @endif" v-if="!isMobile()">
<!-- <header class="sticky-header" v-if="!isMobile()"> -->
    <!-- <div class="row col-12 remove-padding-margin velocity-divide-page">
        <logo-component></logo-component>
        <searchbar-component></searchbar-component>
    </div> -->
    <div class="row" style="height:65px;"> 
        <div class="col-4" style="display: flex; justify-content: left; padding-left: 4%; align-items: center; font-size: 12px; letter-spacing: normal;">      
                Rewards Members Save 30% OFF! Free to Join!
        </div>
        <div class="col-4 mid-section">
            <logo-component></logo-component>
        </div>
        <div class="col-4 right-section">
            <searchbar-component></searchbar-component>
        </div>
    </div>
</header>

@push('scripts')
    <script type="text/javascript">
        (() => {

            document.addEventListener('scroll', e => {
                scrollPosition = Math.round(window.scrollY);

                if (scrollPosition > 50){
                    document.querySelector('header').classList.add('header-shadow');
                } else {
                    document.querySelector('header').classList.remove('header-shadow');
                }
            });
        })()
    </script>
@endpush
