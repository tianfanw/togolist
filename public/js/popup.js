$( document ).ready(function() {

    // Click on link to show popup window
    $(document).on('click', '.static-popup-link', function(e) {
        e.preventDefault();
        var $popup_id = $(this).attr('data-popup-id');
        $('.popup:visible').fadeTo('0.2s', 0, function() {
            $(this).css('visibility', 'hidden');
            $(this).css('display', 'none');
        });
        var $popup_window = $('.popup[id=' + $popup_id + ']');
        $popup_window.css('visibility', 'visible');
        $popup_window.css('display', 'block');
        $popup_window .fadeTo('0.2s', 1);
    });

    // Click on cross to close popup window
    $(document).on('click', '.popup-close', function() {
        var $popup_window = $(this).parent().parent();
        $popup_window.fadeTo('0.2s', 0, function() {
            $popup_window.css('visibility', 'hidden');
            $popup_window.css('display', 'none');
        });
    });

    // Popup window form ajax submit
    $('.popup').on('submit', 'form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $ajax_loader = $form.find('.ajax-loader');
        var $popup_window = $form.closest('.popup');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            beforeSend: function(){
                $ajax_loader.show();
            },
            success: function(data) {
                console.log(data);
                if(data.error) {
                    $popup_window.find('.error-message').html(data.message);
                } else {
                    if(data.html) {
                        for(var id in data.html) {
                            $('#' + id).html(data.html[id]);
                        }
                    }
                    if(data.close_window) {
                        $popup_window.fadeTo('1.0s', 0, function() {
                            $popup_window.css('visibility', 'hidden');
                            $popup_window.css('display', 'none');
                            if(data.hidden_html) {
                                for(var id in data.hidden_html) {
                                    $('#' + id).html(data.hidden_html[id]);
                                }
                            }
                            if(data.flash_message) {
                                flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                            }
                        });
                    } else {
                        if(data.hidden_html) {
                            for(var id in data.hidden_html) {
                                $('#' + id).html(data.hidden_html[id]);
                            }
                        }
                        if(data.flash_message) {
                            flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                        }
                    }
                }
            },
            complete: function() {
                $ajax_loader.hide();
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    for(var item in jsonResponse) {
                        $popup_window.find('.error-message').html(jsonResponse[item]);
                        return;
                    }
                } else {
                    // Something else goes wrong
                }
            }
        });
    })
});