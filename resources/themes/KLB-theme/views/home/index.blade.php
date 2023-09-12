@extends('shop::layouts.master')

@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
@inject ('productRatingHelper', 'Webkul\Product\Helpers\Review')

@php
    $channel = core()->getCurrentChannel();

    $homeSEO = $channel->home_seo;

    if (isset($homeSEO)) {
        $homeSEO = json_decode($channel->home_seo);

        $metaTitle = $homeSEO->meta_title;

        $metaDescription = $homeSEO->meta_description;

        $metaKeywords = $homeSEO->meta_keywords;
    }

    $showRecentlyViewed = false;

@endphp

@section('page_title')
    {{ isset($metaTitle) ? $metaTitle : "" }}
@endsection

@section('head')

    @if (isset($homeSEO))
        @isset($metaTitle)
            <meta name="title" content="{{ $metaTitle }}" />
        @endisset

        @isset($metaDescription)
            <meta name="description" content="{{ $metaDescription }}" />
        @endisset

        @isset($metaKeywords)
            <meta name="keywords" content="{{ $metaKeywords }}" />
        @endisset
    @endif
@endsection

@push('css')
    <style type="text/css">
        .product-price span:first-child, .product-price span:last-child {
            font-size: 18px;
            font-weight: 600;
        }
    </style>
@endpush

@section('top-content')
    @include('shop::home.slider')
@endsection

@section('full-content-wrapper')
<!-- home page sections -->
    <div class="full-content-wrapper">
        {!! view_render_event('bagisto.shop.home.content.before') !!}
        @include('shop::home.new-products')
        @include('shop::home.shop-lamer')
        @include('shop::home.advertisements.advertisement-two')
        @include('shop::home.best-sellers')
        @include('shop::home.last-chance-deals')
        @include('shop::home.advertisements.advertisement-four')
        @include('shop::home.trending-now')
        @include('shop::home.kalista-edit')
        {{ view_render_event('bagisto.shop.home.content.after') }}
    </div>

@endsection

