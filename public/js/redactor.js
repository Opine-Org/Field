if ($('.redactor').length) {
    console.log("Loading redactor");
    require.ensure([], function(require) {
        var $ = require('jquery');
        require('../../bower_components/imperavi-redactor-widget/assets/redactor.css');
        require('../../bower_components/imperavi-redactor-widget/assets/redactor-font.eot');
        require('../../bower_components/imperavi-redactor-widget/dist/redactor.js');
        $('.redactor').each(function () {
            var uniqid = 'redactor-' + Math.random().toString(36).substr(2, 7);
            if (typeof($(this).attr('data-id')) != 'undefined') {
                return;
            }
            $(this).attr('data-id', uniqid);
            $(this).redactor({
                plugins: ['fullscreen', 'fontcolor', 'fontsize', 'table', 'textdirection', 'video'],
                imageUpload: '/Manager/api/upload/redactor/file',
                linkTooltip: true
            });
        });
    });
} else {
    console.log("No editor detected - skipping redactor");
}