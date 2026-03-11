<div class="row no-gutters">
  @if(count($items)>0)
    @foreach($items as $category => $item_array)
      <div class="col-sm-6">
        <div class="thematic-category">
          <h3>{{ trans('master::admin.'.$category) }}</h3>
          @foreach($item_array as $item)
            <p>{{ $item->name }}</p>
          @endforeach
        </div>
      </div>
    @endforeach
  @else
    <p>Actualmente no hay temáticas en esta sección.</p>
  @endif
</div>
