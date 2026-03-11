@extends('layouts/master-clean')

@section('header')

<section class="slider-area">
    <div class="hero-slider-active slick-arrow-style slick-dot-style">
        <div class="hero-slider-item">
            <div class="d-flex align-items-center bg-img h-100" data-bg="{{ asset('assets/img/construction.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 {{-- offset-lg-2 --}} col-md-10 {{-- offset-md-2 --}}">

                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</section>
        
@endsection