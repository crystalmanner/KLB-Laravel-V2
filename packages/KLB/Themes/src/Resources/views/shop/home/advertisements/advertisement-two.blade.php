@php
    $isRendered = false;
    $advertisementTwo = null;
@endphp

<!-- @if ($velocityMetaData && $velocityMetaData->advertisement)
    @php
        $advertisement = json_decode($velocityMetaData->advertisement, true);
        
        if (isset($advertisement[2]) && is_array($advertisement[2])) {
            $advertisementTwo = array_values(array_filter($advertisement[2]));
        }
    @endphp

    @if ($advertisementTwo)
        @php
            $isRendered = true;
        @endphp

        <div class="container-fluid advertisement-two-container">
            <div class="row">
                @if ( isset($advertisementTwo[0]))
                    <a class="col-lg-6 col-md-12 no-padding">
                        <img src="{{ asset('/storage/' . $advertisementTwo[0]) }}" />
                    </a>
                @endif
                
                @if ( isset($advertisementTwo[1]))
                    <a class="col-lg-6 col-md-12 pr0">
                        <img src="{{ asset('/storage/' . $advertisementTwo[1]) }}" />
                    </a>
                @endif
            </div>
        </div>
    @endif
@endif -->

@if (! $isRendered)
    <div class="container-fluid advertisement-two-container">
        <div class="row" style="padding: 30px 30px 0 30px !important;">
            <a class="col-lg-6 col-md-12" style="padding: 20px 60px !important;">
                <img src="{{ asset('/themes/KLB-theme/assets/images/advertisement/advertisement1.jpg') }}" />
            </a>

            <a class="col-lg-6 col-md-12" style="padding: 20px 60px !important;">
                <img src="{{ asset('/themes/KLB-theme/assets/images/advertisement/advertisement2.jpg') }}" />
            </a>
        </div>
    </div>
@endif