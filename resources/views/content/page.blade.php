@extends('layouts/master')
@include('helpers.meta')

@section('css')
  @include('helpers.page-css',['page'=>$page])
@endsection

@section('content')
  @if(count($nodes)>0)
    <section class="main-section">
      @foreach($nodes as $node_name => $node)
        <div class="content-segment content-{{ $node['node']->name }} page-{{ $node['node']->id }}">
          @if($node['node']->folder=='form')
            @include('segments.form', $node['subarray'])
          @else
            @include('segments.'.$node['node']->name, $node['subarray'])
          @endif
        </div>
      @endforeach
    </section>
  @endif
@endsection

@section('script')
  @include('helpers.page-script',['page'=>$page])
@endsection