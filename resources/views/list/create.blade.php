@extends('base')

@section('css')
    <link href="/css/multiple-select.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/flexslider.css">

    <link href="/css/map.css" rel="stylesheet">
    <link href="/css/list/create.css" rel="stylesheet">
@endsection

@section('main')
    @include('forms.list')
    @include('partials.delete-dialog')

@endsection

@section('javascript')
    <script src="/js/jquery.multiple.select.js"></script>
    <script src="/js/jquery.flexslider.js"></script>
    <script src="/js/photoviewer.js"></script>

    <!-- Google Map APIs and utilities -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBf7oT-h6bVFCbNtrujUWCsoySZMxh7khI&libraries=places"></script>
    <script src="/js/infobubble.js"></script>
    <script src="/js/locations.js"></script>

    <script src="/js/list/listform.js"></script>
    <script>
        $(document).ready(function() {
            initMap(true);
        });
    </script>
@endsection