@extends('master::layouts/admin-2')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/sales/store.css') }}">
@endsection

@section('content')

<div class="content-header-left col-md-9 col-12 mb-2">
  <div class="row breadcrumbs-top">
      <div class="col-12">
          <h2 class="content-header-title float-left mb-0">Historial de Pagos</h2>
          <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url(config('customer.redirect_after_login')) }}">Inicio</a></li>
                  <li class="breadcrumb-item active">Historial de Pagos
                  </li>
              </ol>
          </div>
      </div>
  </div>
</div>
<!-- Data list view starts -->
<section id="data-thumb-view" class="data-thumb-view-header">
  <!-- dataTable starts -->
  <div class="table-responsive">
      <table class="table data-thumb-view">
          <thead>
              <tr>
                  <th></th>
                  <th>IMAGEN</th>
                  <th>DETALLE</th>
                  <th>ESTADO</th>
                  <th>MONTO</th>
                  <th>FECHA</th>
                  <th>INFO</th>
              </tr>
          </thead>
          <tbody>
            @foreach($customer->paid_payments as $key => $payment)
              <tr>
                <td> {{ $key+1}}</td>
                <?php $sale_item_id = $payment->payment_item->item_id; ?>
                <td class="product-img">
                  <?php $sale_image = \Solunes\Sales\App\SaleItem::find($sale_item_id); ?>
                  @if($sale_image&&$sale_image->product_bridge->image)
                    <img src="{{ asset(\Asset::get_image_path('product-bridge-image','thumb',$sale_image->product_bridge->image)) }}" alt="Banner">
                  @endif
                </td>
                <td class="product-name">{{ $payment->name }}</td>
                <!--<td class="product-category">Suscripción anual</td>-->
                <td>
                    {{-- <div class="chip chip-warning">
                        <div class="chip-body">
                            @if($payment->invoice_url)
                              <div class="chip-text"><a target="_blank" href="{{ $payment->invoice_url }}">Ver Factura</a></div>
                            @elseif($payment->receipt_url)
                              <div class="chip-text"><a target="_blank" href="{{ asset(\Asset::get_file('payment-receipt_file',$payment->receipt_url)) }}">Ver Recibo</a></div>
                            @else
                              <div class="chip-text"><a href="#">-</a></div>
                            @endif
                        </div>
                    </div> --}}
                    @if ($payment->status == 'paid' && $payment->sale_payment->sale->status == 'pending-delivery')
                    <div class="chip chip-warning">
                      <div class="chip-body">
                          <div class="chip-text"><a class="text-white" target="_blank" href="javascript:void(0)">Pedido en preparación</a></div>
                      </div>
                    </div>
                    @elseif($payment->status == 'paid' && $payment->sale_payment->sale->status == 'delivered')
                    <div class="chip chip-success">
                      <div class="chip-body">
                              <div class="chip-text"><a class="text-white" target="_blank" href="javascript:void(0)">Entregado</a></div>
                      </div>
                    </div>
                    @endif
                </td>
                <td class="product-price">Bs. {{ $payment->real_amount }} </td>
                <td> {{ date('H:i | d M Y ', strtotime($payment->created_at)) }} </td>
                <td>
                    <button type="button" class="btn btn-la-ganga" data-toggle="modal" data-target="#large{{ $payment->id }}">
                        Detalle
                    </button>
                </td>
              </tr>
            @endforeach
          </tbody>
      </table>
      
  </div>
  <!-- dataTable ends -->
</section>
@foreach($customer->paid_payments as $payment)
<div class="modal fade text-left" id="large{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel17">{{ $payment->name }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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
            <div class="modal-body">
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
            </div>
            <div class="modal-footer">
              <h4>Total: Bs. {{ $payment->real_amount  + $payment->payment_shipping->price }}</h4>
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