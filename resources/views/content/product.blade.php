@extends('layouts/master')

@section('css')
    <style>
        .btn-home {
            display: inline-block;
            padding: 8px 35px;
            background-color: #26326f;
            color: #fff;
            font-size: 16px;
            border-radius: 30px;
        }
    </style>
@endsection

@section('header')
    <div class="breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-wrap">
                        <nav aria-label="breadcrumb">
                            <h1>{{ $item->name }}</h1>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('inicio') }}">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $item->name }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')

    <div class="shop-main-wrapper pt-50">
        <div class="container">
            <div class="row">
                <!-- product details wrapper start -->
                <div class="col-lg-12 order-1 order-lg-2">
                    <!-- product details inner end -->
                    @if ($item->active)
                        <div class="product-details-inner">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="product-large-slider mb-20">
                                        @if ($item->image)
                                            <div class="pro-large-img img-zoom">
                                                <img class="img-responsive"
                                                    src="{{ Asset::get_image_path('product-bridge-image', 'normal', $item->image) }}"
                                                    alt="" />
                                            </div>
                                        @else
                                            <div class="pro-large-img img-zoom">
                                                <img src="{{ asset('assets/img/iiicab_min.png') }}"
                                                    alt="imagen del producto" class="img-responsive" />
                                            </div>
                                        @endif
                                        @foreach ($product_gallery as $gallery)
                                            <div class="pro-large-img img-zoom">
                                                <img class="img-responsive"
                                                    src="{{ Asset::get_image_path('product-image-image', 'normal', $gallery->image) }}"
                                                    alt="" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="pro-nav slick-row-10 slick-arrow-style">
                                        @if ($item->image)
                                            <a class="pro-nav-thumb">
                                                <img class="img-responsive"
                                                    src="{{ Asset::get_image_path('product-bridge-image', 'normal', $item->image) }}"
                                                    alt="" />
                                            </a>
                                        @else
                                            <a class="pro-nav-thumb">
                                                <img src="{{ asset('assets/img/iiicab_min.png') }}"
                                                    alt="imagen del producto" class="img-responsive" />
                                            </a>
                                        @endif
                                        @foreach ($product_gallery as $gallery)
                                            <div class="pro-nav-thumb">
                                                <img class="img-responsive"
                                                    src="{{ Asset::get_image_path('product-image-image', 'normal', $gallery->image) }}"
                                                    alt="" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="product-details-des">
                                        <h5 class="product-name">{{ $item->name }}</h5>
                                        <div class="price-box">
                                            <span class="price-regular">Bs.
                                                {{ number_format($item->real_price, 2) }}</span>
                                            @if ($item->real_price != $item->full_price)
                                                <span class="price-old"><del>Bs. {{ $item->price }}</del></span>
                                            @endif
                                        </div>
                                        <p>{{ $item->product->summary }} </p>

                                        @if ($item->category)
                                            <div class="price-box">
                                                <span class="price-regular">Categoría</span>
                                            </div>
                                            <span style="display: block">
                                                <a href="{{ url('categoria/' . $item->category->id) }}">
                                                    {{ $item->category->name }}
                                                </a>
                                            </span>
                                            <br>
                                        @endif
                                        @if ($item->brand)
                                            <div class="price-box">
                                                <span class="price-regular">Marca</span>
                                            </div>
                                            <span style="display: block">
                                                <a href="{{ url('marca/' . $item->brand->id) }}">
                                                    {{ $item->brand->name }}
                                                </a>
                                            </span>
                                            <br>
                                        @endif
                                        <p><strong>Pais: </strong>{{ $item->product->age }} </p>

                                        @if (isset($item->product->author))
                                            <p><strong>Autor: </strong>{{ $item->product->author }} </p>
                                        @endif

                                        @if (isset($item->product->editorial))
                                            <p><strong>Editorial: </strong>{{ $item->product->editorial }} </p>
                                        @endif

                                        @if (isset($item->product->editorial_number))
                                            <p><strong>Edición: </strong>{{ $item->product->editorial_number }} </p>
                                        @endif

                                        @if (isset($item->product->page_number))
                                            <p><strong>Número de páginas: </strong>{{ $item->product->page_number }} </p>
                                        @endif
                                        @if ($item->quantity > 0)
                                            @if (isset($item->price))
                                                <div class="price-box">
                                                    <span class="price-regular">Cantidad</span>
                                                </div>
                                                <form action="{{ url('process/add-cart-item/' . $item->id) }}"
                                                    method="get">


                                                    <div class="quantity-cart-box d-flex align-items-center">
                                                        <div class="quantity">
                                                            <div class="pro-qty">
                                                                <input type="text" name="quantity" class=""
                                                                    value="1" title="Cantidad" step="1"
                                                                    min="1" max="10" size="4"
                                                                    pattern="[0-9]*" inputmode="numeric">
                                                            </div>
                                                        </div>

                                                        <div class="action_link">
                                                            <button type="submit" class="btn btn-cart"><i
                                                                    class="ion-bag"></i>Añadir al carrito</button>
                                                            {{-- <a href="{{ url('process/add-cart-item/'.$item->id) }}" class="btn btn-cart" ><i class="ion-bag"></i> Añadir al carrito</a> --}}
                                                        </div>



                                                    </div>
                                            @endif
                                            <input name="product_id" value="{{ $item->id }}" type="hidden">
                                            @if ($item->real_price != $item->price)
                                                <input type="hidden" id="product_price" name="product_price"
                                                    value="{{ $item->real_price }}" />
                                            @else
                                                <input type="hidden" id="product_price" name="product_price"
                                                    value="{{ $item->price }}" />
                                            @endif
                                            <br>
                                            <div class="price-box d-none">
                                                <span class="price-regular">¿Desea que el producto sea para regalo?</span>
                                            </div>
                                            <div id="demo" class="d-none">
                                                <label><input type="radio" name="example1" value="yes"><span></span>
                                                    SI</label> &nbsp;&nbsp;
                                                <label><input type="radio" name="example1" value="no"
                                                        checked><span></span> NO</label>
                                                <div class="conditional" data-cond-option="example1"
                                                    data-cond-value="yes">
                                                    <div class="input-group">
                                                        <label>Escribe un mensaje para enviar junto con el producto
                                                            (<small>Máximo 300 caracteres</small>)</label>
                                                        <div class="input-group">
                                                            <textarea name="detail" class="form-control" rows="4" cols="12" maxlength="300"></textarea><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            </form>
                                        @else
                                            <div class="price-box">
                                                <span class="price-regular">Agotado</span>
                                            </div>
                                        @endif
                                        <div class="like-icon mt-20">
                                            <a class="facebook" href="{{ \External::share_url('facebook') }}"
                                                data-original-title="Facebook"
                                                onclick="window.open(this.href, this.target, 'width=300,height=400'); return false;"><i
                                                    class="fa fa-facebook"></i>Compartir</a>
                                            <a class="twitter" href="{{ \External::share_url('twitter') }}"
                                                data-original-title="Twitter"
                                                onclick="window.open(this.href, this.target, 'width=300,height=400'); return false;"><i
                                                    class="fa fa-twitter"></i>Twittear</a>
                                            <a class="google" href="{{ \External::share_url('email') }}"
                                                data-original-title="Google"><i
                                                    class="fa fa-google-plus"></i>Compartir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($item->content)
                            <div class="product-details-reviews mt-50">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="product-review-info">
                                            <div class="tab-content reviews-tab">
                                                <div class="tab-pane fade show active" id="tab_one">
                                                    <div class="tab-one">
                                                        <p>{!! $item->content !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="center">
                            <h2>El producto que busca no se encuentra disponible</h2><br>
                            <a class="btn-home" href="{{ url('inicio') }}">Volver al inicio</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="related-products-area pt-50 pb-50">
        <div class="container">
            <div class="deals-wrapper bg-white">
                <div class="row">
                    <div class="col-12">
                        <div class="section-header-deals">
                            <div class="section-title-deals">
                                <h4>Productos Relacionados</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="most-viewed-carousel-2 slick-arrow-style">
                            @foreach ($product as $product)
                                @if ($product->quantity > 0 && $product->active == 1)
                                    <div class="product-slide-item">
                                        <div class="product-item mb-0">
                                            <div class="product-thumb">
                                                <a href="{{ url('producto/' . $product->slug) }}">
                                                    @if ($product->image)
                                                        <img src="{{ Asset::get_image_path('product-bridge-image', 'thumb', $product->image) }}"
                                                            alt="imagen del producto" class="img-responsive" />
                                                    @else
                                                        <img src="{{ asset('assets/img/iiicab_min.png') }}"
                                                            alt="imagen del producto" class="img-responsive" />
                                                    @endif
                                                </a>
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
                                                    @if ($percentage < 100)
                                                        <span class="percent_desc"><span
                                                                class="number">-{{ round(100 - $percentage) }}
                                                                %</span></span>
                                                    @elseif($rest < 0)
                                                        <!--en caso de descuento por numero fijo -->
                                                        <span class="fixed_desc"><span
                                                                class="number">{{ $product->real_price - $product->full_price }}
                                                                Bs.</span></span>
                                                    @endif
                                                </div>
                                                @if ($product->created_at->format('Y-m-d') > date('Y-m-d', strtotime('-15 days')))
                                                    <div class="box-new">
                                                        <span class="new_product"><span class="number">Nuevo</span></span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-content p-0">
                                                <h5 class="product-name pb-0"><a
                                                        href="{{ url('producto/' . $product->slug) }}">{{ $product->name }}</a>
                                                </h5>
                                                <div class="price-box">
                                                  
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
                                                    @if ($product->real_price != $product->full_price)
                                                        <span class="price-old"><del>{{ $product->price }}</del></span>
                                                    @endif
                                                    <span class="price-regular">{{ $product->real_price }} Bs.</span>
                                                </div>
                                                <div class="product-item-action">
                                                    <a class="btn btn-cart"
                                                        href="{{ url('process/add-cart-item/' . $product->id) }}"><i
                                                            class="ion-bag"></i> Añadir al carrito</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        (function($) {
            "use strict";
            jQuery('a[data-gal]').each(function() {
                jQuery(this).attr('rel', jQuery(this).data('gal'));
            });
            jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({
                animationSpeed: 'slow',
                theme: 'light_square',
                slideshow: true,
                overlay_gallery: true,
                social_tools: false,
                deeplinking: false
            });
        })(jQuery);
    </script>
    <script type="text/javascript">
        $('#owl-products-related').owlCarousel({
            loop: true,
            margin: 30,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            nav: true,
            dots: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 4
                }
            }
        })
    </script>
    <script type="text/javascript">
        function incrementQty() {
            var value = document.querySelector('input[name="quantity"]').value;
            value = isNaN(value) ? 1 : value;
            value++;
            document.querySelector('input[name="quantity"]').value = value;
        }

        function decrementQty() {
            var value = document.querySelector('input[name="quantity"]').value;
            value = isNaN(value) ? 1 : value;
            value > 1 ? value-- : value;
            document.querySelector('input[name="quantity"]').value = value;
        }
    </script>
    <script type="text/javascript">
        (function($) {
            $.fn.conditionize = function(options) {

                var settings = $.extend({
                    hideJS: true
                }, options);

                $.fn.showOrHide = function(listenTo, listenFor, $section) {
                    if ($(listenTo).is('select, input[type=text]') && $(listenTo).val() == listenFor) {
                        $section.slideDown();
                    } else if ($(listenTo + ":checked").val() == listenFor) {
                        $section.slideDown();
                    } else {
                        $section.slideUp();
                    }
                }

                return this.each(function() {
                    var listenTo = "[name=" + $(this).data('cond-option') + "]";
                    var listenFor = $(this).data('cond-value');
                    var $section = $(this);

                    //Set up event listener
                    $(listenTo).on('change', function() {
                        $.fn.showOrHide(listenTo, listenFor, $section);
                    });
                    //If setting was chosen, hide everything first...
                    if (settings.hideJS) {
                        $(this).hide();
                    }
                    //Show based on current value on page load
                    $.fn.showOrHide(listenTo, listenFor, $section);
                });
            }
        }(jQuery));

        $('.conditional').conditionize();
    </script>
    <script type="text/javascript">
        function imMaxlengthCounter(selector) {
            $(selector).each(function() {
                var $this = $(this),
                    limit = $this.attr('maxlength'),
                    length = $this.val().length;

                $this.after("<div class='text-info'><span>" + length + "</span> Caracteres</div>");

                $this.on('keyup', function() {
                    length = $this.val().length;

                    if (length <= limit) {
                        $this.siblings('.text-info').find('span').text(length);
                    }
                });
            });
        }
        imMaxlengthCounter('[maxlength]');
    </script>
@endsection
