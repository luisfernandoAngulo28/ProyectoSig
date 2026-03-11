<section class="home-our-videos">
    <div class="shell text-left">
      <h2 class="offset-lg-top-162 offset-top-52">{!! $title !!}</h2>
      <div class="divider divider-left divider-left-shark"></div>
      <p> </p>
      {!! $content !!}
      <div class="offset-top-52">
        <div class="row no-gutters">
          @foreach($items as $video)
            <div class="col-sm-4 grid-item">
              <div class="video-container">
                <div class="video-title">{{ $video->name }}</div>
                <video id="my-video-{{ $video->id }}" class="video-js vjs-controls-disabled" controls="false" preload="auto" width="390" height="{{ 390*$video->proportion }}"
                poster="{{ Asset::get_image_path('video-image', 'thumb', $video->image) }}" data-setup="{}">
                  <source src="{{ asset('assets/videos/'.$video->mp4_link) }}" type='video/mp4'>
                  <!--<source src="{{ asset('assets/videos/'.$video->webm_link) }}" type='video/webm'>-->
                  <p class="vjs-no-js">
                    Para ver este video, debe descargar un navegador moderno que soporte
                    <a href="http://videojs.com/html5-video-support/" target="_blank">HTML5</a>.
                  </p>
                </video>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
</section>