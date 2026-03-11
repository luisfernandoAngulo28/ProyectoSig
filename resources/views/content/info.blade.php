@extends('layouts/master')

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
<div class="container pt-50 pb-50">
    <div class="about-us-wrapper pt-0 pb-0">
        <div class="deals-wrapper bg-white">
            <div class="about-text-wrap">
                <h2>{{ $item->name }}</h2>
                {!! $item->content !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection