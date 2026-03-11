<div class="container">
  <ul class="uk-subnav uk-subnav-pill uk-flex-left" data-uk-switcher="{connect:'#wk-901'}">
    @foreach($items as $program)
      <li><a href="/">{{ $program->short_name }}</a></li>
    @endforeach
  </ul>
  <ul id="wk-901" class="uk-switcher uk-text-left uk-margin-top" data-uk-check-display>
    @foreach($items as $program)
      <li>
        <div class="uk-panel">
          <div class="program-header">
            <h3>{{ $program->name }}</h3>
            {!! $program->content !!}
          </div>
          @foreach($program->subprograms as $subprogram)
            <div class="subprogram">
              <h3>{{ $subprogram->name }}</h3>
              <div class="center">{!! $subprogram->summary !!}</div>
              {!! $subprogram->content !!}
            </div>
          @endforeach
        </div>
      </li>
    @endforeach
  </ul>
</div>