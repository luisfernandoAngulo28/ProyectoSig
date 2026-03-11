@extends('layouts/master')

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1>Tienda</h1>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('inicio') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tienda</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
{{--  <h1>{{ count($nodes['destacados']) }}</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>codigo</th>
        <th>Imagen</th>
    </tr>
    @foreach ($nodes['destacados'] as $dest)
    <tr>
        <td>{{ $dest->id }}</td>
        <td>{{ $dest->name }}</td>
        <td>{{ $dest->barcode }}</td>
        <td>{{ $dest->image }}</td>
    </tr>
    @endforeach
</table> --}} 
<section class="section pt-50 pb-50">
    <div class="container">
        <div class="brand-logo-area bg-white pt-30">
            <h5 class="center product-name">Categorías</h5>
            <br>
            <div class="row no-margin">
                <div class="col-12 grid_cntnt">
                    @if (count($nodes['categories']) == 2)
                        <br>
                    @endif
                    @foreach ($nodes['categories'] as $item)
                        <div class="brand-item contnt_cat-gb">
                            <a href="{{url('categoria/'.$item->id)}}" class="">
                                @if($item->image)
                                    <img src="{{ Asset::get_image_path('category-image', 'thumb', $item->image) }}" alt="" class="img-responsive">
                                @else
                                    <img src="{{ asset('assets/img/logo_act-2.jpg') }}" alt="" class="img-responsive">
                                @endif
                                <div>
                                    <span>{{ $item->name }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                    @if (count($nodes['categories']) == 2)
                        <br>
                    @endif
                </div>
                <div class="col-12">
                    <hr>
                </div>
            </div>
            <h5 class="center product-name">Marcas</h5>
            <br>
            <div class="row no-margin" id="marcas">
                <div class="col-12 grid_cntnt">
                    @foreach($nodes['brands'] as $brand)
                        <div class="brand-item contnt_cat-gb">
                            <a href="{{ url('marca/'.$brand->id) }}" class="@if(request()->segment(1)=='marca'&&request()->segment(2)==$brand->id) active @endif ">
                                @if($brand->image)
                                    <img src="{{ Asset::get_image_path('brand-image', 'normal', $brand->image) }}">
                                @else
                                    <img src="{{ asset('assets/img/logo_act-2.jpg') }}" alt="">
                                @endif
                                <span>{{ $brand->name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection