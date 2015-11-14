@extends('base')

@section('css')
    <link href="/css/fixed.css" rel="stylesheet">
    <link href="/css/list/base.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">
    @yield('list-css')
@endsection

@section('main')
    <div id="main-wrapper">
        <div id="leftbar-wrapper">
            <div class="sidebar left-sidebar">
                <h3 class="sidebar-title">CATEGORIES</h3>
                <ul>
                    @foreach ($list_categories as $category)
                        @if( isset($current_category) and $current_category == $category )
                            <li class="active"><div class="arrow-right"></div><a>{{ $category }}</a></li>
                        @else
                            <li><a href="/list?category={{ $category }}">{{ $category }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <div id="rightbar-wrapper">
            <div class="sidebar right-sidebar">
                <h3 class="sidebar-title">LABELS</h3>
                <ul>
                    <li><a>Beach</a></li>
                    <li><a>Hiking</a></li>
                    <li><a>Camping</a></li>
                    <li><a>Ski</a></li>
                    <li class="active"><div class="arrow-left"></div><a>Parks</a></li>
                    <li><a>Farm</a></li>
                    <li><a>Family Trip</a></li>
                    <li><a>Lake & Pond</a></li>
                    <li><a>Photographic</a></li>
                    <li><a>Road Trip</a></li>
                    <li><a>Animals</a></li>
                </ul>
            </div>
        </div>
        <div id="content-wrapper">
            @yield('content')
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/sidebar.js"></script>
    @yield('list-javascript')
@endsection