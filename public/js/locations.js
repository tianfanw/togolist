/**
 * Global variables
 */
var saved_locations = [];
var search_locations = [];
var batch_operation = true;
var map;

/**
 * Class Location
 * @param options
 * @constructor
 */
function Location(options) {
    for(var key in options) {
        this[key] = options[key];
    }
    this.deleted = false;
    this.createMarker();
}

Location.searchMarkerIcon = {
    url: 'https://maps.gstatic.com/mapfiles/place_api/icons/geocode-71.png',
    size: new google.maps.Size(71, 71),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(17, 34),
    scaledSize: new google.maps.Size(25, 25),
};

Location.savedMarkerIcon = {
    //url: 'https://maps.gstatic.com/mapfiles/place_api/icons/geocode-71.png',
    //url: 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+DQo8c3ZnIHdpZHRoPSI2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBoZWlnaHQ9IjY0Ij4NCjxjaXJjbGUgcj0iMzIiIHN0cm9rZT0iI2ZmZiIgY3k9IjMyIiBjeD0iMzIiIHN0cm9rZS13aWR0aD0iMCIgZmlsbD0iI2RiMjkyOSIvPg0KPC9zdmc+',
    url: 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+DQo8c3ZnIHdpZHRoPSI2NCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBoZWlnaHQ9IjY0Ij4NCjxjaXJjbGUgcj0iMjciIHN0cm9rZT0iIzAwMTU2OSIgY3k9IjMyIiBjeD0iMzIiIHN0cm9rZS13aWR0aD0iMTAiIGZpbGw9IiNmZmYiLz4NCjwvc3ZnPg0K',
    size: new google.maps.Size(64, 64),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(10, 24),
    scaledSize: new google.maps.Size(16, 16),
};

Location.prototype.searchMarkerContent = function() {
    //var $content = $('<div class="place-info-window search-place" data-place-id="' + this.place_id + '">' +
    //        '<h5>' + this.name + '</h5>' +
    //        '<div class="address">' + this.address + '</div>' +
    //        '<div class="bottom-links">' +
    //        '</div>' +
    //    '</div>');
    //if(batch_operation) {
    //    $content.find('.bottom-links').append('<a class="add-to-list">Add to list</a>');
    //} else {
    //    $content.find('.bottom-links').append('<a class="save">Save</a>');
    //}
    //return $content.html();
    if(batch_operation) {
        return '<div class="place-info-window search-place" data-place-id="' + this.place_id + '">' +
            '<h5>' + this.name + '</h5>' +
            '<div class="address">' + this.address + '</div>' +
            '<div class="bottom-links">' +
            '<a class="add-to-list">Add to list</a>' +
            '</div>' +
            '</div>';
    } else {
        return '<div class="place-info-window search-place" data-place-id="' + this.place_id + '">' +
            '<h5>' + this.name + '</h5>' +
            '<div class="address">' + this.address + '</div>' +
            '<div class="bottom-links">' +
            '<a class="save">Save</a>' +
            '</div>' +
            '</div>';
    }
}

Location.prototype.viewMarkerContent = function() {
    return '<div class="place-info-window search-place" style="text-align:center;" data-place-id="' + this.place_id + '">' +
        '<div style="margin-top:10px;">' +
        '<div class="photo-preview"></div>' +
        '<h5>' + this.name + '</h5>' +
        '</div>';
}


Location.prototype.savedMarkerContent = function() {
    var photo_count;
    if(this.photo_viewer) {
        photo_count = this.photo_viewer.photo_count;
    } else {
        photo_count = 0;
    }
    return '<div class="place-info-window search-place" data-place-id="' + this.place_id + '">' +
        '<h5>' + this.name + '</h5>' +
        '<div class="address">' + this.address + '</div>' +
        '<div class="bottom-links">' +
        '<a class="photos"><span class="photo-count">'+ photo_count + '</span>' + ' Photos</a>' +
        ' | <a class="upload-photos">Upload Photos</a>' +
        ' | <a class="delete">Delete</a>' +
        '</div>' +
        '</div>';
}

Location.prototype.createMarker = function(type) {
    if(typeof type === "undefined") {
        type = this.type;
    } else {
        this.type = type;
    }
    var icon = (type == 'search') ? Location.searchMarkerIcon : Location.savedMarkerIcon;
    var content = (type == 'search') ? this.searchMarkerContent() :
        (type == 'saved') ? this.savedMarkerContent() : this.viewMarkerContent();

    // Create marker
    var marker = new google.maps.Marker({
        map: map,
        icon: icon,
        title: this.name,
        position: {lat: this.lat, lng: this.lng},
    });

    var min_height = (type == 'view') ? 10 : 100;
    // Create info window
    var infowindow = new InfoBubble({
        map: map,
        content: content,
        shadowStyle: 1,
        padding: 10,
        backgroundColor: '#001569',
        borderRadius: 10,
        arrowSize: 10,
        borderWidth: 1,
        borderColor: '#fff',
        disableAutoPan: true,
        //hideCloseButton: true,
        arrowPosition: 30,
        backgroundClassName: 'transparent',
        arrowStyle: 0,
        maxHeight: 1000,
        minHeight: min_height,
        minWidth: 200,
        maxWidth: 200,
        disableAnimation: true,
        flash: true,
        is_open: false,
    });

    marker.infowindow = infowindow;

    // Add mouse events on marker and info window
    var mouseover_listener = google.maps.event.addListener(marker, 'mouseover', function() {
        infowindow.open(map, this);
        infowindow.is_open = true;
    });

    var mouseout_listener = google.maps.event.addListener(marker, 'mouseout', function() {
        if(infowindow.flash) {
            infowindow.close();
            infowindow.is_open = false;
        }
    });

    var click_listener = google.maps.event.addListener(marker, 'click', function() {
        if(infowindow.is_open) {
            if(infowindow.flash) {
                infowindow.flash = false;
                infowindow.open(map, this);
            } else {
                infowindow.close();
                infowindow.flash = true;
                infowindow.is_open = false;
            }
        } else {
            infowindow.flash = false;
            infowindow.is_open = true;
            infowindow.open(map, this);
        }
    });

    var close_infowindow_listener = google.maps.event.addListener(infowindow,'closeclick',function(){
        this.flash = true;
        this.is_open = false;
    });

    // Bind button click events to links in the info window
    var location = this;
    var content_listener = google.maps.event.addListener(infowindow, 'domready', function() {
        var content_div = $(infowindow.content_);
        if(type == 'search') {
            //content_div.find('a.add-to-list')[0].addEventListener("click", addLocationToList);
            if(batch_operation) {
                content_div.find('a.add-to-list')[0].addEventListener("click", location.addToList.bind(location), false);
            } else {
                content_div.find('a.save')[0].addEventListener("click", location.save.bind(location), false);
            }
        } else if(type == 'saved') {
            location.updatePhotoCount();
            //content_div.find('a.delete')[0].addEventListener("click", removeLocationFromList);
            content_div.find('a.delete')[0].addEventListener("click", location.removeFromList.bind(location), false);
            content_div.find('a.upload-photos')[0].addEventListener("click", function() {
                location.photo_viewer.display(true);
            }, false);
            content_div.find('a.photos')[0].addEventListener("click", function() {
                location.photo_viewer.display();
            }, false);
        } else if(type == 'view') {
            location.updatePhotoPreview();
            content_div.find('.photo-preview')[0].addEventListener("click", function() {
                location.photo_viewer.display();
            }, false);
        }
    });

    // Keep track of listeners
    marker.listeners = [
        mouseover_listener,
        mouseout_listener,
        click_listener,
        close_infowindow_listener,
        content_listener
    ];

    this.marker = marker;
}

Location.prototype.clearMarker = function() {
    this.marker.setMap(null);
    this.marker.infowindow.close();
    this.marker.infowindow.onRemove();
    this.marker.infowindow = {};
    this.marker.listeners.forEach(function(listener) {
        listener.remove();
    });
    this.marker = {};
}

Location.prototype.createNameDiv = function() {
    this.name_div = $(
        '<tr class="location-name" data-place-id="' + this.place_id + '">' +
            '<td>' + this.name + '</td>' +
        '</tr>');
    if(this.editable) {
        this.name_div.append('<td><span class="glyphicon glyphicon-remove action-icon delete"></span></td>');
        this.name_div.on('click', '.delete', this.removeFromList.bind(this));
    } else {
        this.name_div.append('<td><span class="glyphicon glyphicon-plus action-icon save"></span></td>');
        var location = this;
        this.name_div.on('click', '.save', function() {
            $.ajax({
                type: 'POST',
                url: '/location',
                data: {'id' : location.id},
                success: function(data) {
                    console.log(data);
                    if(data.flash_message) {
                        flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });
    }
    $('#location-name-list').find('tbody').append(this.name_div);

    var location = this;
    this.name_div.on('click', function() {
        map.setCenter({
            lat : location.lat,
            lng : location.lng
        });
        location.marker.infowindow.open(map, location.marker);
        location.marker.infowindow.is_open = true;
        location.marker.infowindow.flash = false;
        $('#location-name-list').find('tr.active').removeClass('active');
        location.name_div.addClass('active');
    });
}

Location.prototype.createLocationDiv = function() {
    this.location_div = $('<div></div>');
    if(this.editable && batch_operation) {
        this.location_div.append(
            '<input type="hidden" name="location-name-' + this.place_id + '" value="' + this.name + '" >' +
            '<input type="hidden" name="location-address-' + this.place_id + '" value="' + this.address + '" >' +
            '<input type="hidden" name="location-lat-' + this.place_id + '" value="' + this.lat + '" >' +
            '<input type="hidden" name="location-lng-' + this.place_id + '" value="' + this.lng + '" >' +
            '<input type="hidden" class="delete" name="location-delete-' + this.place_id + '" value="0">');
    }
    this.location_div.appendTo('#locations');
}

Location.prototype.save = function() {
    var data = {};
    if(this.id) {
        data.id = this.id;
    } else {
        data.place_id = this.place_id;
        data.name = this.name;
        data.address = this.address;
        data.lat = this.lat;
        data.lng = this.lng;
    }
    var location = this;
    $.ajax({
        type: 'POST',
        url: '/location',
        data: data,
        success: function(res) {
            if(res.flash_message) {
                flash(res.flash_message.message, res.flash_message.is_important, res.flash_message.error);
            }
            if(!res.error) {
                location.id = res.id;
                location.is_old = true;
                location.addToList();
            }
        },
        error: function(xhr) {
            console.log(xhr);
        }
    });
}

Location.prototype.addToList = function() {
    // Remove location from search location array (if possible)
    // and put into saved location array
    var i;
    for(i = 0; i < search_locations.length; i++) {
        if(search_locations[i].place_id == this.place_id) break;
    }
    if(i < search_locations.length) {
        search_locations.splice(i, 1)[0];
        // Change location markers and display
        this.clearMarker();
        this.createMarker('saved');
        google.maps.event.trigger(this.marker, 'click');
    }

    for(i = 0; i < saved_locations.length; i++) {
        if(saved_locations[i].place_id == this.place_id) break;
    }
    if(i < saved_locations.length) {
        // location to add already in the saved location array
        this.location_div = saved_locations[i].location_div;
        var $delete_input = this.location_div.find('input.delete');
        if($delete_input.length > 0) $delete_input[0].val(0);
        this.location_div.attr('display', 'block');
        this.photo_viewer = saved_locations[i].photo_viewer;
        this.photo_viewer.location = this;
        this.is_old = true;
        saved_locations.splice(i, 1);
    } else {
        // ajax request to get photos and create location div to contain form and photo viewer
        this.createLocationDiv();
        if(!this.photos) {
            var query = {};
            if (this.id) query['location_id'] = this.id;
            else query['place_id'] = this.place_id;
            if (this.user_id) query['user_id'] = this.user_id;
            var location = this;
            $.ajax({
                type: 'GET',
                url: '/photo',
                data: query,
                success: function (photos) {
                    location.photos = photos;
                    location.photo_viewer = new PhotoViewer(location, photos);
                },
                error: function (xhr) {
                    location.photos = [];
                    location.photo_viewer = new PhotoViewer(location, []);
                }
            });
        } else {
            this.photo_viewer = new PhotoViewer(this, this.photos);
        }
    }
    saved_locations.push(this);
    // Add name div to location name list
    $('#location-name-list').find('.hint').hide();
    this.createNameDiv();
}

Location.prototype.clear = function() {
    // Clear location GUI
    this.clearMarker();
    this.name_div.remove();
    if(saved_locations.length == 0) {
        $('#location-name-list').find('.hint').show();
    }

    if(this.is_old && batch_operation) {
        // soft delete old locations
        this.deleted = true;
        this.location_div.find('input.delete').val(1);
        this.location_div.attr('display', 'none');
    } else {
        var i;
        for(i = 0; i < saved_locations.length; i++) {
            if(saved_locations[i].place_id == this.place_id) break;
        }
        if( i < saved_locations.length ) {
            saved_locations.splice(i, 1)[0];
        }
        this.location_div.remove();
        delete this.photo_viewer;
    }
}

Location.prototype.delete = function() {
    if(this.id) {
        var location = this;
        $.ajax({
            type: 'DELETE',
            url: '/location/' + this.id,
            success: function(res) {
                if(res.flash_message) {
                    flash(res.flash_message.message, res.flash_message.is_important, res.flash_message.error);
                }
                if(!res.error) {
                    location.clear();
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    } else {
        this.clear();
    }
}

Location.prototype.removeFromList = function() {
    // Remove from saved location array
    var i;
    for(i = 0; i < saved_locations.length; i++) {
        if(saved_locations[i].place_id == this.place_id) break;
    }
    if(i == saved_locations.length) return;

    // Pop up confirmation window
    var subject = 'Are you sure to remove the location "'+ this.name + '" ?';
    var location = this;
    confirmDeletion($('#delete-confirmation'), subject, function() {
        if(!batch_operation) {
            location.delete();
        } else {
            location.clear();
        }
    });
}

Location.prototype.updatePhotoCount = function(count) {
    if(typeof count === "undefined") {
        if(this.photo_viewer) count = this.photo_viewer.photo_count;
        else count = 0;
    }
    var counter_div = this.marker.infowindow.content_.getElementsByClassName('photo-count');
    if (counter_div.length > 0) {
        counter_div[0].innerHTML = count;
    }
}

Location.prototype.updatePhotoPreview = function(src) {
    if(typeof src === "undefined") {
        if(this.photo_viewer && this.photo_viewer.photos.length > 0) {
            src = photoURL(this.photo_viewer.photos[0].file_dir);
        }
        else return;
    }
    var preview_div = this.marker.infowindow.content_.getElementsByClassName('photo-preview');
    if (preview_div.length > 0) {
        $(preview_div).prepend('<img src="' + src + '" height="100px" width="150px" style="cursor:pointer;" >');
        //preview_div[0].src = src;
        //preview_div[0].height = 100;
        //preview_div[0].width = 150;
        //preview_div[0].style.cursor = 'pointer';
    }
}

/**
 * Check if the place is already in the location array
 * @param place
 * @param locations
 * @param callback
 */
function isLocationExisted(place, locations, callback) {
    if(locations.length == 0) {
        if(typeof callback === "function") {
            callback(null);
        }
        return;
    } else {
        for (var i = 0; i < locations.length; i++) {
            if (locations[i].place_id == place.place_id && !locations[i].deleted) {
                if(typeof callback === "function") {
                    callback(locations[i]);
                }
                return;
            } else if (i == locations.length - 1) {
                if(typeof callback === "function") {
                    callback(null);
                }
                return;
            }
        }
    }
}

/**
 * Initialize Google map
 * @param enable_search
 */
function initMap(enable_search) {
    var mapOptions = {
        center: new google.maps.LatLng(35.0072, 15.3551),
        zoom: 2,
        mapTypeControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    if(enable_search) {
        // Create the search box and link it to the UI element.
        var input = document.getElementById('map-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function () {
            places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            search_locations.forEach(function (location) {
                location.clearMarker();
            });
            search_locations = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();

            var first_location;
            for(var i = 0; i < places.length; i++) {
                var place = places[i];
                console.log(place);
                var new_location;
                // Check if the place is already saved.
                isLocationExisted(place, saved_locations, function(loc) {
                    if(!loc) {
                        // Create a marker for the place if it's not saved yet.
                        new_location = new Location({
                            name: place.name,
                            address: place.formatted_address,
                            place_id: place.place_id,
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                            type: 'search',
                            editable: true,
                            is_old: false,
                        });
                        search_locations.push(new_location);
                    } else {
                        new_location = loc;
                    }
                })

                // Keep note of the first location
                if (i == 0) {
                    first_location = new_location;
                }

                // Extends map bound
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }

            }; // End of for loop over places

            map.fitBounds(bounds);
            if (places.length == 1) {
                // Zoom out if only one place is found
                google.maps.event.addListenerOnce(map, 'bounds_changed', function (event) {
                    if (this.getZoom() > 16) {
                        this.setZoom(16);
                    }
                });
            }

            google.maps.event.addListenerOnce(map, 'idle', function() {
                // Open the info window for the first place
                if(first_location && !first_location.marker.infowindow.is_open) {
                    google.maps.event.trigger(first_location.marker, 'click');
                }
            });
        });
    }
}