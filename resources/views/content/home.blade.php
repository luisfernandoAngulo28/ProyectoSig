@extends('layouts/master')
@include('helpers.meta')

@section('header')
    <section class="slider-area">
        <div class="hero-slider-active slick-arrow-style slick-dot-style">
            @foreach ($nodes['themes'] as $key => $theme)
                @if ($theme->resp_image)
                    @section('css')
                        <style>
                            @media screen and (max-width: 580px) {
                                body.main-site .bg-img.bg-img-resp {
                                    background-image: url('{{ Asset::get_image_path('theme-resp_image', 'normal', $theme->resp_image) }}') !important;
                                }
                            }
                        </style>
                    @endsection
                @endif
                <div class="hero-slider-item">
                    <div class="d-flex align-items-center bg-img h-100 @if ($theme->resp_image) bg-img-resp @endif"
                        data-bg="{{ Asset::get_image_path('theme-image', 'normal', $theme->image) }}">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-10 {{-- offset-lg-2 --}} col-md-10 {{-- offset-md-2 --}}">

                                    @if ($theme->active_text == 1)
                                        <div class="hero-slider-content">
                                            <h2>{{ $theme->text1 }}</h2>
                                            <h1>{{ $theme->name }}</h1>
                                            <h3>{{ $theme->text2 }}</h3>
                                            <a href="{{ url('productos') }}" class="btn-hero">Ver más</a>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('content')
    <!-- service features start -->
    <section class="service-features pt-50">
        <div class="container">
            <div class="service-features-inner bg-white">
                <div class="row">
                    <div class="col lg-4 col-md-4">
                        <div class="single-features-item">
                            <div class="features-icon">
                                <i class="fa fa-shopping-basket"></i>
                            </div>
                            <div class="features-content">
                                {!! App\Content::find(3)->content !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="single-features-item">
                            <div class="features-icon">
                                <i class="ion-card"></i>
                            </div>
                            <div class="features-content">
                                {!! App\Content::find(4)->content !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="single-features-item">
                            <div class="features-icon">
                                <i class="ion-help-buoy"></i>
                            </div>
                            <div class="features-content">
                                {!! App\Content::find(5)->content !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="img-container">
                <a href="#">
                    <img src="{{ asset(\Asset::get_image_path('image-content-image', 'normal', \Solunes\Master\App\ImageContent::find(1)->image)) }}"
                        alt="">
                </a>
            </div>
        </div>
    </section>
    <!-- service features end -->

    <!-- deals area start -->
    <section class="deals-area pt-50">
        <div class="container">
            <div class="deals-wrapper bg-white">
                <div class="row">
                    <div class="col-12">
                        <div class="section-header-deals">
                            <div class="section-title-deals">
                                <h4>Libros Destacados</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="deals-item-wrapper">
                                <div class="deals-carousel-active slick-arrow-style slick-row-15">

                                    @foreach ($nodes['destacados'] as $destacado)
                                        <div class="deals-slider-item type_2">
                                            <div class="deals-item">
                                                <div class="deals-thumb">
                                                    <a href="{{ url('producto/' . $destacado->product_bridge->slug) }}">
                                                        @if ($destacado->image)
                                                            <img src="{{ Asset::get_image_path('product-image', 'thumb', $destacado->image) }}"
                                                                alt="imagen del producto" />
                                                        @else
                                                            <img src="{{ asset('assets/img/iiicab_min.png') }}"
                                                                alt="imagen del producto" />
                                                        @endif
                                                    </a>
                                                    {{-- <div class="add-to-links">
                                                <a href="wishlist.html" data-toggle="tooltip" title="Add to Wishlist"><i class="ion-android-favorite-outline"></i></a>
                                                <a href="compare.html" data-toggle="tooltip" title="Add to Compare"><i class="ion-stats-bars"></i></a>
                                            </div> --}}
                                                    <div class="box-desc">
                                                        <!--en caso de descuento por porcentaje -->
                                                       <?php 
                                                       $percentage = 0;
                $rest = 0;
                if (isset($product->product_bridge->real_price) && isset($product->product_bridge->full_price)) {
                    $rest = $product->product_bridge->real_price - $product->product_bridge->full_price;
                    $product->product_bridge->real_price / $product->product_bridge->full_price;
                    $percentage = $percentage * 100;
                }
                
                                                        if (is_int($percentage)) {
                                                            \Log::info($percentage);
                                                        } ?>
                                                        {{-- @if (round($percentage, 0) == $percentage && $percentage < 100) --}}
                                                        @if ($percentage < 100)
                                                            <span class="percent_desc"><span
                                                                    class="number">-{{ round(100 - $percentage) }}
                                                                    %</span></span>
                                                        @elseif($rest < 0)
                                                            <!--en caso de descuento por numero fijo -->
                                                            <span class="fixed_desc"><span class="number">Bs.
                                                                    {{ number_format($destacado->product_bridge->real_price - $destacado->product_bridge->full_price, 2) }}</span></span>
                                                        @endif
                                                    </div>
                                                    @if ($destacado->created_at->format('Y-m-d') > date('Y-m-d', strtotime('-15 days')))
                                                        <div class="box-new">
                                                            <span class="new_product"><span
                                                                    class="number">Nuevo</span></span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="deals-content">
                                                    {{-- <div class="ratings">
                                                <span><i class="ion-android-star"></i></span>
                                                <span><i class="ion-android-star"></i></span>
                                                <span><i class="ion-android-star"></i></span>
                                                <span><i class="ion-android-star"></i></span>
                                                <span><i class="ion-android-star"></i></span>
                                            </div> --}}
                                                    <h4 class="product-name"><a
                                                            href="{{ url('producto/' . $destacado->product_bridge->slug) }}">{{ $destacado->name }}</a>
                                                    </h4>
                                                    <p class="product-desc">{{ $destacado->product_bridge->description }}
                                                    </p>

                                                    <div class="price-box">
                                                        <?php  $percentage = 0;
                                                        $rest = 0;
                                                        if (isset($product->product_bridge->real_price) && isset($product->product_bridge->full_price)) {
                                                            $rest = $product->product_bridge->real_price - $product->product_bridge->full_price;
                                                            $product->product_bridge->real_price / $product->product_bridge->full_price;
                                                            $percentage = $percentage * 100;
                                                        }
                                                        
                                                        if (is_int($percentage)) {
                                                            \Log::info($percentage);
                                                        } ?>
                                                        @if ($destacado->product_bridge->real_price != $destacado->product_bridge->full_price)
                                                            <span
                                                                class="price-old"><del>{{ number_format($destacado->price, 2) }}</del></span>
                                                        @endif
                                                        <span class="price-regular">Bs.
                                                            {{ number_format($destacado->product_bridge->real_price, 2) }}</span>
                                                    </div>
                                                    <a class="btn btn-cart"
                                                        href="{{ url('process/add-cart-item/' . $destacado->product_bridge->id) }}">
                                                        <i class="ion-bag"></i><span> Añadir al carrito</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    {{-- <div class="banner-statistics-area pt-50">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="img-container">
                    <a href="#"><img src="{{ asset('assets/images/banner/img1_home4.jpg') }}" alt=""></a>
                </div>
            </div>
        </div>
    </div>
</div> --}}

    <section class="features-categories-area pt-50">
        <div class="container">
            <div class="section-wrapper bg-white">
                <div class="row">
                    <div class="col-12">
                        <div class="section-header">
                            <!-- section title start -->
                            <div class="section-title">
                                <h4>Sistema académico Andres Bello</h4>
                            </div>
                            <!-- section title start -->

                            <!-- product tab menu start -->
                            <div class="feature-menu">
                                <ul class="nav justify-content-start justify-content-lg-end">
                                    @foreach ($nodes['categories'] as $key => $item)
                                        @if (count($item->children) > 0)
                                            <?php $subitems = $item
                                                ->children()
                                                ->first()
                                                ->products()
                                                ->where('quantity', '>', 0)
                                                ->orderBy('created_at', 'DESC')
                                                ->limit(6)
                                                ->get(); ?>
                                        @else
                                            <?php $subitems = $item
                                                ->products()
                                                ->where('quantity', '>', 0)
                                                ->limit(6)
                                                ->orderBy('created_at', 'DESC')
                                                ->get(); ?>
                                        @endif
                                        @if (count($subitems) > 0)
                                            <li><a data-toggle="tab"
                                                    @if ($key === 0) class="active show" @endif
                                                    href="#bag{{ $item->id }}">{{ $item->name }}</a></li>
                                            {{-- <li><a data-toggle="tab" href="#five">tablet</a></li>
                                        <li><a data-toggle="tab" href="#six">Ibuypower</a></li> --}}
                                        @endif
                                    @endforeach
                                    <li><a href="{{ url('productos') }}">Todos</a></li>
                                </ul>
                            </div>
                            <!-- product tab menu start -->
                        </div>
                    </div>
                </div>
                <div class="row" style="align-items: center; margin: 0;">
                    <div class="col-lg-9 col-md-9">
                        <div class="products-area-wrapper mt-30">
                            <div class="tab-content">
                                @foreach ($nodes['categories'] as $key => $item)
                                    @if (count($item->children) > 0)
                                        <?php $subitems = $item
                                            ->children()
                                            ->first()
                                            ->products()
                                            ->where('quantity', '>', 0)
                                            ->orderBy('created_at', 'DESC')
                                            ->limit(6)
                                            ->get(); ?>
                                    @else
                                        <?php $subitems = $item
                                            ->products()
                                            ->where('quantity', '>', 0)
                                            ->limit(6)
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); ?>
                                    @endif
                                    @if (count($subitems) > 0)
                                        <div class="tab-pane fade @if ($key === 0) active show @endif"
                                            id="bag{{ $item->id }}">
                                            <div class="features-categories-wrapper">
                                                <div class="features-categories-active slick-arrow-style">
                                                    @foreach ($subitems as $subitem)
                                                        @if ($subitem->quantity > 0 && $subitem->active)
                                                            <div class="product-slide-item">
                                                                <div class="product-item types">
                                                                    <div class="product-thumb">
                                                                        <a
                                                                            href="{{ url('producto/' . $subitem->product_bridge->slug) }}">
                                                                            @if ($subitem->image)
                                                                                <img src="{{ Asset::get_image_path('product-image', 'thumb', $subitem->image) }}"
                                                                                    alt="imagen del producto" />
                                                                            @else
                                                                                <img src="{{ asset('assets/img/iiicab_min.png') }}"
                                                                                    alt="imagen del producto" />
                                                                            @endif
                                                                        </a>
                                                                        {{-- <div class="add-to-links">
                                                                        <a href="wishlist.html" data-toggle="tooltip" title="Add to Wishlist"><i class="ion-android-favorite-outline"></i></a>
                                                                        <a href="compare.html" data-toggle="tooltip" title="Add to Compare"><i class="ion-stats-bars"></i></a>
                                                                    </div> --}}
                                                                        <div class="box-desc">
                                                                            <!--en caso de descuento por porcentaje -->
                                                                            <?php
                                                                            $percentage = 0;
                                                                            $rest = 0;
                                                                            if (isset($product->product_bridge->real_price) && isset($product->product_bridge->full_price)) {
                                                                                $rest = $product->product_bridge->real_price - $product->product_bridge->full_price;
                                                                                $product->product_bridge->real_price / $product->product_bridge->full_price;
                                                                                $percentage = $percentage * 100;
                                                                            }
                                                                            
                                                                            if (is_int($percentage)) {
                                                                                \Log::info($percentage);
                                                                            } ?>
                                                                            {{-- @if (round($percentage, 0) == $percentage && $percentage < 100) --}}
                                                                            @if ($percentage < 100)
                                                                                <span class="percent_desc"><span
                                                                                        class="number">
                                                                                        -{{ round(100 - $percentage) }}
                                                                                        %</span></span>
                                                                            @elseif($rest < 0)
                                                                                <!--en caso de descuento por numero fijo -->
                                                                                <span class="fixed_desc"><span
                                                                                        class="number">Bs.
                                                                                        {{ number_format($subitem->product_bridge->real_price - $subitem->product_bridge->full_price, 2) }}</span></span>
                                                                            @endif
                                                                        </div>
                                                                        @if ($subitem->created_at->format('Y-m-d') > date('Y-m-d', strtotime('-15 days')))
                                                                            <div class="box-new">
                                                                                <span class="new_product"><span
                                                                                        class="number">Nuevo</span></span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="product-content">
                                                                        <h5 class="product-name"><a
                                                                                href="{{ url('producto/' . $subitem->product_bridge->slug) }}">{{ $subitem->name }}</a>
                                                                        </h5>
                                                                        <div class="price-box">
                                                                            <?php   $percentage = 0;
                                                                            $rest = 0;
                                                                            if (isset($product->product_bridge->real_price) && isset($product->product_bridge->full_price)) {
                                                                                $rest = $product->product_bridge->real_price - $product->product_bridge->full_price;
                                                                                $product->product_bridge->real_price / $product->product_bridge->full_price;
                                                                                $percentage = $percentage * 100;
                                                                            }
                                                                            if (is_int($percentage)) {
                                                                                \Log::info($percentage);
                                                                            } ?>
                                                                            @if ($subitem->product_bridge->real_price != $subitem->product_bridge->full_price)
                                                                                <span
                                                                                    class="price-old"><del>{{ number_format($subitem->price, 2) }}</del></span>
                                                                            @endif
                                                                            <span class="price-regular">Bs.
                                                                                {{ number_format($subitem->product_bridge->real_price, 2) }}</span>
                                                                        </div>

                                                                        @if (isset($subitem->product_bridge->real_price) && $subitem->product_bridge->real_price > 0)

                                                                        <div class="product-item-action">
                                                                            <a class="btn btn-cart"
                                                                                href="{{ url('process/add-cart-item/' . $subitem->product_bridge->id) }}">
                                                                                <i class="ion-bag"></i><span> Añadir al
                                                                                    carrito</span>
                                                                            </a>
                                                                        </div>
                                                                    @else
                                                                        <div class="product-item-action">
                                                                            <a class="btn btn-cart" href="{{ url('producto/' . $subitem->product_bridge->slug) }}"><i
                                                                                    class="ion-bag"></i><span> Más info</span></a>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="section-banner">
                            <img src="{{ asset(\Asset::get_image_path('image-content-image', 'normal', \Solunes\Master\App\ImageContent::find(2)->image)) }}"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br><br>

    {{-- <div class="brand-logo-area bg-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="brand-logo-slider">
                    <div class="brand-logo-carousel">
                        @foreach ($nodes['brands'] as $brand)
                        <div class="brand-item">
                            @if ($brand->image)
                                <img src="{{ Asset::get_image_path('brand-image', 'normal', $brand->image) }}">
                            @else
                                <img src="{{ asset('assets/img/logo_act.png') }}" alt="">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- let's call the following div as the POPUP FRAME -->
@if (App\Information::find(5)->active == 1)
<div id="popup" class="popup panel panel-primary">
    
    <!-- and here comes the image -->
    <div class="div-popup">{!! App\Information::find(5)->content !!}</div> 
    {{-- <img src="{{ asset('assets/img/pop-up.png') }}  "> --}}

    <!-- Now this is the button which closes the popup-->
    <div class="btn-close">
        <button id="close" class="btn btn-close-popup">x</button>
    </div>
    <!-- let's call the following div as the POPUP FRAME -->
    @if (App\Information::find(5)->active == 1)
        <div id="popup" class="popup panel panel-primary">

            <!-- and here comes the image -->
            <div class="div-popup">{!! App\Information::find(5)->content !!}</div>
            {{-- <img src="{{ asset('assets/img/pop-up.png') }}  "> --}}

            <!-- Now this is the button which closes the popup-->
            <div class="btn-close">
                <button id="close" class="btn btn-close-popup">x</button>
            </div>

            <!-- and finally we close the POPUP FRAME-->
            <!-- everything on it will show up within the popup so you can add more things not just an image -->
        </div>
    @endif
@endsection

@section('script')
    @if (App\Information::find(5)->active == 1)
        <script type="text/javascript">
            //with this first line we're saying: "when the page loads (document is ready) run the following script"
            $(document).ready(function() {
                //select the POPUP FRAME and show it
                $("#popup").hide().fadeIn(1000);

                //close the POPUP if the button with id="close" is clicked
                $("#close").on("click", function(e) {
                    e.preventDefault();
                    $("#popup").fadeOut(1000);
                });
            });
        </script>
    @endif

    {{-- flag slider  --}}
    <script>
        $('.brand-logo-slider').slick({
            dots: false,
            infinite: true,
            autoplay: true,
            arrows: false,
            speed: 500,
            slidesToShow: 9,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 8,
                        slidesToScroll: 1,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    </script>
@endsection
