if ($('.datepicker').length) {
    console.log("Loading pikaday");
    require.ensure([], function(require) {
        var $ = require('jquery');
        require('../../bower_components/pikaday/css/pikaday.css');
        require('../../bower_components/pikaday/plugins/pikaday.jquery.js');
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
    });
} else {
    console.log("No calendar detected - skipping pikaday");
}