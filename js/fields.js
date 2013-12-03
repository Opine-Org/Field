$(function () {
    fieldInitialize();
});

var fieldInitialize = function () {
    fileUpladInitialize();
    sliderInitialize();
    datePickerInitialize();
    selectizeInitialize();
};

var selectizeInitialize = function () {
    $('.selectize-tags').each(function () {
        var uniqid = 'tags-' + Math.random().toString(36).substr(2, 7);
        if (typeof($(this).attr('data-id')) != 'undefined') {
            return;
        }
        $(this).attr('data-id', uniqid);
        var controlled = $(this).attr('data-controlled');
        var multiple = $(this).attr('data-multiple');
        if (controlled == 1) {
            $(this).selectize({
                maxItems: 100
            });
        } else {
            if (multiple == 1) {
                $(this).selectize({
                    delimiter: ',',
                    persist: false,
                    createOnBlur: true,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    },
                    maxItems: 100
                });
            } else {
                $(this).selectize({
                    createOnBlur: true,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    },
                    maxItems: 1
                });
            }
        }
    });
};

var datePickerInitialize = function () {
    $('.datepicker').each(function () {
        var uniqid = 'datepicker-' + Math.random().toString(36).substr(2, 7);
        if (typeof($(this).attr('data-id')) != 'undefined') {
            return;
        }
        $(this).attr('data-id', uniqid);
        $(this).pikaday({
            format: 'MM/DD/YYYY',
        });
    });
};

var sliderInitialize = function () {
    $('.ui.checkbox.slider').each(function () {
        var uniqid = 'slider-' + Math.random().toString(36).substr(2, 7);
        if (typeof($(this).attr('data-id')) != 'undefined') {
            return;
        }
        $(this).attr('data-id', uniqid);
        $(this).checkbox({
            onEnable: function () {
                $(this).val('t');
            },
            onDisable: function () {
                $(this).val('f');
            }
        });
        if ($(this).find('input').val() == 't') {
            $(this).checkbox('enable');
        }
    });
};

var fileUpladInitialize = function () {
    $(document).on({
        click: function (event) {
        event.stopPropagation();
    }}, '.trash.button');
    $('.fileupload').each(function () {
        var container = $(this).parents('.field');
        var field = $(this).parents('.field').attr('data-field');
        var manager = $(this).parents('form').attr('data-manager');
        var uniqid = 'fileupload-' + Math.random().toString(36).substr(2, 7);
        if (typeof($(this).attr('data-id')) != 'undefined') {
            return;
        }
        $(this).attr('data-id', uniqid);
        $('input[data-id="' + uniqid + '"]').fileupload({
            url: '/Manager/upload/' + manager + '/' + field,
            dataType: 'json',
            done: function (e, data) {
                $(container).find('input[type="hidden"]').remove();
                $.each(data.result, function (key, value) {
                    $('<input type="hidden" name="' + manager + '[' + field + '][' + key + ']" value="' + value + '" />').appendTo(container);
                });
                var image = '';
                var message = data.result.name;
                var type = data.result.type.toLowerCase();
                if (type.indexOf('jpeg') != -1 || type.indexOf('jpg') != -1 || type.indexOf('png') != -1 || type.indexOf('gif') != -1) {
                    image = '<a href="' + data.result['url'] + '" target="_blank"><img style="z-index: 2" class="ui mini image" src="' + data.result['url'] + '" /></a>';
                } else {
                    image = '<a href="' + data.result['url'] + '" target="_blank"><i style="z-index: 2" class="file icon"></i></a>';
                }
                $(container).find('.fileinput-button > a').remove();
                $(container).find('.fileinput-button').prepend(image);
                $(container).find('.fileinput-button span').html(message);
                $(container).find('.fileinput-button').removeClass('drop');
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $(container).find('.progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        });
    });

    $(document).bind('dragover', function (e) {
        var dropZone = $('.fileupload'),
            timeout = window.dropZoneTimeout;
        if (!timeout) {
            var parent = $(dropZone).parent('.segment');
            parent.addClass('drop');
        } else {
            clearTimeout(timeout);
        }
        var found = false,
            node = e.target;
        do {
            if (node === dropZone[0]) {
                found = true;
                break;
            }
            node = node.parentNode;
        } while (node != null);
        if (found) {
            dropZone.addClass('hover');
        } else {
            dropZone.removeClass('hover');
        }
        window.dropZoneTimeout = setTimeout(function () {
            window.dropZoneTimeout = null;
            dropZone.removeClass('in hover');
        }, 100);
    });

    $('.fileupload').bind('dragleave', function (e) {
        var parent = $(this).parent('.segment');
        parent.removeClass('drop');
    });

};