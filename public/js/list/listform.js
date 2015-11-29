$(document).ready(function() {
    // Disable pressing enter to submit the form
    $('#list-form').find('input').on('keypress', function(e) {
        return e.which !== 13;
    });

    $('#list-form').find('.popup button').on('click', function(e) {
        e.preventDefault();
    });

    // Form recovery
    var $category_div = $('#category');
    var $category_options = $category_div.find('.select-dropdown div');
    var default_category = $category_div.find('input[name="category"]').val();
    if(!default_category) {
        default_category = $category_options.first().html();
    }
    $category_div.find('.selected .selected-option').html(default_category);
    $category_div.find('input[name="category"]').val(default_category);
    $category_options.filter(':contains("' + default_category + '")').addClass('active');

    var labels = $('#labels').find('input[name="labels"]').val().trim();
    if(labels) {
        labels = labels.split(',');
        var $labels_div = $('#labels');
        for(var i = 0; i < labels.length; i++) {
            $labels_div.append(labelSpan(labels[i]));
        }
    }

    // Adding more reference
    $('#add-reference-button').click(function() {
        var $hidden_input = $(this).parent().find('input:visible').last().next();
        var $next = $hidden_input.next();
        if($next.attr('id') == 'add-reference-button') {
            $next.slideUp();
        }
        $hidden_input.slideDown();
    });

    // Adding labels
    $('#add-label-button').click(function() {
        $(this).hide();
        $('#label-edit').slideDown('fast');
    });

    $('#label-edit').find('.plus-button').click(function() {
        var label = $(this).prev().val().trim();
        var $label_edit = $(this).parent();
        validateInput(label, {
            charset: 'alphabet|numeric|space|punctuation',
            min: 2,
            max: 20
        }, function(err) {
            var $labels = $('#labels');
            if(!err) {
                $labels.find('> span').each(function() {
                    if($(this).text() == label) {
                        err = "Label already added.";
                        $label_edit.find('.error-message').html(err);
                    }
                });
                if(!err) {
                    $labels.append(labelSpan(label));
                    $label_edit.find('input').val('');
                    $label_edit.find('.error-message').html('');
                    $label_edit.slideUp('fast');
                    $('#add-label-button').show();
                }
            } else {
                // Display error
                $label_edit.find('.error-message').html(err);
            }
        })

    });

    $('#labels').on('click', 'span.remove-icon', function() {
        $(this).parent().remove();
    });

    // Multi-select plug-in for folder selection
    $('#folder-select').multipleSelect({
        placeholder: 'Choose Folder(s)',
        maxHeight: 120,
        width: 200
    });

    // Assemble labels before submitting the form
    $('#list-form').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var $ajax_loader = $form.find('.ajax-loader');
        assembleLabels(function() {
            // Validate first, submit only if there's no error to avoid unnecessary photo uploading
            serverValidation($form, function (err) {
                if (err) {
                    $ajax_loader.hide();
                } else {
                    $form.find('input[name="validation"]').val(0);
                    // Assemble the location info
                    var form_data = new FormData(document.forms['list-form']);
                    console.log('Validation passed, try submitting!');

                    $.ajax({
                        type: $form.attr('method'),
                        url: $form.attr('action'),
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            console.log(data);
                            if(data.error) {
                                $form.find('.error-message').first().html(data.message);
                                $form.find('input[name="validation"]').val(1);
                            } else {
                                $form.find('.error-message').first().html('');
                                if(data.flash_message) {
                                    flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                                }
                                // Redirect...
                                console.log('List saved, gonna redirect');
                                setTimeout(function() {
                                    window.location.href = data.redirect_to;
                                }, 1500);
                            }
                        },
                        complete: function() {
                            $ajax_loader.hide();
                        },
                        error: function(xhr) {
                            $form.find('input[name="validation"]').val(0);
                            if(xhr.status == 422) {
                                var jsonResponse = JSON.parse(xhr.responseText);
                                for(var item in jsonResponse) {
                                    $form.find('.error-message').first().html(jsonResponse[item]);
                                    return;
                                }
                            } else {
                                // Something else goes wrong
                                console.log(xhr);
                            }
                        }
                    }); // End of ajax submission
                }
            }); // End of server validation
        });
    });
});

function assembleLabels(callback) {
    var labels = "";
    var $label_spans = $('#labels').find('> span');
    var labelNum = $label_spans.length;

    if(labelNum == 0) {
        $('#labels').find('input').val('');
        if(callback && typeof callback === "function") {
            callback();
        }
    } else {
        $label_spans.each(function () {
            if(labels) labels += ",";
            labels += $(this).text();
            labelNum--;
            if (labelNum == 0) {
                $('#labels').find('input').val(labels);
                if(callback && typeof callback === "function") {
                    callback();
                }
            }
        });
    }
}

function serverValidation($form, callback) {
    var $ajax_loader = $form.find('.ajax-loader');
    $form.find('input[name="validation"]').val(1);
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
                $form.find('.error-message').first().html(data.message);
                if(callback && typeof callback === "function") {
                    callback(true);
                }
            } else {
                $form.find('.error-message').first().html('');
                if(data.flash_message) {
                    flash(data.flash_message.message, data.flash_message.is_important, data.flash_message.error);
                }
                if(callback && typeof callback === "function") {
                    callback(false);
                }
            }
        },
        error: function(xhr) {
            if(xhr.status == 422) {
                var jsonResponse = JSON.parse(xhr.responseText);
                for(var item in jsonResponse) {
                    $form.find('.error-message').first().html(jsonResponse[item]);
                    if(callback && typeof callback === "function") {
                        callback(true);
                    }
                    return;
                }
            } else {
                // Something else goes wrong
                console.log(xhr);
                if(callback && typeof callback === "function") {
                    callback(true);
                }
            }
        }
    });
}
function labelSpan(label) {
    return '<span>' + label +'<span class="glyphicon glyphicon-remove remove-icon"></span></span>';
}