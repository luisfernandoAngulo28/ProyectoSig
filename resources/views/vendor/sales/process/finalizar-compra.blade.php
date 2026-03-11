@extends('layouts/master')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/sales/store.css') }}">
@endsection

@section('content')
<!-- checkout-area start -->
<div class="container solunes-store">

  @include('sales::includes.finalizar-compra')

</div>
<!-- checkout-area end -->  
@endsection

@section('script')
  @include('sales::scripts.finalizar-compra-js')
  @if(isset($map_coordinates))
  	@include('master::scripts.map-js')
  	@include('master::scripts.map-register-js')
  @endif
  <script>
      $(document).ready(function(){
        
        $('#city_id').change(function(){
            $('#shipping_id').empty();
            if( $('#city_id').val() == 1 ) {
                var options = `
                  <option value="7" selected>Envío a Domicilio</option>
                  <option value="8">Recojo en la tienda</option>`;
                $('#shipping_id').append(options);
                $('.hide-map').show();
            } else {
                $('.hide-map').hide();
                var option = `<option value="6" id="departamental">Delivery Departamental e Interdepartamental</option>`;
                $('#shipping_id').append(option);
            }
            $('.query_shipping_2').niceSelect('destroy'); //destroy the plugin 
            $('.query_shipping_2').niceSelect();
            // $('#shipping_id').hide();
        });
        $('#city_id').change();
        $('#check-gift').change(function(){
            if( this.checked ) {
                this.checked.value = 'true';
                $('.gitf-class').show();
            } else {
                $('.gitf-class').hide();
            }
        });
        $('#check-gift').change();
      });
      $('#shipping_id').change(function(){
          if( $('#shipping_id').val() == 7 ) {
            $('.main-gift').show();
            $('.gitf-class').show();
            $('.hide-map').show();
          } else {
            $('.hide-map').hide();
            $('.main-gift').hide();
            $('.gitf-class').hide();
            $('#check-gift').val('');
          }
      })
  </script>
@endsection