PhotoViewer.max_photo = 10;
PhotoViewer.default_photo_url = '/image/default-photo.png';

function photoURL(file_dir) {
    var url = file_dir;
    if(url[0] != '/') url = '/' + url;
    return url;
}

function PhotoViewer(location, photos) {
    var editable = location.editable || false;
    this.location = location;
    this.id_counter = 0;
    this.deleted_photo_ids = [];
    var content =
            '<div class="photo" style="margin-bottom: 20px;">' +
                '<img src="/image/default-photo.png" style="height:400px;"/>' +
            '</div>' +
            '<div class="flexslider">' +
                '<ul class="slides">' +
                '</ul>' +
            '</div>';
    this.photo_div = $(
        '<div class="photo-viewer">' +
            '<div class="popup-content" style="width: 900px;">' +
                '<span class="glyphicon glyphicon-remove-circle popup-close"></span>' +
            '</div>' +
        '</div>');
    if(editable) {
        if(batch_operation) {
            this.photo_div.find('.popup-content').append(content +
                '<input type="hidden" class="photos-deleted" name="photos-deleted-' + this.location.place_id + '" value="">');
        } else {
            this.photo_div.find('.popup-content').append('<form class="photo-upload-form" method="POST" action="/photo" enctype="multipart/form-data"></form>');
            this.photo_div.find('form').append('<input type="hidden" name="location_id" value="' + this.location.id + '">' + content);
        }
    } else {
        this.photo_div.find('.popup-content').append(content);
    }

    this.photo_img = this.photo_div.find('.photo img');
    this.slider_div = this.photo_div.find('.flexslider');
    this.slides_div = this.photo_div.find('.slides');

    this.photo_count = photos.length;
    if(editable) {
        this.location.updatePhotoCount(this.photo_count);
    } else {
        this.photos = photos;
        if(photos.length > 0) {
            this.location.updatePhotoPreview(photoURL(photos[0].file_dir));
        }
    }
    if(photos.length == 0) {
        // No photo to display
        this.photo_img.attr("src", PhotoViewer.default_photo_url);
        this.cur_photo_index = -1;
    } else {
        // append photos to slider
        for(var i = 0; i < photos.length; i++) {
            this.slides_div.append(thumbnail(photoURL(photos[i].file_dir), photos[i].id));
        }
        this.photo_img.attr("src", photoURL(photos[0].file_dir));
        this.cur_photo_index = 0;
    }

    if(editable) {
        var name = batch_operation ? 'photo-' + this.id_counter.toString() + '-' + this.location.place_id : 'photo';
        this.slides_div.append(addPhotoButton(name));
        this.id_counter++;
    }
    this.photo_div.appendTo(this.location.location_div);

    var photo_viewer = this;
    this.slider_div.flexslider({
        slideshow: false,
        animation: 'slide',
        animationLoop: false,
        itemWidth: 150,
        itemMargin: 10,
        init: function(slider) {
            photo_viewer.slider = slider;
        },
        start: function(slider) {
            photo_viewer.photo_div.addClass('popup');
            photo_viewer.photo_div.css('left', 0);
        }
    });

    // Event Listeners
    this.slider_div.on('click', 'li.photo-thumbnail', function() {
        photo_viewer.photo_img.attr("src", $(this).find('img').attr('src'));
        photo_viewer.cur_photo_index = $(this).index();
    });

    if(editable) {
        // Add photo events
        this.slider_div.on('click', 'img.add-photo-button', function () {
            $(this).parent().find('input[type=file]').click();
        });
        this.slider_div.on('change', 'input[type=file]', function () {
            var $photo_item = $(this).parent();
            if(!batch_operation) {
                var form_data = new FormData(photo_viewer.photo_div.find('.photo-upload-form')[0]);
                $.ajax({
                    type: 'POST',
                    url: '/photo',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if(data.flash_message) {
                            flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                        }
                        if(!data.error) {
                            $photo_item.attr("class", "photo-thumbnail");
                            $photo_item.find('input').remove();
                            var $img = $photo_item.find('img');
                            $img.attr("class", "");
                            $img.attr("src", data.url);
                            $img.attr("data-photo-id", data.id);

                            photo_viewer.photo_img.attr("src", data.url);
                            photo_viewer.cur_photo_index = $photo_item.index();

                            photo_viewer.photo_count++;
                            photo_viewer.location.updatePhotoCount(photo_viewer.photo_count);
                            if(photo_viewer.photo_count < PhotoViewer.max_photo) {
                                photo_viewer.slider.addSlide(addPhotoButton('photo'));
                                photo_viewer.id_counter++;
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('Failed to upload photo.');
                        console.log(xhr);
                    }
                });
            } else {
                var reader = new FileReader();
                reader.onload = function (e) {

                    $photo_item.attr("class", "photo-thumbnail");
                    var $img = $photo_item.find('img');
                    $img.attr("class", "");
                    $img.attr("src", e.target.result);

                    photo_viewer.photo_img.attr("src", e.target.result);
                    photo_viewer.cur_photo_index = $photo_item.index();

                    photo_viewer.photo_count++;
                    photo_viewer.location.updatePhotoCount(photo_viewer.photo_count);
                    if(photo_viewer.photo_count < PhotoViewer.max_photo) {
                        var name = 'photo-' + photo_viewer.id_counter.toString() + '-' + photo_viewer.location.place_id;
                        photo_viewer.slider.addSlide(addPhotoButton(name));
                        photo_viewer.id_counter++;
                    }
                };
                // read the image file as a data URL.
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Delete photo events
        this.slider_div.on('mouseover', 'li.photo-thumbnail', function() {
            $(this).find('.delete').show();
        });
        this.slider_div.on('mouseout', 'li.photo-thumbnail', function() {
            $(this).find('.delete').hide();
        });
        this.slider_div.on('click', 'span.delete', function() {
            var $photo_item = $(this).parent();
            var subject = 'Are you sure to delete the photo?';
            confirmDeletion($('#delete-confirmation'), subject, function() {
                // Append photo id to deleted photo array if exists
                var photo_id = $photo_item.find('img').attr('data-photo-id');
                if(photo_id) {
                    if(!batch_operation) {
                        $.ajax({
                            type: 'DELETE',
                            url: '/photo/' + photo_id,
                            success: function(data) {
                                if(data.flash_message) {
                                    flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                                }
                                if(!data.error) {
                                    // Update slider and photo count
                                    var del_photo_index = $photo_item.index();
                                    photo_viewer.slider.removeSlide($photo_item);
                                    photo_viewer.photo_count--;
                                    photo_viewer.location.updatePhotoCount(photo_viewer.photo_count);

                                    // Adjust photo viewer display accordingly
                                    if(del_photo_index == photo_viewer.cur_photo_index) {
                                        if(photo_viewer.photo_count == 0) {
                                            photo_viewer.photo_img.attr('src', PhotoViewer.default_photo_url);
                                            photo_viewer.cur_photo_index = -1;
                                        } else{
                                            var disp_photo_index = (del_photo_index == photo_viewer.photo_count ) ? del_photo_index-1 : del_photo_index;
                                            photo_viewer.photo_img.attr("src",
                                                photo_viewer.slides_div.children().eq(disp_photo_index).find('img').attr("src"));
                                            photo_viewer.cur_photo_index = disp_photo_index;
                                        }
                                    }
                                    if( (photo_viewer.photo_count + 1) == PhotoViewer.max_photo) {
                                        photo_viewer.slider.addSlide(addPhotoButton('photo'));
                                        photo_viewer.id_counter++;
                                    }
                                }
                            },
                            error: function(xhr) {
                                console.log('Failed to delete photo.');
                                console.log(xhr);
                            }
                        });
                    } else {
                        photo_viewer.deleted_photo_ids.push(photo_id);
                        photo_viewer.photo_div.find('input.photos-deleted').val(photo_viewer.deleted_photo_ids.join(','));

                        // Update slider and photo count
                        var del_photo_index = $photo_item.index();
                        photo_viewer.slider.removeSlide($photo_item);
                        photo_viewer.photo_count--;
                        photo_viewer.location.updatePhotoCount(photo_viewer.photo_count);

                        // Adjust photo viewer display accordingly
                        if(del_photo_index == photo_viewer.cur_photo_index) {
                            if(photo_viewer.photo_count == 0) {
                                photo_viewer.photo_img.attr('src', PhotoViewer.default_photo_url);
                                photo_viewer.cur_photo_index = -1;
                            } else{
                                var disp_photo_index = (del_photo_index == photo_viewer.photo_count ) ? del_photo_index-1 : del_photo_index;
                                photo_viewer.photo_img.attr("src",
                                    photo_viewer.slides_div.children().eq(disp_photo_index).find('img').attr("src"));
                                photo_viewer.cur_photo_index = disp_photo_index;
                            }
                        }
                        if( (photo_viewer.photo_count + 1) == PhotoViewer.max_photo) {
                            var name = 'photo-' + photo_viewer.id_counter.toString() + '-' + photo_viewer.locaiton.place_id;
                            photo_viewer.slider.addSlide(addPhotoButton(name));
                            photo_viewer.id_counter++;
                        }
                    }
                }
            });
        });
    }
}

PhotoViewer.prototype.display = function(clickButton) {
    if(typeof clickButton === "undefined") clickButton = false;
    this.photo_div.css('visibility', 'visible');
    this.photo_div.css('display', 'block');
    this.photo_div.fadeTo('0.2s', 1);
    if(clickButton) {
        this.slider.flexAnimate(this.slider.last);
        this.slider_div.find('img.add-photo-button').click();
    }
};

//$(window).load(function() {
//    $('.flexslider').flexslider({
//        slideshow: false,
//        animation: 'slide',
//        animationLoop: false,
//        itemWidth: 150,
//        itemMargin: 10,
//        init: function(s) {
//            slider = s;
//        }
//    });
//});
//
//$(document).ready(function() {
//    $('.flexslider').on('click', 'li.photo-thumbnail', function() {
//        var img_src = $(this).find('img').attr("src");
//        var $img = $(this).parent().parent().parent().prev().find('img');
//        $img.attr("src", img_src);
//    });
//
//    // Not sure if this will work on dynamically added elements
//    $('.flexslider').on('click', 'img.add-photo-button', function() {
//        $(this).parent().find('input[type=file]').click();
//    });
//    $('.flexslider').on('change', 'input[type=file]', function() {
//        var reader = new FileReader();
//        var $photo_item = $(this).parent();
//        reader.onload = function (e) {
//            $photo_item.attr("class", "photo-thumbnail");
//            var $img = $photo_item.find('img');
//            $img.attr("src", e.target.result);
//            $img.attr("class", "");
//            slider.addSlide(addPhotoButton(id_counter++));
//            //$add_photo_button.before(createPhotoItem(e.target.result));
//            // get loaded data and render thumbnail.
//            //document.getElementById("image").src = e.target.result;
//        };
//
//        // read the image file as a data URL.
//        reader.readAsDataURL(this.files[0]);
//    })
//});
//

function thumbnail(src, id) {
    var $thumbnail = $('<li class="photo-thumbnail">' +
            '<img src="' + src + '"/>' +
            '<span class="glyphicon glyphicon-trash delete" style="display:none;"></span>' +
        '</li>');
    if(id) $thumbnail.find('img').attr('data-photo-id', id);
    return $thumbnail;
}

function addPhotoButton(name) {
    return $('<li class="add-photo">' +
                '<img class="add-photo-button" src="/image/add-photo-button.png" />' +
                '<span class="glyphicon glyphicon-trash delete" style="display:none;"></span>' +
                '<input type="file" name="' + name + '" style="display:none;" />' +
            '</li>');
}