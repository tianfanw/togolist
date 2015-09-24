@if(Session::has('message'))
    <div class="alert {{ Session::has('error') ? 'alert-danger' : 'alert-success' }}
            alert-dismissible {{ Session::has('is_important') ? 'alert-important' : '' }}" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::pull('message') }}
    </div>
@endif