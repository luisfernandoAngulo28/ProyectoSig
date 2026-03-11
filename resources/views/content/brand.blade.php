@extends('layouts/master')

@section('css')
    <style>
        body.main-site .contnt_cat-gb a { margin: 10px; }
    </style>
@endsection

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1> <strong>{{ $item->name }}</strong> <br><small>Filtrar los productos por Marca</small></h1>
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

<div class="container pt-50">
    <div class="brand-logo-area bg-white">
        <div class="row">
            <div class="col-12">
                <div class="brand-logo-slider">
                    <div class="brand-logo-carousel-2">
                        @if(request()->segment(1)!='marca')
                            <div class="brand-item contnt_cat-gb">
                                <a class="active">
                                    <img src="{{ asset('assets/img/logo_act-2.jpg') }}" alt="" class="img-responsive">
                                    <span>Todos</span>
                                </a>
                            </div>
                        @else
                            <div class="brand-item contnt_cat-gb">
                                <a href="{{ url('productos') }}" class="">
                                    <img src="{{ asset('assets/img/logo_act-2.jpg') }}" alt="" class="img-responsive">
                                    <span>Todos</span>
                                </a>
                            </div>
                        @endif

                        @foreach($brands as $brand)
                            <div class="brand-item contnt_cat-gb">
                                <a href="{{ url('marca/'.$brand->id) }}" @if (request()->segment(1) . '/'. request()->segment(2) =='marca/'.$brand->id) class="active" @endif>
                                    @if($brand->image)
                                        <img src="{{ Asset::get_image_path('brand-image', 'normal', $brand->image) }}">
                                    @else
                                        <img src="{{ asset('assets/img/logo_act-2.jpg') }}" alt="" class="img-responsive">
                                    @endif
                                    <span>{{ $brand->name }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="shop-main-wrapper pt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shop-product-wrapper">

                    <div class="shop-product-wrap grid-view row">
                        @foreach ($item->product_bridges()->has('product')->orderBy('created_at', 'DESC')->get() as $product)
                            <?php $product = $product->product;?>
                            @if($product->quantity>0 && $product->active==1)
                                @include('includes.product-summary')
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('script')

@endsection