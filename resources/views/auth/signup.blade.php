@extends('base')

@section('main')
    <div class="container" style="text-align: center;">
        <div class="page-form-container">
            @include('forms.signup')
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