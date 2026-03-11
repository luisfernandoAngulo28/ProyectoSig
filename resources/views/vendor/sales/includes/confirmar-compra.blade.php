<div class="row">
 <div class="col-md-12 col-sm-12 col-xs-12">
  <form action="{{ url('process/update-cart-two') }}" method="post">       
    @include('sales::includes.cart-full', ['items'=>$cart->cart_items, 'editable'=>true, 'delete'=>true])
    <div class="row">
      <div class="col-md-9 col-sm-7 col-xs-12">
        <div class="buttons-cart">
          {{-- @if($cart=='asd') aqui papu --}}
          <input type="submit" value="Actualizar Carro">
          {{-- @endif --}}
          
          <a href="{{ url(config('business.products_page')) }}">Seguir comprando</a>
        </div>
        @if(config('business.pricing_rules'))
          @if($cart->pricing_rule)
            <p>Descuento aplicado: {{ $cart->pricing_rule->name }}</p>
          @endif
          @if(Solunes\Business\App\PricingRule::where('active', 1)->where('type', 'coupon')->first())
          {{-- aqui tbn papu --}}
          <div class="row">
            <div class="col-sm-6">
              <div class="checkout-form-list">
                <label>Código de Cupón de Descuento<span class="required">*</span></label>
                {!! Form::text('coupon_code', $cart->coupon_code, ['plaecholder'=>'Ej: DXSW8']) !!}
              </div>
            </div>
            <div class="col-sm-3">
              <div class="buttons-cart">
                <br>
                <input type="submit" value="Aplicar Cupón" />
              </div>
            </div>
          </div>
          @endif
        @endif
      </div>
      <div class="col-md-3 col-sm-5 col-xs-12">
        <div class="cart_totals">
          <!--<h2>TOTAL</h2>-->
          <table>
            <tbody>
              <tr class="order-total">
                <th>Total del Pedido</th>
                <td>
                  <strong><span class="amount">{{ $cart->cart_item->currency->name }} {{ $total }}</span></strong>
                </td>
              </tr>                     
            </tbody>
          </table>
          @if(config('business.pricing_rules'))
          <?php $real_order_amount = \Business::getSaleDiscount($total, $cart->coupon_code); ?>
          @if($real_order_amount!=$total)
          <table>
            <tbody>
              <tr class="order-total">
                <th>Descuento</th>
                <td>
                  <strong><span class="amount">- {{ $cart->cart_item->currency->name }} {{ $total-$real_order_amount }}</span></strong>
                </td>
              </tr>                     
            </tbody>
          </table>
          <table>
            <tbody>
              <tr class="order-total">
                <th>Monto Final</th>
                <td>
                  <strong><span class="amount">{{ $cart->cart_item->currency->name }} {{ $real_order_amount }}</span></strong>
                </td>
              </tr>                     
            </tbody>
          </table>
          @endif
          @endif
          @if(config('sales.cart_quotation'))
          <div class="wc-proceed-to-checkout">
            <a href="{{ url('process/finalizar-cotizacion/'.$cart->id.'/true') }}">Generar Cotización</a>
          </div>
          @endif
          <div class="wc-proceed-to-checkout">
            <a href="{{ url('process/finalizar-compra-t/'.$cart->id) }}">Confirmar Compra</a>
          </div>
        </div>
      </div>
    </div>
  </form> 
</div>
</div>