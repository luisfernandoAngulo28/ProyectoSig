<section class="bg-polar section-80 section-md-bottom-195">
  <div class="shell">
    <h2 class="text-left">{!! $title !!}</h2>
    <div class="divider divider-left divider-left-shark z-index"></div>

	@if(count($items)>0)
      @foreach($items as $key => $item)
	    <div class="range range-sm-reverse offset-top-80">
	      <div class="cell-md-7 cell-sm-5">
	        <a href="{{ url('blog/'.$item->id) }}">
	          {!! Asset::get_image('blog-image', 'thumb', $item->image) !!}
	        </a>
	      </div>
	      <div class="cell-md-5 cell-sm-7 text-left section-lg-top-70 z-index">
	        <div class="post">
	          <div class="post-body">
	          	<a href="{{ url('blog/'.$item->id) }}" class="h3 text-primary">{{ $item->name }}</a>
	            <div class="divider divider-img divider-img-var-5 divider-img-brand"></div>
	            <p class="offset-top-16"></p>
	            {!! $item->summary !!}
	          </div>
	          <div class="post-meta">
	            <span class="fa-calendar">
	              <time datetime="{{ $item->created_at->format('Y-m-d') }}">{{ $item->created_at->format('Y-m-d') }}</time>
	            </span>
	            <span class="fa-comments inset-xs-left-32 inset-lg-left-35 inset-md-left-18 inset-left-0">
	              <span class="p">15 Comentarios</span>
	            </span>
	          </div>
	          <a href="{{ url('blog/'.$item->id) }}" class="btn btn-primary btn-sm offset-top-35">Ver Artículo</a>
	        </div>
	      </div>
	    </div>
	  @endforeach
	@endif

  </div>
</section>