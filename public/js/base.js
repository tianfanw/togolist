function getCookie(name) {
    function escape(s) { return s.replace(/([.*+?\^${}()|\[\]\/\\])/g, '\\$1'); };
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}

$( document ).ready(function() {

    // Set up ajax csrf token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Update xsrf token
    //$(document).ajaxSuccess(function() {
    //    var xsrf_token = getCookie('XSRF-TOKEN');
    //});

    $('.alert').not('.alert-important').delay(3000).slideUp(300);

    // Submit popup forms
    $(document).on('click', '.ajax-link', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            success: function(data) {
                if(data.message) {
                    flash(data.message, data.is_important, data.error);
                }
            },
            error: function(xhr) {
                if(xhr.status == 401) {
                    flash('Please log in first', false, true);
                }
            }
        })
    });

    // Dropdown menu
    $('.select-dropdown div').click(function() {
        var $active = $(this);
        var option = $active.html();
        var $dropdown = $(this).parent();
        $dropdown.find('.active').removeClass('active');
        $active.addClass('active');
        var $select = $dropdown.parent();
        $select.find('.selected-option').html(option);
        $select.find('input').val(option);
        $dropdown.slideUp('fast');
    });

    $(document).click(function (e) {
        var $eo = $(e.target);
        if($eo.parent().attr('class') == 'selected') $eo = $eo.parent();
        if($eo.attr('class') == 'selected') {
            var $dropdown = $eo.parent().find('.select-dropdown');
            if(!$dropdown.is(":visible")) {
                $dropdown.slideDown('fast');
            } else {
                $('.select-dropdown').slideUp('fast');
            }
        } else {
            $('.select-dropdown').slideUp('fast');
        }
    });
});

/**
 * Display flash message
 * @param message
 * @param is_important
 * @param is_error
 */
function flash(message, is_important, is_error) {
    var flash_message = '<div class="alert alert-dismissible ';
    if(is_error) {
        flash_message += 'alert-danger ';
    } else {
        flash_message += 'alert-success ';
    }
    flash_message += '" role="alert">';
    flash_message += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
                                <span aria-hidden="true">&times;</span>\
                            </button>' + message + '</div>';
    if(is_important) {
        $('#flash-messages').append(flash_message);
    } else {
        $(flash_message).appendTo($('#flash-messages')).delay(3000).slideUp(300);
    }
}

/**
 * Text input validation
 * @param str
 * @param rules
 * @param callback
 */
function validateInput(str, rules, callback) {
    var err = "";
    for(var rule in rules) {
        switch(rule) {
            case 'charset':
                var charsets = rules[rule].split('|');
                var alphabet = (charsets.indexOf('alphabet') != -1);
                var numeric =  (charsets.indexOf('numeric') != -1);
                var space = (charsets.indexOf('space') != -1);
                var isValid = true;
                for(var i = 0; i < str.length; i++) {
                    if(str[i] == ' ') {
                        if(!space) {
                            isValid = false;
                            break;
                        }
                    } else if(str[i] >= '0' && str[i] <= '9') {
                        if(!numeric) {
                            isValid = false;
                            break;
                        }
                    } else if( (str[i] >= 'a' && str[i] <= 'z') || (str[i] >= 'A' && str[i] <= 'Z') ) {
                        if(!alphabet) {
                            isValid = false;
                            break;
                        }
                    } else {
                        isValid = false;
                        break;
                    }
                }
                if(!isValid) {
                    err = "The input cannot contain special characters.";
                }
                break;
            case 'min':
                if(str.length < rules[rule]) {
                    err = "The input should contain at least " + rules[rule] + "characters.";
                }
                break;
            case 'max':
                if(str.length > rules[rule]) {
                    err = "The input cannot exceed " + rules[rule] + "characters.";
                }
                break;
            default:
                console.log('Unrecognized rule');
                break;
        }
        if(err) break;
    }
    if (typeof callback === "function") {
        callback(err);
    }
};

/**
 * Delete confirmation popup dialog
 * @param dialog
 * @param subject
 * @param deleteFunction
 */
function confirmDeletion(dialog, subject, deleteFunction) {
    dialog.find('.subject').html(subject);
    dialog.css('visibility', 'visible');
    dialog.css('display', 'block');
    dialog.fadeTo('0.2s', 1);
    dialog.on('click', '.confirm', function() {
        dialog.fadeTo('0.2s', 0, function() {
            dialog.css('visibility', 'hidden');
            dialog.css('display', 'none');
        });
        deleteFunction();
        dialog.off('click', '.confirm');
    });
}
