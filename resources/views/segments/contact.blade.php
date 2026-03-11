  <section class="content">
    <div class="container">
      <h1>Lorem Ipsum</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus faucibus justo sit amet blandit consectetur. Donec feugiat ligula in accumsan porta. In ante eros, vehicula eu risus a, molestie sagittis enim. Suspendisse tristique nibh eget orci rhoncus commodo. Interdum et malesuada fames ac ante ipsum primis in faucibus. </p>
    </div>
  </section>
  
  <section class="section-80 section-lg-bottom-265">
    <div class="shell text-left">
      <div class="display-table">
        <div class="table-1">
          <h2 class="offset-top-65">{!! $title !!}</h2>
          <div class="divider divider-left divider-left-shark"></div>
          <div class="range offset-md-top-56">
            <div class="cell-lg-5 cell-sm-6">
              <div class="rd-mailform-validate"></div>
              <form data-result-class="rd-mailform-validate" data-form-type="contact" method="post" action="{{ url('process/model') }}" class="rd-mailform offset-top-4 text-left">
                <input type="text" name="name" data-constraints="@NotEmpty" placeholder="Nombre">
                <input type="text" name="email" data-constraints="@NotEmpty @Email" placeholder="Email">
                <textarea name="message" data-constraints="@NotEmpty" placeholder="Mensaje" class="offset-md-top-63"></textarea>
                <input type="hidden" name="model_node" value="contact-form" />
                <input type="hidden" name="lang_code" value="es" />
                <button class="btn btn-primary btn-md offset-top-56">Enviar</button>
              </form>
            </div>
            <div class="cell-lg-5 cell-sm-6 cell-sm-preffix-0 cell-lg-preffix-2 offset-top-70 offset-sm-top-0">
              <!-- RD Google Map-->
              <div class="rd-google-map">
                <div id="google-map" class="rd-google-map__model rd-google-map-height"></div>
                <ul class="rd-google-map__locations">
                  <li data-y="-16.5445518" data-x="-68.0803528">
                    <p>Jaime Mendoza, La Paz, Bolivia</p>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>