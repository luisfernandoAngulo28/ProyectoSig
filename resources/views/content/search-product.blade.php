@extends('layouts/master')

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1><strong>Busqueda personalizada</strong></h1>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('inicio') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Busqueda personalizada</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="shop-main-wrapper pt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shop-product-wrapper">
                    <div class="shop-top-bar">
                        <div class="row align-items-center">
                            <div class="col-lg-7 col-md-6 order-2 order-md-1">
                                <div class="top-bar-left">
                                    <div class="product-view-mode">
                                        <a class="active" href="#" data-target="grid-view"><i class="fa fa-th"></i></a>
                                        <a href="#" data-target="list-view"><i class="fa fa-list"></i></a>
                                    </div>
                                    <div class="product-amount">
                                        @if(count($product_bridges)==0)
                                         <p id="count-product">No se encontraron resultados para "{{ $search }}".</p> 
                                        @elseif( $search == '' )
                                         <p id="count-product">Productos encontrados "{{ count($product_bridges) }}".</p> 
                                        @else
                                        <p id="count-product">Se encontraron {{ count($product_bridges) }} productos para "{{ $search }}". </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="filter-results"  class="shop-product-wrap grid-view row">
                        @if(count($product_bridges)==0)
                            <h6>Haz una nueva búsqueda. Comprueba la ortografía o busca un término menos específico.</h6>
                        @else
                        @foreach ($product_bridges as $product)
                            <?php $product = $product->product;?>
                            @if($product->quantity>0 && $product->active==1)
                                @include('includes.product-summary')
                            @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('script')
<script type="text/javascript">
    var myNS = new (function() {
        $('.box_age').on('click', function() {
            $('.box_age.active').not(this).removeClass('active');
            $id = "#" + $(this).toggleClass('active').attr('data-id');
            $('.content-box_age:not(' + $id + ')');
            $($id).toggleClass('active_box');

            $('html').one('click',function() {
                setTimeout(() => {
                    $(".box_age").removeClass("active");
                    $('.content-box_age').removeClass("active_box"); 
                }, 500);
            });
            event.stopPropagation();
        });

    });
</script>
@endsection