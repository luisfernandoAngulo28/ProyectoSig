@if($page->meta_title)
  @section('title', $page->meta_title)
@else
  @section('title', $page->name) 
@endif
@if($page->meta_description)
  @section('description', $page->meta_description)
@endif
@section('change-locale', $page->translate()->slug) 
@if($page->id!=1)
	@section('body-class', 'bg-header')
@endif
@if(request()->segment(1)!='inicio')
	@section('header')
	  @include('helpers.banner',['page'=>$page])
	@endsection
@endif