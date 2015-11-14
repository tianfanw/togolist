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

            var url_seg = window.location.href.split('/');
            var list_id = url_seg[url_seg.length-2];
            $.ajax({
                type: 'GET',
                url: '/location',
                data: {'list_id' : list_id },
                success: function(locations) {
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