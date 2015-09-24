@extends('base')

@section('main')
<div class="container" style="text-align: center;">
    <div class="page-form-container">
        @include('forms.forgot_password')
    </div>
</div>
@endsection

@section('javascript')
    <script>
        $( document ).ready(function() {
            $('.static-popup-link').removeClass('static-popup-link');
        });
    </script>
@endsection