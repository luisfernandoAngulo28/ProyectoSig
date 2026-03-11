@if(Session::has('message_error'))
    <br>
    @if($container)
    <div class="m-grid m-grid--ver    m-container m-container--responsive m-container--xxl m-page__container">
    @endif
    <div class="m-alert m-alert--icon m-alert--air m-alert--square alert alert-danger alert-dismissible fade show" role="alert">
        <div class="m-alert__icon">
            <i class="la la-warning"></i>
        </div>
        <div class="m-alert__text">
            <strong>Lo sentimos</strong>
            {{ Session::get('message_error') }}
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @if($container)
    </div>
    @endif
    <br>
@elseif(Session::has('message_success'))
    <br>
    @if($container)
    <div class="m-grid m-grid--ver    m-container m-container--responsive m-container--xxl m-page__container">
    @endif
    <div class="m-alert m-alert--icon m-alert--air m-alert--square alert alert-success alert-dismissible fade show" role="alert">
        <div class="m-alert__icon">
            <i class="la la-warning"></i>
        </div>
        <div class="m-alert__text">
            <strong>Felicidades</strong>
            {{ Session::get('message_success') }}
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @if($container)
    </div>
    @endif
    <br>
@endif