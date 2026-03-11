<div class="col-sm-3 col-xs-6 grid-item">
  <a target="_blank" href="{{ url('convocatoria/'.$item->id) }}">
  	<div class="single-publication" >
        {!! Asset::get_image('publication-image', 'normal', $item->image) !!}
        <div class="item-content" style="border-color: {{ $item->category->color }}">
          <h4 style="color: {{ $item->category->color }};">{{ $item->category->name }}</h4>
          <div class="border-block" style="background-color: {{ $item->category->color }}"></div>
          <h3>{{ $item->name }}</h3>
        	<p style="color: {{ $item->category->color }};">Publicado el {{ $item->created_at->format('d/m/Y') }}</p>
          <p style="color: {{ $item->category->color }};">Por: {{ $item->profile->name }}</p>
        </div>
  	</div>
  </a>
</div>