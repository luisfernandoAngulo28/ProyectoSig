@extends('master::layouts/admin-2')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/sales/store.css') }}">
@endsection

@section('content')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Mis Pagos Pendientes</h2>
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url(config('customer.redirect_after_login')) }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Mis Pagos Pendientes</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="content-body ecommerce-application">             
    <!-- Wishlist Starts -->
    <section id="wishlist" class="grid-view wishlist-items">
        @foreach($customer->pending_payments as $payment)
        <div class="card ecommerce-card">
            <div class="card-content">
                <?php $sale_item_id = $payment->payment_item->item_id; ?>
                @if($sale_item_id)
                <?php $sale_image = \Solunes\Sales\App\SaleItem::find($sale_item_id); ?>
                  @if($sale_image&&$sale_image->product_bridge->image)
                    <div class="item-img text-center">
                      <img src="{{ asset(\Asset::get_image_path('product-bridge-image','thumb',$sale_image->product_bridge->image)) }}" class="img-fluid" alt="img-placeholder">
                    </div>
                  @endif
                @endif
                <div class="card-body">
                    <div class="item-wrapper">
                        <div>
                            <h4 class="item-price">
                                Monto: Bs. {{ $payment->real_amount }} ({{ trans('payments::admin.'.$payment->status) }})
                            </h4>
                        </div>
                    </div>
                    <div class="item-name">
                       <a href="" data-toggle="modal" data-target="#large{{ $payment->id }}"> <span>{{ $payment->name }} </span></a>
                    </div>
                    <div>
                        <p class="item-description">
                            {{ $payment->payment_item->name }}<br>
                        </p>
                    </div>
                </div>
                <div class="item-options text-center">
                    <div class="wishlist remove-wishlist">
                      @if(config('payments.customer_cancel_payments')&&$payment->customer_cancel_payments&&$payment->status=='holding')
                      <a href="{{ url('payments/cancel-payment/'.$payment->id) }}">
                        <i class="feather icon-x align-middle"></i> Cancelar
                      </a>
                      @endif
                    </div>
                    <div class="cart move-cart">
                      @if($payment->sale_payment&&$payment->sale_payment->payment_method->code=='pagostt')
                      <a href="{{ url('pagostt/make-single-payment/'.$customer->id.'/'.$payment->id) }}">
                        <i class="feather icon-home"></i> <span class="move-to-cart">Realizar pago</span>
                      </a>
                      @elseif($payment->sale_payment)
                      <a href="{{ url('process/sale/'.$payment->sale_payment->parent_id) }}">
                        <i class="feather icon-home"></i> <span class="move-to-cart">Ir a Venta</span>
                      </a>
                      @endif
                    </div>
                    <div class="cart move-cart" style="background: #26326f;">
                      <a href="#" data-toggle="modal" data-target="#large{{ $payment->id }}">
                        <i class="feather icon-align-justify align-middle"></i> Detalle
                      </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </section>
    <!-- Wishlist Ends -->            
</div>
@foreach($customer->pending_payments as $payment)
<div class="modal fade text-left" id="large{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel17">{{ $payment->name }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <table class="table data-thumb-view ta-left">
                  <tr>
                    <td>
                      Para: {{ $payment->sale_payment->sale->gift_name }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Mensaje: {{ $payment->sale_payment->sale->gift_message }}
                    </td>
                  </tr>
                  {{-- <tr>
                    <td>
                      Dirección: {{ $payment->sale_payment->sale->sale_deliveries->address }}
                    </td>
                  </tr>--}}
                  <tr>
                    <td>
                      Costo de envío: {{ $payment->currency->name }} {{ $payment->payment_shipping->price }}
                    </td>
                  </tr> 
                </table>
                <table class="table data-thumb-view">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>PRODUCTO</th>
                          <th>CANTIDAD</th>
                          <th>PRECIO</th>
                          <th>PRECIO C/DESC</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($payment->payment_items as $key => $product)
                      <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        @if ($product->discount_price>0)
                        <td><del>{{ $product->currency->name }} {{ round($product->price*$product->quantity,2) }}</del></td>
                        @else
                        <td>{{ $product->currency->name }} {{ round($product->price*$product->quantity,2) }}</td>
                        @endif
                        @if ($product->discount_price>0)
                        <td>{{ $product->currency->name }} {{ round($product->price*$product->quantity,2) - round($product->discount_price*$product->quantity,2) }}</td>
                        @else
                        <td>-</td>
                        @endif
                      </tr>
                      @endforeach
                  </tbody>
                </table>
                <div style="text-align: right; "></div>
            </div>
            <div class="modal-footer">
              <h5></h5>
              <h4>Total: Bs. {{ $payment->real_amount }}</h4>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('script')
  <!--<script>
    new CBPFWTabs(document.getElementById('tabs'));
  </script>-->
@endsection