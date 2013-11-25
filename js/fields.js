$(function () {
    fieldInitialize();
});

var fieldInitialize = function () {
    fileUpladInitialize();
};

var fileUpladInitialize = function () {
    $(document).on({
        click: function (event) {
        event.stopPropagation();
        console.log('Hi');
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
};