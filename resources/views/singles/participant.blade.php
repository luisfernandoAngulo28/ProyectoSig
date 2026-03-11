<div class="row grid">
  @foreach($participants as $participant)
    <a class="lightbox" href="{{ url('participante/'.$participant->id) }}?lightbox[width]=800&lightbox[height]=600">
      <div class="col-sm-6 participant grid-item">
      	<div class="participant-level">
          <h4>{{ $participant->name }} - <span>{{ $participant->type}}</span> - <span>{{ $participant->country}}</span></h4>
          @if($participant->theme_title)
            <p>{{ $participant->theme_title }}</p>
          @endif
      	</div>
      </div>
    </a>
  @endforeach
</div>
