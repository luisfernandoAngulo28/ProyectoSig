<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="product-item types">
        <div class="product-thumb">
            <a href="{{ url('producto/' . $product->product_bridge->slug) }}">
                @if ($product->image)
                    <img src="{{ Asset::get_image_path('product-image', 'thumb', $product->image) }}"
                        alt="imagen del producto" />
                @else
                    <img src="{{ asset('assets/img/iiicab_min.png') }}" alt="imagen del producto" />
                @endif
                <div class="box-desc">
                    <!--en caso de descuento por porcentaje -->

                    <?php $percentage = 0;
                    $rest = 0;
                    
                    if (isset($product->product_bridge->real_price) && isset($product->product_bridge->full_price)) {
                        $rest = $product->product_bridge->real_price - $product->product_bridge->full_price;
                        $product->product_bridge->real_price / $product->product_bridge->full_price;
                        $percentage = $percentage * 100;
                    }
                   
                    if (is_int($percentage)) {
                        \Log::info($percentage);
                    } ?>
                    @if ($percentage < 100 & $percentage > 0)
                        <span class="percent_desc"><span class="number">-{{ round(100 - $percentage) }} %</span></span>
                    @elseif($rest < 0)
                        <!--en caso de descuento por numero fijo -->
                        <span class="fixed_desc"><span
                                class="number">{{ $product->product_bridge->real_price - $product->product_bridge->full_price }}
                                Bs.</span></span>
                    @endif
                </div>
                @if ($product->created_at->format('Y-m-d') > date('Y-m-d', strtotime('-15 days')))
                    <div class="box-new">
                        <span class="new_product"><span class="number">Nuevo</span></span>
                    </div>
                @endif
            </a>
        </div>
        <div class="product-content">
            <h5 class="product-name"><a
                    href="{{ url('producto/' . $product->product_bridge->slug) }}">{{ $product->name }}</a></h5>
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

                @if ($product->product_bridge->real_price != $product->product_bridge->full_price)
                    <span class="price-old"><del>{{ number_format($product->price, 2) }}</del></span>
                @endif
                <span class="price-regular">{{ number_format($product->product_bridge->real_price, 2) }} Bs.</span>


            </div>
            @if (isset($product->product_bridge->real_price) && $product->product_bridge->real_price > 0)
                <div class="product-item-action">
                    <a class="btn btn-cart" href="{{ url('process/add-cart-item/' . $product->product_bridge->id) }}"><i
                            class="ion-bag"></i><span> Añadir al carrito</span></a>
                </div>
            @else
                <div class="product-item-action">
                    <a class="btn btn-cart" href="{{ url('producto/' . $product->product_bridge->slug) }}"><i
                            class="ion-bag"></i><span> Más info</span></a>
                </div>
            @endif

        </div>
    </div>
    <div class="product-list-item">
        <div class="product-thumb">
            <a href="{{ url('producto/' . $product->product_bridge->slug) }}">
                @if ($product->image)
                    <img src="{{ Asset::get_image_path('product-image', 'thumb', $product->image) }}"
                        alt="imagen del producto" />
                @else
                    <img src="{{ asset('assets/img/iiicab_min.png') }}" alt="imagen del producto" />
                @endif
            </a>
        </div>
        <div class="product-content-list">
            <h5 class="product-name"><a
                    href="{{ url('producto/' . $product->product_bridge->slug) }}">{{ $product->name }}</a></h5>
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
                @if (round($percentage, 0) == $percentage && $percentage < 100)
                    <span class="price-old">-{{ 100 - $percentage }} %</span>
                @elseif($rest < 0)
                    <span class="price-regular">{{ number_format($product->real_price - $product->full_price, 2) }}
                        Bs.</span>
                @endif
            </div>
            <p>{{ $product->description }}</p>

            @if (isset($product->product_bridge->real_price) && $product->product_bridge->real_price > 0)
            <div class="product-item-action">
                <a class="btn btn-cart" href="{{ url('process/add-cart-item/' . $product->product_bridge->id) }}"><i
                        class="ion-bag"></i><span> Añadir al carrito</span></a>
            </div>
        @else
            <div class="product-item-action">
                <a class="btn btn-cart" href="{{ url('producto/' . $product->product_bridge->slug) }}"><i
                        class="ion-bag"></i><span> Más info</span></a>
            </div>
        @endif
        </div>
    </div>
</div>
