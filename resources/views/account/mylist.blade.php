@extends('base')

@section('css')
    <link href="/css/fixed.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/flexslider.css">
    <link href="/css/map.css" rel="stylesheet">
    <link href="/css/account/mylist.css" rel="stylesheet">
    <style>
        /* Custom map size */
        #map-wrapper {
            height: 320px;
        }

        #location-name-list {
            width: 150px;
            height: 300px;
        }
        #map {
            height: 300px;
        }
    </style>
@endsection

@section('main')
    <div id="main-wrapper">
        <div id="menu-wrapper">
            <div class="sidebar left-sidebar">
                <h3 class="sidebar-title">MY LIST</h3>
                <ul style="margin-top: 0px; font-size: 14px;">
                    <li><a href="/list/create">Create a List</a></li>
                    <li class="active"><div class="arrow-right"></div><a>My Own List</a></li>
                    <li><a>Liked & Shared</a></li>
                </ul>
                <h3 class="sidebar-title"><a href="/mylocation">MY LOCATION</a></h3>
            </div>
        </div>
        <div id="list-wrapper">
            @if(count($loc_lists) == 0)
                <div>No lists found.</div>
            @else
                @for($i = 0; $i < count($loc_lists); $i++)
                    <div class="list-info {{ $i == 0 ? 'active' : '' }}" data-list-id="{{ $loc_lists[$i]['id'] }}">
                        <h3>{{ $loc_lists[$i]['name'] }}</h3>
                        <p>{{ $loc_lists[$i]['location_count'] }} Locations, By {{ $loc_lists[$i]['creator']['name'] }}, {{ $loc_lists[$i]['created_at'] }}</p>
                    </div>
                @endfor
            @endif
        </div>
        <div id="content-wrapper">
            <div style="display:{{ count($loc_lists) > 0 ? 'none' : 'block' }};">No lists to display.</div>
            <div id="list-content" style="display:{{ count($loc_lists) > 0 ? 'block' : 'none' }};">
                <div class="clearfix">
                    <div style="float:left;">
                        <div class="section" style="margin-top:0;">
                            <p class="subtitle">Description:</p>
                            <p id="list-description" style="width: 500px; text-align: justify;">
                                @if(count($loc_lists) > 0)
                                    @if($loc_lists[0]['description'])
                                        {{ $loc_lists[0]['description'] }}
                                    @else
                                        No description.
                                    @endif
                                @endif
                            </p>
                            <div id="list-labels">
                                @if(count($loc_lists) > 0)
                                    @foreach($loc_lists[0]['labels'] as $label)
                                        <span class="label"> {{ $label }} </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="section">
                            <p class="subtitle">Reference and Information:</p>
                            <div id="list-references">
                                @if(count($loc_lists) > 0)
                                    <?php $ref_count = 0 ?>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if($loc_lists[0]['reference'.$i])
                                            <p>{{ $loc_lists[0]['reference'.$i] }}</p>
                                            <?php $ref_count++; ?>
                                        @endif
                                    @endfor
                                    @if($ref_count == 0)
                                        <p>None.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="list-menu" style="float:right;">
                        <div>
                            <a>
                                <span class="glyphicon glyphicon-heart icon"></span>
                                <div id="like-count">
                                    @if(count($loc_lists) > 0)
                                        {{ $loc_lists[0]['like_count'] }}
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div>
                            <a>
                                <span class="glyphicon glyphicon-share icon"></span>
                                <div id="share_count">
                                    @if(count($loc_lists) > 0)
                                        {{ $loc_lists[0]['share_count'] }}
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div>
                            <a>
                                <span class="glyphicon glyphicon-comment icon"></span>
                                <div id="comment_count">0</div>
                            </a>
                        </div>
                        <div id="edit-button">
                            <a href="{{ count($loc_lists) > 0 ? '/list/'.$loc_lists[0]['id'].'/edit' : '' }}">
                                <span class="glyphicon glyphicon-edit icon"></span>
                                <div>Edit</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div id="map-wrapper">
                        <div id="location-name-list">
                            <div class="hint">No location added for the list.</div>
                        </div>
                        <div style="overflow:hidden;">
                            <div id="map"></div>
                        </div>
                    </div>
                    <div id="locations"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/sidebar.js"></script>
    <script src="/js/jquery.flexslider.js"></script>
    <script src="/js/photoviewer.js"></script>

    <!-- Google Map APIs and utilities -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBf7oT-h6bVFCbNtrujUWCsoySZMxh7khI&libraries=places"></script>
    <script src="/js/infobubble.js"></script>
    <script src="/js/locations.js"></script>
    <script src="/js/account/mylist.js"></script>
@endsection