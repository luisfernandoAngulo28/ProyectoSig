@if(count($items)>0)
    <div class="uk-article">
		<div class="container">
	        @foreach($items as $key => $item)
	        	{!! Asset::get_image('image-image', 'normal', $item->image) !!}
	        @endforeach
	   	</div>
	</div>
@endif