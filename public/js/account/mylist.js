var lists = {};

$(document).ready(function() {

    initMap(false);

    var $lists = $('#list-wrapper').find('.list-info');
    if($lists.length > 0) {
        var list_id = $lists.first().attr('data-list-id');
        retrieveList(list_id);
    }

    $('#list-wrapper').on("click", '.list-info', function() {
        var list_id = $(this).attr('data-list-id');
        if(lists[list_id]) {
            displayList(list_id);
        } else {
            retrieveList(list_id);
        }
    });

    $('#list-wrapper').on("click", '.delete', function() {
        var $list_info = $(this).parent().parent();
        var list_id = $list_info.attr('data-list-id');
        var list_name = $list_info.find('.list-name').html();
        var subject = 'Are you sure to delete the list "'+ list_name + '" ?';
        confirmDeletion($('#delete-confirmation'), subject, function() {
            $.ajax({
                type: 'DELETE',
                url: '/list/' + list_id,
                success: function(data) {
                    if(!data.error) {

                        var is_active = $list_info.hasClass('active');
                        $list_info.remove();
                        if(is_active) {
                            if(lists[list_id]) {
                                saved_locations = [];
                                for (var i = 0; i < lists[list_id].locations.length; i++) {
                                    lists[list_id].locations[i].clear();
                                }
                                delete lists[list_id];
                            }
                            var $next_active_list = $('#list-wrapper').find('.list-info').first();
                            if($next_active_list.length > 0) {
                                var next_list_id = $next_active_list.attr('data-list-id');
                                if(lists[next_list_id]) {
                                    displayList(next_list_id);
                                } else {
                                    retrieveList(next_list_id);
                                }
                            }
                        }
                        else {
                            if(lists[list_id]) {
                                delete lists[list_id];
                            }
                        }
                    }
                    if(data.flash_message) {
                        flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });
    });
});

function retrieveList(list_id) {
    $.ajax({
        type: 'GET',
        url: '/list/' + list_id,
        success: function (data) {
            if (data.error) {
                console.log(data.message);
            } else {
                lists[list_id] = data;
                lists[list_id].locations = [];
                $.ajax({
                    type: 'GET',
                    url: '/location',
                    data: {'list_id': list_id},
                    success: function (locations) {
                        if(locations.length == 0) {
                            displayList(list_id);
                        } else {
                            var i;
                            for (i = 0; i < locations.length; i++) {
                                locations[i].editable = false;
                                locations[i].type = 'view';
                                locations[i].is_old = true;
                                var location = new Location(locations[i]);
                                lists[list_id].locations.push(location);
                                if (i == locations.length - 1) {
                                    displayList(list_id);
                                }
                            }
                        }
                    },
                    error: function (xhr) {
                        console.log("Failed to retrieve locations");
                        console.log(xhr);
                    }
                });
            }
        },
        error: function (xhr) {
            console.log('failed to retrieve list.');
            console.log(xhr);
        }
    });
}

function displayList(list_id) {
    var list = lists[list_id];
    if (list.description) {
        $('#list-description').html(list.description);
    } else {
        $('#list-description').html('No description.');
    }
    var labels = '';
    for (var i = 0; i < list.labels.length; i++) {
        labels += '<span class="label">' + list.labels[i] + '</span>';
    }
    $('#list-labels').html(labels);
    var references = '';
    for (var i = 0; i < 5; i++) {
        if (list['reference' + i]) {
            references += '<p>' + list['reference' + i] + '</p>';
        }
    }
    if (references) {
        $('#list-references').html(references);
    } else {
        $('#list-references').html('None.');
    }
    $('#like-count').html(list.like_count);
    $('#share-count').html(list.share_count);
    $('#edit-button').find('a').attr('href', '/list/' + list.id + '/edit');
    $('#list-wrapper').find('.active').removeClass('active');
    $('#list-wrapper').find('.list-info[data-list-id="' + list_id + '"]').addClass('active');

    for(var i = 0; i < saved_locations.length; i++) {
        saved_locations[i].clear();
    }
    saved_locations = [];
    $('#location-name-list').find('.hint').show();
    var bounds = new google.maps.LatLngBounds();
    for(var i = 0; i < list.locations.length; i++) {
        var location = list.locations[i];
        location.createMarker();
        location.addToList();
        var latlng = new google.maps.LatLng(location.lat, location.lng);
        bounds.extend(latlng);
    }
    map.fitBounds(bounds);
    if (list.locations.length == 1) {
        // Zoom out if only one place present
        google.maps.event.addListenerOnce(map, 'bounds_changed', function (event) {
            if (this.getZoom() > 16) {
                this.setZoom(16);
            }
        });
    }
}