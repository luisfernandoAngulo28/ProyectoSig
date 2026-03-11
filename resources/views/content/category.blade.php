@extends('layouts/master')

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1>Tienda - <strong>{{ $item->name }}</strong></h1>
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
                                        <p id="count-product">Mostrando {{ count($item->product_bridges) }} libros</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 order-1 order-md-2">
                                <div class="top-bar-right">
                                    @foreach($variables as $variable_id => $variable)
                                        <form id="filter-form">
                                            <input type="hidden" value="{{ $item->id }}" name="category">
                                            <div class="product-short">
                                                <p>Pais: </p>
                                                <div class="content_filt content-box_age" id="bia_age">
                                                    <a class="box_age" data-id="bia_age">Seleccionar pais</a>
                                                    <div class="content_list-filt">
                                                        <ul>
                                                            @foreach($variable['items'] as $variation_option_key => $variation_option_name)
                                                            <li style="list-style: none;">
                                                                <input class="styled-checkbox" id="styled-checkbox-{{ $variation_option_key }}" type="checkbox" name="variation[]"  @if(request()->has('variation')&&is_array(request()->input('variation'))&&in_array($variation_option_key, request()->input('variation'))) checked="checked" @endif value="{{ $variation_option_key }}">
                                                                <label for="styled-checkbox-{{ $variation_option_key }}">{{ $variation_option_name }}</label>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                      
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="filter-results"  class="shop-product-wrap grid-view row">
                        @foreach ($item->products()->orderBy('created_at', 'DESC')->get() as $product)
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
<script type="text/javascript">
  $(".gg-comaxnt").click(function () {
    $(this).toggleClass("active");
  })
  function cropMenu(id_div) {
    if($(id_div).css('display')=='block'){
      $(id_div).slideUp(300);
    } else {
      $(id_div).slideDown(300);
    }
    return false;
  }
  $(document).on("click", "a.remove-category" , function(e) {
    e.preventDefault();
    var value = $(this).data('id');
    $('#styled-category-'+value).prop( "checked", false );
    $('#filter-tag-category-'+value).remove();
    updateFilter();
    return false;
  });
  $(document).on("click", "a.remove" , function(e) {
    e.preventDefault();
    var value = $(this).data('id');
    $('#styled-checkbox-'+value).prop( "checked", false );
    $('#filter-tag-variation-'+value).remove();
    updateFilter();
    return false;
  });
  $("#filter-form").submit(function(e){
      return false;
  });
  function updateFilter() {
    var category = $('#category').val() || '0';
    $.post('{{ url("one-samsung-ajax") }}', $('#filter-form').serialize(), function( data ) {
        $('#count-product').text('Mostrando '+data['count']+' Productos');
      $('#filter-results').html(data['html'])
      $('#filter-summary').html(data['variations_html'])
      window.history.pushState("object or string", "Title", "{{ url(request()->url()) }}?"+$('#filter-form').serialize());

      $('.img-carousel').hover(function(){
        var new_this = $(this)
          change_images = setInterval(function() {
            var img = new_this.find("img:not(.hide)");
            img.addClass("hide");
            var nextImg = img.next("img");
            if( img[0] === new_this.find("img").last()[0] )
                nextImg = new_this.find("img").first();
            nextImg.removeClass("hide");
          }, 1000);
      }, function(){
          clearInterval(change_images);
      });
    })
  }

  $(document).ready(function() {
      $('.styled-checkbox').change(function() {
          updateFilter();       
      });
      //updateFilter();       
  });

</script>
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