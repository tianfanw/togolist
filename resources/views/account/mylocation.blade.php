@extends('base')

@section('css')
    <link href="/css/fixed.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/flexslider.css">
    <link href="/css/map.css" rel="stylesheet">
    <link href="/css/account/mylocation.css" rel="stylesheet">
    <style>
        /* Custom map size */
        #map-wrapper {
            height: 520px;
        }

        #location-name-list {
            width: 200px;
            height: 500px;
        }
        #map {
            height: 500px;
        }
    </style>
@endsection

@section('main')
    <div id="main-wrapper">
        <div id="menu-wrapper">
            <div class="sidebar left-sidebar">
                <h3 class="sidebar-title"><a href="/mylist">MY LIST</a></h3>
                <h3 class="sidebar-title" style="margin-top: 10px;">MY LOCATION</h3>
                <ul style="margin-top: 0px; font-size: 14px;">
                    <li><a>Add a Location</a></li>
                    <li class="active"><div class="arrow-right"></div><a>All Saved Locations</a></li>
                </ul>
            </div>
        </div>
        <div id="content-wrapper">
            <div id="map-wrapper">
                <div id="location-name-list">
                    <div class="hint">Search to add locations here...</div>
                    <table><tbody></tbody></table>
                </div>
                <div style="overflow:hidden;">
                    <input id="map-input" class="controls" type="text" placeholder="Search a location">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div id="locations">
        </div>
        @include('partials.delete-dialog')
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

    <script>
        batch_operation = false;
        $(document).ready(function() {
            initMap(true);
            $.ajax({
                type: 'GET',
                url: '/location',
                success: function(locations) {
                    $('.location-count').html(locations.length);
                    var bounds = new google.maps.LatLngBounds();
                    for(var i = 0; i < locations.length; i++) {
                        locations[i].editable = true;
                        locations[i].type = 'saved';
                        locations[i].is_old = true;
                        var location = new Location(locations[i]);
                        location.addToList();
                        var latlng = new google.maps.LatLng(location.lat, location.lng);
                        bounds.extend(latlng);
                    }
                    map.fitBounds(bounds);
                    if (locations.length == 1) {
                        // Zoom out if only one place present
                        google.maps.event.addListenerOnce(map, 'bounds_changed', function (event) {
                            if (this.getZoom() > 16) {
                                this.setZoom(16);
                            }
                        });
                    }

//                    google.maps.event.addListenerOnce(map, 'idle', function() {
//                        for(var i = 0; i < saved_locations.length; i++) {
//                            google.maps.event.trigger(saved_locations[i].marker, 'click');
//                        }
//                    });
                },
                error: function(xhr) {
                    console.log("Failed to retrieve locations");
                    console.log(xhr);
                }
            });
        });
    </script>
@endsection