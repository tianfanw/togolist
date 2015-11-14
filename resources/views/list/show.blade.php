@extends('list.base')

@section('list-css')
    <link rel="stylesheet" href="/css/flexslider.css">
    <link href="/css/map.css" rel="stylesheet">
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

@section('content')
    <div class="clearfix">
        <div style="float:left;">
            <div>
                <h3 id="list-name">{{ $loc_list['name'] }}</h3>
                <span class="location-count">{{ $loc_list['location_count'] }}</span> Locations,
                by <span id="creator-name">{{ $loc_list['creator']['name'] }}</span>, <span id="created_at">{{ $loc_list['created_at'] }}</span> </div>
            <div class="section">
                <p class="subtitle">Description:</p>
                <p id="list-description" style="width: 550px; text-align: justify;">
                    @if($loc_list['description'])
                        {{ $loc_list['description'] }}
                    @else
                        No description.
                    @endif
                <div id="list-labels">
                @foreach($loc_list['labels'] as $label)
                    <span class="label"> {{ $label }} </span>
                @endforeach
                </div>
            </div>
            <div class="section">
                <p class="subtitle">Reference and Information:</p>
                <div id="list-references">
                <?php $ref_count = 0 ?>
                @for ($i = 1; $i <= 5; $i++)
                    @if($loc_list['reference'.$i])
                        <p>{{ $loc_list['reference'.$i] }}</p>
                        <?php $ref_count++; ?>
                    @endif
                @endfor
                @if($ref_count == 0)
                    <p>None.</p>
                @endif
                </div>
            </div>
        </div>
        <div class="list-menu" style="float:right;">
            <div>
                <a><span class="glyphicon glyphicon-heart icon"></span><div id="like-count">{{ $loc_list['like_count'] }}</div></a>
            </div>
            <div>
                <a><span class="glyphicon glyphicon-share icon"></span><div id="share_count">{{ $loc_list['share_count'] }}</div></a>
            </div>
            <div>
                <a><span class="glyphicon glyphicon-comment icon"></span><div id="comment_count">0</div></a>
            </div>
            @if( Auth::check() && Auth::user()->id == $loc_list['creator']['id'] )
                <div>
                    <a href="/list/{{ $loc_list['id'] }}/edit"><span class="glyphicon glyphicon-edit icon"></span><div>Edit</div></a>
                </div>
            @endif
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
@endsection

@section('list-javascript')
    <script src="/js/jquery.flexslider.js"></script>
    <script src="/js/photoviewer.js"></script>

    <!-- Google Map APIs and utilities -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBf7oT-h6bVFCbNtrujUWCsoySZMxh7khI&libraries=places"></script>
    <script src="/js/infobubble.js"></script>
    <script src="/js/locations.js"></script>

    <script>
        $(document).ready(function() {
            initMap(false);
            var url_seg = window.location.href.split('/');
            var list_id = url_seg[url_seg.length-1];
            $.ajax({
                type: 'GET',
                url: '/location',
                data: {'list_id' : list_id },
                success: function(locations) {
                    var bounds = new google.maps.LatLngBounds();
                    for(var i = 0; i < locations.length; i++) {
                        locations[i].editable = false;
                        locations[i].type = 'view';
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

                    // google.maps.event.addListenerOnce(map, 'idle', function() {
                    //     for(var i = 0; i < saved_locations.length; i++) {
                    //         google.maps.event.trigger(saved_locations[i].marker, 'click');
                    //     }
                    // });
                },
                error: function(xhr) {
                    console.log("Failed to retrieve locations");
                    console.log(xhr);
                }
            });
        });
    </script>
@endsection