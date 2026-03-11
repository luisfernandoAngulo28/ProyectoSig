@if(count($items)>0)
	@foreach($items as $key => $item)
		<section class="form">
			<div class="container inner-container">
	        	{!! $item->content !!}
			</div>
		</section>
	@endforeach
@endif