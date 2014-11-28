if ($('.fileupload').length) {
    console.log("Loading file uploader");
    require.ensure([], function(require) {
        var $ = require('jquery');
        require('../../bower_components/jquery-file-upload/js/jquery.iframe-transport.js');
        require('../../bower_components/jquery-file-upload/js/jquery.fileupload.js');
        require('../../public/css/fileupload.css');

        $(document).on({
            click: function (event) {
                event.stopPropagation();
                var manager = $(this).parents('form').attr('data-manager');
                var field = $(this).parents('.field').attr('data-field');
                var uniqid = Math.random().toString(36).substr(2, 7);
                var div = document.createElement("div");
                $('.ui.modal.delete').remove();
                $(div).addClass('ui small modal delete');
                $(div).attr('id', 'Modal-' + uniqid);
                div.innerHTML = '\
                    <i class="close icon"></i>\
                    <div class="header">Confirm Delete</div>\
                    <div class="delete content"><p>Are you sure you want to delete this image?</p></div>\
                    <div class="actions">\
                        <div class="ui negative button">No</div>\
                        <div class="ui positive right labeled icon confirmed manager-table imagedelete button">Yes<i class="checkmark icon"></i></div>\
                    </div>';
                $('body').append(div);
                $('.delete.modal').modal('show');
                $('.delete.content').html('Are you sure you want to delete this image?');
                $('.confirmed.imagedelete').attr('data-field', field);
                $('.confirmed.imagedelete').attr('data-manager', manager);
        }}, '.fileinput-button i.trash');

        $(document).on({
            click: function (event) {
                event.stopPropagation();
                var field = $(this).attr('data-field');
                var manager = $(this).attr('data-manager');
                var container = $('.field[data-field="' + field + '"]');
                var image = '<a><i style="z-index: 2; opacity: .2" class="add sign box icon"></i></a>';
                var message = '<span>Click to Upload, or Drag and Drop</span>';
                $(container).find('.fileinput-button > a').remove();
                $(container).find('.fileinput-button > span').remove();
                $(container).find('.fileinput-button').prepend(message);
                $(container).find('.fileinput-button').prepend(image);
                $(container).find('input[type="hidden"]').remove();
                $(container).append('<input type="hidden" name="' + manager + '[' + field + ']" value="" />');
        }}, '.confirmed.imagedelete');

        $('.fileupload').each(function () {
            var container = $(this).parents('.field');
            var field = $(this).parents('.field').attr('data-field');
            var manager = $(this).parents('form').attr('data-manager');
            var managerClass = $(this).parents('form').attr('data-class');
            var uniqid = 'fileupload-' + Math.random().toString(36).substr(2, 7);
            if (typeof($(this).attr('data-id')) != 'undefined') {
                return;
            }
            $(this).attr('data-id', uniqid);
            $('input[data-id="' + uniqid + '"]').fileupload({
                url: '/Manager/api/upload/' + manager + '/' + field,
                dataType: 'json',
                done: function (e, data) {
                    $(container).find('input[type="hidden"]').remove();
                    $.each(data.result, function (key, value) {
                        $('<input type="hidden" name="' + managerClass + '[' + field + '][' + key + ']" value="' + value + '" />').appendTo(container);
                    });
                    var image = '';
                    var message = data.result.name;
                    var type = data.result.type.toLowerCase();
                    if (type.indexOf('jpeg') != -1 || type.indexOf('jpg') != -1 || type.indexOf('png') != -1 || type.indexOf('gif') != -1) {
                        image = '<a href="' + data.result['url'] + '" target="_blank"><img style="z-index: 2" class="ui mini image" src="' + data.result['url'] + '" /></a>';
                    } else {
                        image = '<a href="' + data.result['url'] + '" target="_blank"><i style="z-index: 2" class="file icon"></i></a>';
                    }
                    $(container).find('.fileinput-button').addClass('uploaded');
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
/*
        $('.fileupload').bind('dragleave', function (e) {
            var parent = $(this).parent('.segment');
            parent.removeClass('drop');
        });
*/
    });
} else {
    console.log("No file uploader detected - skipping file uploader");
}