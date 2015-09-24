$( document ).ready(function() {
    $('.alert').not('.alert-important').delay(3000).slideUp(300);

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
});

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