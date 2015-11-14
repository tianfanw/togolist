$(document).ready(function() {
    initMap(false);

    var $lists = $('#list-wrapper').find('.list-info');
    if($lists.length > 0) {
        var list_id = $lists.first().attr('data-list-id');
        getLocations(list_id);
    }

    $('#list-wrapper').on("click", '.list-info', function() {
        var $list_info_div = $(this);
        var list_id = $(this).attr('data-list-id');
        $.ajax({
            type: 'GET',
            url: '/list/' + list_id,
            success: function(data) {
                if(data.error) {
                    console.log(data.message);
                } else {
                    if(data.description) {
                        $('#list-description').html(data.description);
                    } else {
                        $('#list-description').html('No description.');
                    }
                    var labels = '';
                    for(var i = 0; i < data.labels.length; i++) {
                        labels += '<span class="label">' + data.labels[i] + '</span>';
                    }
                    $('#list-labels').html(labels);
                    var references = '';
                    for(var i = 0; i < 5; i++) {
                        if(data['reference' + i]) {
                            references += '<p>' + data['reference' + i] + '</p>';
                        }
                    }
                    if(references) {
                        $('#list-references').html(references);
                    } else {
                        $('#list-references').html('None.');
                    }
                    $('#like-count').html(data.like_count);
                    $('#share-count').html(data.share_count);
                    $('#edit-button').find('a').attr('href', '/list/' + data.id + '/edit');
                    $('#list-wrapper').find('div.active').removeClass('active');
                    $list_info_div.addClass('active');
                    getLocations(data.id);
                }
            },
            error: function(xhr) {
                console.log('failed to retrieve list.');
                console.log(xhr);
            }
        });
    });
});

function getLocations(list_id) {
    for(var i = 0; i < saved_locations.length; i++) {
        saved_locations[i].clear();
    }
    saved_locations = [];
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

            google.maps.event.addListenerOnce(map, 'idle', function() {
                for(var i = 0; i < saved_locations.length; i++) {
                    google.maps.event.trigger(saved_locations[i].marker, 'click');
                }
            });
        },
        error: function(xhr) {
            console.log("Failed to retrieve locations");
            console.log(xhr);
        }
    });
}