@extends('layouts/master')

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1><strong>Ofertas </strong></h1>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('inicio') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ofertas especiales</li>
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
                                        <p id="count-product">Mostrando {{ count($product_bridges) }} productos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="filter-results"  class="shop-product-wrap grid-view row">
                        @foreach ($product_bridges as $product)
                            @include('includes.product-summary')
                        @endforeach
                    </div>
                    <div id="pag" class="pagination-desing">
                       {!! $product_bridges->render() !!}  
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('script')

@endsection