if ($('.ui.checkbox.slider').length) {
    console.log("Loading semantic and slider");
    require.ensure([], function(require) {
        var $ = require('jquery');
        require('semantic');
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
    });
} else {
    console.log("No semantic slider detected - skipping slider");
}